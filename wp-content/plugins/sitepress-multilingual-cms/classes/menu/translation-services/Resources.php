<?php

namespace WPML\TM\Menu\TranslationServices;

class Resources implements \IWPML_Backend_Action {

	public function add_hooks() {
		if ( $this->is_active() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}
	}

	public function enqueue_styles() {
		wp_enqueue_style(
			'wpml-tm-ts-admin-section',
			WPML_TM_URL . '/res/css/admin-sections/translation-services.css',
			array(),
			ICL_SITEPRESS_VERSION
		);

		wp_enqueue_style(
			'wpml-tm-translation-services',
			WPML_TM_URL . '/dist/css/translationServices/styles.css',
			[],
			ICL_SITEPRESS_VERSION
		);
	}

	public function enqueue_scripts() {

		wp_enqueue_script(
			'wpml-tm-ts-admin-section',
			WPML_TM_URL . '/res/js/translation-services.js',
			array(),
			ICL_SITEPRESS_VERSION
		);

		wp_enqueue_script(
			'wpml-tm-translation-services',
			WPML_TM_URL . '/dist/js/translationServices/app.js',
			array(),
			ICL_SITEPRESS_VERSION
		);

		wp_enqueue_script(
			'wpml-tp-api',
			WPML_TM_URL . '/res/js/wpml-tp-api.js',
			array( 'jquery', 'wp-util' ),
			ICL_SITEPRESS_VERSION
		);
	}

	private function is_active() {
		return isset( $_GET['sm'] ) && 'translators' === $_GET['sm'];
	}
}
