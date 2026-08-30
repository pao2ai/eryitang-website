<?php
/**
 * 全站 SEO / GEO 公共能力。
 *
 * @package EryitangCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** 规整并限制摘要长度。 */
function eryitang_seo_trim( $text, $length = 150 ) {
	$text = preg_replace( '/\s+/u', ' ', wp_strip_all_tags( strip_shortcodes( (string) $text ) ) );
	$text = trim( (string) $text );
	return mb_strlen( $text ) > $length ? mb_substr( $text, 0, $length ) . '…' : $text;
}

/** 当前内容的 SEO 标题。 */
function eryitang_seo_title( $fallback = '' ) {
	if ( is_singular() ) {
		$custom = get_post_meta( get_queried_object_id(), '_eryitang_seo_title', true );
		if ( $custom ) {
			return eryitang_seo_trim( $custom, 65 );
		}
	}
	if ( is_category() ) {
		$term = get_queried_object();
		$custom = get_term_meta( $term->term_id, '_eryitang_seo_title', true );
		return $custom ? eryitang_seo_trim( $custom, 65 ) : single_cat_title( '', false ) . '｜尔意堂中医馆';
	}
	if ( is_front_page() ) {
		return '尔意堂中医馆｜成都锦江区董氏奇穴针灸与中医调理';
	}
	if ( is_page( 'brand' ) ) {
		return '品牌介绍｜尔意堂中医馆';
	}
	if ( is_page( 'contact' ) ) {
		return '联系我们｜成都锦江区尔意堂中医馆';
	}
	if ( is_page( 'articles' ) || is_home() ) {
		return '中医养生与医馆资讯｜尔意堂中医馆';
	}
	if ( is_singular( 'post' ) ) {
		return eryitang_seo_trim( get_the_title(), 50 ) . '｜尔意堂中医馆';
	}
	return $fallback ? $fallback : get_bloginfo( 'name' );
}

/** 当前内容的 SEO 描述。 */
function eryitang_seo_description() {
	if ( is_singular() ) {
		$custom = get_post_meta( get_queried_object_id(), '_eryitang_seo_description', true );
		if ( $custom ) {
			return eryitang_seo_trim( $custom );
		}
	}
	if ( is_front_page() ) {
		return '尔意堂中医馆位于成都市锦江区梨花街8号名望大厦501，展示董氏奇穴针灸、中医调理方向、医师团队与医馆资讯。预约咨询：199 8209 7343。';
	}
	if ( is_page( 'brand' ) ) {
		return '了解尔意堂中医馆的董氏奇穴传承、医馆理念、资质荣誉、诊疗原则与团队信息。';
	}
	if ( is_page( 'contact' ) ) {
		$address = eryitang_get_option( 'address', '成都市锦江区梨花街8号名望大厦501' );
		$hours   = eryitang_get_option( 'business_hours', '每日 09:00—19:00' );
		$phone   = eryitang_get_option( 'phone', '199 8209 7343' );
		return eryitang_seo_trim( '尔意堂中医馆到馆信息：' . $address . '；营业时间：' . $hours . '；预约咨询：' . $phone . '。' );
	}
	if ( is_page( 'articles' ) || is_home() ) {
		return '浏览尔意堂中医馆发布的特色疗法、调理方向、养生茶品、案例故事与医馆资讯。内容仅供健康科普参考。';
	}
	if ( is_category() ) {
		$term = get_queried_object();
		$custom = get_term_meta( $term->term_id, '_eryitang_seo_description', true );
		if ( $custom ) {
			return eryitang_seo_trim( $custom );
		}
		$description = term_description( $term );
		return $description ? eryitang_seo_trim( $description ) : eryitang_seo_trim( '浏览尔意堂中医馆关于“' . single_cat_title( '', false ) . '”的文章、医馆资讯与健康科普内容。' );
	}
	if ( is_singular( 'post' ) ) {
		$post = get_queried_object();
		$excerpt = has_excerpt( $post ) ? $post->post_excerpt : $post->post_content;
		return eryitang_seo_trim( $excerpt );
	}
	return eryitang_seo_trim( get_bloginfo( 'description' ) );
}

