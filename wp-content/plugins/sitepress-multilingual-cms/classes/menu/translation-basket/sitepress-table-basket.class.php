<?php

use WPML\API\Sanitize;
use WPML\TM\Menu\TranslationBasket\Strings;
use function WPML\Container\make;

require_once WPML_TM_PATH . '/menu/sitepress-table.class.php';

class SitePress_Table_Basket extends SitePress_Table {

	public static function enqueue_js() {
		/** @var WP_Locale $wp_locale */
		global $wp_locale;

		wp_enqueue_script(
			'wpml-tm-translation-basket-and-options',
			WPML_TM_URL . '/res/js/translation-basket-and-options.js',
			array( 'wpml-tm-scripts', 'jquery-ui-progressbar', 'jquery-ui-datepicker', 'wpml-tooltip', 'wpml-tm-progressbar' ),
			ICL_SITEPRESS_VERSION
		);

		wp_localize_script(
			'wpml-tm-translation-basket-and-options',
			'wpml_tm_translation_basket_and_options',
			array(
				'day_names'    => array_values( $wp_locale->weekday ),
				'day_initials' => array_values( $wp_locale->weekday_initial ),
				'month_names'  => array_values( $wp_locale->month ),
				'nonce'        => wp_create_nonce( 'basket_extra_fields_refresh' ),
			)
		);

		wp_enqueue_style(
			'wpml-tm-jquery-ui-datepicker',
			WPML_TM_URL . '/res/css/jquery-ui/datepicker.css',
			array( 'wpml-tooltip' ),
			ICL_SITEPRESS_VERSION
		);

		/** @var Strings $strings */
		$strings = make( Strings::class );

		$tm_basket_data = array(
			'nonce'       => array(),
			'strings'     => $strings->getAll(),
			'tmi_message' => $strings->duplicatePostTranslationWarning(),
		);

		$tm_basket_data = apply_filters( 'translation_basket_and_options_js_data', $tm_basket_data );
		wp_localize_script(
			'wpml-tm-translation-basket-and-options',
			'tm_basket_data',
			$tm_basket_data
		);

		wp_enqueue_script( 'wpml-tm-translation-basket-and-options' );
	}

	function prepare_items() {
		$this->action_callback();

		$this->get_data();

		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		if ( $this->items ) {
			usort( $this->items, array( &$this, 'usort_reorder' ) );
		}
	}

	function get_columns() {
		$columns = array(
			'title'     => __( 'Title', 'wpml-translation-management' ),
			'type'      => __( 'Type', 'wpml-translation-management' ),
			'status'    => __( 'Status', 'wpml-translation-management' ),
			'languages' => __( 'Languages', 'wpml-translation-management' ),
			'words'     => __( 'Words to translate', 'wpml-translation-management' ),
			'delete'    => '',
		);

		return $columns;
	}

	/**
	 * @param object $item
	 * @param string $column_name
	 *
	 * @return mixed|string
	 */
	function column_default( $item, $column_name ) {
		/**
		 * WP base class is expecting an object, but we are using an array in our implementation.
		 * Casting $item into an array prevents IDE warnings.
		 */
		$item = (array) $item;

		switch ( $column_name ) {
			case 'title':
			case 'notes':
				return $item[ $column_name ];
			case 'type':
				return $this->get_post_type_label( $item[ $column_name ], $item );
			case 'status':
				return $this->get_post_status_label( $item[ $column_name ] );
			case 'words':
				return $item[ $column_name ];
			case 'languages':
				$target_languages_data = $item['target_languages'];
				$source_language_data  = $item['source_language'];
				$target_languages      = explode( ',', $target_languages_data );
				$languages             = sprintf(
					__( '%1$s to %2$s', 'wpml-translation-management' ),
					$source_language_data,
					$target_languages_data
				);
				if ( count( $target_languages ) > 1 ) {
					$last_target_language   = $target_languages[ count( $target_languages ) - 1 ];
					$first_target_languages = array_slice( $target_languages, 0, count( $target_languages ) - 1 );
					$languages              = sprintf(
						__( '%1$s to %2$s and %3$s', 'wpml-translation-management' ),
						$source_language_data,
						implode( ',', $first_target_languages ),
						$last_target_language
					);
				}

				return $languages;
			default:
				return print_r( $item, true ); // Show the whole array for troubleshooting purposes
		}
	}

