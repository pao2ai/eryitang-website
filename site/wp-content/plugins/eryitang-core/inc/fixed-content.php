<?php
/**
 * 页面固定内容后台维护与前台回退。
 *
 * @package EryitangCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** 获取固定内容选项。 */
function eryitang_fixed_content_options() {
	$value = get_option( 'eryitang_fixed_content', array() );
	return is_array( $value ) ? $value : array();
}

/** 获取纯文字固定内容。 */
function eryitang_fixed_text( $key, $default = '' ) {
	$options = eryitang_fixed_content_options();
	return isset( $options[ $key ] ) && '' !== $options[ $key ] ? (string) $options[ $key ] : $default;
}

/** 获取媒体库图片地址，空值回退主题静态资源。 */
function eryitang_fixed_image_url( $key, $fallback = '' ) {
	$options       = eryitang_fixed_content_options();
	$attachment_id = isset( $options[ $key ] ) ? absint( $options[ $key ] ) : 0;
	$url           = $attachment_id ? wp_get_attachment_image_url( $attachment_id, 'full' ) : '';
	return $url ? $url : ( $fallback ? eryitang_theme_asset_url( $fallback ) : '' );
}

/** 固定内容字段定义。 */
function eryitang_fixed_content_schema() {
	$text = static function ( $label, $default, $recommended, $safe, $type = 'text' ) {
		return array( 'type' => $type, 'label' => $label, 'default' => $default, 'recommended' => $recommended, 'safe' => $safe );
	};
	$image = static function ( $label, $fallback, $width, $height, $size = 500 ) {
		return array( 'type' => 'image', 'label' => $label, 'fallback' => $fallback, 'width' => $width, 'height' => $height, 'max_kb' => $size );
	};

	$schema = array(
		'home' => array(
			'label' => '首页固定内容',
			'description' => '维护首页品牌介绍、非遗介绍、八项特色技法和九项调理方向。模块顺序、样式和链接仍由主题锁定。',
			'groups' => array(
				'home_intro' => array( 'label' => '首页品牌介绍', 'fields' => array(
					'home_intro_title' => $text( '标题第一行', '闹市之中', 8, 12 ),
					'home_intro_title_second' => $text( '标题第二行普通文字', '一处', 4, 8 ),
					'home_intro_title_accent' => $text( '标题第二行强调文字', '岐黄静境', 8, 12 ),
					'home_intro_lead' => $text( '品牌介绍正文', '尔意堂栖于闹市，藏楼宇间岐黄静境。诗云 “一针祛痼世人惊，穴蕴玄机指下明”，堂承董氏奇穴非遗文脉，循三脉辨证古法，以银针、正骨、药油推拿、本草药浴、雷音艾灸调和阴阳。木舍藏艾香，暖茶伴脉诊，不止疗沉疴，更筑闹市身心归处，以内敛古雅医道，安抚世人尘劳。', 170, 230, 'textarea' ),
					'home_intro_button_primary' => $text( '主按钮文字', '了解尔意堂', 8, 12 ),
					'home_intro_button_secondary' => $text( '次按钮文字', '查看到店信息', 8, 12 ),
					'home_intro_image_main' => $image( '主图', 'assets/images/home-v4/home-intro-main.webp', 1200, 900, 400 ),
					'home_intro_image_main_alt' => $text( '主图替代文字', '尔意堂中医馆圆形品牌标识墙', 24, 45 ),
					'home_intro_image_top' => $image( '右上图', 'assets/images/home-v4/home-intro-top.webp', 800, 600, 300 ),
					'home_intro_image_top_alt' => $text( '右上图替代文字', '尔意堂医馆本草药材陈列细节', 24, 45 ),
					'home_intro_image_bottom' => $image( '右下图', 'assets/images/home-v4/home-intro-bottom.webp', 800, 600, 300 ),
					'home_intro_image_bottom_alt' => $text( '右下图替代文字', '尔意堂中医文化展示墙', 24, 45 ),
				) ),
				'home_heritage' => array( 'label' => '首页非遗介绍', 'fields' => array(
					'home_heritage_title' => $text( '主标题', '承董氏奇穴文脉', 12, 18 ),
					'home_heritage_subtitle' => $text( '副标题', '续岐黄济世之道', 12, 18 ),
					'home_heritage_desc' => $text( '介绍正文', '医馆承董氏奇穴市级非物质文化遗产，针法溯源华佗道门传承，历经七十三代薪火相传；2024 年针法列入市级非物质文化遗产，后纳入全国中医适宜技术，典籍收录于中医院校教材，2026年由中国中医药出版社出版《董氏奇穴针灸学》纳入中医院校教科书。恪守「针有奇穴之传，药承华佗之脉，道继龙门之真，三脉归一」立馆理念，独创传统脉、道家脉、太素全息脉三脉合参辨证体系，以道医 “观气化、溯先天” 为则，跳出只治病灶的固化思维。', 250, 330, 'textarea' ),
					'home_heritage_image' => $image( '非遗展示图', 'assets/images/home-v4/heritage-certificate.webp', 1200, 900, 450 ),
					'home_heritage_image_alt' => $text( '图片替代文字', '尔意堂市级非物质文化遗产项目传承中心授牌合影', 30, 55 ),
					'home_heritage_tag_1' => $text( '标签一', '市级非遗', 6, 10 ),
					'home_heritage_tag_2' => $text( '标签二', '第三代嫡传', 7, 11 ),
					'home_heritage_tag_3' => $text( '标签三', '三脉合参', 6, 10 ),
				) ),
				'home_therapies' => array( 'label' => '八项特色技法', 'fields' => array(
					'home_therapies_title' => $text( '模块主标题', '八项特色技法', 8, 14 ),
					'home_therapies_subtitle' => $text( '模块强调文字', '，守其本真', 8, 14 ),
				) ),
				'home_conditions' => array( 'label' => '九项调理方向', 'fields' => array(
					'home_conditions_title' => $text( '标题第一列', '从身体所感', 8, 12 ),
					'home_conditions_subtitle' => $text( '标题第二列', '找到调理方向', 8, 12 ),
					'home_conditions_button' => $text( '按钮文字', '了解更多', 6, 10 ),
				) ),
			),
		),
		'brand' => array(
			'label' => '品牌页固定内容',
			'description' => '维护品牌故事、非遗介绍、诊疗原则与未来方向。资质证书和锦旗仍在“资质荣誉”菜单维护。',
			'groups' => array(
				'brand_story' => array( 'label' => '品牌故事', 'fields' => array(
					'brand_story_side_title' => $text( '左栏标题', "顺尔本真\n以意调气", 12, 18, 'textarea' ),
					'brand_story_quote' => $text( '左栏引句', '恬淡虚无，真气从之。', 12, 18 ),
					'brand_story_side_desc' => $text( '左栏说明', '尔意，尔为汝，代表每一位来访者本身；意为本心气机、神意所向；取名源自《黄帝内经》有云 “恬淡虚无，真气从之”，寄寓顺尔本真，以意调气，行医当体察个体禀赋，顺应人身气机规律调和阴阳，帮助身体回归自然平衡。', 130, 180, 'textarea' ),
					'brand_story_main_title' => $text( '正文标题', "闹市藏静境\n医道安尘劳", 14, 20, 'textarea' ),
					'brand_story_p1' => $text( '正文第一段', '尘世车马喧嚣，世人终日耗损元阳、经络淤滞、身心俱疲。尔意堂隐于锦江区梨花街楼宇之内，融黄老道统与岐黄古法，集诊疗、养生、玄谈于一体，于闹市之中筑一处岐黄静境。', 110, 160, 'textarea' ),
					'brand_story_main_image' => $image( '品牌故事主图', 'assets/images/brand-v1/story-scene-top.webp', 1600, 1200, 450 ),
					'brand_story_main_alt' => $text( '主图替代文字', '尔意堂医馆安静开阔的庭院空间', 24, 45 ),
					'brand_story_main_caption' => $text( '主图说明', '一方静境，容身心暂离城市喧嚣', 20, 35 ),
					'brand_story_p2' => $text( '正文第二段', '这里不止是诊疗场所，更是以道与医养心空间。堂内艾香萦绕，清茶常设。以银针、雷音艾灸、正骨推拿、瑶浴药蒸、道地本草方药，针、艾、药、手技内外同调。', 100, 150, 'textarea' ),
					'brand_story_p3' => $text( '正文第三段', '既调筋骨病痛、结节慢病、妇科诸疾，亦关注现代人情志内耗，追求形神共调，固本培元。于锦官闹市开辟一方玄静杏林，不逐速效浮名，以青囊守仁心，以董针渡尘劳，以本草安浮生。', 110, 160, 'textarea' ),
					'brand_story_image_left' => $image( '底部左图', 'assets/images/brand-v1/story-scene-bottom-left.webp', 1200, 900, 350 ),
					'brand_story_image_left_alt' => $text( '底部左图替代文字', '尔意堂医馆雨中风铃景观', 22, 42 ),
					'brand_story_image_left_caption' => $text( '底部左图说明', '静室安然，专注调养', 12, 22 ),
					'brand_story_image_right' => $image( '底部右图', 'assets/images/brand-v1/story-scene-bottom-right.webp', 1200, 900, 350 ),
					'brand_story_image_right_alt' => $text( '底部右图替代文字', '尔意堂中医文化交流场景', 22, 42 ),
					'brand_story_image_right_caption' => $text( '底部右图说明', '薪火相传，教学相长', 12, 22 ),
				) ),
				'brand_heritage' => array( 'label' => '品牌页非遗介绍', 'fields' => array(
					'brand_heritage_title' => $text( '标题', "承董氏奇穴文脉\n续岐黄济世之道", 22, 30, 'textarea' ),
					'brand_heritage_desc' => $text( '介绍正文', '董氏奇穴远溯华佗道医脉系，历经七十三代传至董公景昌，公著《董氏针灸正经奇穴学》立针学体系。2024年针法列入市级非物质文化遗产，后纳入全国中医适宜技术，典籍收录于中医院校教材，2026年由中国中医药出版社出版《董氏奇穴针灸学》纳入中医院校教科书。尔意堂为官方非遗传承中心，承三脉辨证古法，续董针济世薪火。', 190, 260, 'textarea' ),
					'brand_heritage_image' => $image( '非遗展示图', 'assets/images/home-v4/heritage-certificate.webp', 1200, 900, 450 ),
					'brand_heritage_image_alt' => $text( '图片替代文字', '尔意堂董氏奇穴市级非物质文化遗产传承中心授牌场景', 30, 55 ),
					'brand_heritage_caption' => $text( '图片说明', '董氏奇穴市级非物质文化遗产传承中心', 24, 40 ),
					'brand_heritage_tag_1' => $text( '标签一', '市级非遗', 6, 10 ),
					'brand_heritage_tag_2' => $text( '标签二', '第三代嫡传', 7, 11 ),
					'brand_heritage_tag_3' => $text( '标签三', '三脉合参', 6, 10 ),
				) ),
				'brand_values' => array( 'label' => '核心诊疗技术', 'fields' => array(
					'brand_values_title' => $text( '模块标题', '核心诊疗技术', 8, 14 ),
					'brand_values_desc' => $text( '模块说明', '小至肝郁失眠、乳腺结节、女科宫寒；大至三高消渴、中风偏瘫、脊柱侧弯，皆量身定制内外同调方案，固本培元，修复耗散真元。医馆重视当代人心神耗损，坚持形与神同调。', 110, 160, 'textarea' ),
				) ),
				'brand_future' => array( 'label' => '未来发展方向', 'fields' => array(
					'brand_future_title' => $text( '模块标题', '守正精进，让古中医融入当代生活', 20, 30 ),
					'brand_future_desc' => $text( '模块说明', '从临床、传承、科普、康养与空间五个方向持续建设尔意堂。', 35, 55, 'textarea' ),
				) ),
			),
		),
		'contact' => array(
			'label' => '联系页固定内容',
			'description' => '维护联系页环境轮播标题和五张环境图片。地址、电话、地图和营业时间仍在“尔意堂管理”维护。',
			'groups' => array(
				'contact_gallery' => array( 'label' => '医馆环境轮播', 'fields' => array(
					'contact_gallery_eyebrow' => $text( '英文眉题', 'CLINIC SPACE', 12, 20 ),
					'contact_gallery_title' => $text( '模块标题', '闹市藏静境，医道安尘劳', 16, 24 ),
				) ),
			),
		),
		'identity' => array(
			'label' => '公共标识',
			'description' => '顶部深色 Logo 与底部浅色 Logo 分开维护。替换只改变图片，不改变显示宽度和页头页脚结构。',
			'groups' => array(
				'logos' => array( 'label' => '顶部与底部 Logo', 'fields' => array(
					'utility_statement' => $text( '顶部红色信息栏说明', '董氏奇穴针灸技法非物质文化遗产传承医馆', 24, 38 ),
					'header_logo' => $image( '顶部导航 Logo（深色版）', 'assets/images/home-v3/logo-header.webp', 480, 304, 250 ),
					'header_logo_alt' => $text( '顶部 Logo 替代文字', '尔意堂中医馆', 12, 24 ),
					'footer_logo' => $image( '公共底部 Logo（浅色版）', 'assets/images/home-v3/logo-footer.webp', 480, 267, 250 ),
					'footer_logo_alt' => $text( '底部 Logo 替代文字', '尔意堂中医馆', 12, 24 ),
				) ),
			),
		),
		'backgrounds' => array(
			'label' => '高级背景素材',
			'description' => '这些图片决定整屏背景裁切。只有准备好相同比例、主体安全区一致的成品图时才替换。',
			'advanced' => true,
			'groups' => array(
				'backgrounds' => array( 'label' => '页面背景', 'fields' => array(
					'background_paper' => $image( '全站浅色纸纹', 'assets/images/brand-v1/bg-light.webp', 1920, 1920, 500 ),
					'background_home_intro' => $image( '首页品牌介绍背景', 'assets/images/home-v6/bg-hero-beige-mountain.webp', 1920, 1080, 550 ),
					'background_home_doctors' => $image( '首页医师团队背景', 'assets/images/home-v6/bg-doctors-red-cloud.webp', 1920, 1080, 550 ),
					'background_home_therapies' => $image( '首页特色技法背景', 'assets/images/home-v6/bg-therapies-beige-crane.webp', 1920, 1080, 550 ),
					'background_home_conditions' => $image( '首页调理方向背景', 'assets/images/home-v6/bg-conditions.webp', 1920, 1080, 550 ),
					'background_brand_story' => $image( '品牌故事背景', 'assets/images/brand-v2/brand-bg-story.webp', 1920, 1080, 550 ),
					'background_brand_heritage' => $image( '品牌非遗背景', 'assets/images/brand-v2/brand-bg-heritage.webp', 1920, 1080, 550 ),
					'background_brand_credentials' => $image( '品牌资质背景', 'assets/images/brand-v2/brand-bg-credentials.webp', 1920, 1080, 550 ),
					'background_brand_values' => $image( '品牌诊疗原则背景', 'assets/images/brand-v2/brand-bg-principle.webp', 1920, 1080, 550 ),
					'background_brand_future' => $image( '品牌未来方向背景', 'assets/images/brand-v2/brand-bg-future.webp', 1920, 1080, 550 ),
				) ),
			),
		),
	);

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
	foreach ( $therapies as $i => $item ) {
		$n = $i + 1;
		$schema['home']['groups']['home_therapies']['fields'][ 'therapy_' . $n . '_title' ] = $text( '技法 ' . $n . ' 名称', $item[0], 10, 16 );
		$schema['home']['groups']['home_therapies']['fields'][ 'therapy_' . $n . '_desc' ] = $text( '技法 ' . $n . ' 说明', $item[2], 45, 70, 'textarea' );
		$schema['home']['groups']['home_therapies']['fields'][ 'therapy_' . $n . '_image' ] = $image( '技法 ' . $n . ' 图片', 'assets/images/home-v3/' . $item[1], 1200, 900, 320 );
		$schema['home']['groups']['home_therapies']['fields'][ 'therapy_' . $n . '_alt' ] = $text( '技法 ' . $n . ' 图片替代文字', $item[0] . '调理场景', 22, 42 );
	}

	$conditions = array(
		array( '各类疼痛', '如颈椎病、腰椎病、肩周炎、腱鞘炎等职业病引起的疼痛。' ), array( '脊柱侧弯', '青少年脊柱状态与日常护理方向。' ),
		array( '中风偏瘫', '由中风、脑梗等疾病导致的运动、语言等功能受限。' ), array( '慢性疾病', '常见的高血压、糖尿病、高血脂等疾病。' ),
		array( '男科及妇科类疾病', '常见痛经、月经不调、子宫肌瘤、不孕不育、骨盆修复等疾病。' ), array( '五脏调理', '脏腑功能失调出现的症状表现，如肥胖、内分泌失调、头晕头痛等。' ),
		array( '失眠抑郁', '由各类因素导致出现的情绪问题、失眠抑郁等疾病。' ), array( '结节增生类疾病', '常见的乳腺结节、肺结节、乳腺增生、淋巴结节、甲状腺结节等。' ),
		array( '术后康复', '常见骨科手术如踝关节骨折术后、脊柱骨折术后、肩关节骨折术后。' ),
	);
	foreach ( $conditions as $i => $item ) {
		$n = $i + 1;
		$schema['home']['groups']['home_conditions']['fields'][ 'condition_' . $n . '_title' ] = $text( '方向 ' . $n . ' 名称', $item[0], 10, 16 );
		$schema['home']['groups']['home_conditions']['fields'][ 'condition_' . $n . '_desc' ] = $text( '方向 ' . $n . ' 说明', $item[1], 45, 75, 'textarea' );
	}

	$values = array(
		array( '脉诊辨证', '融合传统脉、道家脉、太素全息脉，推演五行脏腑盛衰，溯源先天，标本兼顾。' ), array( '非遗针道', '承董氏奇穴非遗文脉，顺气机升降，疏通经络淤堵，止痛同时调和五脏元阳。' ),
		array( '古法外治', '雷音陈艾艾灸、道家古方药油推拿、筋膜松解、禅龙正骨、运动康复与瑶浴药蒸内外同调。' ), array( '草方药', '遵循一气周流、五行升降原理而制汤药、膏方、外用贴剂，祛邪固本。' ),
	);
	foreach ( $values as $i => $item ) {
		$n = $i + 1;
		$schema['brand']['groups']['brand_values']['fields'][ 'brand_value_' . $n . '_title' ] = $text( '技术 ' . $n . ' 标题', $item[0], 8, 14 );
		$schema['brand']['groups']['brand_values']['fields'][ 'brand_value_' . $n . '_desc' ] = $text( '技术 ' . $n . ' 说明', $item[1], 55, 85, 'textarea' );
	}
	$futures = array(
		array( '深耕临床', '打磨三脉合参辨证体系，沉淀病案，精进针、灸、手法、方药组合方案，提升慢病、疑难问题调理能力。' ), array( '非遗传承', '开设董氏奇穴、筋膜正骨、道医脉法研修课程，推动非遗活态传承。' ),
		array( '文化科普', '举办节气讲座、养生雅叙，传播顺时养生理念，践行上医治未病。' ), array( '医养闭环', '研发居家康养产品，打通医馆诊疗与居家养护的完整闭环链路。' ), array( '空间营造', '持续打造古中医静养空间，兼顾诊疗与养生雅叙。' ),
	);
	foreach ( $futures as $i => $item ) {
		$n = $i + 1;
		$schema['brand']['groups']['brand_future']['fields'][ 'brand_future_' . $n . '_title' ] = $text( '方向 ' . $n . ' 标题', $item[0], 8, 14 );
		$schema['brand']['groups']['brand_future']['fields'][ 'brand_future_' . $n . '_desc' ] = $text( '方向 ' . $n . ' 说明', $item[1], 55, 85, 'textarea' );
	}
	for ( $i = 1; $i <= 5; $i++ ) {
		$schema['contact']['groups']['contact_gallery']['fields'][ 'contact_gallery_' . $i . '_image' ] = $image( '环境图片 ' . $i, 'assets/images/contact-v1/gallery-0' . $i . '.webp', 1600, 1100, 400 );
		$schema['contact']['groups']['contact_gallery']['fields'][ 'contact_gallery_' . $i . '_alt' ] = $text( '环境图片 ' . $i . ' 替代文字', '尔意堂医馆环境', 22, 42 );
	}
	return $schema;
}

