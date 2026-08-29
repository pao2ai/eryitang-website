<?php
/**
 * 后台编辑字段。
 *
 * @package EryitangCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 注册后台元数据面板。
 */
function eryitang_add_meta_boxes() {
	add_meta_box(
		'eryitang-doctor-details',
		'医师展示信息',
		'eryitang_render_doctor_meta_box',
		'doctor',
		'normal',
		'high'
	);

	add_meta_box(
		'eryitang-post-promotion',
		'尔意堂展示设置',
		'eryitang_render_post_meta_box',
		'post',
		'side',
		'default'
	);

	add_meta_box(
		'eryitang-credential-details',
		'资质荣誉展示信息',
		'eryitang_render_credential_meta_box',
		'credential',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'eryitang_add_meta_boxes' );

/**
 * 输出医师资料字段。
 *
 * @param WP_Post $post 当前文章。
 */
function eryitang_render_doctor_meta_box( $post ) {
	wp_nonce_field( 'eryitang_save_doctor_meta', 'eryitang_doctor_nonce' );

	$fields = array(
		'_eryitang_doctor_title'     => array( 'label' => '职称/身份', 'type' => 'text' ),
		'_eryitang_doctor_specialty' => array( 'label' => '专业方向', 'type' => 'textarea' ),
		'_eryitang_doctor_license'   => array( 'label' => '执业与资质信息', 'type' => 'textarea' ),
		'_eryitang_doctor_schedule'  => array( 'label' => '坐诊时间', 'type' => 'textarea' ),
		'_eryitang_doctor_link_url'  => array( 'label' => '首页卡片跳转链接', 'type' => 'url' ),
		'_eryitang_doctor_order'     => array( 'label' => '展示顺序', 'type' => 'number' ),
	);

	foreach ( $fields as $key => $field ) {
		$value = get_post_meta( $post->ID, $key, true );
		echo '<p><label for="' . esc_attr( $key ) . '"><strong>' . esc_html( $field['label'] ) . '</strong></label></p>';

		if ( 'textarea' === $field['type'] ) {
			echo '<p><textarea class="widefat" rows="3" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '">' . esc_textarea( $value ) . '</textarea></p>';
		} else {
			$min = 'number' === $field['type'] ? ' min="0"' : '';
			echo '<p><input class="widefat" type="' . esc_attr( $field['type'] ) . '"' . $min . ' id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '"></p>';
		}
	}

	$is_home = (bool) get_post_meta( $post->ID, '_eryitang_doctor_home', true );
	echo '<p><label><input type="checkbox" name="_eryitang_doctor_home" value="1" ' . checked( $is_home, true, false ) . '> 在首页医师团队中展示</label></p>';
	$new_window = (bool) get_post_meta( $post->ID, '_eryitang_doctor_link_new_window', true );
	echo '<p><label><input type="checkbox" name="_eryitang_doctor_link_new_window" value="1" ' . checked( $new_window, true, false ) . '> 医师卡片链接在新窗口打开</label></p>';
	echo '<hr><p class="description"><strong>医师照片建议：</strong>右侧“医师照片”上传 1200×1500px（4:5）竖图，WebP/JPEG，建议不超过 350KB；人物面部放在画面中上部。</p>';
}

/**
 * 输出文章推荐字段。
 *
 * @param WP_Post $post 当前文章。
 */
function eryitang_render_post_meta_box( $post ) {
	wp_nonce_field( 'eryitang_save_post_meta', 'eryitang_post_nonce' );

	$is_featured = (bool) get_post_meta( $post->ID, '_eryitang_featured', true );
	$is_home     = (bool) get_post_meta( $post->ID, '_eryitang_home_featured', true );
	$home_order = absint( get_post_meta( $post->ID, '_eryitang_home_order', true ) );
	$related    = get_post_meta( $post->ID, '_eryitang_related_posts', true );
	$related    = is_array( $related ) ? implode( ',', array_map( 'absint', $related ) ) : '';

	echo '<p><label><input type="checkbox" name="_eryitang_featured" value="1" ' . checked( $is_featured, true, false ) . '> 设为重点推荐</label></p>';
	echo '<p><label><input type="checkbox" name="_eryitang_home_featured" value="1" ' . checked( $is_home, true, false ) . '> 在首页资讯中展示</label></p>';
	echo '<p><label for="_eryitang_home_order"><strong>首页顺序</strong></label></p>';
	echo '<p><input class="widefat" type="number" min="0" id="_eryitang_home_order" name="_eryitang_home_order" value="' . esc_attr( $home_order ) . '"></p>';
	echo '<p><label for="_eryitang_related_posts"><strong>相关文章ID</strong></label></p>';
	echo '<p><input class="widefat" type="text" id="_eryitang_related_posts" name="_eryitang_related_posts" value="' . esc_attr( $related ) . '" placeholder="例如：12,18,25"></p>';
	echo '<p class="description">最多填写5篇，用英文逗号分隔。后续将升级为可视化选择。</p>';
	echo '<hr><p class="description"><strong>文章特色图片建议：</strong>右侧“特色图片”上传 1600×1200px（4:3），WebP/JPEG，建议不超过 350KB；标题文字不要直接做进图片。</p>';
}

/**
 * 输出资质荣誉展示字段。
 *
 * @param WP_Post $post 当前内容。
 */
function eryitang_render_credential_meta_box( $post ) {
	wp_nonce_field( 'eryitang_save_credential_meta', 'eryitang_credential_nonce' );

	$type  = get_post_meta( $post->ID, '_eryitang_credential_type', true );
	$note  = get_post_meta( $post->ID, '_eryitang_credential_note', true );
	$order = absint( get_post_meta( $post->ID, '_eryitang_credential_order', true ) );
	$type  = in_array( $type, array( 'certificate', 'pennant' ), true ) ? $type : 'certificate';

	echo '<p><label for="_eryitang_credential_type"><strong>资料类型</strong></label></p>';
	echo '<p><select class="widefat" id="_eryitang_credential_type" name="_eryitang_credential_type">';
	echo '<option value="certificate" ' . selected( $type, 'certificate', false ) . '>资质证书</option>';
	echo '<option value="pennant" ' . selected( $type, 'pennant', false ) . '>荣誉锦旗</option>';
	echo '</select></p>';
	echo '<p><label for="_eryitang_credential_note"><strong>证书编号、颁发单位或赠送说明</strong></label></p>';
	echo '<p><input class="widefat" type="text" id="_eryitang_credential_note" name="_eryitang_credential_note" value="' . esc_attr( $note ) . '" placeholder="按真实资料填写，未确认时可留空"></p>';
	echo '<p><label for="_eryitang_credential_order"><strong>展示顺序</strong></label></p>';
	echo '<p><input class="widefat" type="number" min="0" id="_eryitang_credential_order" name="_eryitang_credential_order" value="' . esc_attr( $order ) . '"></p>';
	echo '<p class="description">标题填写证书或锦旗名称，右侧“特色图片”上传对应图片。</p>';
	echo '<p class="description"><strong>图片建议：</strong>证书推荐 1600×1200px（4:3）；锦旗保持原比例、最长边 1600px。优先 WebP/JPEG，建议不超过 500KB，四边留出完整边框。</p>';
}

/**
 * 保存医师资料字段。
 *
 * @param int $post_id 文章ID。
 */
function eryitang_save_doctor_meta( $post_id ) {
	if (
		! isset( $_POST['eryitang_doctor_nonce'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['eryitang_doctor_nonce'] ) ), 'eryitang_save_doctor_meta' )
	) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$text_fields = array(
		'_eryitang_doctor_title',
		'_eryitang_doctor_specialty',
		'_eryitang_doctor_license',
		'_eryitang_doctor_schedule',
	);

	foreach ( $text_fields as $key ) {
		$value = isset( $_POST[ $key ] ) ? sanitize_textarea_field( wp_unslash( $_POST[ $key ] ) ) : '';
		update_post_meta( $post_id, $key, $value );
	}

	$link_url = isset( $_POST['_eryitang_doctor_link_url'] ) ? esc_url_raw( wp_unslash( $_POST['_eryitang_doctor_link_url'] ) ) : '';
	update_post_meta( $post_id, '_eryitang_doctor_link_url', $link_url );

	$order = isset( $_POST['_eryitang_doctor_order'] ) ? absint( $_POST['_eryitang_doctor_order'] ) : 0;
	update_post_meta( $post_id, '_eryitang_doctor_order', $order );
	update_post_meta( $post_id, '_eryitang_doctor_home', isset( $_POST['_eryitang_doctor_home'] ) ? 1 : 0 );
	update_post_meta( $post_id, '_eryitang_doctor_link_new_window', isset( $_POST['_eryitang_doctor_link_new_window'] ) ? 1 : 0 );
}
add_action( 'save_post_doctor', 'eryitang_save_doctor_meta' );

