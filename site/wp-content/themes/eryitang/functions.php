<?php
/**
 * 尔意堂主题基础功能。
 *
 * @package Eryitang
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 加载主题前端资源。
 */
function eryitang_enqueue_assets() {
	$theme            = wp_get_theme();
	$site_css_path    = get_theme_file_path( 'assets/css/v2-site.css' );
	$site_js_path     = get_theme_file_path( 'assets/js/site.js' );
	$home_css_path    = get_theme_file_path( 'assets/css/v2-home.css' );
	$home_js_path     = get_theme_file_path( 'assets/js/home.js' );
	$brand_js_path    = get_theme_file_path( 'assets/js/brand.js' );
	$contact_js_path  = get_theme_file_path( 'assets/js/contact.js' );
	$fallback_version = $theme->get( 'Version' );

	wp_enqueue_style(
		'eryitang-site',
		get_theme_file_uri( 'assets/css/v2-site.css' ),
		array(),
		file_exists( $site_css_path ) ? (string) filemtime( $site_css_path ) : $fallback_version
	);

	wp_enqueue_script(
		'eryitang-site',
		get_theme_file_uri( 'assets/js/site.js' ),
		array(),
		file_exists( $site_js_path ) ? (string) filemtime( $site_js_path ) : $fallback_version,
		true
	);

	if ( is_front_page() ) {
		wp_enqueue_style(
			'eryitang-home',
			get_theme_file_uri( 'assets/css/v2-home.css' ),
			array( 'eryitang-site' ),
			file_exists( $home_css_path ) ? (string) filemtime( $home_css_path ) : $fallback_version
		);

		wp_enqueue_script(
			'eryitang-home',
			get_theme_file_uri( 'assets/js/home.js' ),
			array(),
			file_exists( $home_js_path ) ? (string) filemtime( $home_js_path ) : $fallback_version,
			true
		);
	}

	/*
	 * 与静态 V2 保持同一层叠顺序：页面基础 → 字体规范 → 公共外壳。
	 * 首页专属基础样式同样必须先于 Typography，避免旧字阶覆盖统一规则。
	 */
	wp_enqueue_style(
		'eryitang-typography',
		get_theme_file_uri( 'assets/css/v2-typography.css' ),
		is_front_page() ? array( 'eryitang-home' ) : array( 'eryitang-site' ),
		(string) filemtime( get_theme_file_path( 'assets/css/v2-typography.css' ) )
	);

	wp_enqueue_style(
		'eryitang-shared',
		get_theme_file_uri( 'assets/css/v2-shared.css' ),
		array( 'eryitang-typography' ),
		(string) filemtime( get_theme_file_path( 'assets/css/v2-shared.css' ) )
	);

	if ( is_page( 'brand' ) || is_page_template( 'page-brand' ) ) {
		wp_enqueue_script(
			'eryitang-brand',
			get_theme_file_uri( 'assets/js/brand.js' ),
			array(),
			file_exists( $brand_js_path ) ? (string) filemtime( $brand_js_path ) : $fallback_version,
			true
		);
	}

	if ( is_page( 'contact' ) || is_page_template( 'page-contact' ) ) {
		wp_enqueue_script(
			'eryitang-contact',
			get_theme_file_uri( 'assets/js/contact.js' ),
			array(),
			file_exists( $contact_js_path ) ? (string) filemtime( $contact_js_path ) : $fallback_version,
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'eryitang_enqueue_assets' );

/**
 * 设置主题基础能力。
 */
function eryitang_theme_setup() {
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/site.css' );
}
add_action( 'after_setup_theme', 'eryitang_theme_setup' );

/**
 * 给五个正式模板添加与静态定稿一致的页面标识，供统一 CSS 与首访动效使用。
 *
 * @param string[] $classes Body 类名。
 * @return string[]
 */
function eryitang_v2_body_classes( $classes ) {
	$classes[] = 'has-shared-shell';
	if ( is_front_page() ) {
		$classes[] = 'page-home-v2';
	} elseif ( is_page( 'brand' ) || is_page_template( 'page-brand' ) ) {
		$classes[] = 'page-brand-v1';
	} elseif ( is_page( 'contact' ) || is_page_template( 'page-contact' ) ) {
		$classes[] = 'page-contact-v1';
	} elseif ( is_singular( 'post' ) ) {
		$classes[] = 'page-article-detail-v1';
	} elseif ( is_home() || is_archive() ) {
		$classes[] = 'page-article-list-v1';
	}
	return array_values( array_unique( $classes ) );
}
add_filter( 'body_class', 'eryitang_v2_body_classes' );
