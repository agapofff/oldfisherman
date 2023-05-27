<?php

/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

defined('ABSPATH') || exit;

global $product;

// Ensure visibility.
if (empty($product) || !$product->is_visible()) {
    return;
}
?>

<div class="col-sm-6 col-md-12 col-lg-6 col-xl-4 mb-4">
    <div <?php wc_product_class('card h-100 d-flex text-center', $product); ?>>
        <?php
        /**
         * Hook: woocommerce_before_shop_loop_item.
         *
         * @hooked woocommerce_template_loop_product_link_open - 10
         */
        do_action('woocommerce_before_shop_loop_item');

        /**
         * Hook: woocommerce_before_shop_loop_item_title.
         *
         * @hooked woocommerce_show_product_loop_sale_flash - 10
         * @hooked woocommerce_template_loop_product_thumbnail - 10
         */
        do_action('woocommerce_before_shop_loop_item_title');

        ?>
        <div class="card-body d-flex flex-column">
            <?php do_action('woocommerce_shop_loop_item_title'); ?>
            <div class="row justify-content-between align-items-center">
                <?php do_action('woocommerce_after_shop_loop_item_title'); ?>
                <div class="col-auto">
                    <div class="row justify-content-end align-items-center loop-product-buttons g-0">
                        <?php do_action('woocommerce_after_shop_loop_item'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>