/** 扁平化某分区字段。 */
function eryitang_fixed_section_fields( $section ) {
	$schema = eryitang_fixed_content_schema();
	$fields = array();
	if ( ! isset( $schema[ $section ] ) ) { return $fields; }
	foreach ( $schema[ $section ]['groups'] as $group ) { $fields += $group['fields']; }
	return $fields;
}

/** 注册独立一级菜单。 */
function eryitang_fixed_content_menu() {
	add_menu_page( '页面固定内容', '页面固定内容', 'manage_options', 'eryitang-fixed-content', 'eryitang_fixed_content_dashboard', 'dashicons-layout', 22 );
	add_submenu_page( 'eryitang-fixed-content', '页面固定内容', '管理首页', 'manage_options', 'eryitang-fixed-content', 'eryitang_fixed_content_dashboard' );
	foreach ( array( 'home', 'brand', 'contact', 'identity', 'backgrounds' ) as $section ) {
		$schema = eryitang_fixed_content_schema();
		add_submenu_page( 'eryitang-fixed-content', $schema[ $section ]['label'], $schema[ $section ]['label'], 'manage_options', 'eryitang-fixed-' . $section, static function () use ( $section ) { eryitang_fixed_content_page( $section ); } );
	}
}
add_action( 'admin_menu', 'eryitang_fixed_content_menu', 6 );

