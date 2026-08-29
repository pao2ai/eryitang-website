<?php
/** 全站设置与后台管理入口。 @package EryitangCore */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function eryitang_get_option( $key, $default = '' ) {
	$options = get_option( 'eryitang_options', array() );
	return isset( $options[ $key ] ) ? $options[ $key ] : $default;
}

function eryitang_get_ad_slot( $slot ) {
	$slot = in_array( (int) $slot, array( 1, 2 ), true ) ? (int) $slot : 1;
	return array(
		'enabled' => (bool) eryitang_get_option( 'ad_' . $slot . '_enabled', false ),
		'title' => eryitang_get_option( 'ad_' . $slot . '_title', '' ),
		'image_url' => eryitang_get_option( 'ad_' . $slot . '_image_url', '' ),
		'alt' => eryitang_get_option( 'ad_' . $slot . '_alt', '' ),
		'link_url' => eryitang_get_option( 'ad_' . $slot . '_link_url', '' ),
		'new_window' => (bool) eryitang_get_option( 'ad_' . $slot . '_new_window', false ),
	);
}

/** 后台字段定义；recommendation 同时用于界面提示和媒体选择器标题。 */
function eryitang_settings_schema() {
	return array(
		'basic' => array(
			'clinic_name' => array( 'label' => '医馆名称', 'type' => 'text', 'placeholder' => '尔意堂' ),
			'phone' => array( 'label' => '预约电话', 'type' => 'text', 'placeholder' => '199 8209 7343' ),
			'address' => array( 'label' => '医馆地址', 'type' => 'textarea', 'placeholder' => '填写最终确认的完整地址' ),
			'transport_note' => array( 'label' => '交通说明', 'type' => 'text', 'placeholder' => '天府广场地铁D出口步行约450米' ),
			'business_hours' => array( 'label' => '营业时间', 'type' => 'text', 'placeholder' => '每日 09:00—19:00' ),
			'icp_number' => array( 'label' => 'ICP备案号', 'type' => 'text', 'placeholder' => '蜀ICP备2026043530号' ),
			'police_number' => array( 'label' => '公安备案号', 'type' => 'text', 'placeholder' => '例如：川公网安备 51010402000000号；未取得时留空' ),
			'footer_statement' => array( 'label' => '页脚品牌说明', 'type' => 'textarea', 'placeholder' => '以古法之精，养身心之和' ),
		),
		'media' => array(
			'home_banner_title' => array( 'label' => '首页 Banner 语义标题', 'type' => 'text', 'placeholder' => '尔意堂中医馆' ),
			'home_banner_image_url' => array( 'label' => '首页 Banner 图片', 'type' => 'image', 'recommendation' => '推荐 1920×960px（2:1），WebP/JPEG，建议 ≤ 500KB。' ),
			'home_banner_alt' => array( 'label' => '首页 Banner 替代文字', 'type' => 'text', 'placeholder' => '描述图片中的医馆空间或诊疗场景' ),
			'brand_banner_title' => array( 'label' => '品牌页 Banner 语义标题', 'type' => 'text', 'placeholder' => '尔意堂品牌介绍' ),
			'brand_banner_image_url' => array( 'label' => '品牌页 Banner 图片', 'type' => 'image', 'recommendation' => '推荐 1680×945px（16:9），WebP/JPEG，建议 ≤ 500KB；移动端会居中裁切。' ),
			'brand_banner_alt' => array( 'label' => '品牌页 Banner 替代文字', 'type' => 'text', 'placeholder' => '描述图片主体；纯装饰图可留空' ),
			'contact_banner_title' => array( 'label' => '联系页 Banner 语义标题', 'type' => 'text', 'placeholder' => '联系尔意堂中医馆' ),
			'contact_banner_image_url' => array( 'label' => '联系页 Banner 图片', 'type' => 'image', 'recommendation' => '推荐 1920×640px（3:1），WebP/JPEG，建议 ≤ 450KB。' ),
			'contact_banner_alt' => array( 'label' => '联系页 Banner 替代文字', 'type' => 'text', 'placeholder' => '描述医馆环境；纯装饰图可留空' ),
			'archive_banner_title' => array( 'label' => '文章列表 Banner 语义标题', 'type' => 'text', 'placeholder' => '尔意堂中医馆文章与资讯' ),
			'archive_banner_image_url' => array( 'label' => '文章列表 Banner 图片', 'type' => 'image', 'recommendation' => '推荐 1920×820px（约 2.33:1），WebP/JPEG，建议 ≤ 500KB。' ),
			'archive_banner_alt' => array( 'label' => '文章列表 Banner 替代文字', 'type' => 'text', 'placeholder' => '描述图片主体；纯装饰图可留空' ),
			'contact_image_url' => array( 'label' => '联系页医馆图片', 'type' => 'image', 'recommendation' => '推荐 1600×1200px（4:3），WebP/JPEG，建议 ≤ 350KB。' ),
			'contact_image_alt' => array( 'label' => '联系页医馆图片替代文字', 'type' => 'text', 'placeholder' => '描述图片中的医馆空间' ),
			'wechat_qr_url' => array( 'label' => '微信二维码图片', 'type' => 'image', 'recommendation' => '推荐 800×800px（1:1），PNG，建议 ≤ 300KB；不要裁边、加滤镜或降低对比度。' ),
			'wechat_qr_alt' => array( 'label' => '微信二维码替代文字', 'type' => 'text', 'placeholder' => '尔意堂医馆微信二维码' ),
		),
		'links' => array(
			'map_url' => array( 'label' => '地图导航链接', 'type' => 'url', 'placeholder' => '腾讯地图或高德地图地点链接' ),
			'map_embed_url' => array( 'label' => '地图嵌入地址', 'type' => 'url', 'placeholder' => '正式地图可嵌入地址，未接入时留空' ),
			'consultation_float_enabled' => array( 'label' => '启用合作咨询入口', 'type' => 'checkbox', 'description' => '在全站显示“合作咨询”悬浮入口' ),
			'consultation_float_label' => array( 'label' => '合作咨询显示文字', 'type' => 'text', 'placeholder' => '合作咨询' ),
			'consultation_float_url' => array( 'label' => '合作咨询跳转链接', 'type' => 'url', 'placeholder' => 'https:// 或站内完整链接' ),
			'consultation_float_new_window' => array( 'label' => '合作咨询打开方式', 'type' => 'checkbox', 'description' => '在新窗口打开' ),
		),
		'ads' => eryitang_ad_schema(),
	);
}

