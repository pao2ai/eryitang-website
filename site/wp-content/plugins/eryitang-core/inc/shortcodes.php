<?php
/**
 * 供区块主题使用的动态全站信息。
 *
 * @package EryitangCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 获取随主题发布的默认视觉素材。
 *
 * 后台一旦选择正式图片，会覆盖这些回退素材。
 *
 * @param string $relative_path 相对主题目录的路径。
 * @return string
 */
function eryitang_theme_asset_url( $relative_path ) {
	return get_theme_file_uri( ltrim( $relative_path, '/' ) );
}

/**
 * 输出带尺寸与 srcset 的媒体库图片；非媒体库地址使用安全回退。
 *
 * @param string $url 图片地址。
 * @param string $alt 替代文本。
 * @param array  $attributes 图片属性。
 * @return string
 */
function eryitang_responsive_image_html( $url, $alt = '', $attributes = array() ) {
	$attachment_id = attachment_url_to_postid( $url );
	$attributes     = array_merge(
		array(
			'alt'      => $alt,
			'decoding' => 'async',
		),
		$attributes
	);

	if ( $attachment_id ) {
		return wp_get_attachment_image( $attachment_id, 'full', false, $attributes );
	}

	$attribute_html = '';
	foreach ( $attributes as $name => $value ) {
		if ( '' !== $value && false !== $value && null !== $value ) {
			$attribute_html .= ' ' . esc_attr( $name ) . '="' . esc_attr( $value ) . '"';
		}

	}
	return '<img src="' . esc_url( $url ) . '"' . $attribute_html . '>';
}

/**
 * 输出预约电话。
 *
 * @return string
 */
function eryitang_phone_shortcode() {
	$phone         = eryitang_get_option( 'phone', '199 8209 7343' );
	$phone_display = $phone ? $phone : '199 8209 7343';
	$phone_href    = preg_replace( '/[^0-9+]/', '', $phone_display );

	return sprintf(
		'<p class="eryitang-header__phone"><a href="%1$s">%2$s</a></p>',
		esc_url( 'tel:' . $phone_href ),
		esc_html( $phone_display )
	);
}
add_shortcode( 'eryitang_phone', 'eryitang_phone_shortcode' );

/**
 * 输出公共页头或页脚品牌标识。
 *
 * @param array $atts 短代码参数。
 * @return string
 */
function eryitang_logo_shortcode( $atts ) {
	$atts    = shortcode_atts( array( 'variant' => 'header' ), $atts, 'eryitang_logo' );
	$variant = 'footer' === $atts['variant'] ? 'footer' : 'header';
	$file    = 'footer' === $variant ? 'logo-footer.webp' : 'logo-header.webp';
	$key     = 'footer' === $variant ? 'footer_logo' : 'header_logo';
	$alt_key = 'footer' === $variant ? 'footer_logo_alt' : 'header_logo_alt';
	$url     = eryitang_fixed_image_url( $key, 'assets/images/home-v3/' . $file );
	return '<a class="eryitang-logo eryitang-logo--' . esc_attr( $variant ) . '" href="' . esc_url( home_url( '/' ) ) . '" aria-label="返回尔意堂首页">' . eryitang_responsive_image_html( $url, eryitang_fixed_text( $alt_key, '尔意堂中医馆' ), array( 'width' => 480, 'height' => 'footer' === $variant ? 267 : 304 ) ) . '</a>';
}
add_shortcode( 'eryitang_logo', 'eryitang_logo_shortcode' );

/**
 * 输出页脚品牌说明。
 *
 * @return string
 */
function eryitang_footer_statement_shortcode() {
	$statement = eryitang_get_option( 'footer_statement', '以古法之精，养身心之和' );
	$statement = $statement ? $statement : '以古法之精，养身心之和';

	return '<p class="eryitang-footer__statement">' . esc_html( $statement ) . '</p>';
}
add_shortcode( 'eryitang_footer_statement', 'eryitang_footer_statement_shortcode' );

/**
 * 输出页脚电话号码。
 *
 * @return string
 */
function eryitang_footer_phone_shortcode() {
	$phone         = eryitang_get_option( 'phone', '199 8209 7343' );
	$phone_display = $phone ? $phone : '199 8209 7343';
	$phone_href    = preg_replace( '/[^0-9+]/', '', $phone_display );

	return sprintf(
		'<p class="eryitang-footer__phone"><a href="%1$s">%2$s</a></p>',
		esc_url( 'tel:' . $phone_href ),
		esc_html( $phone_display )
	);
}
add_shortcode( 'eryitang_footer_phone', 'eryitang_footer_phone_shortcode' );

/**
 * 获取文章总列表地址。
 *
 * 正式使用时建议在「设置 → 阅读」中指定文章页；未指定时使用约定地址。
 *
 * @return string
 */
function eryitang_get_articles_url() {
	$posts_page_id = (int) get_option( 'page_for_posts' );

	if ( $posts_page_id ) {
		return get_permalink( $posts_page_id );
	}

	return home_url( '/articles/' );
}

/**
 * 输出全站四类共用图片 Banner。
 *
 * Banner 只显示图片；语义标题保留给辅助技术与搜索引擎，不叠加在图片上。
 *
 * @param array $atts 短代码参数。
 * @return string
 */
function eryitang_page_banner_shortcode( $atts ) {
	$atts = shortcode_atts( array( 'type' => 'archive' ), $atts, 'eryitang_page_banner' );
	$type = sanitize_key( $atts['type'] );
	$types = array(
		'home'    => array( 'title' => '尔意堂中医馆', 'class' => 'image-banner', 'image_class' => 'image-banner-media' ),
		'brand'   => array( 'title' => '尔意堂品牌介绍', 'class' => 'brand-banner', 'image_class' => '' ),
		'contact' => array( 'title' => '联系尔意堂中医馆', 'class' => 'contact-hero', 'image_class' => '' ),
		'archive' => array( 'title' => '尔意堂中医馆文章与资讯', 'class' => 'inner-hero banner-only', 'image_class' => 'inner-banner-image' ),
	);

	if ( ! isset( $types[ $type ] ) ) {
		$type = 'archive';
	}

	$title = eryitang_get_option( $type . '_banner_title', '' ) ?: $types[ $type ]['title'];
	$defaults = array(
		'home'    => 'assets/images/home-v4/banner-firstscreen.webp',
		'brand'   => 'assets/images/brand-v2/brand-banner.webp',
		'contact' => 'assets/images/contact-v1/contact-banner.webp',
		'archive' => 'assets/images/article-list-v2/archive-banner.webp',
	);
	$image = eryitang_get_option( $type . '_banner_image_url', '' ) ?: eryitang_theme_asset_url( $defaults[ $type ] );
	$alt   = eryitang_get_option( $type . '_banner_alt', '' );

	$style   = 'contact' === $type && $image ? ' style="background-image:url(' . esc_url( $image ) . ')"' : '';
	$output  = '<' . ( 'contact' === $type ? 'header' : 'section' ) . ' class="' . esc_attr( $types[ $type ]['class'] ) . ( 'contact' === $type ? ' contact-hero-image-only' : '' ) . '"' . $style . ' aria-labelledby="eryitang-' . esc_attr( $type ) . '-banner-title">';
	$output .= '<h1 id="eryitang-' . esc_attr( $type ) . '-banner-title" class="sr-only">' . esc_html( $title ) . '</h1>';
	if ( $image && 'contact' !== $type ) {
		$image_attributes = array( 'fetchpriority' => 'high', 'loading' => 'eager' );
		if ( $types[ $type ]['image_class'] ) {
			$image_attributes['class'] = $types[ $type ]['image_class'];
		}
		$output .= '<div class="banner-media-slot">' . eryitang_responsive_image_html( $image, $alt, $image_attributes ) . '</div>';
	} else {
		$output .= '<div class="eryitang-page-banner__placeholder" role="img" aria-label="' . esc_attr( $title . ' Banner 图片待配置' ) . '"></div>';
	}
	$output .= '</' . ( 'contact' === $type ? 'header' : 'section' ) . '>';

	return $output;
}
add_shortcode( 'eryitang_page_banner', 'eryitang_page_banner_shortcode' );

