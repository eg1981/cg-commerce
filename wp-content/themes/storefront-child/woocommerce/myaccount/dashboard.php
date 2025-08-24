<?php
/**
 * My Account Dashboard
 *
 * Shows the first intro screen on the account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Template: My Account Dashboard (custom sections, RTL-friendly, no CSS)
 * Path: yourtheme/woocommerce/myaccount/dashboard.php
 */

defined('ABSPATH') || exit;

$user_id      = get_current_user_id();
$customer     = new WC_Customer( $user_id );
$account_url  = wc_get_page_permalink( 'myaccount' );

// Endpoints
$edit_account_url       = wc_get_endpoint_url( 'edit-account', '', $account_url );
$payment_methods_url    = wc_get_endpoint_url( 'payment-methods', '', $account_url );
$add_payment_method_url = wc_get_endpoint_url( 'add-payment-method', '', $account_url );
$edit_shipping_url      = wc_get_endpoint_url( 'edit-address', 'shipping', $account_url );
$edit_billing_url       = wc_get_endpoint_url( 'edit-address', 'billing', $account_url );

// Account details
$acc_name    = trim( $customer->get_first_name() . ' ' . $customer->get_last_name() );
$acc_email   = $customer->get_email();
$acc_company = $customer->get_billing_company();
$acc_phone   = $customer->get_billing_phone();

// Shipping
$shipping = [
  'name'      => trim( $customer->get_shipping_first_name() . ' ' . $customer->get_shipping_last_name() ),
  'company'   => $customer->get_shipping_company(),
  'city'      => $customer->get_shipping_city(),
  'address_1' => $customer->get_shipping_address_1(),
  'address_2' => $customer->get_shipping_address_2(),
  'postcode'  => $customer->get_shipping_postcode(),
  'state'     => $customer->get_shipping_state(),
  'country'   => $customer->get_shipping_country(),
  // אם אין טלפון משלוח שמור, נשתמש בבילינג
  'phone'     => $customer->get_meta('shipping_phone') ?: $customer->get_billing_phone(),
];

// Billing
$billing = [
  'name'      => trim( $customer->get_billing_first_name() . ' ' . $customer->get_billing_last_name() ),
  'company'   => $customer->get_billing_company(),
  'city'      => $customer->get_billing_city(),
  'address_1' => $customer->get_billing_address_1(),
  'address_2' => $customer->get_billing_address_2(),
  'postcode'  => $customer->get_billing_postcode(),
  'state'     => $customer->get_billing_state(),
  'country'   => $customer->get_billing_country(),
  'phone'     => $customer->get_billing_phone(),
];

// Helper for fallback dash
$val = function( $v ) { return $v ? esc_html( $v ) : '—'; };

// Optional: Google Maps link from parts
$build_map_q = function( $arr ) {
  $parts = array_filter( [ $arr['address_1'], $arr['address_2'], $arr['city'], $arr['postcode'], $arr['country'] ] );
  return 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( implode(', ', $parts) );
};
?>