function eryitang_ad_schema() {
	$fields = array(
		'footer_ad_enabled' => 'checkbox', 'footer_ad_image_url' => 'url', 'footer_ad_alt' => 'text',
		'footer_ad_link_url' => 'url', 'footer_ad_new_window' => 'checkbox',
	);
	foreach ( array( 1, 2 ) as $slot ) {
		foreach ( array( 'enabled' => 'checkbox', 'title' => 'text', 'image_url' => 'url', 'alt' => 'text', 'link_url' => 'url', 'new_window' => 'checkbox' ) as $suffix => $type ) {
			$fields[ 'ad_' . $slot . '_' . $suffix ] = $type;
		}
	}
	return array_map( static function ( $type ) { return array( 'type' => $type ); }, $fields );
}

function eryitang_register_settings() {
	register_setting( 'eryitang_settings_group', 'eryitang_options', array( 'type' => 'object', 'sanitize_callback' => 'eryitang_sanitize_settings', 'default' => array() ) );
}
add_action( 'admin_init', 'eryitang_register_settings' );

/** 分区增量保存，防止拆分菜单后保存一页清空其他页。 */
function eryitang_sanitize_settings( $input ) {
	$input = is_array( $input ) ? $input : array();
	$current = get_option( 'eryitang_options', array() );
	$sanitized = is_array( $current ) ? $current : array();
	$section = isset( $input['_section'] ) ? sanitize_key( $input['_section'] ) : '';
	$schema = eryitang_settings_schema();
	if ( 'links' === $section ) {
		foreach ( range( 1, 8 ) as $index ) { $schema['links'][ 'home_therapy_' . $index . '_url' ] = array( 'type' => 'url' ); }
	}
	if ( ! isset( $schema[ $section ] ) ) { return $sanitized; }
	foreach ( $schema[ $section ] as $key => $field ) {
		$type = $field['type'] ?? 'text';
		$value = isset( $input[ $key ] ) ? wp_unslash( $input[ $key ] ) : '';
		if ( 'checkbox' === $type ) { $sanitized[ $key ] = isset( $input[ $key ] ) ? 1 : 0; }
		elseif ( in_array( $type, array( 'url', 'image' ), true ) ) { $sanitized[ $key ] = esc_url_raw( $value ); }
		elseif ( 'textarea' === $type ) { $sanitized[ $key ] = sanitize_textarea_field( $value ); }
		else { $sanitized[ $key ] = sanitize_text_field( $value ); }
	}
	return $sanitized;
}

