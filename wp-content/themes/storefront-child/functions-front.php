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
            <span class="icon"></span>
            <?php 
                if ( is_user_logged_in() ) {
                    $user = wp_get_current_user();
                    echo '<p>היי, ' . esc_html( $user->first_name ) . '</p>';
                } else {
                    echo '<p>שלום אורח, התחבר.</p>';
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

