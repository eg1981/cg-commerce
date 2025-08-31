<?php
/**
 * Cart totals
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-totals.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.3.6
 */

defined( 'ABSPATH' ) || exit;

?>
<div class="cart_totals <?php echo ( WC()->customer->has_calculated_shipping() ) ? 'calculated_shipping' : ''; ?>">

    <div class="cart_totals_inner">

	<?php do_action( 'woocommerce_before_cart_totals' ); ?>

	<h2><?php esc_html_e( 'סיכום הזמנה', 'woocommerce' ); ?></h2>

	<ul cellspacing="0" class="shop_table shop_table_responsive">

        <?php if ( wc_coupons_enabled() ): ?>
        <li class="cart-coupon">
            <td colspan="2">
                <form class="eg-cart-coupon-in-totals" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
                    <label for="eg_coupon_code"><?php esc_html_e('קוד קופון','woocommerce'); ?></label>
                    <div class="eg-coupon-row">
                        <input type="text" name="coupon_code" class="input-text" id="eg_coupon_code"
                            placeholder="<?php esc_attr_e('הזן קופון','woocommerce'); ?>" />
                        <button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e('Apply coupon','woocommerce'); ?>">
                            <?php esc_html_e('הפעל','woocommerce'); ?>
                        </button>
                    </div>
                </form>
            </td>
        </li>    
        <?php endif; ?>
		<li class="cart-subtotal">
			<label><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></label>
			<span data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>"><?php wc_cart_totals_subtotal_html(); ?></span>
		</li>

		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<li class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
				<label><?php wc_cart_totals_coupon_label( $coupon ); ?></label>
				<span data-title="<?php echo esc_attr( wc_cart_totals_coupon_label( $coupon, false ) ); ?>"><?php wc_cart_totals_coupon_html( $coupon ); ?></span>
			</li>
		<?php endforeach; ?>

		<li class="shipping">
			<label><?php esc_html_e( 'Shipping', 'woocommerce' ); ?></label>
			<span data-title="<?php esc_attr_e( 'Shipping', 'woocommerce' ); ?>"><?php echo ( WC()->cart && WC()->cart->show_shipping() ) ? wc_price( (float) (WC()->cart->get_totals()['shipping_total'] ?? 0) + (float) (WC()->cart->get_totals()['shipping_tax'] ?? 0) ) : ''; ?></span>
		</li>

		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<li class="fee">
				<label><?php echo esc_html( $fee->name ); ?></label>
				<span data-title="<?php echo esc_attr( $fee->name ); ?>"><?php wc_cart_totals_fee_html( $fee ); ?></span>
			</li>
		<?php endforeach; ?>

		<?php
		if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) {
			$taxable_address = WC()->customer->get_taxable_address();
			$estimated_text  = '';

			if ( WC()->customer->is_customer_outside_base() && ! WC()->customer->has_calculated_shipping() ) {
				/* translators: %s location. */
				$estimated_text = sprintf( ' <small>' . esc_html__( '(estimated for %s)', 'woocommerce' ) . '</small>', WC()->countries->estimated_for_prefix( $taxable_address[0] ) . WC()->countries->countries[ $taxable_address[0] ] );
			}

			if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) {
				foreach ( WC()->cart->get_tax_totals() as $code => $tax ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					?>
					<tr class="tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
						<th><?php echo esc_html( $tax->label ) . $estimated_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
						<td data-title="<?php echo esc_attr( $tax->label ); ?>"><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
					</tr>
					<?php
				}
			} else {
				?>
				<li class="tax-total">
					<label><?php echo esc_html( WC()->countries->tax_or_vat() ) . $estimated_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></label>
					<span data-title="<?php echo esc_attr( WC()->countries->tax_or_vat() ); ?>"><?php wc_cart_totals_taxes_total_html(); ?></span>
				</li>
				<?php
			}
		}
		?>

		<?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>

		<li class="order-total">
			<label><?php esc_html_e( 'Total', 'woocommerce' ); ?></label>
			<span data-title="<?php esc_attr_e( 'Total', 'woocommerce' ); ?>"><?php wc_cart_totals_order_total_html(); ?></span>
		</li>

		<?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>

	</ul>

    </div>

	<div class="wc-proceed-to-checkout">
		<?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
	</div>

	<?php do_action( 'woocommerce_after_cart_totals' ); ?>

</div>