function eryitang_add_settings_pages() {
	add_menu_page( '尔意堂网站管理', '尔意堂管理', 'edit_posts', 'eryitang-manage', 'eryitang_render_manage_page', 'dashicons-admin-home', 21 );
	add_submenu_page( 'eryitang-manage', '尔意堂管理首页', '管理首页', 'edit_posts', 'eryitang-manage', 'eryitang_render_manage_page' );
	add_submenu_page( 'eryitang-manage', '基础信息', '基础信息', 'manage_options', 'eryitang-basic-info', 'eryitang_render_basic_page' );
	add_submenu_page( 'eryitang-manage', '页面图片', '页面图片', 'manage_options', 'eryitang-page-images', 'eryitang_render_media_page' );
	add_submenu_page( 'eryitang-manage', '广告配置', '广告配置', 'manage_options', 'eryitang-ad-settings', 'eryitang_render_ads_page' );
	add_submenu_page( 'eryitang-manage', '链接与入口', '链接与入口', 'manage_options', 'eryitang-link-settings', 'eryitang_render_links_page' );
}
add_action( 'admin_menu', 'eryitang_add_settings_pages', 5 );

function eryitang_enqueue_settings_assets() {
	$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
	if ( ! in_array( $page, array( 'eryitang-manage', 'eryitang-basic-info', 'eryitang-page-images', 'eryitang-ad-settings', 'eryitang-link-settings' ), true ) ) { return; }
	wp_enqueue_media();
	wp_enqueue_script( 'eryitang-admin-media', plugins_url( 'assets/admin-media.js', dirname( __FILE__ ) ), array(), ERYITANG_CORE_VERSION, true );
	wp_enqueue_style( 'eryitang-admin-settings', plugins_url( 'assets/admin-settings.css', dirname( __FILE__ ) ), array(), ERYITANG_CORE_VERSION );
}
add_action( 'admin_enqueue_scripts', 'eryitang_enqueue_settings_assets' );

function eryitang_render_image_policy_notice() {
	echo '<div class="notice notice-info inline eryitang-image-policy"><p><strong>图片上传规则：</strong>照片和 Banner 优先使用 WebP，其次 JPEG；二维码使用 PNG。上传前按下方比例裁切，通常控制在 500KB 内。系统会把超大照片的前台有效尺寸自动缩到最长边 2560px，并以 82 质量生成 JPEG/WebP 衍生图；原始文件仍保留，方便回退。</p></div>';
}

function eryitang_render_fields_table( $fields, $options ) {
	echo '<table class="form-table" role="presentation">';
	foreach ( $fields as $key => $field ) {
		$value = $options[ $key ] ?? '';
		echo '<tr><th scope="row"><label for="eryitang-' . esc_attr( $key ) . '">' . esc_html( $field['label'] ) . '</label></th><td>';
		if ( 'textarea' === $field['type'] ) {
			echo '<textarea class="large-text" rows="3" id="eryitang-' . esc_attr( $key ) . '" name="eryitang_options[' . esc_attr( $key ) . ']" placeholder="' . esc_attr( $field['placeholder'] ?? '' ) . '">' . esc_textarea( $value ) . '</textarea>';
		} elseif ( 'image' === $field['type'] ) {
			$tip = $field['recommendation'] ?? '';
			echo '<span class="eryitang-media-field" data-recommendation="' . esc_attr( $tip ) . '"><input class="large-text eryitang-media-url" type="url" id="eryitang-' . esc_attr( $key ) . '" name="eryitang_options[' . esc_attr( $key ) . ']" value="' . esc_attr( $value ) . '" placeholder="从媒体库选择图片"><button class="button eryitang-media-select" type="button">选择图片</button><button class="button-link-delete eryitang-media-clear" type="button">清除</button></span><p class="description eryitang-image-spec">' . esc_html( $tip ) . '</p>';
		} elseif ( 'checkbox' === $field['type'] ) {
			echo '<label><input type="checkbox" id="eryitang-' . esc_attr( $key ) . '" name="eryitang_options[' . esc_attr( $key ) . ']" value="1" ' . checked( ! empty( $value ), true, false ) . '> ' . esc_html( $field['description'] ?? '' ) . '</label>';
		} else {
			echo '<input class="large-text" type="' . esc_attr( $field['type'] ) . '" id="eryitang-' . esc_attr( $key ) . '" name="eryitang_options[' . esc_attr( $key ) . ']" value="' . esc_attr( $value ) . '" placeholder="' . esc_attr( $field['placeholder'] ?? '' ) . '">';
		}
		echo '</td></tr>';
	}
	echo '</table>';
}