/**
 * 获取当前分类及其一级分类。
 *
 * @return array{current:WP_Term|null,parent:WP_Term|null}
 */
function eryitang_get_category_context() {
	$current = is_category() ? get_queried_object() : null;

	if ( ! $current instanceof WP_Term ) {
		return array(
			'current' => null,
			'parent'  => null,
		);
	}

	$parent = $current;
	if ( $current->parent ) {
		$parent_term = get_term( $current->parent, 'category' );
		if ( $parent_term instanceof WP_Term ) {
			$parent = $parent_term;
		}
	}

	return array(
		'current' => $current,
		'parent'  => $parent,
	);
}

/**
 * 输出文章列表页头部。
 *
 * @return string
 */
function eryitang_archive_intro_shortcode() {
	return eryitang_page_banner_shortcode( array( 'type' => 'archive' ) );
}
add_shortcode( 'eryitang_archive_intro', 'eryitang_archive_intro_shortcode' );

/**
 * 输出一级与二级分类筛选。
 *
 * @return string
 */
function eryitang_category_filter_shortcode() {
	$category_slugs = array( 'therapies', 'conditions', 'tea', 'cases', 'news' );
	$context        = eryitang_get_category_context();
	$current        = $context['current'];
	$parent         = $context['parent'];
	$output         = '<nav class="category-filter-shell" aria-label="文章分类筛选">';
	$output        .= '<div class="category-bar">';

	foreach ( $category_slugs as $slug ) {
		$term = get_category_by_slug( $slug );
		if ( ! $term ) {
			continue;
		}

		$is_active = $parent && (int) $parent->term_id === (int) $term->term_id;
		$output   .= sprintf(
			'<a class="%1$s" href="%2$s"%3$s>%4$s</a>',
			$is_active ? 'active' : '',
			esc_url( get_category_link( $term ) ),
			$is_active ? ' aria-current="page"' : '',
			esc_html( $term->name )
		);
	}
	$output .= '</div>';

	if ( $parent ) {
		$children = get_terms(
			array(
				'taxonomy'   => 'category',
				'parent'     => $parent->term_id,
				'hide_empty' => false,
				'orderby'    => 'term_order',
				'order'      => 'ASC',
			)
		);

		if ( ! is_wp_error( $children ) && $children ) {
			$output        .= '<div class="subcategory-panel"><div class="subcategory-links">';
			$output        .= sprintf(
				'<a class="%1$s" href="%2$s"%3$s>全部</a>',
				$current && (int) $current->term_id === (int) $parent->term_id ? 'active' : '',
				esc_url( get_category_link( $parent ) ),
				$current && (int) $current->term_id === (int) $parent->term_id ? ' aria-current="page"' : ''
			);

			foreach ( $children as $child ) {
				$is_active = $current && (int) $current->term_id === (int) $child->term_id;
				$output   .= sprintf(
					'<a class="%1$s" href="%2$s"%3$s>%4$s</a>',
					$is_active ? 'active' : '',
					esc_url( get_category_link( $child ) ),
					$is_active ? ' aria-current="page"' : '',
					esc_html( $child->name )
				);
			}
			$output .= '</div></div>';
		}
	}

	$output .= '</nav>';
	return $output;
}
add_shortcode( 'eryitang_category_filter', 'eryitang_category_filter_shortcode' );

/**
 * 输出重点推荐列表。
 *
 * @return string
 */
function eryitang_recommended_posts_shortcode() {
	$limit        = 5;
	$excluded     = is_singular( 'post' ) ? array( get_the_ID() ) : array();
	$featured_ids = get_posts(
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => $limit,
			'ignore_sticky_posts' => true,
			'post__not_in'        => $excluded,
			'fields'              => 'ids',
			'meta_query'          => array(
				array(
					'key'     => '_eryitang_featured',
					'value'   => '1',
					'compare' => '=',
				),
			),
		)
	);

	$post_ids = array_map( 'absint', $featured_ids );
	if ( count( $post_ids ) < $limit ) {
		$fallback_ids = get_posts(
			array(
				'post_type'           => 'post',
				'post_status'         => 'publish',
				'posts_per_page'      => $limit - count( $post_ids ),
				'ignore_sticky_posts' => true,
				'post__not_in'        => array_merge( $excluded, $post_ids ),
				'fields'              => 'ids',
			)
		);
		$post_ids     = array_merge( $post_ids, array_map( 'absint', $fallback_ids ) );
	}

	$query = new WP_Query(
		array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => count( $post_ids ),
		'ignore_sticky_posts' => true,
			'post__in'            => $post_ids ? $post_ids : array( 0 ),
			'orderby'             => 'post__in',
		)
	);

	$output = '<section class="sidebar-block"><h2 class="sidebar-title">重点推荐</h2>';
	if ( ! $query->have_posts() ) {
		$placeholders = array(
			array( '董氏奇穴针灸：从精准取穴理解传统针法', '特色疗法', '2026.07.09' ),
			array( '尔意堂董氏奇穴非遗讲座开放报名', '医馆资讯', '2026.07.18' ),
			array( '从睡眠与情绪理解身心共养', '调理方向', '2026.06.28' ),
			array( '雷音艾灸的选材与温养之道', '特色疗法', '2026.06.16' ),
			array( '从日常习惯理解身体调养方向', '调理方向', '2026.06.08' ),
		);
		$output      .= '<div class="recommended-list">';
		foreach ( $placeholders as $index => $item ) {
			$output .= sprintf(
				'<div class="recommend-item is-placeholder"><div class="recommend-no">%1$02d</div><div><h3>%2$s</h3><div class="recommend-meta">%3$s</div></div></div>',
				$index + 1,
				esc_html( $item[0] ),
				esc_html( $item[1] )
			);
		}
		$output .= '</div>';
	} else {
		$output .= '<div class="recommended-list">';
		$index   = 0;
		while ( $query->have_posts() ) {
			$query->the_post();
			++$index;
			$category = eryitang_get_primary_category( get_the_ID() );
			$meta     = $category ? $category->name : '医馆资讯';
			$output  .= sprintf(
				'<div class="recommend-item"><div class="recommend-no">%1$02d</div><div><h3><a href="%2$s">%3$s</a></h3><div class="recommend-meta">%4$s</div></div></div>',
				$index,
				esc_url( get_permalink() ),
				esc_html( get_the_title() ),
				esc_html( $meta )
			);
		}
		$output .= '</div>';
	}
	$output .= '</section>';
	wp_reset_postdata();

	return $output;
}
add_shortcode( 'eryitang_recommended_posts', 'eryitang_recommended_posts_shortcode' );

/**
 * 输出单个广告位。
 *
 * @param array $atts 短代码参数。
 * @return string
 */