/** 使用统一标题，保留后台与特殊请求的 WordPress 默认行为。 */
function eryitang_seo_filter_title( $title ) {
	if ( is_admin() || is_feed() || wp_doing_ajax() ) {
		return $title;
	}
	return eryitang_seo_title( $title );
}
add_filter( 'pre_get_document_title', 'eryitang_seo_filter_title', 20 );

/** 当前页面规范 URL。 */
function eryitang_seo_url() {
	if ( is_front_page() ) {
		return home_url( '/' );
	}
	if ( is_singular() ) {
		return (string) wp_get_canonical_url( get_queried_object_id() );
	}
	if ( is_category() ) {
		$url = get_term_link( get_queried_object() );
		return is_wp_error( $url ) ? home_url( '/' ) : $url;
	}
	return home_url( wp_unslash( $_SERVER['REQUEST_URI'] ?? '/' ) );
}

/** WordPress 核心只为单页输出 canonical，这里补齐文章首页和分类归档。 */
function eryitang_seo_archive_canonical() {
	if ( is_home() || is_category() ) {
		echo '<link rel="canonical" href="' . esc_url( eryitang_seo_url() ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'eryitang_seo_archive_canonical', 10 );

/** 社交分享主图。 */
function eryitang_seo_image() {
	if ( is_singular() && has_post_thumbnail( get_queried_object_id() ) ) {
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( get_queried_object_id() ), 'full' );
		if ( $image ) {
			return array( 'url' => $image[0], 'width' => $image[1], 'height' => $image[2] );
		}
	}
	if ( is_singular( 'post' ) ) {
		return array();
	}
	$key = 'home_banner_image_url';
	if ( is_page( 'brand' ) ) {
		$key = 'brand_banner_image_url';
	} elseif ( is_page( 'contact' ) ) {
		$key = 'contact_banner_image_url';
	} elseif ( is_page( 'articles' ) || is_home() || is_category() ) {
		$key = 'archive_banner_image_url';
	}
	$url = eryitang_get_option( $key, '' );
	if ( ! $url ) {
		$url = eryitang_get_option( 'home_banner_image_url', '' );
	}
	return $url ? array( 'url' => esc_url_raw( $url ) ) : array();
}

/** 输出 description、Open Graph 与 Twitter Card。 */
function eryitang_seo_meta_tags() {
	if ( is_404() || is_search() || is_feed() ) {
		return;
	}
	$title       = eryitang_seo_title();
	$description = eryitang_seo_description();
	$url         = eryitang_seo_url();
	$image       = eryitang_seo_image();
	$type        = is_singular( 'post' ) ? 'article' : 'website';

	echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
	echo '<meta property="og:locale" content="zh_CN">' . "\n";
	echo '<meta property="og:type" content="' . esc_attr( $type ) . '">' . "\n";
	echo '<meta property="og:site_name" content="尔意堂中医馆">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	echo '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";
	echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
	echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '">' . "\n";
	if ( ! empty( $image['url'] ) ) {
		echo '<meta property="og:image" content="' . esc_url( $image['url'] ) . '">' . "\n";
		echo '<meta name="twitter:image" content="' . esc_url( $image['url'] ) . '">' . "\n";
		if ( ! empty( $image['width'] ) && ! empty( $image['height'] ) ) {
			echo '<meta property="og:image:width" content="' . absint( $image['width'] ) . '">' . "\n";
			echo '<meta property="og:image:height" content="' . absint( $image['height'] ) . '">' . "\n";
		}
	}
	if ( 'article' === $type ) {
		echo '<meta property="article:published_time" content="' . esc_attr( get_the_date( DATE_W3C ) ) . '">' . "\n";
		echo '<meta property="article:modified_time" content="' . esc_attr( get_the_modified_date( DATE_W3C ) ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'eryitang_seo_meta_tags', 2 );

/** 站点实体结构化数据。 */
function eryitang_seo_clinic_schema() {
	$name    = eryitang_get_option( 'clinic_name', '尔意堂中医馆' );
	$phone   = eryitang_get_option( 'phone', '199 8209 7343' );
	$address = eryitang_get_option( 'address', '成都市锦江区梨花街8号名望大厦501' );
	$logo    = function_exists( 'eryitang_fixed_image_url' ) ? eryitang_fixed_image_url( 'header_logo', 'assets/images/home-v3/logo-header.webp' ) : ( function_exists( 'eryitang_theme_asset_url' ) ? eryitang_theme_asset_url( 'assets/images/home-v3/logo-header.webp' ) : '' );
	$digits = preg_replace( '/\D+/', '', $phone );
	$schema = array(
		'@type'        => 'MedicalClinic',
		'@id'          => home_url( '/#clinic' ),
		'name'         => $name,
		'url'          => home_url( '/' ),
		'telephone'    => 11 === strlen( $digits ) ? '+86' . $digits : $phone,
		'description'  => get_bloginfo( 'description' ),
		'address'      => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => preg_replace( '/^成都市/u', '', $address ),
			'addressLocality' => '成都市',
			'addressRegion'   => '四川省',
			'addressCountry'  => 'CN',
		),
	);
	$hours = eryitang_get_option( 'business_hours', '' );
	if ( preg_match( '/(\d{1,2}:\d{2}).*?(\d{1,2}:\d{2})/u', $hours, $matches ) ) {
		$schema['openingHoursSpecification'] = array(
			'@type'     => 'OpeningHoursSpecification',
			'dayOfWeek' => array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' ),
			'opens'     => $matches[1],
			'closes'    => $matches[2],
		);
	}
	$map_url = eryitang_get_option( 'map_url', '' );
	if ( $map_url ) {
		$schema['hasMap'] = $map_url;
	}
	if ( $logo ) {
		$schema['logo'] = $logo;
		$schema['image'] = $logo;
	}
	return $schema;
}

/** 面包屑结构化数据。 */
function eryitang_seo_breadcrumb_schema() {
	if ( is_front_page() ) {
		return array();
	}
	$items = array(
		array( '@type' => 'ListItem', 'position' => 1, 'name' => '首页', 'item' => home_url( '/' ) ),
	);
	$position = 2;
	if ( is_singular( 'post' ) || is_category() ) {
		$articles = get_page_by_path( 'articles' );
		$items[] = array( '@type' => 'ListItem', 'position' => $position++, 'name' => '文章与资讯', 'item' => $articles ? get_permalink( $articles ) : home_url( '/articles/' ) );
	}
	if ( is_category() ) {
		$term = get_queried_object();
		$ancestor_ids = array_reverse( get_ancestors( $term->term_id, 'category', 'taxonomy' ) );
		foreach ( $ancestor_ids as $ancestor_id ) {
			$ancestor = get_term( $ancestor_id, 'category' );
			if ( is_wp_error( $ancestor ) ) {
				continue;
			}
			$link = get_term_link( $ancestor );
			if ( ! is_wp_error( $link ) ) {
				$items[] = array( '@type' => 'ListItem', 'position' => $position++, 'name' => $ancestor->name, 'item' => $link );
			}
		}
		$items[] = array( '@type' => 'ListItem', 'position' => $position, 'name' => $term->name, 'item' => eryitang_seo_url() );
	} elseif ( is_singular( 'post' ) ) {
		$category = function_exists( 'eryitang_get_primary_category' ) ? eryitang_get_primary_category( get_queried_object_id() ) : null;
		if ( $category ) {
			$ancestor_ids = array_reverse( get_ancestors( $category->term_id, 'category', 'taxonomy' ) );
			$ancestor_ids[] = $category->term_id;
			foreach ( array_unique( $ancestor_ids ) as $category_id ) {
				$category_term = get_term( $category_id, 'category' );
				if ( is_wp_error( $category_term ) ) {
					continue;
				}
				$link = get_term_link( $category_term );
				if ( ! is_wp_error( $link ) ) {
					$items[] = array( '@type' => 'ListItem', 'position' => $position++, 'name' => $category_term->name, 'item' => $link );
				}
			}
		}
		$items[] = array( '@type' => 'ListItem', 'position' => $position, 'name' => get_the_title(), 'item' => eryitang_seo_url() );
	} elseif ( is_singular() ) {
		$items[] = array( '@type' => 'ListItem', 'position' => $position, 'name' => get_the_title(), 'item' => eryitang_seo_url() );
	}
	return array( '@type' => 'BreadcrumbList', '@id' => eryitang_seo_url() . '#breadcrumb', 'itemListElement' => $items );
}

/** 输出 JSON-LD 图谱。 */
function eryitang_seo_jsonld() {
	if ( is_404() || is_search() || is_feed() ) {
		return;
	}
	$url         = eryitang_seo_url();
	$title       = eryitang_seo_title();
	$description = eryitang_seo_description();
	$image       = eryitang_seo_image();
	$graph       = array(
		eryitang_seo_clinic_schema(),
		array(
			'@type'       => 'WebSite',
			'@id'         => home_url( '/#website' ),
			'url'         => home_url( '/' ),
			'name'        => '尔意堂中医馆',
			'description' => get_bloginfo( 'description' ),
			'inLanguage'  => 'zh-CN',
			'publisher'   => array( '@id' => home_url( '/#clinic' ) ),
		),
	);
	$page_type = 'WebPage';
	if ( is_page( 'brand' ) ) {
		$page_type = 'AboutPage';
	} elseif ( is_page( 'contact' ) ) {
		$page_type = 'ContactPage';
	} elseif ( is_page( 'articles' ) || is_home() || is_category() ) {
		$page_type = 'CollectionPage';
	}
	$page = array(
		'@type'       => $page_type,
		'@id'         => $url . '#webpage',
		'url'         => $url,
		'name'        => $title,
		'description' => $description,
		'isPartOf'    => array( '@id' => home_url( '/#website' ) ),
		'about'       => array( '@id' => home_url( '/#clinic' ) ),
		'inLanguage'  => 'zh-CN',
	);
	if ( ! empty( $image['url'] ) ) {
		$page['primaryImageOfPage'] = array( '@type' => 'ImageObject', 'url' => $image['url'] );
	}
	if ( ! is_front_page() ) {
		$page['breadcrumb'] = array( '@id' => $url . '#breadcrumb' );
	}
	$graph[] = $page;
	if ( is_singular( 'post' ) ) {
		$post = get_queried_object();
		$article = array(
			'@type'            => 'Article',
			'@id'              => $url . '#article',
			'headline'         => get_the_title( $post ),
			'description'      => $description,
			'datePublished'    => get_the_date( DATE_W3C, $post ),
			'dateModified'     => get_the_modified_date( DATE_W3C, $post ),
			'mainEntityOfPage' => array( '@id' => $url . '#webpage' ),
			'author'           => array( '@id' => home_url( '/#clinic' ) ),
			'publisher'        => array( '@id' => home_url( '/#clinic' ) ),
			'inLanguage'       => 'zh-CN',
			'isAccessibleForFree' => true,
		);
		$categories = wp_get_post_terms( $post->ID, 'category', array( 'fields' => 'names' ) );
		if ( ! is_wp_error( $categories ) && $categories ) {
			$article['articleSection'] = $categories;
		}
		if ( ! empty( $image['url'] ) ) {
			$article['image'] = $image['url'];
		}
		$graph[] = $article;
	}
	$breadcrumb = eryitang_seo_breadcrumb_schema();
	if ( $breadcrumb ) {
		$graph[] = $breadcrumb;
	}
	echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@graph' => $graph ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}
add_action( 'wp_head', 'eryitang_seo_jsonld', 20 );

/** 医疗与养生文章统一显示内容边界，避免科普摘要被理解为个体诊疗建议。 */
function eryitang_seo_content_notice( $content ) {
	if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}
	$notice = '<div class="article-content-notice" data-nosnippet="true"><p><strong>内容说明：</strong>本文仅作健康科普与传统中医文化交流，不构成诊断、处方或个体化治疗建议；身体不适请到正规医疗机构就诊。</p>';
	if ( is_single( 'low-back-pain-scoliosis-case' ) ) {
		$notice .= '<p>文中个体情况不代表普遍结果，具体方案需结合个人情况评估。</p>';
	}
	return $content . $notice . '</div>';
}
add_filter( 'the_content', 'eryitang_seo_content_notice', 20 );

/** 文章与页面的手动 SEO 字段。 */
function eryitang_seo_add_meta_box() {
	foreach ( array( 'post', 'page' ) as $type ) {
		add_meta_box( 'eryitang-seo-meta', 'SEO 设置', 'eryitang_seo_render_meta_box', $type, 'normal', 'default' );
	}
}
add_action( 'add_meta_boxes', 'eryitang_seo_add_meta_box' );

function eryitang_seo_render_meta_box( $post ) {
	wp_nonce_field( 'eryitang_seo_save', 'eryitang_seo_nonce' );
	$title = get_post_meta( $post->ID, '_eryitang_seo_title', true );
	$description = get_post_meta( $post->ID, '_eryitang_seo_description', true );
	?>
	<p><label for="eryitang-seo-title"><strong>SEO 标题</strong></label></p>
	<input id="eryitang-seo-title" name="eryitang_seo_title" type="text" class="widefat" maxlength="65" value="<?php echo esc_attr( $title ); ?>">
	<p class="description">建议不超过 30 个中文字符；留空时使用全站自动标题。</p>
	<p><label for="eryitang-seo-description"><strong>Meta 描述</strong></label></p>
	<textarea id="eryitang-seo-description" name="eryitang_seo_description" class="widefat" rows="3" maxlength="160"><?php echo esc_textarea( $description ); ?></textarea>
	<p class="description">建议 70–120 个中文字符，准确概括页面内容；不要堆砌关键词或填写无法核实的医疗功效。</p>
	<?php
}

function eryitang_seo_save_meta( $post_id ) {
	if ( ! isset( $_POST['eryitang_seo_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['eryitang_seo_nonce'] ) ), 'eryitang_seo_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$title = isset( $_POST['eryitang_seo_title'] ) ? sanitize_text_field( wp_unslash( $_POST['eryitang_seo_title'] ) ) : '';
	$description = isset( $_POST['eryitang_seo_description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['eryitang_seo_description'] ) ) : '';
	update_post_meta( $post_id, '_eryitang_seo_title', $title );
	update_post_meta( $post_id, '_eryitang_seo_description', $description );
}
add_action( 'save_post', 'eryitang_seo_save_meta' );

/** 分类目录 SEO 字段。 */
function eryitang_seo_category_fields( $term = null ) {
	$id = $term instanceof WP_Term ? $term->term_id : 0;
	$title = $id ? get_term_meta( $id, '_eryitang_seo_title', true ) : '';
	$description = $id ? get_term_meta( $id, '_eryitang_seo_description', true ) : '';
	$wrapper = $id ? 'tr' : 'div';
	echo '<' . esc_attr( $wrapper ) . ' class="form-field"><' . ( $id ? 'th scope="row"' : 'label' ) . '>SEO 标题</' . ( $id ? 'th' : 'label' ) . '><' . ( $id ? 'td' : 'div' ) . '><input type="text" name="eryitang_seo_title" maxlength="65" value="' . esc_attr( $title ) . '"><p class="description">留空时自动使用“分类名｜尔意堂中医馆”。</p></' . ( $id ? 'td' : 'div' ) . '></' . esc_attr( $wrapper ) . '>';
	echo '<' . esc_attr( $wrapper ) . ' class="form-field"><' . ( $id ? 'th scope="row"' : 'label' ) . '>Meta 描述</' . ( $id ? 'th' : 'label' ) . '><' . ( $id ? 'td' : 'div' ) . '><textarea name="eryitang_seo_description" rows="4" maxlength="160">' . esc_textarea( $description ) . '</textarea><p class="description">留空时优先使用分类描述，再使用安全的自动描述。</p></' . ( $id ? 'td' : 'div' ) . '></' . esc_attr( $wrapper ) . '>';
}
add_action( 'category_add_form_fields', 'eryitang_seo_category_fields' );
add_action( 'category_edit_form_fields', 'eryitang_seo_category_fields' );

function eryitang_seo_save_category_fields( $term_id ) {
	if ( ! current_user_can( 'manage_categories' ) ) {
		return;
	}
	$title = isset( $_POST['eryitang_seo_title'] ) ? sanitize_text_field( wp_unslash( $_POST['eryitang_seo_title'] ) ) : '';
	$description = isset( $_POST['eryitang_seo_description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['eryitang_seo_description'] ) ) : '';
	update_term_meta( $term_id, '_eryitang_seo_title', $title );
	update_term_meta( $term_id, '_eryitang_seo_description', $description );
}
add_action( 'created_category', 'eryitang_seo_save_category_fields' );
add_action( 'edited_category', 'eryitang_seo_save_category_fields' );

/** 为 AI 检索工具提供站点说明文件。 */
function eryitang_seo_llms_query_var( $vars ) {
	$vars[] = 'eryitang_llms';
	return $vars;
}
add_filter( 'query_vars', 'eryitang_seo_llms_query_var' );

function eryitang_seo_llms_rule() {
	add_rewrite_rule( '^llms\.txt$', 'index.php?eryitang_llms=1', 'top' );
}
add_action( 'init', 'eryitang_seo_llms_rule' );

/** llms.txt 是标准文件路径，不应被 WordPress 强制追加斜杠。 */
function eryitang_seo_llms_canonical( $redirect_url ) {
	return get_query_var( 'eryitang_llms' ) ? false : $redirect_url;
}
add_filter( 'redirect_canonical', 'eryitang_seo_llms_canonical' );

function eryitang_seo_render_llms() {
	if ( ! get_query_var( 'eryitang_llms' ) ) {
		return;
	}
	header( 'Content-Type: text/plain; charset=utf-8' );
	header( 'Cache-Control: public, max-age=3600' );
	$lines = array(
		'# 尔意堂中医馆',
		'',
		'> 成都锦江区的中医馆官方网站，提供医馆介绍、董氏奇穴传承、中医调理方向、健康科普与到馆信息。',
		'',
		'## 核心页面',
		'- [首页](' . home_url( '/' ) . ')：医馆概览、特色技法与调理方向',
		'- [品牌介绍](' . home_url( '/brand/' ) . ')：医馆理念、传承、资质与团队',
		'- [文章与资讯](' . home_url( '/articles/' ) . ')：健康科普与医馆资讯',
		'- [联系我们](' . home_url( '/contact/' ) . ')：地址、营业时间、电话与交通信息',
		'',
		'## 最新文章',
	);
	$posts = get_posts( array( 'numberposts' => 10, 'post_status' => 'publish' ) );
	foreach ( $posts as $post ) {
		$lines[] = '- [' . get_the_title( $post ) . '](' . get_permalink( $post ) . ')';
	}
	$lines[] = '';
	$lines[] = '## 重要说明';
	$lines[] = '- 健康内容用于科普和就诊前参考，不能替代专业医疗诊断与个体化治疗建议。';
	$lines[] = '- 医馆地址：' . eryitang_get_option( 'address', '成都市锦江区梨花街8号名望大厦501' );
	$lines[] = '- 预约咨询：' . eryitang_get_option( 'phone', '199 8209 7343' );
	echo implode( "\n", $lines ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	exit;
}
add_action( 'template_redirect', 'eryitang_seo_render_llms' );
