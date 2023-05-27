<?php
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wlwmanifest_link');
// remove_action('wp_head', 'wp_resource_hints', 2);
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'rel_canonical');
remove_action('wp_head', 'wp_site_icon');
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'noindex', 1);
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
remove_action('wp_head', 'index_rel_link'); 
remove_action('wp_head', 'start_post_rel_link', 10);  
remove_action('wp_head', 'parent_post_rel_link', 10); 
remove_action('wp_head', 'adjacent_posts_rel_link', 10);
remove_action('wp_head', 'pagenavi_css'); 


// Отключаем WP-API версий 1.x
add_filter('json_enabled', '__return_false');
add_filter('json_jsonp_enabled', '__return_false');
 
// Отключаем WP-API версий 2.x
add_filter('rest_enabled', '__return_false');
add_filter('rest_jsonp_enabled', '__return_false');
 
// Удаляем информацию о REST API из заголовков HTTP и секции head
remove_action('xmlrpc_rsd_apis', 'rest_output_rsd');
remove_action('wp_head', 'rest_output_link_wp_head');
remove_action('template_redirect', 'rest_output_link_header', 11);
 
// Отключаем фильтры REST API
remove_action('auth_cookie_malformed', 'rest_cookie_collect_status');
remove_action('auth_cookie_expired', 'rest_cookie_collect_status');
remove_action('auth_cookie_bad_username', 'rest_cookie_collect_status');
remove_action('auth_cookie_bad_hash', 'rest_cookie_collect_status');
remove_action('auth_cookie_valid', 'rest_cookie_collect_status');
remove_filter('rest_authentication_errors', 'rest_cookie_check_errors', 100);
 
// Отключаем события REST API
remove_action('init', 'rest_api_init');
remove_action('rest_api_init', 'rest_api_default_filters', 10, 1);
remove_action('parse_request', 'rest_api_loaded');
 
// Отключаем Embeds связанные с REST API
remove_action('rest_api_init', 'wp_oembed_register_route');
remove_filter('rest_pre_serve_request', '_oembed_rest_pre_serve_request', 10, 4);
remove_action('wp_head', 'wp_oembed_add_discovery_links');
remove_action('wp_head', 'wp_oembed_add_host_js');
 
// Редиректим со страницы /wp-json/ на главную
add_action('template_redirect', function() {
    if (preg_match('#\/wp-json\/.*?#', $_SERVER['REQUEST_URI'])) {
        wp_redirect(get_option('siteurl'), 301);
        die();
    }
});


// disable password strength-meter 
function disable_password_strength_meter() {
    wp_dequeue_script('wc-password-strength-meter');
}
add_action('wp_print_scripts', 'disable_password_strength_meter', 20);



// Disable the emoji's
function disable_emojis_tinymce($plugins) {
	if (is_array($plugins)) {
		return array_diff($plugins, array('wpemoji'));
	} else {
		return array();
	}
}
function disable_emojis_remove_dns_prefetch($urls, $relation_type) {
    if ('dns-prefetch' == $relation_type) {
        $emoji_svg_url = apply_filters('emoji_svg_url', 'https://s.w.org/images/core/emoji/2.2.1/svg/');
        $urls = array_diff($urls, array($emoji_svg_url));
    }
    return $urls;
}
function disable_emojis() {
	remove_action('wp_head', 'print_emoji_detection_script', 7);
	remove_action('admin_print_scripts', 'print_emoji_detection_script');
	remove_action('wp_print_styles', 'print_emoji_styles');
	remove_action('admin_print_styles', 'print_emoji_styles');	
	remove_filter('the_content_feed', 'wp_staticize_emoji');
	remove_filter('comment_text_rss', 'wp_staticize_emoji');	
	remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
	add_filter('tiny_mce_plugins', 'disable_emojis_tinymce');
	add_filter('tiny_mce_plugins', 'disable_emojis_remove_dns_prefetch', 10, 2);
}
add_action('init', 'disable_emojis');



// disable json api 
add_filter('rest_endpoints', function ($endpoints) {
    if (isset($endpoints['/wp/v2/users'])) {
        unset($endpoints['/wp/v2/users']);
    }
    if (isset($endpoints['/wp/v2/users/(?P<id>[\d]+)'])) {
        unset($endpoints['/wp/v2/users/(?P<id>[\d]+)']);
    }
    return $endpoints;
});