function eryitang_ad_slot_shortcode( $atts ) {
	$atts = shortcode_atts( array( 'slot' => 1 ), $atts, 'eryitang_ad_slot' );
	$slot = in_array( (int) $atts['slot'], array( 1, 2 ), true ) ? (int) $atts['slot'] : 1;
	$ad   = eryitang_get_ad_slot( $slot );

	if ( ! $ad['enabled'] || ! $ad['image_url'] ) {
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return '';
		}

		return sprintf(
			'<div class="ad-placeholder ad-placeholder--empty ad-placeholder--%1$d"><span>侧边广告位 %2$02d</span><small>在「尔意堂设置」中启用并上传素材</small></div>',
			$slot,
			$slot
		);
	}

	$image = sprintf(
		'<img src="%1$s" alt="%2$s"><span><b>%3$s</b></span>',
		esc_url( $ad['image_url'] ),
		esc_attr( $ad['alt'] ),
		esc_html( $ad['title'] )
	);

	if ( ! $ad['link_url'] ) {
		return '<div class="ad-placeholder ad-placeholder--' . esc_attr( $slot ) . '">' . $image . '</div>';
	}

	return sprintf(
		'<a class="ad-placeholder ad-placeholder--%1$d" href="%2$s"%3$s>%4$s</a>',
		$slot,
		esc_url( $ad['link_url'] ),
		$ad['new_window'] ? ' target="_blank" rel="noopener noreferrer"' : '',
		$image
	);
}
add_shortcode( 'eryitang_ad_slot', 'eryitang_ad_slot_shortcode' );

/**
 * 获取当前文章的相关文章。
 *
 * @return WP_Query
 */
function eryitang_get_related_posts_query() {
	$post_id = get_the_ID();
	$ids     = get_post_meta( $post_id, '_eryitang_related_posts', true );
	$ids     = is_array( $ids ) ? array_values( array_filter( array_map( 'absint', $ids ) ) ) : array();
	$args    = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => 4,
		'ignore_sticky_posts' => true,
		'post__not_in'        => array( $post_id ),
	);

	if ( $ids ) {
		$args['post__in'] = $ids;
		$args['orderby']  = 'post__in';
	} else {
		$category_ids = wp_get_post_categories( $post_id );
		if ( $category_ids ) {
			$args['category__in'] = $category_ids;
		}
	}

	return new WP_Query( $args );
}

/**
 * 输出相关文章：左侧一篇图文，右侧三篇标题。
 *
 * @return string
 */
function eryitang_related_posts_shortcode() {
	if ( ! is_singular( 'post' ) ) {
		return '';
	}

	$query = eryitang_get_related_posts_query();
	if ( ! $query->have_posts() ) {
		return '';
	}

	$posts  = $query->posts;
	$first  = array_shift( $posts );
	$output = '<section class="related-content"><div class="related-heading"><div><span>EXTENDED READING</span><h2>延伸阅读</h2></div></div><div class="related-grid">';
	$output .= '<a class="related-feature" href="' . esc_url( get_permalink( $first ) ) . '"><span class="related-cover">';
	if ( has_post_thumbnail( $first ) ) {
		$output .= get_the_post_thumbnail( $first, 'large', array( 'loading' => 'lazy' ) );
	} else {
		$output .= '<span class="related-placeholder">相关资讯封面图片占位</span>';
	}
	$first_category = eryitang_get_primary_category( $first->ID );
	$output .= '</span><span class="related-feature-copy"><small>' . esc_html( $first_category ? $first_category->name : '医馆资讯' ) . '</small><strong>' . esc_html( get_the_title( $first ) ) . '</strong></span></a>';
	$output .= '<div class="related-link-list">';
	foreach ( $posts as $related_post ) {
		$related_category = eryitang_get_primary_category( $related_post->ID );
		$output .= sprintf(
			'<a href="%1$s"><span>%3$s</span><strong>%2$s</strong></a>',
			esc_url( get_permalink( $related_post ) ),
			esc_html( get_the_title( $related_post ) ),
			esc_html( $related_category ? $related_category->name : '医馆资讯' )
		);
	}
	$output .= '</div></div></section>';
	wp_reset_postdata();

	return $output;
}
add_shortcode( 'eryitang_related_posts', 'eryitang_related_posts_shortcode' );

/**
 * 输出文末上一篇与下一篇两行文字链接。
 *
 * @return string
 */
function eryitang_post_navigation_shortcode() {
	if ( ! is_singular( 'post' ) ) {
		return '';
	}

	$previous = get_previous_post();
	$next     = get_next_post();
	if ( ! $previous && ! $next ) {
		return '';
	}

	$output = '<nav class="article-nav" aria-label="相邻文章">';
	if ( $previous ) {
		$output .= sprintf(
			'<a class="article-nav-prev" href="%1$s"><span>← 上一篇</span><strong>%2$s</strong></a>',
			esc_url( get_permalink( $previous ) ),
			esc_html( get_the_title( $previous ) )
		);
	}
	if ( $next ) {
		$output .= sprintf(
			'<a class="article-nav-next" href="%1$s"><span>下一篇 →</span><strong>%2$s</strong></a>',
			esc_url( get_permalink( $next ) ),
			esc_html( get_the_title( $next ) )
		);
	}
	$output .= '</nav>';

	return $output;
}
add_shortcode( 'eryitang_post_navigation', 'eryitang_post_navigation_shortcode' );

/**
 * 输出首页全宽 Banner。
 *
 * @return string
 */
function eryitang_home_banner_shortcode() {
	return eryitang_page_banner_shortcode( array( 'type' => 'home' ) );
}
add_shortcode( 'eryitang_home_banner', 'eryitang_home_banner_shortcode' );

/**
 * 输出首页、文章列表和文章详情共用的底部图片广告。
 *
 * @return string
 */
function eryitang_footer_ad_shortcode() {
	if ( ! eryitang_get_option( 'footer_ad_enabled', true ) ) {
		return '';
	}

	$image = eryitang_get_option( 'footer_ad_image_url', '' ) ?: eryitang_theme_asset_url( 'assets/images/home-v6/bg-footer-ad.webp' );
	if ( ! $image ) {
		return '';
	}

	$alt        = eryitang_get_option( 'footer_ad_alt', '' );
	$link       = eryitang_get_option( 'footer_ad_link_url', '' );
	$new_window = (bool) eryitang_get_option( 'footer_ad_new_window', false );
	$image_html = eryitang_responsive_image_html( $image, $alt, array( 'loading' => 'lazy' ) );

	if ( $link ) {
		$target = $new_window ? ' target="_blank" rel="noopener noreferrer"' : '';
		$image_html = '<a href="' . esc_url( $link ) . '"' . $target . '>' . $image_html . '</a>';
	}

	return '<aside class="homepage-ad" aria-label="图片广告位">' . $image_html . '</aside>';
}
add_shortcode( 'eryitang_footer_ad', 'eryitang_footer_ad_shortcode' );

/**
 * 输出全站合作咨询悬浮入口。
 */
function eryitang_render_consultation_float() {
	if ( is_admin() || ! eryitang_get_option( 'consultation_float_enabled', false ) ) {
		return;
	}

	$url = eryitang_get_option( 'consultation_float_url', '' );
	if ( ! $url ) {
		return;
	}

	$label      = eryitang_get_option( 'consultation_float_label', '合作咨询' ) ?: '合作咨询';
	$new_window = (bool) eryitang_get_option( 'consultation_float_new_window', false );
	printf(
		'<a class="shared-consultation-float eryitang-consultation-float" href="%1$s" aria-label="%2$s"%4$s>%3$s<span aria-hidden="true">↗</span></a>',
		esc_url( $url ),
		esc_attr( $label ),
		esc_html( $label ),
		$new_window ? ' target="_blank" rel="noopener noreferrer"' : ''
	);
}
add_action( 'wp_footer', 'eryitang_render_consultation_float', 20 );

/**
 * 输出首页八项疗法，并读取后台配置的卡片链接。
 *
 * @return string
 */