	function column_title( $item ) {
		return esc_html( $item['title'] );
	}

	/**
	 * @param array $item
	 *
	 * @return string
	 */
	function column_delete( $item ) {
		$qs              = $_GET;
		$qs['page']      = $_REQUEST['page'];
		$qs['action']    = 'delete';
		$qs['id']        = $item['ID'];
		$qs['item_type'] = $item['item_type'];

		$new_qs = esc_attr( http_build_query( $qs ) );

		return sprintf(
			'<a href="?%s" title="%s" class="otgs-ico-cancel wpml-tm-delete"></a>',
			$new_qs,
			__( 'Remove from Translation Basket', 'wpml-translation-management' )
		);
	}

	function no_items() {
		_e( 'The basket is empty', 'wpml-translation-management' );
	}

	function get_sortable_columns() {
		$sortable_columns = array(
			'title'     => array( 'title', true ),
			'type'      => array( 'type', false ),
			'status'    => array( 'status', false ),
			'languages' => array( 'languages', false ),
			'words'     => array( 'words', false ),
		);

		return $sortable_columns;
	}

	/**
	 * @param $post_id
	 * @param $data
	 * @param $item_type
	 */
	private function build_basket_item( $post_id, $data, $item_type ) {
		$this->items[ $item_type . '|' . $post_id ]['ID']               = $post_id;
		$this->items[ $item_type . '|' . $post_id ]['title']            = $data['post_title'];
		$this->items[ $item_type . '|' . $post_id ]['notes']            = isset( $data['post_notes'] ) ? $data['post_notes'] : '';
		$this->items[ $item_type . '|' . $post_id ]['type']             = $data['post_type'];
		$this->items[ $item_type . '|' . $post_id ]['status']           = isset( $data['post_status'] ) ? $data['post_status'] : '';
		$this->items[ $item_type . '|' . $post_id ]['source_language']  = $data['from_lang_string'];
		$this->items[ $item_type . '|' . $post_id ]['target_languages'] = $data['to_langs_string'];
		$this->items[ $item_type . '|' . $post_id ]['item_type']        = $item_type;
		$this->items[ $item_type . '|' . $post_id ]['words']            = $this->get_words_count( $post_id, $item_type, count( $data['to_langs'] ) );
		$this->items[ $item_type . '|' . $post_id ]['auto_added']       = isset( $data['auto_added'] ) && $data['auto_added'];
	}

	/**
	 * @param $element_id
	 * @param $element_type
	 * @param $languages_count
	 */
	private function get_words_count( $element_id, $element_type, $languages_count ) {
		$records_factory        = new WPML_TM_Word_Count_Records_Factory();
		$single_process_factory = new WPML_TM_Word_Count_Single_Process_Factory();
		$st_package_factory     = class_exists( 'WPML_ST_Package_Factory' ) ? new WPML_ST_Package_Factory() : null;
		$element_provider       = new WPML_TM_Translatable_Element_Provider( $records_factory->create(), $single_process_factory->create(), $st_package_factory );
		$translatable_element   = $element_provider->get_from_type( $element_type, $element_id );

		$count = null !== $translatable_element ? $translatable_element->get_words_count() : 0;

		return $count * $languages_count;
	}

	/**
	 * @param $cart_items
	 * @param $item_type
	 */
	private function build_basket_items( $cart_items, $item_type ) {
		if ( $cart_items ) {
			foreach ( $cart_items as $post_id => $data ) {
				$this->build_basket_item( $post_id, $data, $item_type );
			}
		}
	}

	private function usort_reorder( $a, $b ) {
		$sortable_columns_keys = array_keys( $this->get_sortable_columns() );
		$first_column_key      = $sortable_columns_keys[0];
		// If no sort, default to first column
		$orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : $first_column_key;
		// If no order, default to asc
		$order = ( ! empty( $_GET['order'] ) ) ? $_GET['order'] : 'asc';
		// Determine sort order
		$result = strcmp( $a[ $orderby ], $b[ $orderby ] );

		// Send final sort direction to usort
		return ( $order === 'asc' ) ? $result : - $result;
	}

