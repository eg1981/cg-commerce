<?php
/**
 * Review order table
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/review-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

defined( 'ABSPATH' ) || exit;

$t   = WC()->cart->get_totals();
$pre = max(0, (float) ($t['total'] ?? 0) - (float) ($t['total_tax'] ?? 0));
?>
<ul class="shop_table woocommerce-checkout-review-order-table">
	<ul class="items">
		<?php
		do_action( 'woocommerce_review_order_before_cart_contents' );

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
            $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
            $desc = '';
                if ($_product->is_type('variation')) {
                    $desc = $_product->get_description();
                    if (!$desc) {
                        $parent = wc_get_product($_product->get_parent_id());
                        $desc = $parent ? $parent->get_short_description() : '';
                    }
                } else {
                    $desc = $_product->get_short_description();
               }
                
			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				?>
				<li class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
                    <div class="product-img">
                        <?php echo $thumbnail; ?>
                    </div>
                    <div class="product-details">
                        <div class="product-name">
                            <?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) ) . '&nbsp;'; ?>
                        </div>
                        <div class="item-desc"><?php echo $desc; ?></div>                            
                        <?php echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>                        
                        <div class="product-total">
                            <span><b>מחיר:</b> <?php echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); ?></span>
                            <span><b>כמות:</b> <?php echo $cart_item['quantity']; ?></span>
                            <span><b>סה"כ:</b> <?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>                            
                        </div>
                    </div>
				</li>
				<?php
			}
		}

		do_action( 'woocommerce_review_order_after_cart_contents' );
		?>
	</ul>
    
	<ul class="totals">

		<li class="cart-subtotal">
			<div><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></div>
			<div><?php wc_cart_totals_subtotal_html(); ?></div>
		</li>

		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<li class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
				<div><?php wc_cart_totals_coupon_label( $coupon ); ?></div>
				<div><?php wc_cart_totals_coupon_html( $coupon ); ?></div>
			</li>
		<?php endforeach; ?>

		<li class="shipping">
			<div><?php esc_html_e( 'Shipping', 'woocommerce' ); ?></div>
			<div data-title="<?php esc_attr_e( 'Shipping', 'woocommerce' ); ?>"><?php echo ( WC()->cart && WC()->cart->show_shipping() ) ? wc_price( (float) (WC()->cart->get_totals()['shipping_total'] ?? 0) + (float) (WC()->cart->get_totals()['shipping_tax'] ?? 0) ) : ''; ?></div>
		</li>

		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<li class="fee">
				<div><?php echo esc_html( $fee->name ); ?></div>
				<div><?php wc_cart_totals_fee_html( $fee ); ?></div>
			</li>
		<?php endforeach; ?>

		<?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
			<?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
				<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited ?>
					<li class="tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
						<div><?php echo esc_html( $tax->label ); ?></div>
						<div><?php echo wp_kses_post( $tax->formatted_amount ); ?></div>
					</li>
				<?php endforeach; ?>
			<?php else : ?>
				<li class="tax-total">
					<div><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></div>
					<div><?php wc_cart_totals_taxes_total_html(); ?></div>
				</li>
			<?php endif; ?>
		<?php endif; ?>

		<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

		<li class="eg-pre-vat">
			<div><?php esc_html_e('מחיר לפני מע״מ','woocommerce'); ?>:</div>
			<div><?php echo wc_price($pre); ?></div>
		</li>			

		<li class="order-total">
			<div><?php esc_html_e( 'Total', 'woocommerce' ); ?></div>
			<div><?php wc_cart_totals_order_total_html(); ?></div>
		</li>

		<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>

	</ul>
</ul>