/**
 * 保存资质荣誉字段。
 *
 * @param int $post_id 内容ID。
 */
function eryitang_save_credential_meta( $post_id ) {
	if (
		! isset( $_POST['eryitang_credential_nonce'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['eryitang_credential_nonce'] ) ), 'eryitang_save_credential_meta' )
	) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$type = isset( $_POST['_eryitang_credential_type'] ) ? sanitize_key( wp_unslash( $_POST['_eryitang_credential_type'] ) ) : 'certificate';
	$type = in_array( $type, array( 'certificate', 'pennant' ), true ) ? $type : 'certificate';
	$note = isset( $_POST['_eryitang_credential_note'] ) ? sanitize_text_field( wp_unslash( $_POST['_eryitang_credential_note'] ) ) : '';

	update_post_meta( $post_id, '_eryitang_credential_type', $type );
	update_post_meta( $post_id, '_eryitang_credential_note', $note );
	update_post_meta( $post_id, '_eryitang_credential_order', isset( $_POST['_eryitang_credential_order'] ) ? absint( $_POST['_eryitang_credential_order'] ) : 0 );
}
add_action( 'save_post_credential', 'eryitang_save_credential_meta' );

/**
 * 保存文章展示字段。
 *
 * @param int $post_id 文章ID。
 */
function eryitang_save_post_meta( $post_id ) {
	if (
		! isset( $_POST['eryitang_post_nonce'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['eryitang_post_nonce'] ) ), 'eryitang_save_post_meta' )
	) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	update_post_meta( $post_id, '_eryitang_featured', isset( $_POST['_eryitang_featured'] ) ? 1 : 0 );
	update_post_meta( $post_id, '_eryitang_home_featured', isset( $_POST['_eryitang_home_featured'] ) ? 1 : 0 );
	update_post_meta( $post_id, '_eryitang_home_order', isset( $_POST['_eryitang_home_order'] ) ? absint( $_POST['_eryitang_home_order'] ) : 0 );

	$related_raw = isset( $_POST['_eryitang_related_posts'] ) ? sanitize_text_field( wp_unslash( $_POST['_eryitang_related_posts'] ) ) : '';
	$related     = array_values(
		array_slice(
			array_filter( array_unique( array_map( 'absint', explode( ',', $related_raw ) ) ) ),
			0,
			5
		)
	);
	update_post_meta( $post_id, '_eryitang_related_posts', $related );
}
add_action( 'save_post_post', 'eryitang_save_post_meta' );