	/**
	 * @param $post_status
	 *
	 * @return string
	 */
	private function get_post_status_label( $post_status ) {
		static $post_status_object;
		// Get and store the post status "as verb", if available
		if ( ! isset( $post_status_object[ $post_status ] ) ) {
			$post_status_object[ $post_status ] = get_post_status_object( $post_status );
		}
		$post_status_label = ucfirst( $post_status );
		if ( isset( $post_status_object[ $post_status ] ) ) {
			$post_status_object_item = $post_status_object[ $post_status ];
			if ( isset( $post_status_object_item->label ) && $post_status_object_item->label ) {
				$post_status_label = $post_status_object_item->label;
			}
		}

		return $post_status_label;
	}

	/**
	 * @param string $post_type
	 * @param array  $item
	 *
	 * @return string
	 */
	private function get_post_type_label( $post_type, array $item ) {
		static $post_type_object;

		if ( ! isset( $post_type_object[ $post_type ] ) ) {
			$post_type_object[ $post_type ] = get_post_type_object( $post_type );
		}
		$post_type_label = ucfirst( $post_type );

		if ( isset( $post_type_object[ $post_type ] ) ) {
			$post_type_object_item = $post_type_object[ $post_type ];
			if ( isset( $post_type_object_item->labels->singular_name ) && $post_type_object_item->labels->singular_name ) {
				$post_type_label = $post_type_object_item->labels->singular_name;
			}
		}

		if ( isset( $item['auto_added'] ) && $item['auto_added'] ) {
			if ( 'wp_block' === $post_type ) {
				$post_type_label = __( 'Reusable Block', 'wpml-translation-management' );
			}

			$tooltip = '<a class="js-otgs-popover-tooltip otgs-ico-help"
							data-tippy-zindex="999999" tabindex="0"
							title="' . esc_attr__( "WPML added this item because it's linked to another in the basket. If you wish, you can manually remove it.", 'wpml-translation-management' ) . '"
							</a>';

			$post_type_label .= '<br><small>'
								. sprintf( esc_html__( 'automatically added %s', 'wpml-translation-management' ), $tooltip ) .
								'</small>';
		}

		return $post_type_label;
	}

	private function action_callback() {
		if ( isset( $_GET['clear_basket'] ) && isset( $_GET['clear_basket_nonce'] ) && $_GET['clear_basket'] == 1 ) {
			if ( wp_verify_nonce( $_GET['clear_basket_nonce'], 'clear_basket' ) ) {
				TranslationProxy_Basket::delete_all_items_from_basket();
			}
		}
		if ( $this->current_action() == 'delete_selected' ) {
			// Delete basket items from post action
			TranslationProxy_Basket::delete_items_from_basket( $_POST['icl_translation_basket_delete'] );
		} elseif ( $this->current_action() == 'delete' && isset( $_GET['id'] ) && isset( $_GET['item_type'] ) ) {
			// Delete basket item from post action
			$delete_basket_item_id   = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
			$delete_basket_item_type = Sanitize::stringProp( 'item_type', $_GET );
			if ( $delete_basket_item_id && $delete_basket_item_type ) {
				TranslationProxy_Basket::delete_item_from_basket(
					$delete_basket_item_id,
					$delete_basket_item_type,
					true
				);
			}
		}
	}

	private function get_data() {
		global $iclTranslationManagement;

		$translation_jobs_basket = TranslationProxy_Basket::get_basket();

		$basket_items_types = TranslationProxy_Basket::get_basket_items_types();
		foreach ( $basket_items_types as $item_type_name => $item_type ) {
			$translation_jobs_cart[ $item_type_name ] = false;
			if ( $item_type == 'core' ) {
				if ( ! empty( $translation_jobs_basket[ $item_type_name ] ) ) {
					$basket_type_items = $translation_jobs_basket[ $item_type_name ];
					if ( $item_type_name == 'string' ) {
						$translation_jobs_cart[ $item_type_name ] = $iclTranslationManagement->get_translation_jobs_basket_strings( $basket_type_items );
					} else {
						$translation_jobs_cart[ $item_type_name ] = $iclTranslationManagement->get_translation_jobs_basket_posts( $basket_type_items );
					}
					$this->build_basket_items( $translation_jobs_cart[ $item_type_name ], $item_type_name );
				}
			} elseif ( $item_type == 'custom' ) {
				$translation_jobs_cart_externals = apply_filters(
					'wpml_tm_translation_jobs_basket',
					array(),
					$translation_jobs_basket,
					$item_type_name
				);
				$this->build_basket_items( $translation_jobs_cart_externals, $item_type_name );
			}
		}
	}