/** 加载后台资源。 */
function eryitang_fixed_content_assets() {
	$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
	if ( 0 !== strpos( $page, 'eryitang-fixed' ) ) { return; }
	wp_enqueue_media();
	wp_enqueue_style( 'eryitang-fixed-content', plugins_url( 'assets/admin-fixed-content.css', dirname( __FILE__ ) ), array(), ERYITANG_CORE_VERSION );
	wp_enqueue_script( 'eryitang-fixed-content', plugins_url( 'assets/admin-fixed-content.js', dirname( __FILE__ ) ), array(), ERYITANG_CORE_VERSION, true );
	wp_localize_script( 'eryitang-fixed-content', 'eryitangFixedContent', array( 'ackKey' => 'eryitang-fixed-content-ack-v1' ) );
}
add_action( 'admin_enqueue_scripts', 'eryitang_fixed_content_assets' );

/** 管理首页。 */
function eryitang_fixed_content_dashboard() {
	if ( ! current_user_can( 'manage_options' ) ) { return; }
	$schema = eryitang_fixed_content_schema();
	echo '<div class="wrap eryitang-fixed-wrap"><h1>页面固定内容</h1><p class="eryitang-fixed-lead">用于替换主题中原本写死的图片和文字。保存后会直接更新正式网站，但不会允许修改字体、颜色、间距、页面结构或响应式规则。</p><div class="eryitang-fixed-warning"><strong>进入修改前请确认</strong><p>文字尽量保持当前长度，图片保持提示中的比例和主体安全区。明显超长文字需要二次确认；比例明显错误的图片会阻止保存。</p></div><div class="eryitang-fixed-cards">';
	foreach ( array( 'home', 'brand', 'contact', 'identity', 'backgrounds' ) as $key ) {
		$item = $schema[ $key ];
		echo '<a href="' . esc_url( admin_url( 'admin.php?page=eryitang-fixed-' . $key ) ) . '"' . ( ! empty( $item['advanced'] ) ? ' class="is-advanced"' : '' ) . '><strong>' . esc_html( $item['label'] ) . '</strong><span>' . esc_html( $item['description'] ) . '</span></a>';
	}
	echo '</div></div>';
}

