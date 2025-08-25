<?php
/**
 * Template: My Account Orders with inline toggle (no "view" page)
 * Path: yourtheme/woocommerce/myaccount/orders.php
 */

defined('ABSPATH') || exit;

$current_page = empty($_GET['paged']) ? 1 : absint($_GET['paged']);
$per_page     = (int) apply_filters('woocommerce_my_account_my_orders_per_page', 10);

$query_args = apply_filters(
  'woocommerce_my_account_my_orders_query',
  array(
    'customer' => get_current_user_id(),
    'page'     => $current_page,
    'paginate' => true,
    'limit'    => $per_page,
    'type'     => wc_get_order_types('view-orders'),
    'status'   => array_keys( wc_get_order_statuses() ),
  )
);

$customer_orders = wc_get_orders( $query_args );
$orders          = $customer_orders && ! empty($customer_orders->orders) ? $customer_orders->orders : array();
$max_pages       = $customer_orders && isset($customer_orders->max_num_pages) ? (int) $customer_orders->max_num_pages : 1;

$build_pm_label = function( WC_Order $order ) {
  $title = $order->get_payment_method_title();
  $last4 = '';
  // ניסיונות לשליפת 4 ספרות נפוצות בין שערים שונים
  $candidates = array('_stripe_card_last4','_stripe_last4','_card_last4','_cc_last4','last4','_payment_method_last4');
  foreach ($candidates as $key) {
    $v = $order->get_meta($key);
    if ($v) { $last4 = trim($v); break; }
  }
  return $last4 ? sprintf('%s • מסתיים ב־%s', $title, esc_html($last4)) : $title;
};

$format_addr_html = function( WC_Order $order, $type = 'shipping' ) {
  $formatted = 'shipping' === $type ? $order->get_formatted_shipping_address() : $order->get_formatted_billing_address();
  if ( ! $formatted ) {
    $formatted = $order->get_formatted_billing_address(); // fallback
  }
  return $formatted ? wp_kses_post( $formatted ) : '—';
};

