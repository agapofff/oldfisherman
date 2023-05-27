<?php
// отключение ненужных функций
include('reset.php');

// WooCommerce
require get_theme_file_path('/woocommerce/woocommerce-functions.php');


// style and scripts
add_action('wp_enqueue_scripts', 'bootscore_child_enqueue_styles');
function bootscore_child_enqueue_styles() {
    // PJAX
    wp_enqueue_script('pjax', get_stylesheet_directory_uri() . '/js/pjax.min.js', false, '', true);
    
    // TOASTR
	wp_enqueue_style('toastr', get_stylesheet_directory_uri() . '/css/toastr.css');
	wp_enqueue_script('toastr', get_stylesheet_directory_uri() . '/js/toastr.min.js', false, '', true);
    
    // OWL
	wp_enqueue_style('slick', get_stylesheet_directory_uri() . '/css/owl.carousel.css');
	wp_enqueue_style('slick', get_stylesheet_directory_uri() . '/css/owl.theme.default.css');
	wp_enqueue_script('slick', get_stylesheet_directory_uri() . '/js/owl.carousel.js', false, '', true);
    
	// input mask
	wp_enqueue_script('mask-js', get_stylesheet_directory_uri() . '/js/jquery.mask.js', false, '', true);
    
    // style.css
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');

    // Compiled main.css
    $modified_bootscoreChildCss = date('YmdHi', filemtime(get_stylesheet_directory() . '/css/main.css'));
    wp_enqueue_style('main', get_stylesheet_directory_uri() . '/css/main.css', array('parent-style'), $modified_bootscoreChildCss);

    // custom.js
    wp_enqueue_script('custom-js', get_stylesheet_directory_uri() . '/js/custom.js', false, '', true);
}


// новые места для модулей
function axio_register_widgets() {	
	register_sidebar([
        'name'          => 'Мобильное меню',
        'id'            => 'mobile-menu',
        'description'   => 'mobile menu',
        // 'before_widget' => '<div class="container">',
        // 'after_widget'  => '</div>',
        // 'before_title'  => '<div class="d-none">',
        // 'after_title'   => '</div>',
    ]);
    
	register_sidebar([
        'name'          => 'Шапка (слева)',
        'id'            => 'hat-left',
        'description'   => 'hat left',
        'before_widget' => '<div class="col-auto py-2">',
        'after_widget'  => '</div>',
        'before_title'  => '<div class="d-none">',
        'after_title'   => '</div>',
    ]);
	register_sidebar([
        'name'          => 'Шапка (справа)',
        'id'            => 'hat-right',
        'description'   => 'hat right',
        'before_widget' => '<div class="col-auto py-2">',
        'after_widget'  => '</div>',
        'before_title'  => '<div class="d-none">',
        'after_title'   => '</div>',
    ]);
    
	register_sidebar([
        'name'          => 'Под меню', //название виджета в админ-панели
        'id'            => 'top', //идентификатор виджета
        'description'   => 'top', //описание виджета в админ-панели
        'before_widget' => '<section class="top">', //открывающий тег виджета с динамичным идентификатором
        'after_widget'  => '</section>', //закрывающий тег виджета с очищающим блоком
        'before_title'  => '', //открывающий тег заголовка виджета
        'after_title'   => '',//закрывающий тег заголовка виджета
    ]);
    
	register_sidebar([
        'name'          => 'Над контентом',
        'id'            => 'up',
        'description'   => 'up',
        'before_widget' => '<section class="up">',
        'after_widget'  => '</section',
        'before_title'  => '',
        'after_title'   => '',
    ]);
    
	register_sidebar([
        'name'          => 'Под контентом',
        'id'            => 'down',
        'description'   => 'down',
        'before_widget' => '<section class="down">',
        'after_widget'  => '</section>',
        'before_title'  => '',
        'after_title'   => '',
    ]);
	
	register_sidebar([
        'name'          => 'Над футером',
        'id'            => 'bottom',
        'description'   => 'bottom',
        'before_widget' => '<section class="bottom">',
        'after_widget'  => '</section>',
        'before_title'  => '',
        'after_title'   => '',
    ]);
	
	register_sidebar([
        'name'          => 'Примененные фильтры',
        'id'            => 'applied-filters',
        'description'   => 'Примененные фильтры',
        'before_widget' => '',
        'after_widget'  => '',
        'before_title'  => '',
        'after_title'   => '',
    ]);
}

add_action('widgets_init', 'axio_register_widgets');


// добавление краткого описания товаров в категориях
/*
add_action('woocommerce_after_shop_loop_item_title', 'ats_add_short_description', 9); // 9 приоритет
function ats_add_short_description() {
	global $post;
	$text = $post->post_excerpt;
	$text = preg_replace('~\[[^\]]+\]~', '', $text); // убирает шорткоды
	$text = strip_tags($text);

	// количество символов
	// $maxchar = 120;
	// if ( mb_strlen( $text ) > $maxchar ){
		// $text = mb_substr( $text, 0, $maxchar );
		// $text = preg_replace('@(.*)\s[^\s]*$@s', '\\1 ...', $text );
	// }
	
	echo '<div class="woocommerce-product-loop-short-description align-self-start">' . $text . '</div>';
}
*/