// Отключение rss ленты
function fb_disable_feed() {
    wp_redirect(get_option('siteurl'));
}
add_action('do_feed', 'fb_disable_feed', 1);
add_action('do_feed_rdf', 'fb_disable_feed', 1);
add_action('do_feed_rss', 'fb_disable_feed', 1);
add_action('do_feed_rss2', 'fb_disable_feed', 1);
add_action('do_feed_atom', 'fb_disable_feed', 1);


// Отменяем srcset
add_filter('wp_calculate_image_srcset_meta', '__return_null');
add_filter('wp_calculate_image_sizes', '__return_false', 99);
remove_filter('the_content', 'wp_make_content_images_responsive');


// Отключаем Gutenberg
add_filter('use_block_editor_for_post_type', '__return_false', 100);
add_action('admin_init', function () {
    remove_action('admin_notices', ['WP_Privacy_Policy_Content', 'notice']);
    add_action('edit_form_after_title', ['WP_Privacy_Policy_Content', 'notice']);
});

function gut_style_disable() {
    wp_dequeue_style('wp-block-library');
} 
add_action('wp_enqueue_scripts', 'gut_style_disable', 100);


// Отключение XML-RPC
// add_filter('xmlrpc_enabled', '__return_false'); 
function block_xmlrpc_attacks($methods) {
    unset($methods['pingback.ping']);
    unset($methods['pingback.extensions.getPingbacks']);
    return $methods;
}
add_filter('xmlrpc_methods', 'block_xmlrpc_attacks');

function remove_x_pingback_header($headers) {
    unset($headers['X-Pingback']);
    return $headers;
}
add_filter('wp_headers', 'remove_x_pingback_header');


// убираем стили .recentcomments
function remove_recent_comments_style() {
    global $wp_widget_factory;
    remove_action('wp_head', array($wp_widget_factory -> widgets['WP_Widget_Recent_Comments'], 'recent_comments_style'));
}
add_action('widgets_init', 'remove_recent_comments_style');


// Отключить jQuery migrate
function remove_jquery_migrate(&$scripts) {
    if (!is_admin()) {
        $scripts->remove('jquery');
        $scripts->add('jquery', false, ['jquery-core'], '1.12.4');
    }
}
add_filter('wp_default_scripts', 'remove_jquery_migrate');


// remove css js version
// ВНИМАНИЕ! Отключить для корректной работы плагина кеширования
function remove_cssjs_ver($src) {
    if (!is_admin()) {
        if (strpos($src,'?ver=')) {
            $src = remove_query_arg('ver', $src);
            return $src;
        }
    } else {
        return $src;
    }
}
add_filter('style_loader_src', 'remove_cssjs_ver', 10, 2);
add_filter('script_loader_src', 'remove_cssjs_ver', 10, 2);


/* убрать Font Awesome */ 
function remove_font_awesome() {
	wp_dequeue_style('fontawesome');
}
add_action('wp_enqueue_scripts', 'remove_font_awesome', 999);
remove_filter('style_loader_tag', 'bootscore_fa_preload', 999);


//Поддержка темы
add_theme_support('title-tag');
add_theme_support('post-thumbnails');
add_theme_support('html5', ['comment-list', 'comment-form', 'search-form', 'gallery', 'caption']);


// Добавляет SVG в список разрешенных для загрузки файлов.
add_filter('upload_mimes', 'svg_upload_allow');
function svg_upload_allow($mimes) {
    $mimes['svg']  = 'image/svg+xml';
    return $mimes;
}
add_filter('wp_check_filetype_and_ext', 'fix_svg_mime_type', 10, 5);
function fix_svg_mime_type($data, $file, $filename, $mimes, $real_mime = '') {
    if (version_compare($GLOBALS['wp_version'], '5.1.0', '>=')) {
        $dosvg = in_array($real_mime, ['image/svg', 'image/svg+xml']);
    } else {
        $dosvg = ('.svg' === strtolower(substr($filename, -4)));
    }
    if ($dosvg) {
        if (current_user_can('manage_options')) {
            $data['ext']  = 'svg';
            $data['type'] = 'image/svg+xml';
        } else {
            $data['ext'] = $type_and_ext['type'] = false;
        }
    }
    return $data;
}