function eryitang_render_settings_form( $section, $title, $description, $fields, $show_image_notice = false ) {
	if ( ! current_user_can( 'manage_options' ) ) { return; }
	$options = get_option( 'eryitang_options', array() );
	echo '<div class="wrap eryitang-admin-wrap"><h1>' . esc_html( $title ) . '</h1><p>' . esc_html( $description ) . '</p>';
	if ( $show_image_notice ) { eryitang_render_image_policy_notice(); }
	echo '<form method="post" action="options.php">';
	settings_fields( 'eryitang_settings_group' );
	echo '<input type="hidden" name="eryitang_options[_section]" value="' . esc_attr( $section ) . '">';
	eryitang_render_fields_table( $fields, $options );
	submit_button( '保存' . $title );
	echo '</form></div>';
}

/** 使用与广告配置一致的卡片布局渲染一个分区设置页。 */
function eryitang_render_panelized_settings_form( $section, $title, $description, $fields, $panels, $show_image_notice = false ) {
	if ( ! current_user_can( 'manage_options' ) ) { return; }
	$options = get_option( 'eryitang_options', array() );
	echo '<div class="wrap eryitang-admin-wrap"><h1>' . esc_html( $title ) . '</h1><p>' . esc_html( $description ) . '</p>';
	if ( $show_image_notice ) { eryitang_render_image_policy_notice(); }
	echo '<form method="post" action="options.php">';
	settings_fields( 'eryitang_settings_group' );
	echo '<input type="hidden" name="eryitang_options[_section]" value="' . esc_attr( $section ) . '">';
	foreach ( $panels as $panel ) {
		$panel_fields = array();
		foreach ( $panel['fields'] as $key ) {
			if ( isset( $fields[ $key ] ) ) { $panel_fields[ $key ] = $fields[ $key ]; }
		}
		if ( empty( $panel_fields ) ) { continue; }
		echo '<div class="eryitang-settings-panel"><h2>' . esc_html( $panel['title'] ) . '</h2>';
		if ( ! empty( $panel['description'] ) ) { echo '<p>' . esc_html( $panel['description'] ) . '</p>'; }
		eryitang_render_fields_table( $panel_fields, $options );
		echo '</div>';
	}
	submit_button( '保存' . $title );
	echo '</form></div>';
}

function eryitang_render_manage_page() {
	if ( ! current_user_can( 'edit_posts' ) ) { return; }
	$cards = array(
		array( '基础信息', '电话、地址、营业时间、备案与页脚说明', 'admin.php?page=eryitang-basic-info', 'manage_options' ),
		array( '页面图片', '各页面 Banner、联系页图片与二维码', 'admin.php?page=eryitang-page-images', 'manage_options' ),
		array( '广告配置', '底部广告与文章侧边广告', 'admin.php?page=eryitang-ad-settings', 'manage_options' ),
		array( '链接与入口', '地图、疗法卡片与合作咨询链接', 'admin.php?page=eryitang-link-settings', 'manage_options' ),
		array( '医师团队', '医师文字资料和展示照片', 'edit.php?post_type=doctor', 'edit_posts' ),
		array( '资质荣誉', '证书、备案与锦旗图片', 'edit.php?post_type=credential', 'edit_posts' ),
		array( '文章内容', '文章、分类、特色图片与推荐设置', 'edit.php', 'edit_posts' ),
	);
	echo '<div class="wrap eryitang-admin-wrap"><h1>尔意堂网站管理</h1><p>按内容类型进入对应菜单，避免把广告、基础文字和页面图片混在同一张表单中。</p><div class="eryitang-admin-cards">';
	foreach ( $cards as $card ) {
		if ( current_user_can( $card[3] ) ) { echo '<a href="' . esc_url( admin_url( $card[2] ) ) . '"><strong>' . esc_html( $card[0] ) . '</strong><span>' . esc_html( $card[1] ) . '</span></a>'; }
	}
	echo '</div>';
	eryitang_render_image_policy_notice();
	echo '</div>';
}