// проверка наличия товара в корзине
function woo_in_cart($product_id){
	global $woocommerce;
	foreach ($woocommerce->cart->get_cart() as $key => $val){
		$_product = $val['data'];
		if ($product_id == $_product->id){
			return true;
		}
	}
	return false;
}


// редирект на Главную при выходе
add_action('wp_logout', 'go_home');
function go_home(){
	wp_redirect(home_url());
	exit();
}



// дополнительные статусы заказов
function register_my_new_order_statuses() {
	register_post_status('wc-paid', array(
		'label'                     => _x('Оплачен', 'Order status', 'woocommerce'),
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop('Оплачен <span class="count">(%s)</span>', 'Оплачен <span class="count">(%s)</span>', 'woocommerce')
	) );
	
	register_post_status('wc-delivered', array(
		'label'                     => _x('Доставлен', 'Order status', 'woocommerce'),
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop('Доставлен <span class="count">(%s)</span>', 'Доставлен <span class="count">(%s)</span>', 'woocommerce')
	) );
}
add_action('init', 'register_my_new_order_statuses');

function my_new_wc_order_statuses($order_statuses) {
	$order_statuses['wc-paid'] = _x('Оплачен', 'Order status', 'woocommerce');
	$order_statuses['wc-delivered'] = _x('Доставлен', 'Order status', 'woocommerce');

	return $order_statuses;
}
add_filter('wc_order_statuses', 'my_new_wc_order_statuses');



// переименование статусов заказов
add_filter('wc_order_statuses', 'wc_renaming_order_status');
function wc_renaming_order_status($order_statuses) {
	foreach ($order_statuses as $key => $status) {
		if ('wc-on-hold' === $key){
			$order_statuses['wc-on-hold'] = _x('Сборка', 'Order status', 'woocommerce');
		}
		
		if ('wc-delivering' === $key){
			$order_statuses['wc-delivering'] = _x('Доставляется', 'Order status', 'woocommerce');
		}
		
		if ('wc-shipped' === $key){
			$order_statuses['wc-shipped'] = _x('Отправлен', 'Order status', 'woocommerce');
		}
		
		if ('wc-completed' === $key){
			$order_statuses['wc-completed'] = _x('Выполнен', 'Order status', 'woocommerce');
		}
	}
	return $order_statuses;
}



// вывод товаров по метке
function woo_products_by_tags_shortcode($atts, $content = null) {
	// Get attribuets
	extract(shortcode_atts([
		'tags' => '',
		'title' => '',
		'class' => '',
		'order' => 'title',
	], $atts));

	ob_start();

	// Define Query Arguments
	$args = [
		'post_type' => 'product',
		'posts_per_page' => 10,
		'product_tag' => $tags,
		'orderby' => $order,
	];

	// Create the new query
	$loop = new WP_Query($args);

	// Get products number
	$product_count = $loop->post_count;

	if ($product_count > 0) {
		echo '<div class="products-carousel ' . $class . '">
				<div class="row">
					<div class="col-auto">
						<h4 class="text-uppercase">
							' . $title . '
						</h4>
					</div>
					<div class="col">
						<hr>
					</div>
				</div>
				<div class="category-products-container">';
		while ($loop->have_posts()) {
            $loop->the_post(); 
            global $product;
			wc_get_template_part('content', 'product');
		}

		echo '</div></div>';
    } else {
		_e('No product matching your criteria.');
	} // endif $product_count > 0

	return ob_get_clean();
}
add_shortcode('woo_products_by_tags', 'woo_products_by_tags_shortcode');



// убрать лишние вкладки в товаре
add_filter('woocommerce_product_tabs', 'woo_remove_product_tabs', 98);
function woo_remove_product_tabs($tabs) {
    unset($tabs['description']);
    unset($tabs['additional_information']);
    return $tabs;
}




// БЕСПЛАТНАЯ ДОСТАВКА

// добавляем поля и скрипты в админку
add_filter('woocommerce_get_sections_shipping', 'free_shipping_terms_add_fields');
 
function free_shipping_terms_add_fields($sections) {
	$sections['free_shipping_terms'] = 'Условия бесплатной доставки';
	return $sections;
}

add_filter('woocommerce_get_settings_shipping', 'free_shipping_terms_add_settings', 10, 2);
 