function eryitang_home_therapies_shortcode() {
	$therapies = array(
		array( '董氏奇穴针灸', 'therapy-acupuncture-new.webp', '董氏奇穴取穴精准，疗效独特，是中医文化的深厚底蕴与专业传承。' ),
		array( '道家推拿', 'therapy-tuina-new.webp', '推拿老师均为从业多年的专家，以手代针，松筋解结，有效缓解身体疲劳与疼痛。' ),
		array( '禅龙正骨', 'therapy-bone-new.webp', '结合多种传统正骨手法，从筋骨状态出发进行针对性调理。' ),
		array( '运动康复治疗', 'therapy-rehab-v2.webp', '将中医养生与运动康复理念结合，制定个体化康复计划。' ),
		array( '古方药油推拿', 'therapy-oil-new.webp', '采用古方制作方法调配药油，适合脏腑调理与全身推拿。' ),
		array( '道家降龙药蒸', 'therapy-steam-new.webp', '选用道地药材熬制中药，以温养方式帮助身体放松。' ),
		array( '非遗瑶浴', 'therapy-bath-new.webp', '选用瑶山道地药材，以传统熬制方法营造温润调养体验。' ),
		array( '雷音艾灸', 'therapy-moxa-new.webp', '选用优质艾绒，温通经络，帮助身体恢复温暖与舒展。' ),
	);

	$output  = '<section class="section therapies" id="therapies"><div class="container">';
	$output .= '<div class="section-head reveal"><div><h2 class="section-title art-title"><span>' . esc_html( eryitang_fixed_text( 'home_therapies_title', '八项特色技法' ) ) . '</span><em>' . esc_html( eryitang_fixed_text( 'home_therapies_subtitle', '，守其本真' ) ) . '</em></h2></div></div><div class="therapy-grid">';

	foreach ( $therapies as $index => $therapy ) {
		$number    = $index + 1;
		$title     = eryitang_fixed_text( 'therapy_' . $number . '_title', $therapy[0] );
		$desc      = eryitang_fixed_text( 'therapy_' . $number . '_desc', $therapy[2] );
		$alt       = eryitang_fixed_text( 'therapy_' . $number . '_alt', $title . '调理场景' );
		$url       = eryitang_get_option( 'home_therapy_' . ( $index + 1 ) . '_url', '' );
		$link_attr = $url ? ' data-card-href="' . esc_url( $url ) . '" role="link" tabindex="0" aria-label="了解' . esc_attr( $title ) . '"' : '';
		$image_url = eryitang_fixed_image_url( 'therapy_' . $number . '_image', 'assets/images/home-v3/' . $therapy[1] );
		$output   .= '<article class="therapy-item reveal delay-' . esc_attr( (string) ( $index % 4 ) ) . '" data-no="' . esc_attr( sprintf( '%02d', $index + 1 ) ) . '"' . $link_attr . '><div class="therapy-image">' . eryitang_responsive_image_html( $image_url, $alt, array( 'loading' => 'lazy' ) ) . '</div><div class="therapy-body"><h3>' . esc_html( $title ) . '</h3><p>' . esc_html( $desc ) . '</p></div><span class="therapy-mark"></span></article>';
	}

	$output .= '</div></div></section>';
	return $output;
}
add_shortcode( 'eryitang_home_therapies', 'eryitang_home_therapies_shortcode' );

/**
 * 输出首页医师团队。
 *
 * @return string
 */
function eryitang_home_doctors_shortcode() {
	$query = new WP_Query(
		array(
			'post_type'      => 'doctor',
			'post_status'    => 'publish',
			'posts_per_page' => 5,
			'meta_key'       => '_eryitang_doctor_order',
			'orderby'        => array( 'meta_value_num' => 'ASC', 'menu_order' => 'ASC', 'date' => 'ASC' ),
			'meta_query'     => array(
				array(
					'key'     => '_eryitang_doctor_home',
					'value'   => '1',
					'compare' => '=',
				),
			),
		)
	);

	$output = '<section class="section doctors" id="doctors"><div class="container">';
	$output .= '<div class="section-head reveal"><div><h2 class="section-title art-title"><span>医者守正</span><em>，以专业为本</em></h2></div></div>';
	$output .= '<div class="doctor-carousel" data-carousel><div class="doctor-viewport"><div class="doctor-grid">';
	$index   = 0;

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			++$index;
			$title     = get_post_meta( get_the_ID(), '_eryitang_doctor_title', true );
			$specialty = get_post_meta( get_the_ID(), '_eryitang_doctor_specialty', true );
			$link_url  = get_post_meta( get_the_ID(), '_eryitang_doctor_link_url', true );
			$new_window = (bool) get_post_meta( get_the_ID(), '_eryitang_doctor_link_new_window', true );
			$summary   = get_the_excerpt() ?: $specialty;
			$link_attr = $link_url ? ' data-card-href="' . esc_url( $link_url ) . '" role="link" tabindex="0" aria-label="了解' . esc_attr( get_the_title() ) . '医师"' . ( $new_window ? ' data-card-new-window="1"' : '' ) : '';
			$output   .= '<article class="doctor-card reveal"' . $link_attr . '>';
			if ( has_post_thumbnail() ) {
				$output .= '<div class="doctor-photo">' . get_the_post_thumbnail( get_the_ID(), 'large', array( 'loading' => 'lazy' ) ) . '</div>';
			} else {
				$output .= '<div class="doctor-photo" data-label="医师照片占位"></div>';
			}
			$output .= '<div class="doctor-info">';
			$output .= '<h3>' . esc_html( get_the_title() ) . '</h3>';
			$output .= '<p>' . esc_html( $summary ) . '</p>';
			if ( $specialty || $title ) {
				$tags = array_filter( preg_split( '/[、，,｜|\/]+/u', $specialty ?: $title ) );
				$output .= '<div class="doctor-tags">';
				foreach ( array_slice( $tags, 0, 4 ) as $tag_text ) {
					$output .= '<span>' . esc_html( trim( $tag_text ) ) . '</span>';
				}
				$output .= '</div>';
			}
			$output .= '</div></article>';
		}
	}

	$placeholders = array(
		array( '朱守一', '深耕中医诊疗与筋膜筋骨调理多年，恪守守一不移、固本培元的行医初心。' ),
		array( '陈纤纤', '中医骨伤硕士，关注运动创伤性疾病、常见筋骨疾病及运动康复治疗。' ),
		array( '刘杭', '康复治疗师，关注肩颈腰腿疼痛、运动创伤康复与功能训练。' ),
		array( '董氏奇穴传承医师', '专注董氏奇穴针灸及相关调理方向，重视辨证、取穴与施治过程。' ),
		array( '资深中医坐诊医师', '重视中医辨证与个体情况，从整体状态出发关注脏腑、经络与日常调养。' ),
	);
	while ( $index < 5 ) {
		$placeholder = $placeholders[ $index ];
		++$index;
		$output .= '<article class="doctor-card reveal"><div class="doctor-photo" data-label="医师照片占位"></div><div class="doctor-info"><h3>' . esc_html( $placeholder[0] ) . '</h3><p>' . esc_html( $placeholder[1] ) . '</p></div></article>';
	}

	$output .= '</div></div><div class="doctor-controls" aria-label="医师轮播控制"><button class="doctor-control doctor-prev" type="button" aria-label="上一位医师">←</button><button class="doctor-control doctor-next" type="button" aria-label="下一位医师">→</button></div></div></div></section>';
	wp_reset_postdata();
	return $output;
}
add_shortcode( 'eryitang_home_doctors', 'eryitang_home_doctors_shortcode' );

/**
 * 输出首页最新资讯。
 *
 * @return string
 */
