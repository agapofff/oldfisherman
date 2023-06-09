<?php
/**
 * The template for displaying the waitlist elements on an archive page (e.g. shop)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/waitlist-archive.php.
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
$user_email  = $user ? $user->user_email : '';
// Don't display anything on the archive page if users are required to register (unnecessary clutter)
if ( 'yes' == get_option( 'woocommerce_waitlist_registration_needed' ) && ! $user_email ) {
	return;
}
$button_text = wcwl_get_button_text( $context );
?>
<div class="wcwl_frontend_wrap">
	<?php if ( ! $on_waitlist ) { ?>
		<div class="wcwl_toggle">
			<button type="button" class="single_add_to_cart_button btn btn-outline-primary rounded-0 mx-auto px-3"><?php echo $button_text; ?></button>
			<div class="spinner"></div>
		</div>
		<?php echo wcwl_get_waitlist_fields( $product_id, $context, $notice, $lang ); ?>
	<?php } else { ?>
		<div class="wcwl_notice woocommerce-message alert alert-info px-2 py-2 position-absolute bottom-0 end-0">
			<div aria-live="polite">
				<p class="m-0"><?php echo $notice; ?></p>
			</div>
		</div>
		<div class="wcwl_email_elements">
			<label for="wcwl_email_<?php echo $product_id; ?>" class="wcwl_email_label wcwl_visually_hidden"><?php echo $email_address_label_text; ?></label>
			<input type="email" value="<?php echo $user_email; ?>" id="wcwl_email_<?php echo $product_id; ?>" name="wcwl_email" class="wcwl_email" placeholder="<?php echo $email_address_placeholder_text; ?>" <?php if ( $user_email ) { echo 'disabled'; } ?>/>
		</div>
		<a class="wcwl_control" rel="nofollow" href="<?php echo $url; ?>" data-nonce="<?php echo wp_create_nonce( 'wcwl-ajax-process-user-request-nonce' ); ?>" data-product-id="<?php echo $product_id; ?>" data-context="<?php echo $context; ?>" data-wpml-lang="<?php echo $lang; ?>">
			<button type="button" class="woocommerce_waitlist single_add_to_cart_button btn btn-outline-primary mx-auto px-2 rounded-0"><?php echo $button_text; ?></button>
			<div aria-live="polite" class="wcwl_visually_hidden d-none"></div>
			<div class="spinner-border text-primary" role="status" style="display:none">
				<span class="visually-hidden">Загрузка...</span>
			</div>

		</a>
	<?php } ?>
</div><!-- wcwl_frontend_wrap -->