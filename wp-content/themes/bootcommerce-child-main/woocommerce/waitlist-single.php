<?php
/**
 * The template for displaying the waitlist elements on a single product page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/waitlist-single.php.
 *
 * HOWEVER, on occasion WooCommerce Waitlist will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @version 1.9.0
 */
?>
<div class="wcwl_elements bg-light shadow pt-3 pb-3 px-2 position-absolute bottom-0">
	<button type="button" class="wcwl_elements_close btn btn-link position-absolute top-0 end-0 px-1 pt-0">
		<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
			<path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
		</svg>
	</button>
	<?php
	$user_email = $user ? $user->user_email : '';
	if ( 'yes' == get_option( 'woocommerce_waitlist_registration_needed' ) && ! $user_email ) { ?>
		<div class="wcwl_notice woocommerce-info">
			<?php echo $registration_required_text; ?>
		</div>
	<?php } else {
		$join_button_text = wcwl_get_button_text( 'join' ); ?>
		<div class="wcwl_intro">
			<p><?php echo $intro; ?></p>
		</div>
		<div class="wcwl_notice woocommerce-message alert alert-info px-2 py-2 position-absolute bottom-0 end-0">
			<div aria-live="polite">
				<p><?php echo $notice; ?></p>
			</div>
		</div>
		<?php if ( $opt_in && ! $on_waitlist ) { ?>
			<div class="wcwl_optin">
				<input type="checkbox" name="wcwl_optin" id="wcwl_optin">
				<label for="wcwl_optin"><?php echo $opt_in_text; ?></label>
			</div>
		<?php } ?>
		<div class="wcwl_email_elements <?php echo $email_class; ?>">
			<label for="wcwl_email_<?php echo $product_id; ?>" class="wcwl_email_label wcwl_visually_hidden"><?php echo $email_address_label_text; ?></label>
			<input type="email" value="<?php echo $user_email; ?>" id="wcwl_email_<?php echo $product_id; ?>" name="wcwl_email" class="wcwl_email form-control rounded-pill mb-3" placeholder="<?php echo $email_address_placeholder_text; ?>" <?php if ( $user_email ) { echo 'disabled'; } ?>/>
		</div>
		<?php if ( 'join' === $context && ( 'true' === $is_archive || is_shop() || is_product_category() ) ) {
			$context          = __( 'confirm', 'woocommerce-waitlist' );
			$join_button_text = wcwl_get_button_text( 'confirm' );
		} ?>
		<input type="hidden" name="wcwl_join_button_text" value="<?php echo $join_button_text; ?>"/>
		<input type="hidden" name="wcwl_leave_button_text" value="<?php echo wcwl_get_button_text( 'leave' ); ?>"/>
		<a rel="nofollow" class="wcwl_control" href="<?php echo $url; ?>" data-nonce="<?php echo wp_create_nonce( 'wcwl-ajax-process-user-request-nonce' ); ?>" data-product-id="<?php echo $product_id; ?>" data-context="<?php echo $context; ?>" data-wpml-lang="<?php echo $lang; ?>">
			<button type="button" class="woocommerce_waitlist single_add_to_cart_button btn btn-outline-primary mx-auto  rounded-0">
				<?php echo wcwl_get_button_text('confirm'); ?>
			</button>
			<div aria-live="polite" class="wcwl_visually_hidden"></div>
			<div class="spinner-border text-primary" role="status" style="display:none">
				<span class="visually-hidden">Загрузка...</span>
			</div>
		</a>
	<?php } ?>
</div><!-- wcwl_elements -->