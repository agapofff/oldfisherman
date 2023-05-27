<?php

/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Bootscore
 *
 * @version 5.2.0.0-beta1
 */

if (!is_active_sidebar('sidebar-1')) {
    return;
}
?>
<div class="col-md-4 col-lg-3 order-first pt-4">
    <aside id="secondary" class="widget-area position-relative mt-3">
        <?php /* the_breadcrumb(); */ ?>

        <button class="btn btn-link mb-4 d-md-none position-absolute top-0 end-100" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar">
            <i class="fal fa-sliders-v"></i>
        </button>

        <div class="offcanvas-md offcanvas-end" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel">
            <div class="offcanvas-header bg-light">
                <span class="h5 offcanvas-title" id="sidebarLabel">
                    <?php esc_html_e('Sidebar', 'bootscore'); ?>
                </span>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebar" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body flex-column">
                <?php dynamic_sidebar('sidebar-1'); ?>
            </div>
        </div>

  </aside><!-- #secondary -->
</div>