/** 渲染字段。 */
function eryitang_fixed_render_field( $key, $field, $options ) {
	$value = $options[ $key ] ?? '';
	if ( 'image' === $field['type'] ) {
		$current_id = absint( $value );
		$url        = $current_id ? wp_get_attachment_image_url( $current_id, 'medium' ) : eryitang_theme_asset_url( $field['fallback'] );
		$ratio      = round( $field['width'] / $field['height'], 4 );
		echo '<div class="eryitang-fixed-media" data-ratio="' . esc_attr( (string) $ratio ) . '" data-label="' . esc_attr( $field['label'] ) . '"><img src="' . esc_url( $url ) . '" alt=""><input type="hidden" name="fields[' . esc_attr( $key ) . ']" value="' . esc_attr( (string) $current_id ) . '" data-original="' . esc_attr( (string) $current_id ) . '"><div><button class="button eryitang-fixed-media-select" type="button">从媒体库替换</button> <button class="button-link eryitang-fixed-media-clear" type="button">恢复主题默认图</button><p>推荐 ' . esc_html( $field['width'] . '×' . $field['height'] . 'px' ) . '（比例 ' . esc_html( $field['width'] . ':' . $field['height'] ) . '），WebP/JPEG，建议 ≤ ' . esc_html( (string) $field['max_kb'] ) . 'KB。</p><p class="eryitang-fixed-media-status" aria-live="polite"></p></div></div>';
		return;
	}
	$length = function_exists( 'mb_strlen' ) ? mb_strlen( (string) ( '' !== $value ? $value : $field['default'] ) ) : strlen( (string) ( '' !== $value ? $value : $field['default'] ) );
	$value  = '' !== $value ? $value : $field['default'];
	$attrs  = ' data-original="' . esc_attr( $value ) . '" data-recommended="' . esc_attr( (string) $field['recommended'] ) . '" data-safe="' . esc_attr( (string) $field['safe'] ) . '"';
	if ( 'textarea' === $field['type'] ) {
		echo '<textarea class="large-text eryitang-fixed-text" rows="4" name="fields[' . esc_attr( $key ) . ']"' . $attrs . '>' . esc_textarea( $value ) . '</textarea>';
	} else {
		echo '<input class="large-text eryitang-fixed-text" type="text" name="fields[' . esc_attr( $key ) . ']" value="' . esc_attr( $value ) . '"' . $attrs . '>';
	}
	echo '<p class="eryitang-fixed-counter" aria-live="polite"><span>' . esc_html( (string) $length ) . '</span> 字；建议不超过 ' . esc_html( (string) $field['recommended'] ) . ' 字，安全上限 ' . esc_html( (string) $field['safe'] ) . ' 字。</p>';
}

