<?php

//slick
wp_enqueue_script( 'slick', get_stylesheet_directory_uri() .'/assets/slick/slick.min.js', array( 'jquery' ), time(), true );
wp_enqueue_style( 'slick-style', 	get_stylesheet_directory_uri() .'/assets/slick/slick.css',    array(), time() );
wp_enqueue_style( 'slick-stheme', 	get_stylesheet_directory_uri() .'/assets/slick/slick-theme.css',    array(), time() );

//theme-script
wp_enqueue_script( 'theme-script', get_stylesheet_directory_uri() .'/assets/js/theme.js', array( 'jquery' ), time(), true );

// יצירת דף אפשרויות ראשי
add_action('acf/init', function () {
    if ( function_exists('acf_add_options_page') ) {
        acf_add_options_page([
            'page_title'  => 'הגדרות האתר',
            'menu_title'  => 'הגדרות האתר',
            'menu_slug'   => 'theme-settings',
            'capability'  => 'manage_options',
            'position'    => 61,               // אחרי "Appearance"
            'icon_url'    => 'dashicons-admin-generic',
            'redirect'    => false,            // נשארים בדף הראשי
            'update_button' => __('שמירה', 'textdomain'),
            'updated_message' => __('ההגדרות נשמרו.', 'textdomain'),
        ]);
    }
});

add_action('storefront_before_header','top_header');
function top_header(){
    if ( have_rows('top_header', 'option') ) :
    ?>
        <div class="top-header">
            <div class="top-header-container">
                <div class="stop-header-m">
                    <?php while ( have_rows('top_header', 'option') ) : the_row();
                        $top_header_m = get_sub_field('top_header_m');
                    ?>
                        <div class="item"><?php echo $top_header_m; ?></div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    <?php
    endif;
}

//header icons area & search
add_action( 'after_setup_theme', function() {
    remove_action( 'storefront_header', 'storefront_header_cart', 60 );
    remove_action( 'storefront_header', 'storefront_product_search', 40 );
    add_action( 'storefront_header', 'storefront_product_search_fibo', 23 );
    add_action( 'storefront_header', 'storefront_header_icons_wrap', 24 );
    add_action( 'storefront_header', 'storefront_header_cart', 25 );
    add_action( 'storefront_header', 'storefront_header_icons', 25 );
    add_action( 'storefront_header', 'storefront_header_icons_wrap_end', 27 );

} );

function storefront_header_icons_wrap(){
    echo '<div class="header-icons">';
}

function storefront_header_icons_wrap_end(){
    echo '</div>';
}

function storefront_product_search_fibo(){
    echo do_shortcode('[fibosearch]');
}

function storefront_header_icons(){
    ?>
        <div class="messages-icon">
            <span class="message-icon"><span class="message-count"></span></span>
        </div>
        <div class="wish-icon">
            <?php echo do_shortcode('[ti_wishlist_products_counter]'); ?>
        </div>    
        <div class="login-icon">
            <a href="<?php echo wc_get_page_permalink( 'myaccount' ); ?>"><span class="icon"></span></a>
            <?php 
                if ( is_user_logged_in() ) {
                    $user = wp_get_current_user();
                    echo '<p><a href="'.wc_get_page_permalink( 'myaccount' ).'">היי, ' . esc_html( $user->first_name ) . '</a></p>';
                    echo '<div class="user-drop"><div class="user-drop-inner">';
                        echo '<a href="'.wc_get_page_permalink( 'myaccount' ).'" class="to-account">איזור אישי</a>';
                        echo '<a href="'.esc_url( wp_logout_url( home_url('/') ) ).'" class="to-logout">יציאה</a>';
                    echo '</div></div>';
                } else {
                    echo '<p><a href="'.wc_get_page_permalink( 'myaccount' ).'">שלום אורח, התחבר.</a></p>';
                 }
            ?>
        </div>            
    <?php
}

//product category
remove_action( 'woocommerce_shop_loop_item_title','woocommerce_template_loop_product_title',10 );

// Add custom product title with <div>
add_action( 'woocommerce_before_shop_loop_item', function() {
    echo '<div class="woocommerce-loop-product__title">' . get_the_title() . '</div>';
}, 10 );

add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_shop_loop_sku_desc', 10 );
function woocommerce_shop_loop_sku_desc(){
    global $product;
    echo '<div class="item-sku">מק"ט: '.$product->get_sku().'</div>';
    echo '<div class="item-desc">'.$product->get_short_description().'</div>';
}

//add_action( 'woocommerce_before_shop_loop_item_title', 'add_to_wish', 11 );
function add_to_wish(){
    echo do_shortcode('[ti_wishlists_addtowishlist loop=yes]');
}

remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );

add_action( 'woocommerce_after_shop_loop_item_title', function() {
    global $product;

    $average = $product->get_average_rating();
    $count   = $product->get_rating_count();

    // מציגים רק אם יש לפחות חוות דעת אחת
        echo '<div class="star-rating-wrap">';
        if ( $count > 0 ) {
                $reviews_link = get_permalink( $product->get_id() ) . '#reviews';
                 echo wc_get_rating_html( $average, $count );
                 echo '<a class="review-count" href="' . esc_url( $reviews_link ) . '">(' . $count . ' ביקורות)</a>';
        }
        echo '</div>';
    
}, 5 );

//Footer
add_action( 'wp', 'bbloomer_remove_storefront_credits' );
 
function bbloomer_remove_storefront_credits() {
   remove_action( 'storefront_footer', 'storefront_credit', 20 );
   add_action( 'storefront_after_footer', 'footer_bottom', 20 );
}

function footer_bottom(){
    $footer_logo = get_field('footer_logo', 'option');
    $footer_bottom_html = get_field('footer_bottom_html', 'option');
    $footer_bottom_copy = get_field('footer_bottom_copy', 'option');
    ?>
        <div class="footer-bottom">
            <div class="col-full">
                <div class="footer-bottom-content">
                    <div class="footer-logo">
                        <img src="<?php echo $footer_logo; ?>" alt="<?php echo get_bloginfo('name'); ?>" />
                    </div>
                    <div class="footer-bottom-content-links">
                        <?php echo $footer_bottom_html; ?>
                    </div>
                </div>
                <div class="footer-copy">
                        <?php echo $footer_bottom_copy; ?>
                </div>
            </div>
        </div>
    <?php
}