<div class="wc-account-dashboard">

  <!-- פרטי חשבון -->
  <section class="wc-section wc-section--account">
    <header class="wc-section__header">>פרטי חשבון</header>

    <div class="wc-section__body">
        <ul class="wc-list wc-list--kv">
            <li><span class="kv__key">שם:</span> <span class="kv__val"><?php echo $val($acc_name); ?></span></li>
            <li><span class="kv__key">חברה:</span> <span class="kv__val"><?php echo $val($acc_company); ?></span></li>
            <li><span class="kv__key">דוא״ל:</span> <span class="kv__val"><?php echo $val($acc_email); ?></span></li>
            <li><span class="kv__key">טלפון:</span> <span class="kv__val"><?php echo $val($acc_phone); ?></span></li>
        </ul>

        <div class="wc-actions">
            <a class="btn" href="<?php echo esc_url( $edit_account_url ); ?>">עדכון פרטי חשבון / סיסמה</a>
        </div>
        <div class="btn-edit">
            <a class="btn btn--small" href="<?php echo esc_url( $edit_account_url ); ?>">עריכה</a>
        </div>
    </div>
  </section>

  <!-- שיטת תשלום (HTML בלבד כרצונך) -->
  <section class="wc-section wc-section--payments">
    <header class="wc-section__header">שיטת תשלום</header>

    <div class="wc-section__body">
      <!-- סימולציה/דוגמה סטטית של כרטיסים; אין לוגיקה כאן -->
      <ul class="payment-list">
        <li class="payment-item">
          <div class="payment-item__meta">
            <strong>Visa</strong> • מסתיים ב־<span dir="ltr">**** 9999</span> • תפוגה 12/27
          </div>
          <div class="payment-item__actions">
            <!-- אין מחיקה דינמית כאן; שולחים לניהול -->
            <a class="btn btn--small btn--ghost" href="<?php echo esc_url( $payment_methods_url ); ?>">הסר</a>
          </div>
        </li>
        <li class="payment-item">
          <div class="payment-item__meta">
            <strong>Mastercard</strong> • מסתיים ב־<span dir="ltr">**** 2222</span> • תפוגה 03/28
          </div>
          <div class="payment-item__actions">
            <a class="btn btn--small btn--ghost" href="<?php echo esc_url( $payment_methods_url ); ?>">הסר</a>
          </div>
        </li>
      </ul>
    </div>
      <div class="btn-edit">
        <a class="btn btn--ghost" href="<?php echo esc_url( $payment_methods_url ); ?>">ניהול שיטות תשלום</a>
        <a class="btn" href="<?php echo esc_url( $add_payment_method_url ); ?>">הוסף אמצעי תשלום</a>
      </div>    
  </section>

  <!-- כתובת למשלוח -->
  <section class="wc-section wc-section--shipping">
    <header class="wc-section__header">כתובת למשלוח</header>

    <div class="wc-section__body">
      <ul class="wc-list wc-list--kv">
        <li><span class="kv__key">שם:</span> <span class="kv__val"><?php echo $val($shipping['name']); ?></span></li>
        <li><span class="kv__key">חברה:</span> <span class="kv__val"><?php echo $val($shipping['company']); ?></span></li>
        <li><span class="kv__key">עיר:</span> <span class="kv__val"><?php echo $val($shipping['city']); ?></span></li>
        <li><span class="kv__key">כתובת:</span> <span class="kv__val"><?php echo $val(trim($shipping['address_1'] . ' ' . $shipping['address_2'])); ?></span></li>
        <li><span class="kv__key">מיקוד:</span> <span class="kv__val"><?php echo $val($shipping['postcode']); ?></span></li>
        <li><span class="kv__key">טלפון:</span> <span class="kv__val"><?php echo $val($shipping['phone']); ?></span></li>
      </ul>
      <div class="btn-edit">
        <a class="btn btn--small" href="<?php echo esc_url( $edit_shipping_url ); ?>">עריכה</a>
      </div>      
    </div>
  </section>

  <!-- כתובת לחיוב -->
  <section class="wc-section wc-section--billing">
    <header class="wc-section__header">כתובת לחיוב</header>

    <div class="wc-section__body">
      <ul class="wc-list wc-list--kv">
        <li><span class="kv__key">שם:</span> <span class="kv__val"><?php echo $val($billing['name']); ?></span></li>
        <li><span class="kv__key">חברה:</span> <span class="kv__val"><?php echo $val($billing['company']); ?></span></li>
        <li><span class="kv__key">עיר:</span> <span class="kv__val"><?php echo $val($billing['city']); ?></span></li>
        <li><span class="kv__key">כתובת:</span> <span class="kv__val"><?php echo $val(trim($billing['address_1'] . ' ' . $billing['address_2'])); ?></span></li>
        <li><span class="kv__key">מיקוד:</span> <span class="kv__val"><?php echo $val($billing['postcode']); ?></span></li>
        <li><span class="kv__key">טלפון:</span> <span class="kv__val"><?php echo $val($billing['phone']); ?></span></li>
      </ul>
      <div class="btn-edit">
        <a class="btn btn--small" href="<?php echo esc_url( $edit_billing_url ); ?>">עריכה</a>
      </div>      
    </div>
  </section>

</div>