/** 分区编辑页。 */
function eryitang_fixed_content_page( $section ) {
	if ( ! current_user_can( 'manage_options' ) ) { return; }
	$schema = eryitang_fixed_content_schema();
	if ( ! isset( $schema[ $section ] ) ) { return; }
	$options = eryitang_fixed_content_options();
	$item    = $schema[ $section ];
	echo '<div class="wrap eryitang-fixed-wrap"><h1>' . esc_html( $item['label'] ) . '</h1><p class="eryitang-fixed-lead">' . esc_html( $item['description'] ) . '</p>';
	if ( isset( $_GET['updated'] ) ) { echo '<div class="notice notice-success is-dismissible"><p>固定内容已保存，刷新前台即可看到新内容。</p></div>'; }
	if ( isset( $_GET['restored'] ) ) { echo '<div class="notice notice-success is-dismissible"><p>已恢复此页面上一次保存前的内容。</p></div>'; }
	if ( isset( $_GET['ratio_error'] ) ) { echo '<div class="notice notice-error"><p>图片比例偏差过大，本次没有保存。请按字段推荐比例重新裁切后上传。</p></div>'; }
	echo '<div class="eryitang-fixed-warning eryitang-fixed-gate"><strong>样式安全提醒</strong><p>这里只替换内容，不会自动重排页面。请保持文字长度和图片比例接近当前值；明显超长文字可在二次确认后保存，比例明显错误的图片不能保存。</p><label><input type="checkbox"> 我已了解，并按现有结构和推荐比例修改</label></div>';
	echo '<form class="eryitang-fixed-form" method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"><input type="hidden" name="action" value="eryitang_save_fixed_content"><input type="hidden" name="section" value="' . esc_attr( $section ) . '">';
	wp_nonce_field( 'eryitang_save_fixed_content_' . $section );
	if ( 'backgrounds' === $section ) {
		echo '<details class="eryitang-fixed-advanced"><summary>展开高级背景素材</summary><div class="eryitang-fixed-advanced-body">';
	}
	foreach ( $item['groups'] as $group ) {
		echo '<section class="eryitang-fixed-panel"><h2>' . esc_html( $group['label'] ) . '</h2><table class="form-table" role="presentation">';
		foreach ( $group['fields'] as $key => $field ) {
			echo '<tr><th scope="row">' . esc_html( $field['label'] ) . '</th><td>';
			eryitang_fixed_render_field( $key, $field, $options );
			echo '</td></tr>';
		}
		echo '</table></section>';
	}
	if ( 'backgrounds' === $section ) {
		echo '</div></details>';
	}
	echo '<div class="eryitang-fixed-changes" hidden><h2>本次变更摘要</h2><ul></ul></div><div class="eryitang-fixed-actions">';
	submit_button( '确认并保存到正式网站', 'primary', 'submit', false );
	echo '</div></form>';
	$previous = get_option( 'eryitang_fixed_content_previous_' . $section, array() );
	if ( is_array( $previous ) && $previous ) {
		echo '<div class="eryitang-fixed-actions"><form class="eryitang-fixed-restore" method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"><input type="hidden" name="action" value="eryitang_restore_fixed_content"><input type="hidden" name="section" value="' . esc_attr( $section ) . '">';
		wp_nonce_field( 'eryitang_restore_fixed_content_' . $section );
		echo '<button class="button" type="submit">恢复此页上一次内容</button></form></div>';
	}
	echo '</div>';
}

/** 保存固定内容。 */
function eryitang_save_fixed_content() {
	if ( ! current_user_can( 'manage_options' ) ) { wp_die( '没有权限。' ); }
	$section = isset( $_POST['section'] ) ? sanitize_key( wp_unslash( $_POST['section'] ) ) : '';
	check_admin_referer( 'eryitang_save_fixed_content_' . $section );
	$fields = eryitang_fixed_section_fields( $section );
	if ( ! $fields ) { wp_die( '无效分区。' ); }
	$input   = isset( $_POST['fields'] ) && is_array( $_POST['fields'] ) ? wp_unslash( $_POST['fields'] ) : array();
	$current = eryitang_fixed_content_options();
	$next    = $current;
	foreach ( $fields as $key => $field ) {
		$value = $input[ $key ] ?? '';
		if ( 'image' === $field['type'] ) {
			$id = absint( $value );
			if ( $id ) {
				$meta = wp_get_attachment_metadata( $id );
				if ( empty( $meta['width'] ) || empty( $meta['height'] ) ) { wp_safe_redirect( admin_url( 'admin.php?page=eryitang-fixed-' . $section . '&ratio_error=1' ) ); exit; }
				$expected = $field['width'] / $field['height'];
				$actual   = $meta['width'] / $meta['height'];
				if ( abs( $actual - $expected ) / $expected > 0.08 ) { wp_safe_redirect( admin_url( 'admin.php?page=eryitang-fixed-' . $section . '&ratio_error=1' ) ); exit; }
			}
			$next[ $key ] = $id;
		} elseif ( 'textarea' === $field['type'] ) {
			$next[ $key ] = sanitize_textarea_field( $value );
		} else {
			$next[ $key ] = sanitize_text_field( $value );
		}
	}
	$previous = array();
	foreach ( $fields as $key => $field ) {
		$previous[ $key ] = array_key_exists( $key, $current ) ? $current[ $key ] : ( 'image' === $field['type'] ? 0 : $field['default'] );
	}
	update_option( 'eryitang_fixed_content_previous_' . $section, $previous, false );
	update_option( 'eryitang_fixed_content', $next, false );
	wp_safe_redirect( admin_url( 'admin.php?page=eryitang-fixed-' . $section . '&updated=1' ) );
	exit;
}
add_action( 'admin_post_eryitang_save_fixed_content', 'eryitang_save_fixed_content' );

/** 恢复分区上一次内容。 */
function eryitang_restore_fixed_content() {
	if ( ! current_user_can( 'manage_options' ) ) { wp_die( '没有权限。' ); }
	$section = isset( $_POST['section'] ) ? sanitize_key( wp_unslash( $_POST['section'] ) ) : '';
	check_admin_referer( 'eryitang_restore_fixed_content_' . $section );
	$fields   = eryitang_fixed_section_fields( $section );
	$previous = get_option( 'eryitang_fixed_content_previous_' . $section, array() );
	$current  = eryitang_fixed_content_options();
	$before   = array();
	foreach ( $fields as $key => $field ) {
		$before[ $key ] = array_key_exists( $key, $current ) ? $current[ $key ] : ( 'image' === $field['type'] ? 0 : $field['default'] );
		if ( is_array( $previous ) && array_key_exists( $key, $previous ) ) { $current[ $key ] = $previous[ $key ]; } else { unset( $current[ $key ] ); }
	}
	update_option( 'eryitang_fixed_content', $current, false );
	update_option( 'eryitang_fixed_content_previous_' . $section, $before, false );
	wp_safe_redirect( admin_url( 'admin.php?page=eryitang-fixed-' . $section . '&restored=1' ) );
	exit;
}
add_action( 'admin_post_eryitang_restore_fixed_content', 'eryitang_restore_fixed_content' );

/** 将后台多行标题安全转换为换行。 */
function eryitang_fixed_lines( $value ) {
	return nl2br( esc_html( $value ) );
}