	function display_tablenav( $which ) {
		return;
	}

	function display() {
		parent::display();
		if ( TranslationProxy_Basket::get_basket_items_count() ) {
			$clear_basket_nonce = wp_create_nonce( 'clear_basket' );
			?>
			<a href="admin.php?page=<?php echo WPML_TM_FOLDER; ?>/menu/main.php&sm=basket&clear_basket=1&clear_basket_nonce=<?php echo $clear_basket_nonce; ?>"
			   class="button-secondary wpml-tm-clear-basket-button" name="clear-basket">
				<i class="otgs-ico-cancel"></i>
				<?php _e( 'Clear Basket', 'wpml-translation-management' ); ?>
			</a>
			<?php
		}

		$this->display_total_word_count_info();
	}

	private function display_total_word_count_info() {

		$grand_total_words_count = 0;

		if ( $this->items ) {
			foreach ( $this->items as $item ) {
				$grand_total_words_count += $item['words'];
			}
		}

		$service         = TranslationProxy::get_current_service();
		$tm_ate          = new WPML_TM_ATE();
		$is_ate_enabled  = $tm_ate->is_translation_method_ate_enabled();
		$display_message = '';

		$ate_doc_link = '';
		$ate_name     = '';

		if ( $is_ate_enabled ) {
			$ate_name     = __( 'Advanced Translation Editor', 'wpml-translation-management' );
			$ate_doc_link = 'https://wpml.org/documentation/translating-your-contents/advanced-translation-editor/';
		}

		if ( $service && $is_ate_enabled ) {
			$service_message = __( '%1$s and the %2$s use a translation memory, which can reduce the number of words you need to translate.', 'wpml-translation-management' );
			$display_message = sprintf(
				$service_message,
				'<a class="wpml-external-link" href="' . $service->doc_url . '" target="blank">' . $service->name . '</a>',
				'<a class="wpml-external-link" href="' . $ate_doc_link . '" target="blank">' . $ate_name . '</a>'
			);
		} elseif ( $service ) {
			$service_message = __( '%s uses a translation memory, which can reduce the number of words you need to translate.', 'wpml-translation-management' );
			$display_message = sprintf( $service_message, '<a class="wpml-external-link" href="' . $service->doc_url . '" target="blank">' . $service->name . '</a>' );
		} elseif ( $is_ate_enabled ) {
			$service_message = __( 'The %s uses a translation memory, which can reduce the number of words you need to translate.', 'wpml-translation-management' );
			$display_message = sprintf( $service_message, '<a class="wpml-external-link" href="' . $ate_doc_link . '" target="blank">' . $ate_name . '</a>' );
		}

		if ( $service ) {
			$words_count_url  = 'https://wpml.org/documentation/translating-your-contents/getting-a-word-count-of-your-wordpress-site/?utm_source=plugin&utm_medium=gui&utm_campaign=wpmltm#differences-in-word-count-between-wpml-and-translation-service-providers';
			$words_count_text = '<a class="wpml-external-link" href="' . $words_count_url . '" target="_blank">';

			// translators: "%s" is replaced by the name of a translation service.
			$words_count_text .= sprintf( __( '%s may produce a different word count', 'wpml-translation-management' ), $service->name );
			$words_count_text .= '</a>';

			// translators: "%s" is replaced by the the previous string.
			$words_count_message = sprintf( __( 'The number of words WPML will send to translation (%s):', 'wpml-translation-management' ), $words_count_text );
		} else {
			$words_count_message = __( 'The number of words WPML will send to translation:', 'wpml-translation-management' );
		}

		?>
		<div class="words-count-summary">
			<p class="words-count-summary-info">
				<strong><?php echo $words_count_message; ?></strong>
				<span class="words-count-total"><?php echo $grand_total_words_count; ?></span>
			</p>
			<?php if ( $display_message ) { ?>
				<p class="words-count-summary-ts">
					<?php echo $display_message; ?>
				</p>
			<?php } ?>
		</div>

		<?php
	}

}
