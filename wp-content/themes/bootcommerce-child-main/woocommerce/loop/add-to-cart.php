<?php

/**
 * Loop Add to Cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/add-to-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.6.1
 */

if (!defined('ABSPATH')) {
  exit;
}

global $product;

echo apply_filters(
    'woocommerce_loop_add_to_cart_link', // WPCS: XSS ok.
    sprintf(
        '<div class="add-to-cart-container">
            <a href="%s" data-quantity="%s" class="%s product_type_%s single_add_to_cart_button category-add-to-cart-button btn btn-primary border-0 mx-auto rounded-0 d-flex justify-content-center align-items-center py-2 %s %s" %s>
                <span class="material-symbols-outlined fs-5 my-1">
                    %s
                </span>
            </a>
        </div>',
        esc_url($product->add_to_cart_url()),
        esc_attr(isset($args['quantity']) ? $args['quantity'] : 1),
        $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : 'd-none',
        esc_attr($product->get_type()),
        $product->get_type() == 'simple' ? 'ajax_add_to_cart' : '',
        woo_in_cart($product->id) ? 'added' : '',
        isset($args['attributes']) ? wc_implode_html_attributes($args['attributes']) : '',
        woo_in_cart($product->id) ? 'add_shopping_cart' : 'add_shopping_cart',
        // esc_html($product->add_to_cart_text())
        // 'добавить<br>в корзину',
        // 'товар<br>в корзине'
    ),
    $product,
    $args
);