/** 首页品牌介绍。 */
function eryitang_fixed_home_intro_shortcode() {
	$main   = eryitang_fixed_image_url( 'home_intro_image_main', 'assets/images/home-v4/home-intro-main.webp' );
	$top    = eryitang_fixed_image_url( 'home_intro_image_top', 'assets/images/home-v4/home-intro-top.webp' );
	$bottom = eryitang_fixed_image_url( 'home_intro_image_bottom', 'assets/images/home-v4/home-intro-bottom.webp' );
	return '<section class="hero"><div class="container hero-inner"><div class="hero-copy reveal"><h2>' . esc_html( eryitang_fixed_text( 'home_intro_title', '闹市之中' ) ) . '<br>' . esc_html( eryitang_fixed_text( 'home_intro_title_second', '一处' ) ) . '<span class="accent">' . esc_html( eryitang_fixed_text( 'home_intro_title_accent', '岐黄静境' ) ) . '</span></h2><p class="hero-lead">' . esc_html( eryitang_fixed_text( 'home_intro_lead', '尔意堂栖于闹市，藏楼宇间岐黄静境。诗云 “一针祛痼世人惊，穴蕴玄机指下明”，堂承董氏奇穴非遗文脉，循三脉辨证古法，以银针、正骨、药油推拿、本草药浴、雷音艾灸调和阴阳。木舍藏艾香，暖茶伴脉诊，不止疗沉疴，更筑闹市身心归处，以内敛古雅医道，安抚世人尘劳。' ) ) . '</p><div class="hero-actions"><a class="btn btn-primary" href="' . esc_url( home_url( '/brand/' ) ) . '"><span>' . esc_html( eryitang_fixed_text( 'home_intro_button_primary', '了解尔意堂' ) ) . '</span></a><a class="btn btn-outline" href="' . esc_url( home_url( '/contact/' ) ) . '"><span>' . esc_html( eryitang_fixed_text( 'home_intro_button_secondary', '查看到店信息' ) ) . '</span></a></div></div><div class="hero-visual hero-collage reveal delay-1" aria-label="尔意堂中医馆环境"><figure class="hero-collage-item hero-collage-main">' . eryitang_responsive_image_html( $main, eryitang_fixed_text( 'home_intro_image_main_alt', '尔意堂中医馆圆形品牌标识墙' ), array( 'style' => 'object-position:78% 52%;' ) ) . '</figure><figure class="hero-collage-item hero-collage-side">' . eryitang_responsive_image_html( $top, eryitang_fixed_text( 'home_intro_image_top_alt', '尔意堂医馆本草药材陈列细节' ), array( 'style' => 'object-position:100% 50%;' ) ) . '</figure><figure class="hero-collage-item hero-collage-detail">' . eryitang_responsive_image_html( $bottom, eryitang_fixed_text( 'home_intro_image_bottom_alt', '尔意堂中医文化展示墙' ), array( 'style' => 'object-position:100% 48%;' ) ) . '</figure></div></div></section>';
}
add_shortcode( 'eryitang_fixed_home_intro', 'eryitang_fixed_home_intro_shortcode' );

/** 首页非遗介绍。 */
function eryitang_fixed_home_heritage_shortcode() {
	$image = eryitang_fixed_image_url( 'home_heritage_image', 'assets/images/home-v4/heritage-certificate.webp' );
	return '<section class="section heritage" id="heritage"><div class="container heritage-grid"><div class="heritage-visual reveal"><figure class="heritage-main">' . eryitang_responsive_image_html( $image, eryitang_fixed_text( 'home_heritage_image_alt', '尔意堂市级非物质文化遗产项目传承中心授牌合影' ), array( 'loading' => 'lazy' ) ) . '<figcaption class="heritage-watermark"><small>ER YI TANG · INTANGIBLE HERITAGE</small><strong>董氏奇穴三代嫡传<br>续岐黄医道薪火</strong></figcaption></figure></div><div class="heritage-copy reveal delay-1"><h2 class="section-title art-title art-title-stacked"><span>' . esc_html( eryitang_fixed_text( 'home_heritage_title', '承董氏奇穴文脉' ) ) . '</span><em>' . esc_html( eryitang_fixed_text( 'home_heritage_subtitle', '续岐黄济世之道' ) ) . '</em></h2><p class="section-desc">' . esc_html( eryitang_fixed_text( 'home_heritage_desc', '医馆承董氏奇穴市级非物质文化遗产，针法溯源华佗道门传承，历经七十三代薪火相传；2024 年针法列入市级非物质文化遗产，后纳入全国中医适宜技术，典籍收录于中医院校教材，2026年由中国中医药出版社出版《董氏奇穴针灸学》纳入中医院校教科书。恪守「针有奇穴之传，药承华佗之脉，道继龙门之真，三脉归一」立馆理念，独创传统脉、道家脉、太素全息脉三脉合参辨证体系，以道医 “观气化、溯先天” 为则，跳出只治病灶的固化思维。' ) ) . '</p><div class="heritage-points heritage-honors" aria-label="尔意堂传承荣誉"><div class="heritage-honor"><strong>' . esc_html( eryitang_fixed_text( 'home_heritage_tag_1', '市级非遗' ) ) . '</strong></div><div class="heritage-honor"><strong>' . esc_html( eryitang_fixed_text( 'home_heritage_tag_2', '第三代嫡传' ) ) . '</strong></div><div class="heritage-honor"><strong>' . esc_html( eryitang_fixed_text( 'home_heritage_tag_3', '三脉合参' ) ) . '</strong></div></div></div></div></section>';
}
add_shortcode( 'eryitang_fixed_home_heritage', 'eryitang_fixed_home_heritage_shortcode' );

/** 首页九项调理方向。 */
function eryitang_fixed_home_conditions_shortcode() {
	$defaults = array(
		array( '各类疼痛', '如颈椎病、腰椎病、肩周炎、腱鞘炎等职业病引起的疼痛。' ), array( '脊柱侧弯', '青少年脊柱状态与日常护理方向。' ), array( '中风偏瘫', '由中风、脑梗等疾病导致的运动、语言等功能受限。' ),
		array( '慢性疾病', '常见的高血压、糖尿病、高血脂等疾病。' ), array( '男科及妇科类疾病', '常见痛经、月经不调、子宫肌瘤、不孕不育、骨盆修复等疾病。' ), array( '五脏调理', '脏腑功能失调出现的症状表现，如肥胖、内分泌失调、头晕头痛等。' ),
		array( '失眠抑郁', '由各类因素导致出现的情绪问题、失眠抑郁等疾病。' ), array( '结节增生类疾病', '常见的乳腺结节、肺结节、乳腺增生、淋巴结节、甲状腺结节等。' ), array( '术后康复', '常见骨科手术如踝关节骨折术后、脊柱骨折术后、肩关节骨折术后。' ),
	);
	$cards = '';
	foreach ( $defaults as $i => $item ) {
		$n = $i + 1;
		$cards .= '<article class="condition reveal' . ( $i % 3 ? ' delay-' . ( $i % 3 ) : '' ) . '"><span class="index">' . esc_html( sprintf( '%02d', $n ) ) . '</span><h3>' . esc_html( eryitang_fixed_text( 'condition_' . $n . '_title', $item[0] ) ) . '</h3><p>' . esc_html( eryitang_fixed_text( 'condition_' . $n . '_desc', $item[1] ) ) . '</p></article>';
	}
	return '<section class="section conditions" id="conditions"><div class="container conditions-layout"><div class="conditions-heading reveal"><h2 class="section-title art-title conditions-title"><span>' . esc_html( eryitang_fixed_text( 'home_conditions_title', '从身体所感' ) ) . '</span><em>' . esc_html( eryitang_fixed_text( 'home_conditions_subtitle', '找到调理方向' ) ) . '</em></h2><a class="btn btn-outline conditions-more" href="' . esc_url( eryitang_category_url( 'conditions' ) ) . '"><span>' . esc_html( eryitang_fixed_text( 'home_conditions_button', '了解更多' ) ) . '</span></a></div><div class="condition-grid">' . $cards . '</div></div></section>';
}
add_shortcode( 'eryitang_fixed_home_conditions', 'eryitang_fixed_home_conditions_shortcode' );

