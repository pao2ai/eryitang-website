<?php
/**
 * Plugin Name: 尔意堂网站功能
 * Description: 为尔意堂官网提供医师资料、资质荣誉、文章推荐字段和全站信息设置。
 * Version: 0.5.1
 * Requires at least: 6.8
 * Requires PHP: 8.3
 * Author: 尔意堂官网项目组
 * Text Domain: eryitang-core
 *
 * @package EryitangCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ERYITANG_CORE_VERSION', '0.5.1' );
define( 'ERYITANG_CORE_PATH', plugin_dir_path( __FILE__ ) );

require_once ERYITANG_CORE_PATH . 'inc/post-types.php';
require_once ERYITANG_CORE_PATH . 'inc/meta-boxes.php';
require_once ERYITANG_CORE_PATH . 'inc/settings.php';
require_once ERYITANG_CORE_PATH . 'inc/permalinks.php';
require_once ERYITANG_CORE_PATH . 'inc/fixed-content.php';
require_once ERYITANG_CORE_PATH . 'inc/media-policy.php';
require_once ERYITANG_CORE_PATH . 'inc/seo.php';
require_once ERYITANG_CORE_PATH . 'inc/shortcodes.php';

/**
 * 插件启用时刷新固定链接规则。
 */
function eryitang_core_activate() {
	eryitang_register_content_types();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'eryitang_core_activate' );

/**
 * 插件停用时刷新固定链接规则。
 */
function eryitang_core_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'eryitang_core_deactivate' );