function free_shipping_terms_add_settings($settings, $current_section) {
	if ('free_shipping_terms' === $current_section) { 
		$settings = [];
		$settings[] = [
			'name' => 'Условия бесплатной доставки',
			'type' => 'title',
			'desc' => 'Обнулять доставку, если рассчитанная стоимость доставки находится в указанном диапазоне, и стоимость корзины больше или равна указанной'
		];
		$settings[] = [
			'name' => 'Курьером',
			'id' => 'free_shipping_terms_delivery',
			'type' => 'text',
			'css' => 'display:none',
			'class' => 'free_shipping_terms',
		];
		$settings[] = [
			'name' => 'Постамат',
			'id' => 'free_shipping_terms_postamat',
			'type' => 'text',
			'css' => 'display:none',
			'class' => 'free_shipping_terms',
		];
		$settings[] = [
			'type' => 'sectionend',
		];
	}

	return $settings;
}

function free_shipping_terms_script($hook) {
    wp_enqueue_script('my_custom_script', get_stylesheet_directory_uri() . '/js/free_shipping_terms.js');
}

add_action('admin_enqueue_scripts', 'free_shipping_terms_script');

// параметры бесплатной доставки на для javascript-расчётов
add_action('wp_head', 'free_shipping_terms_add_script_wp_head');
/*
function free_shipping_terms_add_script_wp_head() {
?>
	<script>
		var freeShippingTermsDelivery = JSON.parse('<?= get_option("free_shipping_terms_delivery") ?>'),
			freeShippingTermsPostamat = JSON.parse('<?= get_option("free_shipping_terms_postamat") ?>');
	</script>
<?php
}
*/

// обнуляем доставку при соблюдении условий
function free_shipping_terms_set($rates, $package) {
	foreach ($rates as $key => $rate) { 
		if ($rate->instance_id == 1) {
			$freeShippingTerms = json_decode(get_option('free_shipping_terms_delivery'));
		}
		if ($rate->instance_id == 3) {
			$freeShippingTerms = json_decode(get_option('free_shipping_terms_postamat'));
		}
		foreach ($freeShippingTerms as $term) {
			if (
				$rates[$key]->cost >= $term->min && 
				$rates[$key]->cost <= $term->max && 
				WC()->cart->subtotal >= $term->sum
			){
				$rates[$key]->cost = 0;
			}
		}
	}

    return $rates;
}

add_filter('woocommerce_package_rates', 'free_shipping_terms_set', 10, 2);




// Font Awesome CDN Setup Webfont
// This will load Font Awesome from the Font Awesome Free or Pro CDN.
// if (!function_exists('fa_custom_setup_cdn_webfont')){
    // function fa_custom_setup_cdn_webfont($cdn_url = '', $integrity = null) {
        // $matches = [];
        // $match_result = preg_match('|/([^/]+?)\.css$|', $cdn_url, $matches);
        // $resource_handle_uniqueness = ($match_result === 1) ? $matches[1] : md5($cdn_url);
        // $resource_handle = "font-awesome-cdn-webfont-$resource_handle_uniqueness";

        // foreach (['wp_enqueue_scripts', 'admin_enqueue_scripts', 'login_enqueue_scripts'] as $action) {
            // add_action(
                // $action,
                // function () use ($cdn_url, $resource_handle) {
                    // wp_enqueue_style($resource_handle, $cdn_url, [], null);
                // }
            // );
        // }

        // if ($integrity) {
            // add_filter(
                // 'style_loader_tag',
                // function ($html, $handle) use ($resource_handle, $integrity) {
                    // if (in_array($handle, [$resource_handle], true)) {
                        // return preg_replace('/\/>$/', 'integrity="' . $integrity . '" crossorigin="anonymous" />', $html, 1);
                    // } else {
                        // return $html;
                    // }
                // },
                // 10,
                // 2
            // );
        // }
    // }
// }
// fa_custom_setup_cdn_webfont('//cdn.jsdelivr.net/gh/hung1001/font-awesome-pro@4cac1a6/css/all.css');



// СТИЛИ В АДМИНКЕ
add_action('admin_head', function (){
    echo '<style>
            .trp-license-notice {
                display: none !important;
            }
        </style>';
});


// заголовок woocommerce
// add_filter('woocommerce_page_title', 'theme_shop_page_title', 10);
// function theme_shop_page_title($page_title) {
    // return '<h1 class="page-title h2 mb-4 mt-n2">' . $page_title . '</h1>';
// }


// скрыть корзину на странице подтверждения оформления заказа
add_filter('woocommerce_output_cart_shortcode_content', 'hide_cart_on_finish', 25); 
function hide_cart_on_finish($display_cart) {
	if (is_wc_endpoint_url('order-received')) {
		$display_cart = false;
	}
	return $display_cart;
}


// редирект в каталог, если корзина пуста
function redirect_empty_cart() {
	if (
        is_cart() 
        && is_checkout() 
        && WC()->cart->get_cart_contents_count() == 0
        && !is_wc_endpoint_url('order-pay') 
        && !is_wc_endpoint_url('order-received') 
	) {
		wp_safe_redirect(home_url());
		exit;
	}
}
add_action('template_redirect', 'redirect_empty_cart', 25);


// Цены в RSD, вместо РСД
function change_currency_rsd($args) {
	$args['price_format'] = '%2$s RSD';
	return $args;
}
add_filter('wc_price_args', 'change_currency_rsd');