/** 品牌故事。 */
function eryitang_fixed_brand_story_shortcode() {
	return '<section class="brand-story brand-paper-section"><div class="container"><div class="brand-section-ornament" aria-hidden="true"><span></span><i></i></div><div class="brand-story-layout"><aside class="brand-story-sidebar reveal"><h2>' . eryitang_fixed_lines( eryitang_fixed_text( 'brand_story_side_title', "顺尔本真\n以意调气" ) ) . '</h2><blockquote>' . esc_html( eryitang_fixed_text( 'brand_story_quote', '恬淡虚无，真气从之。' ) ) . '</blockquote><p>' . esc_html( eryitang_fixed_text( 'brand_story_side_desc', '尔意，尔为汝，代表每一位来访者本身；意为本心气机、神意所向；取名源自《黄帝内经》有云 “恬淡虚无，真气从之”，寄寓顺尔本真，以意调气，行医当体察个体禀赋，顺应人身气机规律调和阴阳，帮助身体回归自然平衡。' ) ) . '</p></aside><article class="brand-story-scroll reveal delay-1"><header class="brand-story-lead"><h2>' . eryitang_fixed_lines( eryitang_fixed_text( 'brand_story_main_title', "闹市藏静境\n医道安尘劳" ) ) . '</h2></header><p>' . esc_html( eryitang_fixed_text( 'brand_story_p1', '尘世车马喧嚣，世人终日耗损元阳、经络淤滞、身心俱疲。尔意堂隐于锦江区梨花街楼宇之内，融黄老道统与岐黄古法，集诊疗、养生、玄谈于一体，于闹市之中筑一处岐黄静境。' ) ) . '</p><figure class="brand-story-main-image">' . eryitang_responsive_image_html( eryitang_fixed_image_url( 'brand_story_main_image', 'assets/images/brand-v1/story-scene-top.webp' ), eryitang_fixed_text( 'brand_story_main_alt', '尔意堂医馆安静开阔的庭院空间' ), array( 'loading' => 'lazy' ) ) . '<figcaption>' . esc_html( eryitang_fixed_text( 'brand_story_main_caption', '一方静境，容身心暂离城市喧嚣' ) ) . '</figcaption></figure><div class="brand-story-prose"><p>' . esc_html( eryitang_fixed_text( 'brand_story_p2', '这里不止是诊疗场所，更是以道与医养心空间。堂内艾香萦绕，清茶常设。以银针、雷音艾灸、正骨推拿、瑶浴药蒸、道地本草方药，针、艾、药、手技内外同调。' ) ) . '</p><p>' . esc_html( eryitang_fixed_text( 'brand_story_p3', '既调筋骨病痛、结节慢病、妇科诸疾，亦关注现代人情志内耗，追求形神共调，固本培元。于锦官闹市开辟一方玄静杏林，不逐速效浮名，以青囊守仁心，以董针渡尘劳，以本草安浮生。' ) ) . '</p></div><div class="brand-story-image-pair"><figure>' . eryitang_responsive_image_html( eryitang_fixed_image_url( 'brand_story_image_left', 'assets/images/brand-v1/story-scene-bottom-left.webp' ), eryitang_fixed_text( 'brand_story_image_left_alt', '尔意堂医馆雨中风铃景观' ), array( 'loading' => 'lazy' ) ) . '<figcaption>' . esc_html( eryitang_fixed_text( 'brand_story_image_left_caption', '静室安然，专注调养' ) ) . '</figcaption></figure><figure>' . eryitang_responsive_image_html( eryitang_fixed_image_url( 'brand_story_image_right', 'assets/images/brand-v1/story-scene-bottom-right.webp' ), eryitang_fixed_text( 'brand_story_image_right_alt', '尔意堂中医文化交流场景' ), array( 'loading' => 'lazy' ) ) . '<figcaption>' . esc_html( eryitang_fixed_text( 'brand_story_image_right_caption', '薪火相传，教学相长' ) ) . '</figcaption></figure></div></article></div></div></section>';
}
add_shortcode( 'eryitang_fixed_brand_story', 'eryitang_fixed_brand_story_shortcode' );

/** 品牌非遗介绍。 */
function eryitang_fixed_brand_heritage_shortcode() {
	return '<section class="brand-heritage"><div class="container brand-heritage-layout"><figure class="brand-heritage-image reveal">' . eryitang_responsive_image_html( eryitang_fixed_image_url( 'brand_heritage_image', 'assets/images/home-v4/heritage-certificate.webp' ), eryitang_fixed_text( 'brand_heritage_image_alt', '尔意堂董氏奇穴市级非物质文化遗产传承中心授牌场景' ), array( 'loading' => 'lazy' ) ) . '<figcaption>' . esc_html( eryitang_fixed_text( 'brand_heritage_caption', '董氏奇穴市级非物质文化遗产传承中心' ) ) . '</figcaption></figure><div class="brand-heritage-copy reveal delay-1"><div class="brand-section-ornament is-light" aria-hidden="true"><span></span><i></i></div><h2>' . eryitang_fixed_lines( eryitang_fixed_text( 'brand_heritage_title', "承董氏奇穴文脉\n续岐黄济世之道" ) ) . '</h2><p>' . esc_html( eryitang_fixed_text( 'brand_heritage_desc', '董氏奇穴远溯华佗道医脉系，历经七十三代传至董公景昌，公著《董氏针灸正经奇穴学》立针学体系。2024年针法列入市级非物质文化遗产，后纳入全国中医适宜技术，典籍收录于中医院校教材，2026年由中国中医药出版社出版《董氏奇穴针灸学》纳入中医院校教科书。尔意堂为官方非遗传承中心，承三脉辨证古法，续董针济世薪火。' ) ) . '</p><div class="brand-honor-tags" aria-label="传承荣誉"><span>' . esc_html( eryitang_fixed_text( 'brand_heritage_tag_1', '市级非遗' ) ) . '</span><span>' . esc_html( eryitang_fixed_text( 'brand_heritage_tag_2', '第三代嫡传' ) ) . '</span><span>' . esc_html( eryitang_fixed_text( 'brand_heritage_tag_3', '三脉合参' ) ) . '</span></div></div></div></section>';
}
add_shortcode( 'eryitang_fixed_brand_heritage', 'eryitang_fixed_brand_heritage_shortcode' );