function eryitang_render_basic_page() {
	$s = eryitang_settings_schema();
	$panels = array(
		array(
			'title'       => '医馆基本资料',
			'description' => '全站页头、页脚和联系区域共用的医馆名称与预约电话。',
			'fields'      => array( 'clinic_name', 'phone' ),
		),
		array(
			'title'       => '到馆与营业信息',
			'description' => '联系页面和全站页脚使用的地址、交通说明与营业时间。',
			'fields'      => array( 'address', 'transport_note', 'business_hours' ),
		),
		array(
			'title'       => '备案与页脚说明',
			'description' => '网站底部展示的备案编号和品牌说明。ICP备案自动链接工信部；公安备案填入真实编号后自动生成警徽与官方查询链接。',
			'fields'      => array( 'icp_number', 'police_number', 'footer_statement' ),
		),
	);
	eryitang_render_panelized_settings_form( 'basic', '基础信息', '管理全站共用的文字资料。此页不包含广告和页面图片。', $s['basic'], $panels );
}
function eryitang_render_media_page() {
	$schema  = eryitang_settings_schema();
	$fields  = $schema['media'];
	$panels  = array(
		array(
			'title'       => '首页 Banner',
			'description' => '首页顶部主视觉图片及其语义信息。',
			'fields'      => array( 'home_banner_title', 'home_banner_image_url', 'home_banner_alt' ),
		),
		array(
			'title'       => '品牌页 Banner',
			'description' => '品牌介绍页顶部主视觉图片及其语义信息。',
			'fields'      => array( 'brand_banner_title', 'brand_banner_image_url', 'brand_banner_alt' ),
		),
		array(
			'title'       => '联系页 Banner',
			'description' => '联系我们页面顶部主视觉图片及其语义信息。',
			'fields'      => array( 'contact_banner_title', 'contact_banner_image_url', 'contact_banner_alt' ),
		),
		array(
			'title'       => '文章列表 Banner',
			'description' => '文章列表和分类页面共用的顶部图片及其语义信息。',
			'fields'      => array( 'archive_banner_title', 'archive_banner_image_url', 'archive_banner_alt' ),
		),
		array(
			'title'       => '联系页医馆图片',
			'description' => '联系页面到馆信息区域展示的医馆环境图片。',
			'fields'      => array( 'contact_image_url', 'contact_image_alt' ),
		),
		array(
			'title'       => '微信二维码',
			'description' => '全站页脚及联系信息区域使用的微信二维码。',
			'fields'      => array( 'wechat_qr_url', 'wechat_qr_alt' ),
		),
	);

	eryitang_render_panelized_settings_form( 'media', '页面图片', '管理各页面 Banner、联系页医馆图片和二维码。请按每个字段下方的比例准备素材。', $fields, $panels, true );
}
function eryitang_render_links_page() {
	$s = eryitang_settings_schema();
	$labels = array( '董氏奇穴针灸', '道家推拿', '禅龙正骨', '古方药油推拿', '中医康复治疗', '道家降龙药蒸', '非遗瑶浴', '非遗雷音艾灸' );
	foreach ( $labels as $i => $label ) { $s['links'][ 'home_therapy_' . ( $i + 1 ) . '_url' ] = array( 'label' => $label . '跳转链接', 'type' => 'url', 'placeholder' => 'https:// 或站内完整链接；留空则不跳转' ); }
	$panels = array(
		array(
			'title'       => '地图导航',
			'description' => '联系页面使用的外部地图导航地址和地图嵌入地址。',
			'fields'      => array( 'map_url', 'map_embed_url' ),
		),
		array(
			'title'       => '合作咨询入口',
			'description' => '管理全站右侧合作咨询悬浮入口的状态、文字和跳转方式。',
			'fields'      => array( 'consultation_float_enabled', 'consultation_float_label', 'consultation_float_url', 'consultation_float_new_window' ),
		),
		array(
			'title'       => '首页疗法卡片跳转',
			'description' => '分别设置首页八张特色疗法卡片的目标页面；留空时该卡片不跳转。',
			'fields'      => array_map( static function ( $index ) { return 'home_therapy_' . $index . '_url'; }, range( 1, 8 ) ),
		),
	);
	eryitang_render_panelized_settings_form( 'links', '链接与入口', '管理地图、首页疗法卡片和全站合作咨询入口。', $s['links'], $panels );
}

function eryitang_render_ad_image_field( $id, $name, $value, $tip ) {
	echo '<span class="eryitang-media-field" data-recommendation="' . esc_attr( $tip ) . '"><input class="large-text eryitang-media-url" type="url" id="' . esc_attr( $id ) . '" name="eryitang_options[' . esc_attr( $name ) . ']" value="' . esc_attr( $value ) . '" placeholder="从媒体库选择图片"><button class="button eryitang-media-select" type="button">选择图片</button><button class="button-link-delete eryitang-media-clear" type="button">清除</button></span><p class="description eryitang-image-spec">' . esc_html( $tip ) . '</p>';
}

