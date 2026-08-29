<?php
/**
 * 内容类型与结构化字段。
 *
 * @package EryitangCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 注册医师内容类型。
 */
function eryitang_register_content_types() {
	register_post_type(
		'doctor',
		array(
			'labels'              => array(
				'name'                  => '医师团队',
				'singular_name'         => '医师',
				'add_new'               => '添加医师',
				'add_new_item'          => '添加医师资料',
				'edit_item'             => '编辑医师资料',
				'new_item'              => '新医师',
				'view_item'             => '查看医师资料',
				'search_items'          => '搜索医师',
				'not_found'             => '暂无医师资料',
				'not_found_in_trash'    => '回收站中没有医师资料',
				'menu_name'             => '医师团队',
				'featured_image'        => '医师照片',
				'set_featured_image'    => '设置医师照片',
				'remove_featured_image' => '移除医师照片',
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => 'eryitang-manage',
			'show_in_rest'        => true,
			'menu_icon'           => 'dashicons-businessperson',
			'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
			'has_archive'         => false,
			'rewrite'             => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
		)
	);

	register_post_type(
		'credential',
		array(
			'labels'              => array(
				'name'                  => '资质荣誉',
				'singular_name'         => '资质或荣誉',
				'add_new'               => '添加资质荣誉',
				'add_new_item'          => '添加资质或荣誉',
				'edit_item'             => '编辑资质或荣誉',
				'new_item'              => '新资质或荣誉',
				'view_item'             => '查看资质或荣誉',
				'search_items'          => '搜索资质荣誉',
				'not_found'             => '暂无资质荣誉资料',
				'not_found_in_trash'    => '回收站中没有资质荣誉资料',
				'menu_name'             => '资质荣誉',
				'featured_image'        => '证书或锦旗图片',
				'set_featured_image'    => '设置证书或锦旗图片',
				'remove_featured_image' => '移除证书或锦旗图片',
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => 'eryitang-manage',
			'show_in_rest'        => true,
			'menu_icon'           => 'dashicons-awards',
			'supports'            => array( 'title', 'thumbnail' ),
			'has_archive'         => false,
			'rewrite'             => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
		)
	);
}
add_action( 'init', 'eryitang_register_content_types' );

/**
 * 注册REST可用的结构化元数据。
 */
function eryitang_register_meta_fields() {
	$doctor_fields = array(
		'_eryitang_doctor_title'     => 'sanitize_text_field',
		'_eryitang_doctor_specialty' => 'sanitize_textarea_field',
		'_eryitang_doctor_license'   => 'sanitize_textarea_field',
		'_eryitang_doctor_schedule'  => 'sanitize_textarea_field',
		'_eryitang_doctor_link_url'  => 'esc_url_raw',
	);

	foreach ( $doctor_fields as $key => $sanitize_callback ) {
		register_post_meta(
			'doctor',
			$key,
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => $sanitize_callback,
				'auth_callback'     => static function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}

	register_post_meta(
		'doctor',
		'_eryitang_doctor_order',
		array(
			'type'              => 'integer',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'absint',
			'auth_callback'     => static function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);

	$credential_fields = array(
		'_eryitang_credential_type' => 'sanitize_key',
		'_eryitang_credential_note' => 'sanitize_text_field',
	);

	foreach ( $credential_fields as $key => $sanitize_callback ) {
		register_post_meta(
			'credential',
			$key,
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => $sanitize_callback,
				'auth_callback'     => static function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}

	register_post_meta(
		'credential',
		'_eryitang_credential_order',
		array(
			'type'              => 'integer',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'absint',
			'auth_callback'     => static function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);

	register_post_meta(
		'doctor',
		'_eryitang_doctor_home',
		array(
			'type'              => 'boolean',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'rest_sanitize_boolean',
			'auth_callback'     => static function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);

	register_post_meta(
		'doctor',
		'_eryitang_doctor_link_new_window',
		array(
			'type'              => 'boolean',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'rest_sanitize_boolean',
			'auth_callback'     => static function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);

	register_post_meta(
		'post',
		'_eryitang_featured',
		array(
			'type'              => 'boolean',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'rest_sanitize_boolean',
			'auth_callback'     => static function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);

	register_post_meta(
		'post',
		'_eryitang_home_featured',
		array(
			'type'              => 'boolean',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'rest_sanitize_boolean',
			'auth_callback'     => static function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);

	register_post_meta(
		'post',
		'_eryitang_home_order',
		array(
			'type'              => 'integer',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'absint',
			'auth_callback'     => static function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);

	register_post_meta(
		'post',
		'_eryitang_related_posts',
		array(
			'type'          => 'array',
			'single'        => true,
			'show_in_rest'  => array(
				'schema' => array(
					'type'  => 'array',
					'items' => array(
						'type' => 'integer',
					),
				),
			),
			'auth_callback' => static function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);
}
add_action( 'init', 'eryitang_register_meta_fields' );
