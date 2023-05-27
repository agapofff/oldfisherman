<?php

/**
 * Template Name: Left Sidebar
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Bootscore
 */

get_header();
?>

<div id="content" class="site-content container my-3 my-md-4 my-lg-5">
    <div id="primary" class="content-area">

        <!-- Hook to add something nice -->
        <?php bs_after_primary(); ?>

        <div class="row">
            <?php get_sidebar(); ?>
            <div class="col-md-8 col-lg-9 order-first order-md-last">

                <main id="main" class="site-main">

                    <header class="entry-header">
                        <?php the_post(); ?>
                        <h1 class="page-title h2 mb-4 mt-n2"><?php the_title(); ?></h1>
                        <?php bootscore_post_thumbnail(); ?>
                    </header>

                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>

                    <footer class="entry-footer">
                        <?php comments_template(); ?>
                    </footer>

                </main>

            </div>
        </div>

    </div>
</div>

<?php
get_footer();