function eryitang_home_news_shortcode() {
	$query_args = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => 5,
		'ignore_sticky_posts' => true,
		'meta_key'            => '_eryitang_home_order',
		'orderby'             => array( 'meta_value_num' => 'ASC', 'date' => 'DESC' ),
		'meta_query'          => array(
			array(
				'key'     => '_eryitang_home_featured',
				'value'   => '1',
				'compare' => '=',
			),
		),
	);
	$query = new WP_Query( $query_args );
	if ( ! $query->have_posts() ) {
		unset( $query_args['meta_key'], $query_args['meta_query'] );
		$query_args['orderby'] = 'date';
		$query = new WP_Query( $query_args );
	}

	$output = '<section class="section news" id="news"><div class="container">';
	$output .= '<div class="section-head reveal"><div><h2 class="section-title art-title"><span>医馆</span><em>资讯</em></h2><p class="section-desc">记录医馆活动、非遗传承与中医文化交流，让传统智慧在当代生活中继续生长。</p></div><div class="btn btn-outline" data-card-href="' . esc_url( eryitang_get_articles_url() ) . '" role="link" tabindex="0"><span>查看全部资讯</span></div></div>';

	if ( $query->have_posts() ) {
		$posts   = $query->posts;
		$feature = array_shift( $posts );
		$output .= '<div class="news-layout"><article class="news-feature reveal" data-card-href="' . esc_url( get_permalink( $feature ) ) . '" role="link" tabindex="0">';
		if ( has_post_thumbnail( $feature ) ) {
			$output .= '<div class="news-image has-image">' . get_the_post_thumbnail( $feature, 'large', array( 'loading' => 'lazy' ) ) . '</div>';
		} else {
			$output .= '<div class="news-image" data-label="活动资讯图片占位"></div>';
		}
		$category = eryitang_get_primary_category( $feature->ID );
		$output .= '<div class="news-feature-copy"><div class="news-meta">' . esc_html( $category ? $category->name : '医馆资讯' ) . ' · ' . esc_html( get_the_date( 'Y', $feature ) ) . '</div><h3>' . esc_html( get_the_title( $feature ) ) . '</h3><p>' . esc_html( get_the_excerpt( $feature ) ) . '</p><div class="text-link">阅读全文</div></div></article>';
		$output .= '<div class="news-list reveal delay-1">';
		foreach ( $posts as $post_item ) {
			$output .= '<article class="news-row" data-card-href="' . esc_url( get_permalink( $post_item ) ) . '" role="link" tabindex="0"><div class="news-date">' . esc_html( get_the_date( 'd', $post_item ) ) . '<small>' . esc_html( get_the_date( 'n月', $post_item ) ) . '</small></div><div><h4>' . esc_html( get_the_title( $post_item ) ) . '</h4><p>' . esc_html( get_the_excerpt( $post_item ) ) . '</p></div></article>';
		}
		$output .= '</div></div>';
	} else {
		$output .= '<div class="news-layout"><div class="news-feature reveal"><div class="news-image" data-label="活动资讯图片占位"></div><div class="news-feature-copy"><span class="news-meta">内容准备中</span><h3>首批医馆资讯发布后将在这里自动展示</h3><p>后台勾选“在首页资讯中展示”并设置顺序，即可更新首页内容。</p></div></div></div>';
	}

	$output .= '</div></section>';
	wp_reset_postdata();
	return $output;
}
add_shortcode( 'eryitang_home_news', 'eryitang_home_news_shortcode' );

/**
 * 输出品牌页资质证书与荣誉锦旗。
 *
 * @return string
 */
function eryitang_brand_credentials_shortcode() {
	$query = new WP_Query(
		array(
			'post_type'      => 'credential',
			'post_status'    => 'publish',
			'posts_per_page' => 12,
			'meta_key'       => '_eryitang_credential_order',
			'orderby'        => array( 'meta_value_num' => 'ASC', 'date' => 'ASC' ),
		)
	);

	$output  = '<section class="brand-credentials brand-paper-section" id="credentials"><div class="container">';
	$output .= '<header class="brand-section-heading reveal"><div class="brand-section-ornament" aria-hidden="true"><span></span><i></i></div><h2>资质为凭，荣誉见证</h2></header><div class="brand-credential-grid">';

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$type       = get_post_meta( get_the_ID(), '_eryitang_credential_type', true );
			$type       = 'pennant' === $type ? 'pennant' : 'certificate';
			$type_label = 'pennant' === $type ? '荣誉锦旗' : '资质证书';
			$note       = get_post_meta( get_the_ID(), '_eryitang_credential_note', true );
			$note       = $note ?: ( 'pennant' === $type ? '赠送信息待补充' : '证书信息待补充' );

			$output .= '<figure class="brand-credential-card reveal">';
			$output .= '<div class="brand-credential-image is-real">';
			if ( has_post_thumbnail() ) {
				$output .= get_the_post_thumbnail( get_the_ID(), 'large', array( 'loading' => 'lazy' ) );
			} else {
				$output .= '<div class="brand-credential-placeholder"><span>' . esc_html( $type_label ) . '图片占位</span><strong>' . esc_html( get_the_title() ) . '</strong></div>';
			}
			$output .= '</div><figcaption><strong>' . esc_html( get_the_title() ) . '</strong></figcaption></figure>';
		}
	} else {
		$placeholders = array(
			array( 'certificate', '医馆执业资质', '中医诊所备案证', '证书名称与编号待补充' ),
			array( 'certificate', '非遗传承证明', '非物质文化遗产传承资质', '颁发单位与时间待补充' ),
			array( 'certificate', '专业认证证书', '医馆专业资质证书', '证书信息待补充' ),
			array( 'pennant', '仁心仁术', '来访者荣誉锦旗', '锦旗内容与赠送信息待补充' ),
			array( 'pennant', '医德高尚', '医馆荣誉锦旗', '锦旗内容与赠送信息待补充' ),
		);

		foreach ( $placeholders as $item ) {
			$type_label = 'pennant' === $item[0] ? '荣誉锦旗图片占位' : '资质证书图片占位';
			$fallback = eryitang_theme_asset_url( 'assets/images/brand-v1/credential-registration.png' );
			$output  .= '<figure class="brand-credential-card reveal"><div class="brand-credential-image is-real is-placeholder"><img src="' . esc_url( $fallback ) . '" alt="' . esc_attr( $item[2] . '图片占位' ) . '" loading="lazy"></div><figcaption><strong>' . esc_html( $item[2] ) . '</strong></figcaption></figure>';
		}
	}

	$output .= '</div></div></section>';
	wp_reset_postdata();

	return $output;
}
add_shortcode( 'eryitang_brand_credentials', 'eryitang_brand_credentials_shortcode' );

/**
 * 输出联系页联系信息、微信二维码和医馆图片。
 *
 * @return string
 */