?>
<div class="wc-account-dashboard orders">
<section class="wc-section wc-section--account">
    <header class="wc-section__header">ההזמנות שלי</header>
    <div class="wc-section__body">
        <?php if ( $orders ) : ?>
            <table class="shop_table shop_table_responsive my_account_orders">
            <thead>
                <tr>
                <th class="order-date">תאריך</th>
                <th class="order-number">מספר הזמנה</th>
                <th class="order-status">סטטוס</th>
                <th class="order-total">סכום</th>
                <th class="order-toggle"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $orders as $order ) :
                $order      = wc_get_order( $order ); // מבטיח אובייקט WC_Order
                $order_id   = $order->get_id();
                $status     = wc_get_order_status_name( $order->get_status() );
                $date       = $order->get_date_created() ? wc_format_datetime( $order->get_date_created() ) : '—';
                $total_html = wc_price( $order->get_total(), array( 'currency' => $order->get_currency() ) );
                $phone      = $order->get_billing_phone() ?: $order->get_meta('shipping_phone') ?: '—';
                $pm_label   = $build_pm_label( $order );
                ?>
                <!-- שורת סיכום -->
                <tr class="order-row" data-order-id="<?php echo esc_attr($order_id); ?>">
                    <td class="order-date" data-title="תאריך"><?php echo esc_html( $date ); ?></td>
                    <td class="order-number" data-title="מספר הזמנה"><?php echo esc_html( $order->get_order_number() ); ?></td>
                    <td class="order-status" data-title="סטטוס"><?php echo esc_html( $status ); ?></td>
                    <td class="order-total" data-title="סכום"><?php echo wp_kses_post( $total_html ); ?></td>
                    <td class="order-toggle" data-title="פרטים">
                    <button type="button"
                            class="btn btn--small js-order-toggle"
                            aria-expanded="false"
                            aria-controls="order-details-<?php echo esc_attr($order_id); ?>">
                        פרטים
                    </button>
                    </td>
                </tr>

                <!-- שורת פרטים (טוגל) -->
                <tr id="order-details-<?php echo esc_attr($order_id); ?>" class="order-details" hidden>
                    <td colspan="5">
                    <div class="order-details__inner">

                        <!-- מוצרים בהזמנה -->
                        <div class="order-items">
                        <table class="shop_table shop_table_responsive order_items_table">
                            <tbody>
                            <?php
                            $inc_tax = $order->get_prices_include_tax();
                            foreach ( $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) ) as $item_id => $item ) :
                                $product    = $item->get_product();
                                $qty        = $item->get_quantity();
                                $line_total = (float) $item->get_total() + ( $inc_tax ? (float) $item->get_total_tax() : 0 );
                                $unit       = $qty ? $line_total / $qty : $line_total;
                                if ( $product ) {
                                    $thumb_html = $product->get_image( 'woocommerce_thumbnail', array( 'alt' => $item->get_name() ) );
                                    $prod_link  = $product->is_visible() ? $product->get_permalink() : '';
                                } else {
                                    $thumb_html = wc_placeholder_img( 'woocommerce_thumbnail' );
                                    $prod_link  = '';
                                }
                            ?>
                                <tr>
                                <td class="item-thumb">
                                    <?php if ( $prod_link ) : ?>
                                    <a href="<?php echo esc_url( $prod_link ); ?>" class="product-thumbnail">
                                        <?php echo $thumb_html; ?>
                                    </a>
                                    <?php else : ?>
                                    <span class="product-thumbnail"><?php echo $thumb_html; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="item-name">
                                    <?php echo esc_html( $item->get_name() ); ?>
                                    <?php
                                    // מטא של וריאציות/תוספים
                                    $meta_html = wc_display_item_meta( $item, array( 'echo' => false ) );
                                    if ( $meta_html ) { echo '<div class="item-meta">'.$meta_html.'</div>'; }
                                    ?>
                                </td>
                                <td class="item-unit"><span class="title">מחיר ליחידה:</span> <?php echo wc_price( $unit, array( 'currency' => $order->get_currency() ) ); ?></td>
                                <td class="item-qty"><span class="title">כמות:</span> <?php echo esc_html( $qty ); ?></td>
                                <td class="item-line-total"><span class="title">מחיר כולל:</span> <?php echo wc_price( $line_total, array( 'currency' => $order->get_currency() ) ); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                        </div>

                        <!-- מידע נוסף -->
                        <div class="order-extras">
                        <ul class="wc-list wc-list--kv">                            
                            <li><span class="kv__key">כתובת משלוח:</span> <span class="kv__val addr"><?php echo $format_addr_html($order,'shipping'); ?></span></li>
                            <li><span class="kv__key">טלפון:</span> <span class="kv__val"><?php echo esc_html( $phone ); ?></span></li>
                        </ul>
                        </div>

                        <!-- סיכומי הזמנה (משלוח/מס/סה״כ) -->
                        <div class="order-totals">
                        <ul class="wc-list wc-list--kv">
                            <?php foreach ( $order->get_order_item_totals() as $key => $total ) : ?>
                            <li class="total-row total-row-<?php echo esc_attr( $key ); ?>">
                                <span class="kv__key"><?php echo wp_kses_post( $total['label'] ); ?></span>
                                <span class="kv__val"><?php echo wp_kses_post( $total['value'] ); ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        </div>

                    </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            </table>

            <?php if ( $max_pages > 1 ) : ?>
            <nav class="woocommerce-pagination">
                <ul class="page-numbers">
                <?php
                $base_url = wc_get_endpoint_url( 'orders', '', wc_get_page_permalink('myaccount') );
                for ( $i = 1; $i <= $max_pages; $i++ ) :
                    $url   = esc_url( add_query_arg( 'paged', $i, $base_url ) );
                    $class = $i === (int) $current_page ? 'page-numbers current' : 'page-numbers';
                ?>
                    <li><a class="<?php echo esc_attr($class); ?>" href="<?php echo $url; ?>"><?php echo esc_html($i); ?></a></li>
                <?php endfor; ?>
                </ul>
            </nav>
            <?php endif; ?>

        <?php else : ?>
            <p class="no-orders"><?php esc_html_e( 'לא נמצאו הזמנות.', 'woocommerce' ); ?></p>
            <p><a class="button" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">חזרה לחנות</a></p>
        <?php endif; ?>
    </div>
</section>
</div>

<!-- JS קטן לטוגל -->
<script>
document.addEventListener('click', function(e){
  var btn = e.target.closest('.js-order-toggle');
  if (!btn) return;
  e.preventDefault();

  var id = btn.getAttribute('aria-controls');
  var row = document.getElementById(id);

  // סגור אחרים
  document.querySelectorAll('.order-details').forEach(function(el){
    if (el.id !== id) {
      el.hidden = true;
      var b = document.querySelector('[aria-controls="'+ el.id +'"]');
      if (b) b.setAttribute('aria-expanded','false');
    }
  });

  var isHidden = row.hasAttribute('hidden');
  if (isHidden) {
    row.removeAttribute('hidden');
    btn.setAttribute('aria-expanded','true');
  } else {
    row.setAttribute('hidden','');
    btn.setAttribute('aria-expanded','false');
  }
});
</script>
