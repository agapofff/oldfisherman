<?php
/**
 * @package Polylang-Pro
 */

/**
 * Setup the block editor plugin
 *
 * @since 2.6
 */
class PLL_Block_Editor_Plugin {
	/**
	 * @var PLL_Model
	 */
	protected $model;

	/**
	 * @var PLL_CRUD_Posts
	 */
	protected $posts;

	/**
	 * @var array
	 */
	protected $options;

	/**
	 * @var PLL_Language|false
	 */
	protected $curlang;

	/**
	 * Constructor
	 *
	 * @since 2.6
	 *
	 * @param PLL_Frontend|PLL_Admin|PLL_Settings|PLL_REST_Request $polylang Polylang object.
	 */
	public function __construct( &$polylang ) {
		$this->model   = &$polylang->model;
		$this->posts   = &$polylang->posts;
		$this->options = &$polylang->options;
		$this->curlang = &$polylang->curlang;

		new PLL_Block_Editor_Filter_Preload_Paths( array( $this, 'preload_paths_for_post' ), 50, 2 ); // For posts only.
		new PLL_Block_Editor_Filter_Preload_Paths( array( $this, 'preload_paths' ), 50, 2 ); // For posts and widgets.
		add_filter( 'navigation_editor_preload_paths', array( $this, 'preload_paths' ), 50 ); // Experimental for Gutenberg.
		add_filter( 'widget_types_to_hide_from_legacy_widget_block', array( $this, 'filter_legacy_widgets' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	/**
	 * Filters the preload REST requests by the current language of the post.
	 *
	 * Necessary otherwise subsequent REST requests filtered by the language
	 * would not hit the preloaded requests.
	 *
	 * @since 3.1
	 *
	 * @param (string|string[])[] $preload_paths Array of paths to preload.
	 * @param WP_Post             $post          The post resource data.
	 * @return (string|string[])[]
	 */
	public function preload_paths_for_post( $preload_paths, $post ) {
		if ( ! $post instanceof WP_Post || ! $this->model->is_translated_post_type( $post->post_type ) ) {
			return $preload_paths;
		}

		// Set default language according to the context if no language is defined yet.
		$this->posts->set_default_language( $post->ID );
		$lang = $this->model->post->get_language( $post->ID );

		$preload_paths = array_merge(
			$preload_paths,
			array(
				'/wp/v2/users/me', // Add users/me without post_type parameter for core data preloading.
			)
		);

		$preload_paths = $this->add_preload_paths_parameters(
			$preload_paths,
			array(
				'lang' => $lang->slug,
			)
		);


		return $preload_paths;
	}

	/**
	 * Adds endpoint for languages to preloaded data, as well as is_block_editor parameter.
	 *
	 * @since 3.1
	 *
	 * @param (string|string[])[]             $preload_paths Array of paths to preload.
	 * @param WP_Block_Editor_Context|WP_Post $context       Block editor context or post resource data.
	 * @return (string|string[])[]
	 */
	public function preload_paths( $preload_paths, $context = null ) {
		if ( $context instanceof WP_Post && ! $this->model->is_translated_post_type( $context->post_type ) ) {
			return $preload_paths;
		}

		$preload_paths = array_merge( $preload_paths, array( '/pll/v1/languages' ) );
		return $this->add_preload_paths_parameters( $preload_paths, array( 'is_block_editor' => 'true' ) );
	}

	/**
	 * Add query parameters to the preload paths.
	 *
	 * @since 3.1
	 *
	 * @param (string|string[])[] $preload_paths Array of paths to preload.
	 * @param array               $args Optional args.
	 * @return (string|string[])[]
	 */
	private function add_preload_paths_parameters( $preload_paths, $args = array() ) {
		foreach ( $preload_paths as $k => $path ) {
			if ( empty( $path ) ) {
				continue;
			}

			$query_params = array();
			// If the method request is OPTIONS, $path is an array and the first element is the path
			if ( is_array( $path ) ) {
				$temp_path = $path[0];
			} else {
				$temp_path = $path;
			}

			$path_parts = wp_parse_url( $temp_path );

			if ( ! empty( $path_parts['query'] ) ) {
				parse_str( $path_parts['query'], $query_params );
			}

			if ( is_array( $args ) ) {
				// Add params in query params
				foreach ( $args as $key => $value ) {
					$query_params[ $key ] = $value;
				}
			}

			// Sort query params to put it in the same order as the preloading middleware does
			ksort( $query_params );

			// Replace the key by the correct path with query params reordered
			$sorted_path = add_query_arg( urlencode_deep( $query_params ), $path_parts['path'] );

			if ( is_array( $path ) ) {
				$preload_paths[ $k ][0] = $sorted_path;
			} else {
				$preload_paths[ $k ] = $sorted_path;
			}
		}

		return $preload_paths;
	}

	/**
	 * Enqueue scripts for the block editor plugin.
	 *
	 * @since 2.6
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts() {
		$screen = get_current_screen();
		if ( empty( $screen ) ) {
			return;
		}

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		$this->enqueue_style_for_specific_screen( $screen, $suffix );

		// Enqueue scripts for widget screen
		if ( $this->is_widget_screen( $screen ) ) {
			$script_filename = '/js/build/widget-editor-plugin' . $suffix . '.js';
			wp_enqueue_script(
				'pll_widget-editor-plugin',
				plugins_url( $script_filename, POLYLANG_BASENAME ),
				array(
					'wp-api-fetch',
					'wp-data',
					'lodash',
				),
				POLYLANG_VERSION,
				true
			);

			// Translated strings used in JS code
			wp_set_script_translations( 'pll_widget-editor-plugin', 'polylang-pro' );

			return;
		}

		// Enqueue scripts for post screen and in block editor context
		if ( $this->is_translatable_post_screen( $screen ) && $this->is_block_editor( $screen ) ) {
			$script_filename = '/js/build/block-editor-plugin' . $suffix . '.js';
			$script_handle   = 'pll_block-editor-plugin';
			wp_register_script(
				$script_handle,
				plugins_url( $script_filename, POLYLANG_ROOT_FILE ),
				array(
					'wp-api-fetch',
					'wp-data',
					'wp-sanitize',
					'lodash',
				),
				POLYLANG_VERSION,
				true
			);

			// Set default language according to the context if no language is defined yet.
			$editor_lang = $this->get_editor_language();
			if ( ! empty( $editor_lang ) ) {
				$editor_lang                    = get_object_vars( $editor_lang );
				$editor_lang['is_default_lang'] = $this->model->options['default_lang'] === $editor_lang['slug'];

			}
			$pll_settings = 'let pll_block_editor_plugin_settings = ' . wp_json_encode(
				array(
					'lang' => $editor_lang,
				)
			);
			wp_add_inline_script( $script_handle, $pll_settings, 'before' );
			wp_enqueue_script( $script_handle );

			$script_filename = '/js/build/sidebar' . $suffix . '.js';
			wp_enqueue_script(
				'pll_sidebar',
				plugins_url( $script_filename, POLYLANG_ROOT_FILE ),
				array(
					'wp-api-fetch',
					'wp-data',
					'wp-i18n',
					'lodash',
				),
				POLYLANG_VERSION,
				true
			);

			// Translated strings used in JS code
			wp_set_script_translations( 'pll_sidebar', 'polylang-pro' );
		}

		if ( $this->is_navigation_screen( $screen ) ) {
			$navigation_script_handle = 'pll_navigation-editor-plugin';
			wp_register_script(
				$navigation_script_handle,
				plugins_url( '/js/build/navigation-editor-plugin' . $suffix . '.js', POLYLANG_ROOT_FILE ),
				array(
					'wp-api-fetch',
					'wp-data',
					'wp-sanitize',
					'lodash',
				),
				POLYLANG_VERSION,
				true
			);
			$navigation_default_language = 'let pll_block_editor_plugin_settings = ' . wp_json_encode(
				array(
					'lang' => $this->model->get_language( $this->options['default_lang'] ),
				)
			);
			wp_add_inline_script( $navigation_script_handle, $navigation_default_language, 'before' );
			wp_enqueue_script( $navigation_script_handle );
		}
	}

	/**
	 * Enqueue style for a specific screen.
	 *
	 * @since 3.1
	 *
	 * @param  WP_Screen $screen The current screen.
	 * @param  string    $suffix The file suffix.
	 * @return void
	 */
	private function enqueue_style_for_specific_screen( $screen, $suffix ) {
		// Enqueue specific styles for block and widget editor UI
		if ( $this->is_widget_screen( $screen ) || $this->is_widget_customizer_screen( $screen ) ||
			( $this->is_translatable_post_screen( $screen ) && $this->is_block_editor( $screen ) ) ) {
			wp_enqueue_style(
				'polylang-block-widget-editor-css',
				plugins_url( '/css/build/style' . $suffix . '.css', POLYLANG_ROOT_FILE ),
				array( 'wp-components' ),
				POLYLANG_VERSION
			);
		}
	}

	/**
	 * Checks if we're in the context of post or site editor screen.
	 *
	 * @since 3.1
	 *
	 * @param  WP_Screen $screen The current screen.
	 * @return bool              True if post screen, false otherwise.
	 */
	private function is_translatable_post_screen( $screen ) {
		return ( 'post' === $screen->base && $this->model->is_translated_post_type( $screen->post_type ) ) ||
				( 'site-editor' === $screen->base && $this->model->is_translated_post_type( 'wp_template_part' ) ) ||
				( 'appearance_page_gutenberg-edit-site' === $screen->base && $this->model->is_translated_post_type( 'wp_template_part' ) );
	}

	/**
	 * Check if we're in the context of a widget screen.
	 *
	 * @since 3.1
	 *
	 * @param  WP_Screen $screen The current screen.
	 * @return bool              True if widget screen, false otherwise.
	 */
	private function is_widget_screen( $screen ) {
		return 'widgets' === $screen->base && function_exists( 'wp_use_widgets_block_editor' ) && wp_use_widgets_block_editor();
	}

	/**
	 * Check if we're in the context of a block editor.
	 *
	 * @since 3.1
	 *
	 * @param  WP_Screen $screen The current screen.
	 * @return bool              True if block editor, false otherwise.
	 */
	private function is_block_editor( $screen ) {
		return method_exists( $screen, 'is_block_editor' ) && $screen->is_block_editor();
	}

	/**
	 * Check if we're in the context of a Navigation Screen
	 *
	 * @since 3.1
	 *
	 * @param WP_Screen $screen The current screen.
	 * @return bool True if Navigation Screen, false otherwise.
	 */
	private function is_navigation_screen( $screen ) {
		return 'gutenberg_page_gutenberg-navigation' === $screen->base;
	}

	/**
	 * Check if we're in the context of a widget customizer screen.
	 *
	 * @since 3.2
	 *
	 * @param  WP_Screen $screen The current screen.
	 * @return bool              True if widget customizer screen, false otherwise.
	 */
	private function is_widget_customizer_screen( $screen ) {
		return 'customize' === $screen->base;
	}

	/**
	 * Returns the language to use in the editor.
	 *
	 * @since 3.2
	 *
	 * @return PLL_Language|null
	 */
	private function get_editor_language() {
		global $post;

		if ( ! empty( $this->curlang ) && PLL_FSE_Tools::is_site_editor() ) {
			return $this->curlang;
		}

		if ( ! empty( $post ) && $this->model->is_translated_post_type( $post->post_type ) ) {
			$this->posts->set_default_language( $post->ID );
			$post_lang = $this->model->post->get_language( $post->ID );
			return ! empty( $post_lang ) ? $post_lang : null;
		}

		return null;
	}

	/**
	 * Method that allow legacy widgets in widget block editor previously removed by WP and hide legacy Polylang widget.
	 *
	 * @since 3.2
	 *
	 * @param array $widget_ids An array of hidden widget ids.
	 * @return array
	 */
	public function filter_legacy_widgets( $widget_ids ) {
		$widgets_to_show = array( 'custom_html' );
		$widget_ids = array_diff( $widget_ids, $widgets_to_show );

		$widgets_to_hide = array( 'polylang' );
		$widget_ids = array_merge( $widget_ids, $widgets_to_hide );

		return $widget_ids;
	}

}