function eryitang_contact_information_shortcode() {
	$clinic_name = eryitang_get_option( 'clinic_name', '尔意堂' ) ?: '尔意堂';
	$phone       = eryitang_get_option( 'phone', '199 8209 7343' ) ?: '199 8209 7343';
	$phone_href  = preg_replace( '/[^0-9+]/', '', $phone );
	$address     = eryitang_get_option( 'address', '成都市锦江区梨花街8号名望大厦501' ) ?: '成都市锦江区梨花街8号名望大厦501';
	$transport   = eryitang_get_option( 'transport_note', '天府广场地铁D出口步行约450米' ) ?: '天府广场地铁D出口步行约450米';
	$hours       = eryitang_get_option( 'business_hours', '每日 09:00—19:00' ) ?: '每日 09:00—19:00';
	$qr_url      = eryitang_get_option( 'wechat_qr_url', '' );
	$qr_alt      = eryitang_get_option( 'wechat_qr_alt', '' ) ?: $clinic_name . '医馆微信二维码';
	$image_url   = eryitang_get_option( 'contact_image_url', '' );
	$image_alt   = eryitang_get_option( 'contact_image_alt', '' ) ?: $clinic_name . '医馆空间';

	$output  = '<section class="eryitang-contact-section eryitang-contact-information" id="contact-information"><div class="eryitang-shell eryitang-contact-information__grid">';
	$output .= '<div class="eryitang-contact-information__content"><p class="eryitang-contact-kicker">Contact Information</p><h2>与尔意堂<br>相见于<span>成都</span></h2><p class="eryitang-contact-lead">医馆靠近天府广场，公共交通便利。建议到馆前电话联系，以便了解当日坐诊及预约情况。</p>';
	$output .= '<div class="eryitang-contact-card eryitang-contact-reveal">';
	$output .= '<div class="eryitang-contact-item"><p class="eryitang-contact-item__label">医馆地址</p><div class="eryitang-contact-item__value"><p>' . nl2br( esc_html( $address ) ) . '</p><small>' . esc_html( $transport ) . '</small></div></div>';
	$output .= '<div class="eryitang-contact-item"><p class="eryitang-contact-item__label">预约咨询</p><div class="eryitang-contact-item__value"><p><a href="' . esc_url( 'tel:' . $phone_href ) . '">' . esc_html( $phone ) . '</a></p><small>可咨询坐诊时间与预约安排</small></div></div>';
	$output .= '<div class="eryitang-contact-item"><p class="eryitang-contact-item__label">营业时间</p><div class="eryitang-contact-item__value"><p>' . esc_html( $hours ) . '</p><small>节假日安排请提前电话确认</small></div></div>';
	$output .= '<div class="eryitang-contact-item eryitang-contact-item--wechat"><p class="eryitang-contact-item__label">微信联系</p><div class="eryitang-contact-wechat"><div class="eryitang-contact-item__value"><p>扫码添加医馆微信</p><small>正式二维码上传后将在此自动替换</small></div>';
	if ( $qr_url ) {
		$output .= '<div class="eryitang-contact-qr"><img src="' . esc_url( $qr_url ) . '" alt="' . esc_attr( $qr_alt ) . '" loading="lazy"></div>';
	} else {
		$output .= '<div class="eryitang-contact-qr eryitang-contact-qr--placeholder" role="img" aria-label="' . esc_attr( $qr_alt . '图片占位' ) . '"><span>二维码<br>图片占位</span></div>';
	}
	$output .= '</div></div></div></div>';

	if ( $image_url ) {
		$output .= '<figure class="eryitang-contact-visual eryitang-contact-reveal"><img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $image_alt ) . '" loading="lazy"><figcaption>' . esc_html( $image_alt ) . '</figcaption></figure>';
	} else {
		$output .= '<div class="eryitang-contact-visual eryitang-contact-visual--placeholder eryitang-contact-reveal" role="img" aria-label="' . esc_attr( $image_alt . '图片占位' ) . '"><div><strong>' . esc_html( $clinic_name ) . '</strong><span>中医馆</span></div><p>医馆外景或空间图片占位</p></div>';
	}

	$output .= '</div></section>';

	return $output;
}
add_shortcode( 'eryitang_contact_information', 'eryitang_contact_information_shortcode' );

/**
 * 输出联系页地图示意或正式嵌入地图。
 *
 * @return string
 */
function eryitang_contact_map_shortcode() {
	$clinic_name = eryitang_get_option( 'clinic_name', '尔意堂' ) ?: '尔意堂';
	$address     = eryitang_get_option( 'address', '成都市锦江区梨花街8号名望大厦501' ) ?: '成都市锦江区梨花街8号名望大厦501';
	$transport   = eryitang_get_option( 'transport_note', '天府广场地铁D出口步行约450米' ) ?: '天府广场地铁D出口步行约450米';
	$map_url     = eryitang_get_option( 'map_url', '' );
	$embed_url   = eryitang_get_option( 'map_embed_url', '' );

	$output  = '<section class="eryitang-contact-section eryitang-contact-map-section" id="map"><div class="eryitang-shell">';
	$output .= '<header class="eryitang-contact-section-head eryitang-contact-reveal"><div><p class="eryitang-contact-kicker">How to Arrive</p><h2>从天府广场，<br>步行约450米到达</h2></div><p>以下为位置关系示意。正式上线时可接入腾讯地图或高德地图，并保留一键导航入口。</p></header>';
	$output .= '<div class="eryitang-contact-map eryitang-contact-reveal">';

	if ( $embed_url ) {
		$output .= '<div class="eryitang-contact-map__embed"><iframe src="' . esc_url( $embed_url ) . '" title="' . esc_attr( $clinic_name . '地图位置' ) . '" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe></div>';
	} else {
		$output .= '<div class="eryitang-contact-map__schematic" role="img" aria-label="从天府广场地铁D出口沿梨花街步行约450米到达' . esc_attr( $clinic_name ) . '">';
		$output .= '<div class="eryitang-contact-map__blocks" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i></div>';
		$output .= '<div class="eryitang-contact-map__road eryitang-contact-map__road--horizontal"><span>人民东路 · 梨花街</span></div><div class="eryitang-contact-map__road eryitang-contact-map__road--vertical"><span>顺城大街</span></div>';
		$output .= '<div class="eryitang-contact-map__metro"><strong>D</strong><span>天府广场地铁D出口</span></div><div class="eryitang-contact-map__route"><span>步行约 450 米</span></div><div class="eryitang-contact-map__clinic"><strong>' . esc_html( $clinic_name ) . '</strong><span>中医馆</span></div></div>';
	}

	$output .= '<div class="eryitang-contact-map__note"><h3>' . esc_html( $clinic_name ) . '中医馆</h3><p>' . nl2br( esc_html( $address ) ) . '</p><p>' . esc_html( $transport ) . '</p>';
	if ( $map_url ) {
		$output .= '<p class="eryitang-contact-map__action"><a href="' . esc_url( $map_url ) . '" target="_blank" rel="noopener noreferrer">打开地图导航</a></p>';
	}
	$output .= '</div></div></div></section>';

	return $output;
}
add_shortcode( 'eryitang_contact_map', 'eryitang_contact_map_shortcode' );

/**
 * 输出定稿联系页的到馆信息与路线截图。
 *
 * @return string
 */
