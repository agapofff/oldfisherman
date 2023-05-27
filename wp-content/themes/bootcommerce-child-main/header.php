<?php

/**
 * The header for our WooCommerce theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Bootscore
 * 
 * @version 5.2.3.1
 */

?>
<!doctype html>
<html <?php language_attributes() ?>>

    <head>
        <meta charset="<?php bloginfo('charset') ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Favicons -->
        <link rel="apple-touch-icon" sizes="180x180" href="<?= get_stylesheet_directory_uri() ?>/img/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="<?= get_stylesheet_directory_uri() ?>/img/favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="<?= get_stylesheet_directory_uri() ?>/img/favicon/favicon-16x16.png">
        <link rel="manifest" href="<?= get_stylesheet_directory_uri() ?>/img/favicon/site.webmanifest">
        <link rel="mask-icon" href="<?= get_stylesheet_directory_uri() ?>/img/favicon/safari-pinned-tab.svg" color="#0d6efd">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="theme-color" content="#ffffff">
        <?php wp_head() ?>
        <link rel="preconnect" href="//fonts.googleapis.com">
        <link rel="preconnect" href="//fonts.gstatic.com" crossorigin>
        <link href= "//fonts.googleapis.com/css2?family=Roboto+Condensed:wght@300;400;700&family=Roboto:wght@300;400;500;700&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,300,0,-25&display=swap" rel="stylesheet">
        <!-- <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css"> -->
    </head>

    <body <?php body_class() ?>>
    
        <div id="loader" class="fixed-top vw-100 vh-100 bg-white opacity-75">
            <div class="d-flex vw-100 vh-100 align-items-center justify-content-center">
                <svg class="spinner">
                    <circle cx="20" cy="20" r="18"></circle>
                </svg>
            </div>
        </div>

        <?php wp_body_open() ?>

        <div id="page" class="site">
            <section class="hat border-bottom">
                <div class="container">
                    <div class="row justify-content-between align-items-center">
                        <div class="col-auto">
                            <div class="row align-items-center">
                                <?php dynamic_sidebar('hat-left') ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="row align-items-center">
                                <?php dynamic_sidebar('hat-right') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <header id="masthead" class="site-header">

                <div class="fixed-top-">

                    <nav id="nav-main" class="navbar navbar-expand-lg py-1 py-xl-2 border-bottom">
                        <div class="container">
                            <!-- Navbar Brand -->
                            <a class="navbar-brand" href="<?= esc_url(home_url()) ?>">
                                <img src="<?= esc_url(get_stylesheet_directory_uri()) ?>/img/logo/Old_Fisherman-logo-150.png" alt="logo" class="logo">
                            </a>

                            <div class="collapse navbar-collapse">
                                <?php
                                    wp_nav_menu([
                                        'theme_location' => 'main-menu',
                                        'container' => false,
                                        'menu_class' => '',
                                        'fallback_cb' => '__return_false',
                                        'items_wrap' => '<ul id="bootscore-navbar" class="navbar-nav ms-auto %2$s">%3$s</ul>',
                                        'depth' => 2,
                                        'walker' => new bootstrap_5_wp_nav_menu_walker()
                                    ]);
                                ?>
                            </div>

                            <div id="nav-actions" class="header-actions d-flex align-items-center">

                                <!-- Top Nav Widget -->
                                <div class="top-nav-widget">
                            <?php 
                                if (is_active_sidebar('top-nav')) { 
                            ?>
                                    <div>
                                        <?php dynamic_sidebar('top-nav') ?>
                                    </div>
                            <?php 
                                } 
                            ?>
                                </div>

                                <!-- Search Toggler -->
                                <button class="btn btn-link ms-1 ms-md-2 px-2 top-nav-search-md" type="button" data-bs-toggle="offcanvas" data-bs-target="#search" aria-expanded="false" aria-controls="search">
                                    <span class="material-symbols-outlined fs-3">
                                        search
                                    </span>
                                </button>

                                <!-- User Toggler -->
                                <a href="/my-account" class="btn btn-link ms-1 ms-md-2 px-2">
                                    <span class="material-symbols-outlined fs-3">
                                        <?= is_user_logged_in() ? 'how_to_reg' : 'person' ?>
                                    </span>
                                </a>
                                
                                <a href="/my-account/wishlist" class="btn btn-link ms-1 ms-md-2 px-2">
                                    <span class="material-symbols-outlined fs-3">
                                        favorite
                                    </span>
                                </a>

                                <!-- Mini Cart Toggler -->
                                <button class="btn btn-link ms-1 ms-md-2 ps-2 pe-1 position-relative" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvas-cart" aria-controls="offcanvas-cart">
                                    <span class="material-symbols-outlined fs-3">
                                        shopping_cart_checkout
                                    </span>
                            <?php 
                                $count = WC()->cart->cart_contents_count;
                            ?>
                                    <span class="cart-content">
                                        <span class="cart-content-count position-absolute top-0 start-100 translate-middle badge rounded-circle bg-primary border border-light fw-light d-flex justify-content-center align-items-center p-2 mt-2 ms-n2">
                                            <small class="position-absolute">
                                                <?php echo esc_html($count ?: 0); ?>
                                            </small>
                                        </span>
                                    </span>
                                </button>

                                <!-- Navbar Toggler -->
                                <button class="btn btn-link d-lg-none ms-1 ms-md-2 px-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#menu" aria-controls="menu">
                                    <i class="fal fa-bars fa-lg"></i>
                                </button>

                            </div>
                        </div>
                    </nav>

                </div>
                
                <!-- offcanvas search -->
                <div id="search" class="offcanvas offcanvas-top text-bg-dark" tabindex="-1">
                    <div class="offcanvas-header">
                        <span class="h5 mb-0 text-uppercase text-white">
                            <?php esc_html_e('Search', 'bootscore') ?>
                        </span>
                        <button type="button" class="btn-close btn-close-white text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">
                <?php 
                    if (is_active_sidebar('top-nav-search')) {
                ?>
                        <div class="mb-2">
                            <?php dynamic_sidebar('top-nav-search') ?>
                        </div>
                <?php 
                    } 
                ?>
                    </div>
                </div>
                
                <!-- offcanvas menu -->
                <div id="menu" class="offcanvas offcanvas-end" tabindex="-1">
                    <div class="offcanvas-header bg-light">
                        <span class="h5 offcanvas-title text-uppercase">
                            <?php esc_html_e('Menu', 'bootscore') ?>
                        </span>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body flex-column">
                        <?php
                            wp_nav_menu([
                                'theme_location' => 'main-menu',
                                'container' => false,
                                'menu_class' => '',
                                'fallback_cb' => '__return_false',
                                'items_wrap' => '<ul id="mobile-navbar" class="navbar-nav ms-auto %2$s">%3$s</ul>',
                                'depth' => 5,
                                'walker' => new bootstrap_5_wp_nav_menu_walker()
                            ]);
                        ?>
                    </div>
                </div>

                <!-- offcanvas cart -->
                <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvas-cart">
                    <div class="offcanvas-header">
                        <span class="h5 mb-0 text-uppercase">
                            <?php esc_html_e('Cart', 'bootscore') ?>
                        </span>
                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body p-0">
                        <div class="cart-list">
                            <div class="widget_shopping_cart_content">
                                <?php woocommerce_mini_cart() ?>
                            </div>
                        </div>
                    </div>
                </div>
                
        <?php 
            if (is_active_sidebar('top')) { 
        ?>
                <section class="top">
                    <?php dynamic_sidebar('top') ?>
                </section>
        <?php
            }
        ?>

            </header>

    <?php 
        if (is_active_sidebar('up')) { 
    ?>
            <section class="up">
                <?php dynamic_sidebar('up') ?>
            </section>
    <?php
        }
    ?>