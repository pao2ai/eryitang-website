<?php
/** 以后上传图片时统一执行的尺寸与质量策略。 @package EryitangCore */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** 超过最长边 2560px 时，让 WordPress 生成前台使用的 scaled 版本。 */
function eryitang_big_image_threshold() { return 2560; }
add_filter( 'big_image_size_threshold', 'eryitang_big_image_threshold' );

/** JPEG 与 WebP 衍生图统一使用兼顾清晰度和体积的质量。 */
function eryitang_image_editor_quality( $quality, $mime_type ) {
	return in_array( $mime_type, array( 'image/jpeg', 'image/webp' ), true ) ? 82 : $quality;
}
add_filter( 'wp_editor_set_quality', 'eryitang_image_editor_quality', 10, 2 );
add_filter( 'jpeg_quality', static function () { return 82; } );

/** 在文章、医师和资质编辑页的特色图片框中同步显示规格。 */
function eryitang_featured_image_instruction( $content, $post_id ) {
	$post_type = get_post_type( $post_id );
	$tips = array(
		'post' => '推荐 1600×1200px（4:3），WebP/JPEG，≤ 350KB。',
		'doctor' => '推荐 1200×1500px（4:5），WebP/JPEG，≤ 350KB。',
		'credential' => '证书推荐 1600×1200px（4:3）；锦旗保持原比例、最长边1600px；≤ 500KB。',
	);
	if ( isset( $tips[ $post_type ] ) ) {
		$content .= '<p class="howto"><strong>上传建议：</strong>' . esc_html( $tips[ $post_type ] ) . '</p>';
	}
	return $content;
}
add_filter( 'admin_post_thumbnail_html', 'eryitang_featured_image_instruction', 10, 2 );