function eryitang_render_ads_page() {
	if ( ! current_user_can( 'manage_options' ) ) { return; }
	$o = get_option( 'eryitang_options', array() );
	echo '<div class="wrap eryitang-admin-wrap"><h1>广告配置</h1><p>此处只管理广告素材、开关和跳转。素材尚未确认时请保持对应开关关闭。</p>';
	eryitang_render_image_policy_notice();
	echo '<form method="post" action="options.php">'; settings_fields( 'eryitang_settings_group' ); echo '<input type="hidden" name="eryitang_options[_section]" value="ads">';
	echo '<div class="eryitang-settings-panel"><h2>全站底部图片广告</h2><p>首页、文章列表页和文章详情页共用。</p><table class="form-table"><tr><th>是否启用</th><td><label><input type="checkbox" name="eryitang_options[footer_ad_enabled]" value="1" ' . checked( ! empty( $o['footer_ad_enabled'] ), true, false ) . '> 显示全站底部广告</label></td></tr><tr><th><label for="eryitang-footer-ad-image">广告图片</label></th><td>';
	eryitang_render_ad_image_field( 'eryitang-footer-ad-image', 'footer_ad_image_url', $o['footer_ad_image_url'] ?? '', '推荐 1920×710px（约 2.7:1），WebP/JPEG，建议 ≤ 450KB。' );
	echo '</td></tr><tr><th>替代文字</th><td><input class="large-text" type="text" name="eryitang_options[footer_ad_alt]" value="' . esc_attr( $o['footer_ad_alt'] ?? '' ) . '"></td></tr><tr><th>跳转链接</th><td><input class="large-text" type="url" name="eryitang_options[footer_ad_link_url]" value="' . esc_attr( $o['footer_ad_link_url'] ?? '' ) . '"></td></tr><tr><th>打开方式</th><td><label><input type="checkbox" name="eryitang_options[footer_ad_new_window]" value="1" ' . checked( ! empty( $o['footer_ad_new_window'] ), true, false ) . '> 在新窗口打开</label></td></tr></table></div>';
	$defaults = array( 1 => '医师联合坐诊 / 预约咨询', 2 => '养生茶品 / 医馆活动' );
	foreach ( $defaults as $slot => $default ) {
		$p = 'ad_' . $slot . '_';
		echo '<div class="eryitang-settings-panel"><h2>文章侧边广告位 ' . esc_html( sprintf( '%02d', $slot ) ) . '</h2><table class="form-table"><tr><th>是否启用</th><td><label><input type="checkbox" name="eryitang_options[' . esc_attr( $p ) . 'enabled]" value="1" ' . checked( ! empty( $o[ $p . 'enabled' ] ), true, false ) . '> 素材和链接确认后显示</label></td></tr><tr><th>标题</th><td><input class="large-text" type="text" name="eryitang_options[' . esc_attr( $p ) . 'title]" value="' . esc_attr( $o[ $p . 'title' ] ?? '' ) . '" placeholder="' . esc_attr( $default ) . '"></td></tr><tr><th>广告图片</th><td>';
		eryitang_render_ad_image_field( 'eryitang-' . $p . 'image', $p . 'image_url', $o[ $p . 'image_url' ] ?? '', '推荐 1200×900px（4:3），WebP/JPEG，建议 ≤ 300KB；重要文字不要贴近四边。' );
		echo '</td></tr><tr><th>替代文字</th><td><input class="large-text" type="text" name="eryitang_options[' . esc_attr( $p ) . 'alt]" value="' . esc_attr( $o[ $p . 'alt' ] ?? '' ) . '"></td></tr><tr><th>跳转链接</th><td><input class="large-text" type="url" name="eryitang_options[' . esc_attr( $p ) . 'link_url]" value="' . esc_attr( $o[ $p . 'link_url' ] ?? '' ) . '"></td></tr><tr><th>打开方式</th><td><label><input type="checkbox" name="eryitang_options[' . esc_attr( $p ) . 'new_window]" value="1" ' . checked( ! empty( $o[ $p . 'new_window' ] ), true, false ) . '> 在新窗口打开</label></td></tr></table></div>';
	}
	submit_button( '保存广告配置' ); echo '</form></div>';
}