/** 品牌核心诊疗技术。 */
function eryitang_fixed_brand_values_shortcode() {
	$defaults = array( array( '脉诊辨证', '融合传统脉、道家脉、太素全息脉，推演五行脏腑盛衰，溯源先天，标本兼顾。' ), array( '非遗针道', '承董氏奇穴非遗文脉，顺气机升降，疏通经络淤堵，止痛同时调和五脏元阳。' ), array( '古法外治', '雷音陈艾艾灸、道家古方药油推拿、筋膜松解、禅龙正骨、运动康复与瑶浴药蒸内外同调。' ), array( '草方药', '遵循一气周流、五行升降原理而制汤药、膏方、外用贴剂，祛邪固本。' ) );
	$cards = '';
	foreach ( $defaults as $i => $item ) { $n = $i + 1; $cards .= '<article class="brand-principle-card reveal' . ( $i ? ' delay-' . $i : '' ) . '"><h3>' . esc_html( eryitang_fixed_text( 'brand_value_' . $n . '_title', $item[0] ) ) . '</h3><p>' . esc_html( eryitang_fixed_text( 'brand_value_' . $n . '_desc', $item[1] ) ) . '</p></article>'; }
	return '<section class="brand-principle"><div class="container"><header class="brand-section-heading reveal"><div class="brand-section-ornament" aria-hidden="true"><span></span><i></i></div><h2>' . esc_html( eryitang_fixed_text( 'brand_values_title', '核心诊疗技术' ) ) . '</h2><p>' . esc_html( eryitang_fixed_text( 'brand_values_desc', '小至肝郁失眠、乳腺结节、女科宫寒；大至三高消渴、中风偏瘫、脊柱侧弯，皆量身定制内外同调方案，固本培元，修复耗散真元。医馆重视当代人心神耗损，坚持形与神同调。' ) ) . '</p></header><div class="brand-principle-grid">' . $cards . '</div></div></section>';
}
add_shortcode( 'eryitang_fixed_brand_values', 'eryitang_fixed_brand_values_shortcode' );

/** 品牌未来方向。 */
function eryitang_fixed_brand_future_shortcode() {
	$defaults = array( array( '深耕临床', '打磨三脉合参辨证体系，沉淀病案，精进针、灸、手法、方药组合方案，提升慢病、疑难问题调理能力。' ), array( '非遗传承', '开设董氏奇穴、筋膜正骨、道医脉法研修课程，推动非遗活态传承。' ), array( '文化科普', '举办节气讲座、养生雅叙，传播顺时养生理念，践行上医治未病。' ), array( '医养闭环', '研发居家康养产品，打通医馆诊疗与居家养护的完整闭环链路。' ), array( '空间营造', '持续打造古中医静养空间，兼顾诊疗与养生雅叙。' ) );
	$cards = '';
	foreach ( $defaults as $i => $item ) { $n = $i + 1; $cards .= '<article class="brand-future-card reveal' . ( $i % 4 ? ' delay-' . ( $i % 4 ) : '' ) . '"><h3>' . esc_html( eryitang_fixed_text( 'brand_future_' . $n . '_title', $item[0] ) ) . '</h3><p>' . esc_html( eryitang_fixed_text( 'brand_future_' . $n . '_desc', $item[1] ) ) . '</p></article>'; }
	return '<section class="brand-future brand-paper-section"><div class="container"><header class="brand-section-heading reveal"><div class="brand-section-ornament" aria-hidden="true"><span></span><i></i></div><h2>' . esc_html( eryitang_fixed_text( 'brand_future_title', '守正精进，让古中医融入当代生活' ) ) . '</h2><p>' . esc_html( eryitang_fixed_text( 'brand_future_desc', '从临床、传承、科普、康养与空间五个方向持续建设尔意堂。' ) ) . '</p></header><div class="brand-future-grid">' . $cards . '</div></div></section>';
}
add_shortcode( 'eryitang_fixed_brand_future', 'eryitang_fixed_brand_future_shortcode' );

/** 输出用户替换的背景覆盖层。 */
function eryitang_fixed_background_styles() {
	$options = eryitang_fixed_content_options();
	$rules   = array(
		'background_paper' => 'body.has-shared-shell{--eryitang-paper-texture:url("%s")}',
		'background_home_intro' => '.page-home-v2 .hero{background-image:linear-gradient(rgba(250,247,239,.74),rgba(250,247,239,.72)),url("%s")!important}',
		'background_home_doctors' => '.page-home-v2 .doctors{background-image:linear-gradient(rgba(91,22,20,.54),rgba(91,22,20,.62)),url("%s")!important}',
		'background_home_therapies' => '.page-home-v2 .therapies{background-image:linear-gradient(rgba(249,247,241,.56),rgba(249,247,241,.62)),url("%s")!important}',
		'background_home_conditions' => '.page-home-v2 .conditions{background-image:linear-gradient(90deg,rgba(247,242,232,.92) 0 25%,rgba(247,242,232,.79) 48%,rgba(247,242,232,.72) 100%),url("%s")!important}',
		'background_brand_story' => '.page-brand-v1 .brand-story{background-image:linear-gradient(rgba(251,248,241,.83),rgba(251,248,241,.83)),url("%s")!important}',
		'background_brand_heritage' => '.page-brand-v1 .brand-heritage{background-image:linear-gradient(rgba(91,27,24,.16),rgba(91,27,24,.16)),url("%s")!important}',
		'background_brand_credentials' => '.page-brand-v1 .brand-credentials{background-image:linear-gradient(rgba(250,247,240,.36),rgba(250,247,240,.36)),url("%s")!important}',
		'background_brand_values' => '.page-brand-v1 .brand-principle{background-image:linear-gradient(rgba(247,239,224,.46),rgba(247,239,224,.46)),url("%s")!important}',
		'background_brand_future' => '.page-brand-v1 .brand-future{background-image:linear-gradient(rgba(244,242,237,.52),rgba(244,242,237,.52)),url("%s")!important}',
	);
	$css = '';
	foreach ( $rules as $key => $rule ) {
		$id = isset( $options[ $key ] ) ? absint( $options[ $key ] ) : 0;
		if ( $id ) { $url = wp_get_attachment_image_url( $id, 'full' ); if ( $url ) { $css .= sprintf( $rule, esc_url_raw( $url ) ); } }
	}
	if ( $css ) { echo '<style id="eryitang-fixed-backgrounds">' . wp_strip_all_tags( $css ) . '</style>'; }
}
add_action( 'wp_head', 'eryitang_fixed_background_styles', 30 );
