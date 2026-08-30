<?php
/**
 * 文章与分类固定链接规则。
 *
 * @package EryitangCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** 当前固定链接规则版本。 */
const ERYITANG_PERMALINK_RULES_VERSION = '1';

/**
 * 获取分类从一级到当前分类的 slug 路径。
 *
 * @param WP_Term|int $term 分类或分类ID。
 * @return string
 */
function eryitang_category_path( $term ) {
	$term = is_numeric( $term ) ? get_term( (int) $term, 'category' ) : $term;
	if ( ! $term instanceof WP_Term || 'category' !== $term->taxonomy ) {
		return '';
	}

	$slugs = array();
	foreach ( array_reverse( get_ancestors( $term->term_id, 'category', 'taxonomy' ) ) as $ancestor_id ) {
		$ancestor = get_term( $ancestor_id, 'category' );
		if ( $ancestor instanceof WP_Term ) {
			$slugs[] = $ancestor->slug;
		}
	}
	$slugs[] = $term->slug;
	return implode( '/', array_filter( $slugs ) );
}

/**
 * 返回分类的新地址；分类不存在时仍返回可预测的一级目录地址。
 *
 * @param string $slug 一级分类 slug。
 * @return string
 */
function eryitang_category_url( $slug ) {
	$term = get_category_by_slug( sanitize_title( $slug ) );
	if ( $term ) {
		$url = get_category_link( $term );
		if ( ! is_wp_error( $url ) ) {
			return $url;
		}
	}
	return home_url( user_trailingslashit( sanitize_title( $slug ), 'category' ) );
}

/**
 * 分类归档移除 /category/ 前缀，同时保留父子分类目录。
 */
function eryitang_filter_category_link( $termlink, $term_id ) {
	$path = eryitang_category_path( $term_id );
	return $path ? home_url( user_trailingslashit( $path, 'category' ) ) : $termlink;
}
add_filter( 'category_link', 'eryitang_filter_category_link', 20, 2 );

/**
 * 为每个现有分类注册无 category 前缀的归档、分页和订阅规则。
 */
function eryitang_register_category_routes() {
	$terms = get_terms(
		array(
			'taxonomy'   => 'category',
			'hide_empty' => false,
		)
	);
	if ( is_wp_error( $terms ) ) {
		return;
	}

	usort(
		$terms,
		static function ( $a, $b ) {
			return substr_count( eryitang_category_path( $b ), '/' ) <=> substr_count( eryitang_category_path( $a ), '/' );
		}
	);

	foreach ( $terms as $term ) {
		$path = eryitang_category_path( $term );
		if ( ! $path ) {
			continue;
		}
		$quoted = preg_quote( $path, '#' );
		$query  = 'index.php?category_name=' . $path;
		add_rewrite_rule( '^' . $quoted . '/?$', $query, 'top' );
		add_rewrite_rule( '^' . $quoted . '/page/?([0-9]{1,})/?$', $query . '&paged=$matches[1]', 'top' );
		add_rewrite_rule( '^' . $quoted . '/(?:feed/)?(feed|rdf|rss|rss2|atom)/?$', $query . '&feed=$matches[1]', 'top' );
	}
}
add_action( 'init', 'eryitang_register_category_routes', 20 );

/**
 * 获取分类所属的一级分类。
 *
 * @param WP_Term $term 分类。
 * @return WP_Term|null
 */
function eryitang_top_level_category( $term ) {
	if ( ! $term instanceof WP_Term || 'category' !== $term->taxonomy ) {
		return null;
	}
	$ancestors = get_ancestors( $term->term_id, 'category', 'taxonomy' );
	$top_id    = $ancestors ? (int) end( $ancestors ) : (int) $term->term_id;
	$top       = get_term( $top_id, 'category' );
	return $top instanceof WP_Term ? $top : null;
}

/**
 * 获取文章用于网址和面包屑的一级主分类。
 *
 * @param int $post_id 文章ID。
 * @return WP_Term|null
 */
function eryitang_get_primary_category( $post_id ) {
	$categories = wp_get_post_terms( $post_id, 'category' );
	if ( is_wp_error( $categories ) || ! $categories ) {
		return null;
	}

	$branches = array();
	foreach ( $categories as $category ) {
		$top = eryitang_top_level_category( $category );
		if ( $top ) {
			$branches[ $top->term_id ] = $top;
		}
	}

	$saved_id = absint( get_post_meta( $post_id, '_eryitang_primary_category', true ) );
	if ( $saved_id && isset( $branches[ $saved_id ] ) ) {
		return $branches[ $saved_id ];
	}

	ksort( $branches, SORT_NUMERIC );
	return $branches ? reset( $branches ) : null;
}