function eryitang_contact_main_shortcode() {
	$clinic_name = eryitang_get_option( 'clinic_name', '尔意堂中医馆' ) ?: '尔意堂中医馆';
	$phone       = eryitang_get_option( 'phone', '199 8209 7343' ) ?: '199 8209 7343';
	$phone_href  = preg_replace( '/[^0-9+]/', '', $phone );
	$address     = eryitang_get_option( 'address', '成都市锦江区梨花街8号名望大厦501' ) ?: '成都市锦江区梨花街8号名望大厦501';
	$hours       = eryitang_get_option( 'business_hours', '每日 09:00—19:00' ) ?: '每日 09:00—19:00';
	$map_url     = eryitang_get_option( 'map_url', 'https://surl.amap.com/8kfLojo1ecZq' ) ?: 'https://surl.amap.com/8kfLojo1ecZq';
	$map_image   = eryitang_theme_asset_url( 'assets/images/contact-v1/contact-route-map.webp' );

	$output  = '<section class="section contact-main-section"><div class="container contact-main-grid">';
	$output .= '<article class="contact-panel reveal"><div class="contact-panel-heading"><span>到馆信息</span><h2>' . esc_html( $clinic_name ) . '</h2><p>目的地位于名望大厦5楼501。地图路线到达南1门后，请继续按楼内指引前往医馆。</p></div>';
	$output .= '<dl class="contact-facts"><div><dt>医馆地址</dt><dd>' . nl2br( esc_html( $address ) ) . '</dd></div><div><dt>预约咨询</dt><dd><a href="' . esc_url( 'tel:' . $phone_href ) . '">' . esc_html( $phone ) . '</a></dd></div><div><dt>营业时间</dt><dd>' . esc_html( $hours ) . '</dd></div></dl>';
	$output .= '<div class="contact-actions"><a class="contact-primary-action" href="' . esc_url( $map_url ) . '" target="_blank" rel="noopener noreferrer">地图导航</a><a class="contact-copy-action" href="' . esc_url( 'tel:' . $phone_href ) . '">电话预约</a></div></article>';
	$output .= '<figure class="contact-map-card reveal delay-1">' . eryitang_responsive_image_html( $map_image, '从天府广场地铁站到名望大厦南1门的地图路线截图', array( 'class' => 'contact-map-image', 'loading' => 'lazy' ) ) . '</figure>';
	$output .= '</div></section>';
	return $output;
}
add_shortcode( 'eryitang_contact_main', 'eryitang_contact_main_shortcode' );

/**
 * 输出联系页医馆环境横向滚动图集。
 *
 * @return string
 */
function eryitang_contact_gallery_shortcode() {
	$images = array();
	for ( $i = 1; $i <= 5; $i++ ) {
		$images[] = array(
			'url' => eryitang_fixed_image_url( 'contact_gallery_' . $i . '_image', 'assets/images/contact-v1/gallery-0' . $i . '.webp' ),
			'alt' => eryitang_fixed_text( 'contact_gallery_' . $i . '_alt', '尔意堂医馆环境' ),
		);
	}
	$items = '';
	foreach ( array_merge( $images, $images ) as $image ) {
		$items .= '<figure>' . eryitang_responsive_image_html( $image['url'], $image['alt'], array( 'loading' => 'lazy' ) ) . '</figure>';
	}
	return '<section class="contact-gallery-section"><div class="container contact-gallery-heading"><span>' . esc_html( eryitang_fixed_text( 'contact_gallery_eyebrow', 'CLINIC SPACE' ) ) . '</span><h2>' . esc_html( eryitang_fixed_text( 'contact_gallery_title', '闹市藏静境，医道安尘劳' ) ) . '</h2></div><div class="contact-gallery-marquee reveal"><div class="contact-gallery-track">' . $items . '</div></div></section>';
}
add_shortcode( 'eryitang_contact_gallery', 'eryitang_contact_gallery_shortcode' );

/**
 * 输出公共页脚备案信息。
 *
 * 公安备案查询地址只在取得可用备案编号后生成，避免把占位文案
 * 拼接成无效的官方查询链接。警徽图标使用内联矢量图，避免额外请求。
 *
 * @return string
 */
function eryitang_footer_records_html() {
	$icp_number          = trim( (string) eryitang_get_option( 'icp_number', '蜀ICP备2026043530号' ) );
	$police_number       = trim( (string) eryitang_get_option( 'police_number', '' ) );
	$police_record_code  = preg_replace( '/\D+/', '', $police_number );
	$police_icon         = '<span class="footer-record__police-icon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><path d="M12 2.7c2.2 1.55 4.68 2.42 7.3 2.58v5.96c0 4.55-2.88 8.12-7.3 10.06-4.42-1.94-7.3-5.51-7.3-10.06V5.28C7.32 5.12 9.8 4.25 12 2.7Z"/><path d="m12 7.15 1.08 2.18 2.41.35-1.74 1.7.41 2.4L12 12.64l-2.16 1.14.41-2.4-1.74-1.7 2.41-.35L12 7.15Z"/></svg></span>';
	$records             = array();

	if ( '' !== $icp_number ) {
		$records[] = '<a class="footer-record footer-record--icp" href="https://beian.miit.gov.cn/" target="_blank" rel="noopener noreferrer" aria-label="前往工信部备案查询系统查询' . esc_attr( $icp_number ) . '"><span>' . esc_html( $icp_number ) . '</span></a>';
	}

	if ( '' !== $police_number && '' !== $police_record_code ) {
		$police_url = add_query_arg(
			'recordcode',
			$police_record_code,
			'https://www.beian.gov.cn/portal/registerSystemInfo'
		);
		$records[] = '<a class="footer-record footer-record--police" href="' . esc_url( $police_url ) . '" target="_blank" rel="noopener noreferrer" aria-label="前往全国互联网安全管理服务平台查询' . esc_attr( $police_number ) . '">' . $police_icon . '<span>' . esc_html( $police_number ) . '</span></a>';
	}

	return '<span class="footer-records">' . implode( '<span class="footer-records__separator" aria-hidden="true">·</span>', $records ) . '</span>';
}

/**
 * 输出全站统一完整页脚。
 *
 * @return string
 */
function eryitang_full_footer_shortcode() {
	$phone       = eryitang_get_option( 'phone', '199 8209 7343' ) ?: '199 8209 7343';
	$address     = eryitang_get_option( 'address', '成都市锦江区梨花街8号名望大厦501' ) ?: '成都市锦江区梨花街8号名望大厦501';
	$hours       = eryitang_get_option( 'business_hours', '每日 09:00—19:00' ) ?: '每日 09:00—19:00';
	$statement   = eryitang_get_option( 'footer_statement', '尔意堂承董氏奇穴非遗文脉与岐黄医道，以传统针灸、推拿、正骨等方式，营造一方安静、温暖的中式医馆空间。' );
	$qr_url      = eryitang_get_option( 'wechat_qr_url', '' ) ?: eryitang_theme_asset_url( 'assets/images/home-v4/wechat-qr.png' );
	$year        = wp_date( 'Y' );

	$output  = '<div class="eryitang-footer__grid"><div class="eryitang-footer__brand">' . do_shortcode( '[eryitang_logo variant="footer"]' ) . '<p>' . esc_html( $statement ) . '</p></div>';
	$output .= '<nav aria-label="页脚快速导航"><h2>快速导航</h2><a href="' . esc_url( home_url( '/' ) ) . '">首页</a><a href="' . esc_url( home_url( '/brand/' ) ) . '">品牌介绍</a><a href="' . esc_url( eryitang_category_url( 'news' ) ) . '">医馆资讯</a><a href="' . esc_url( home_url( '/contact/' ) ) . '">联系我们</a></nav>';
	$output .= '<nav aria-label="页脚内容分类"><h2>内容分类</h2><a href="' . esc_url( eryitang_category_url( 'therapies' ) ) . '">特色疗法</a><a href="' . esc_url( eryitang_category_url( 'conditions' ) ) . '">调理方向</a><a href="' . esc_url( eryitang_category_url( 'tea' ) ) . '">养生茶品</a><a href="' . esc_url( eryitang_category_url( 'cases' ) ) . '">案例故事</a></nav>';
	$output .= '<div class="eryitang-footer__contact"><h2>联系我们</h2><p>' . nl2br( esc_html( $address ) ) . '</p><p>营业时间：' . esc_html( $hours ) . '</p><p>预约咨询：' . esc_html( $phone ) . '</p><img src="' . esc_url( $qr_url ) . '" alt="尔意堂医馆微信二维码" loading="lazy"></div></div>';
	$output .= '<div class="eryitang-footer__legal"><span>Copyright © ' . esc_html( $year ) . ' 尔意堂中医馆</span>' . eryitang_footer_records_html() . '</div>';
	return $output;
}
add_shortcode( 'eryitang_full_footer', 'eryitang_full_footer_shortcode' );