/** 使用后台指定的一级主分类替换 WordPress 默认分类选择。 */
function eryitang_filter_post_link_category( $category, $categories, $post ) {
	$primary = $post instanceof WP_Post ? eryitang_get_primary_category( $post->ID ) : null;
	return $primary ?: $category;
}
add_filter( 'post_link_category', 'eryitang_filter_post_link_category', 20, 3 );

/**
 * 为旧的根目录文章地址保留精确路由，随后统一301到新地址。
 */
function eryitang_register_legacy_post_routes() {
	$post_ids = get_posts(
		array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		)
	);
	foreach ( $post_ids as $post_id ) {
		$slug = get_post_field( 'post_name', $post_id );
		if ( ! $slug || get_page_by_path( $slug, OBJECT, 'page' ) || get_category_by_slug( $slug ) ) {
			continue;
		}
		add_rewrite_rule( '^' . preg_quote( $slug, '#' ) . '/?$', 'index.php?post_type=post&name=' . $slug . '&eryitang_legacy_post=1', 'top' );
	}
}
add_action( 'init', 'eryitang_register_legacy_post_routes', 21 );

/** 注册旧文章地址识别参数。 */
function eryitang_permalink_query_vars( $vars ) {
	$vars[] = 'eryitang_legacy_post';
	return $vars;
}
add_filter( 'query_vars', 'eryitang_permalink_query_vars' );

/**
 * 旧分类和旧文章地址301跳转到唯一新地址。
 */
function eryitang_redirect_legacy_urls() {
	if ( is_admin() || wp_doing_ajax() ) {
		return;
	}

	$request_path = trim( (string) wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ), PHP_URL_PATH ), '/' );
	if ( is_category() && 0 === strpos( $request_path, 'category/' ) ) {
		$target = home_url( user_trailingslashit( substr( $request_path, strlen( 'category/' ) ), 'category' ) );
		wp_safe_redirect( $target, 301, 'Eryitang Permalinks' );
		exit;
	}

	if ( get_query_var( 'eryitang_legacy_post' ) && is_singular( 'post' ) ) {
		wp_safe_redirect( get_permalink( get_queried_object_id() ), 301, 'Eryitang Permalinks' );
		exit;
	}
}
add_action( 'template_redirect', 'eryitang_redirect_legacy_urls', 1 );

/**
 * 将后台链接配置中的本站旧分类前缀更新为新前缀。
 *
 * @param mixed $value 配置值。
 * @return mixed
 */
function eryitang_normalize_legacy_category_urls( $value ) {
	if ( is_array( $value ) ) {
		foreach ( $value as $key => $item ) {
			$value[ $key ] = eryitang_normalize_legacy_category_urls( $item );
		}
		return $value;
	}

	if ( ! is_string( $value ) ) {
		return $value;
	}

	return str_replace( home_url( '/category/' ), home_url( '/' ), $value );
}

/**
 * 一次性切换文章结构并为已有文章固化一级主分类。
 */
function eryitang_upgrade_permalink_rules() {
	if ( ERYITANG_PERMALINK_RULES_VERSION === get_option( 'eryitang_permalink_rules_version' ) ) {
		return;
	}

	$post_ids = get_posts(
		array(
			'post_type'      => 'post',
			'post_status'    => array( 'publish', 'draft', 'pending', 'future', 'private' ),
			'posts_per_page' => -1,
			'fields'         => 'ids',
		)
	);
	foreach ( $post_ids as $post_id ) {
		$primary = eryitang_get_primary_category( $post_id );
		if ( $primary ) {
			update_post_meta( $post_id, '_eryitang_primary_category', $primary->term_id );
		}
	}

	$options = get_option( 'eryitang_options', array() );
	if ( is_array( $options ) ) {
		update_option( 'eryitang_options', eryitang_normalize_legacy_category_urls( $options ) );
	}

	global $wp_rewrite;
	$wp_rewrite->set_permalink_structure( '/%category%/%postname%/' );
	update_option( 'eryitang_permalink_rules_version', ERYITANG_PERMALINK_RULES_VERSION );
	$wp_rewrite->flush_rules( false );
}
add_action( 'init', 'eryitang_upgrade_permalink_rules', 99 );