/**
 * 输出与静态定稿完全同构的公共页头。
 *
 * @return string
 */
function eryitang_v2_header_shortcode() {
	$phone        = eryitang_get_option( 'phone', '199 8209 7343' ) ?: '199 8209 7343';
	$phone_href   = preg_replace( '/[^0-9+]/', '', $phone );
	$address      = eryitang_get_option( 'address', '成都市锦江区梨花街8号名望大厦501' ) ?: '成都市锦江区梨花街8号名望大厦501';
	$hours        = eryitang_get_option( 'business_hours', '09:00—19:00' ) ?: '09:00—19:00';
	$logo         = eryitang_fixed_image_url( 'header_logo', 'assets/images/home-v3/logo-header.webp' );
	$articles_url = eryitang_get_articles_url();
	$items        = array(
		'home'        => array( home_url( '/' ), '首页' ),
		'brand'       => array( home_url( '/brand/' ), '品牌介绍' ),
		'therapies'   => array( eryitang_category_url( 'therapies' ), '特色疗法' ),
		'conditions'  => array( eryitang_category_url( 'conditions' ), '调理方向' ),
		'tea'         => array( eryitang_category_url( 'tea' ), '养生茶品' ),
		'cases'       => array( eryitang_category_url( 'cases' ), '案例故事' ),
		'news'        => array( eryitang_category_url( 'news' ), '医馆资讯' ),
		'contact'     => array( home_url( '/contact/' ), '联系我们' ),
	);
	$active = 'home';
	if ( is_page( 'brand' ) || is_page_template( 'page-brand' ) ) {
		$active = 'brand';
	} elseif ( is_page( 'contact' ) || is_page_template( 'page-contact' ) ) {
		$active = 'contact';
	} elseif ( is_category() ) {
		$context = eryitang_get_category_context();
		$active  = $context['parent'] ? $context['parent']->slug : ( $context['current'] ? $context['current']->slug : 'news' );
	} elseif ( is_home() || is_singular( 'post' ) || is_archive() ) {
		$active = 'news';
	}
	$links = '';
	foreach ( $items as $key => $item ) {
		$links .= '<a' . ( $key === $active ? ' class="active" aria-current="page"' : '' ) . ' href="' . esc_url( $item[0] ) . '">' . esc_html( $item[1] ) . '</a>';
	}
	return '<div class="utility-bar"><div class="container utility-inner"><div>' . esc_html( eryitang_fixed_text( 'utility_statement', '董氏奇穴针灸技法非物质文化遗产传承医馆' ) ) . '</div><div class="utility-meta"><span>营业时间 ' . esc_html( preg_replace( '/^每日\s*/u', '', $hours ) ) . '</span><span>' . esc_html( $address ) . '</span></div></div></div>'
		. '<header class="site-header" id="siteHeader"><div class="container nav-wrap">'
		. '<div class="brand-cell"><a class="brand" href="' . esc_url( home_url( '/' ) ) . '" aria-label="尔意堂首页">' . eryitang_responsive_image_html( $logo, eryitang_fixed_text( 'header_logo_alt', '尔意堂中医馆' ), array( 'class' => 'brand-logo', 'width' => 480, 'height' => 304 ) ) . '</a></div>'
		. '<nav class="main-nav" id="mainNav" aria-label="主导航">' . $links . '</nav>'
		. '<div class="header-contact-cell"><span class="phone-link phone-desktop" aria-label="咨询电话 ' . esc_attr( $phone ) . '">' . esc_html( $phone ) . '</span><a class="phone-link phone-mobile" href="' . esc_url( 'tel:' . $phone_href ) . '" aria-label="拨打咨询电话 ' . esc_attr( $phone ) . '">' . esc_html( $phone ) . '</a><button class="menu-toggle" id="menuToggle" type="button" aria-label="打开导航" aria-expanded="false"><span></span><span></span><span></span></button></div>'
		. '</div></header>';
}
add_shortcode( 'eryitang_v2_header', 'eryitang_v2_header_shortcode' );

/**
 * 输出与静态定稿完全同构的公共页脚。
 *
 * @return string
 */
function eryitang_v2_footer_shortcode() {
	$phone     = eryitang_get_option( 'phone', '199 8209 7343' ) ?: '199 8209 7343';
	$address   = eryitang_get_option( 'address', '成都市锦江区梨花街8号名望大厦501' ) ?: '成都市锦江区梨花街8号名望大厦501';
	$hours     = eryitang_get_option( 'business_hours', '09:00—19:00' ) ?: '09:00—19:00';
	$statement = eryitang_get_option( 'footer_statement', '尔意堂承董氏奇穴非遗文脉与岐黄医道，以传统脉、道家脉、太素全息脉三脉合参，循气化规律调理形神。于城市日常中营造一方安静、温暖的中式医馆空间。' );
	$logo      = eryitang_fixed_image_url( 'footer_logo', 'assets/images/home-v3/logo-footer.webp' );
	$qr        = eryitang_get_option( 'wechat_qr_url', '' ) ?: eryitang_theme_asset_url( 'assets/images/home-v4/wechat-qr.png' );
	$ad        = ( is_front_page() || is_home() || is_archive() || is_singular( 'post' ) ) ? eryitang_footer_ad_shortcode() : '';
	$ad_html   = $ad ? '<div class="shared-footer-ad">' . $ad . '</div>' : '';
	return '<footer class="site-footer" id="contact">' . $ad_html
		. '<div class="container footer-main"><div class="footer-brand"><div><a class="brand" href="' . esc_url( home_url( '/' ) ) . '" aria-label="尔意堂首页">' . eryitang_responsive_image_html( $logo, eryitang_fixed_text( 'footer_logo_alt', '尔意堂中医馆' ), array( 'class' => 'footer-logo', 'width' => 480, 'height' => 267 ) ) . '</a></div><p>' . esc_html( $statement ) . '</p></div>'
		. '<div class="footer-column"><h3>快速导航</h3><div class="footer-links"><a href="' . esc_url( home_url( '/' ) ) . '">首页</a><a href="' . esc_url( home_url( '/brand/' ) ) . '">品牌介绍</a><a href="' . esc_url( home_url( '/#doctors' ) ) . '">医师团队</a><a href="' . esc_url( eryitang_category_url( 'news' ) ) . '">医馆资讯</a></div></div>'
		. '<div class="footer-column"><h3>内容分类</h3><div class="footer-links"><a href="' . esc_url( eryitang_category_url( 'therapies' ) ) . '">特色疗法</a><a href="' . esc_url( eryitang_category_url( 'conditions' ) ) . '">调理方向</a><a href="' . esc_url( eryitang_category_url( 'tea' ) ) . '">养生茶品</a><a href="' . esc_url( eryitang_category_url( 'cases' ) ) . '">案例故事</a></div></div>'
		. '<div class="footer-column"><h3>联系我们</h3><div class="footer-contact-copy"><div>' . nl2br( esc_html( $address ) ) . '</div><div>营业时间：' . esc_html( preg_replace( '/^每日\s*/u', '', $hours ) ) . '</div><div>预约咨询：' . esc_html( preg_replace( '/\s+/', '', $phone ) ) . '</div></div><div class="qr-placeholder"><img src="' . esc_url( $qr ) . '" alt="尔意堂官方微信二维码" loading="lazy"></div></div></div>'
		. '<div class="container footer-bottom"><div>Copyright © ' . esc_html( wp_date( 'Y' ) ) . ' 尔意堂中医馆</div>' . eryitang_footer_records_html() . '</div></footer>';
}
add_shortcode( 'eryitang_v2_footer', 'eryitang_v2_footer_shortcode' );
