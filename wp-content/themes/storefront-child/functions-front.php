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
                        echo '<a href="'.wc_get_page_permalink( 'myaccount' ).'" class="to_account">איזור אישי</a>';
                        echo '<a href="'.esc_url( wp_logout_url( home_url('/') ) ).'" class="to-logout">יציאה</a>';
                    echo '</div></div>';
                } else {
                    echo '<p><a href="'.wc_get_page_permalink( 'myaccount' ).'">שלום אורח, התחבר</a></p>';
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

//my account navigation
add_action('woocommerce_before_account_navigation', 'navigation_start');
add_action('woocommerce_after_account_navigation', 'navigation_end');
function navigation_start() {
    $user = wp_get_current_user();

    $first_name = !empty($user->first_name) ? mb_substr($user->first_name, 0, 1) : '';
    $last_name  = !empty($user->last_name)  ? mb_substr($user->last_name, 0, 1) : '';

    echo '<div class="user-navigation">';
        echo '<div class="user-initials">';
            echo '<span class="round">'.esc_html($first_name . $last_name).'</span>';
            echo '<span>'.esc_html($user->first_name .' '. $user->last_name).'</span>';
        echo '</div>';
}
function navigation_end(){
    echo '</div>';
}

add_filter( 'woocommerce_account_menu_items', 'custom_remove_my_account_links', 999 );
function custom_remove_my_account_links( $items ) {
    unset( $items['dashboard'] ); // לוח בקרה
    unset( $items['downloads'] ); // הורדות
    return $items;
}

add_filter( 'woocommerce_account_menu_items', 'custom_reorder_my_account_menu' );
function custom_reorder_my_account_menu( $items ) {

    // בנה מערך חדש בסדר שאתה רוצה
    $new_items = array(
        'edit-account'    => __( 'פרטי חשבון', 'woocommerce' ),
        'orders'          => __( 'ההזמנות שלי', 'woocommerce' ),
        'edit-address'    => __( 'הכתובות שלי', 'woocommerce' ), 
        'wishlist'    => __( 'מוצרים שאהבתי', 'woocommerce' ),       
        'customer-logout' => __( 'התנתקות', 'woocommerce' ),
    );

    return $new_items;
}

add_filter( 'woocommerce_account_menu_items', 'custom_add_my_account_link' );
function custom_add_my_account_link( $items ) {
    $new = array();
    foreach ( $items as $key => $label ) {
        $new[$key] = $label;
        if ( 'orders' === $key ) {
            $new['custom-link'] = __( 'עדכונים והתראות', 'textdomain' );
        }
    }

    return $new;
}

//registration
/**
 * Shortcode: [wc_custom_register]
 * יוצר עמוד הרשמה נקי ומסדר את השדות בסדר המבוקש.
 */
add_shortcode('wc_custom_register', function () {
    // אל תייצר פלט ב־Admin/REST (מונע JSON errors בזמן שמירה)
    if ( is_admin() || (defined('REST_REQUEST') && REST_REQUEST) ) return '';
    if ( ! function_exists('wc_get_page_permalink') ) return '';

    // כבר מחובר? לא מפנים כאן (כדי לא לשבור REST), רק מודעה
    if ( is_user_logged_in() ) {
        return '<p class="woocommerce-info">'.esc_html__('כבר מחובר/ת.', 'woocommerce').'</p>';
    }

    // הרשמה חייבת להיות פעילה
    if ( 'yes' !== get_option('woocommerce_enable_myaccount_registration') ) {
        return '<p class="woocommerce-error">'.esc_html__('הרשמה כבויה באתר.', 'woocommerce').'</p>';
    }

    ob_start();
    if ( function_exists('woocommerce_output_all_notices') ) {
        woocommerce_output_all_notices();
    } ?>

    <div class="login-form reg">

    <h2><?php esc_html_e( 'יצירת חשבון חדש', 'woocommerce' ); ?></h2>

    <form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action('woocommerce_register_form_tag'); ?>>

      <?php do_action('woocommerce_register_form_start'); ?>

      <!-- 1) שם פרטי -->
      <p class="form-row form-row-wide">
        <label for="reg_first_name"><?php esc_html_e('שם פרטי','woocommerce'); ?> <span class="required">*</span></label>
        <input type="text" class="input-text" name="first_name" id="reg_first_name"
               value="<?php echo !empty($_POST['first_name']) ? esc_attr($_POST['first_name']) : ''; ?>" required />
      </p>

      <!-- 2) שם משפחה -->
      <p class="form-row form-row-wide">
        <label for="reg_last_name"><?php esc_html_e('שם משפחה','woocommerce'); ?> <span class="required">*</span></label>
        <input type="text" class="input-text" name="last_name" id="reg_last_name"
               value="<?php echo !empty($_POST['last_name']) ? esc_attr($_POST['last_name']) : ''; ?>" required />
      </p>
      <div class="clear"></div>

      <!-- 3) דואר אלקטרוני (שדה ליבה של WC) -->
      <p class="form-row form-row-wide">
        <label for="reg_email"><?php esc_html_e('דואר אלקטרוני','woocommerce'); ?> <span class="required">*</span></label>
        <input type="email" class="input-text" name="email" id="reg_email" autocomplete="email"
               value="<?php echo isset($_POST['email']) ? esc_attr( wp_unslash($_POST['email']) ) : ''; ?>" required />
      </p>

      <!-- 4) סיסמה (לפי הכללים) -->
        <p class="form-row form-row-wide">            
            <label for="reg_password"><?php esc_html_e('סיסמה','woocommerce'); ?> <span class="required">*</span></label>
            <small>(יש לכלול לפחות 8 תווים ללא רווחים, מספרים ואותיות באנגלית, לפחות אות אחת גדולה וסימן מיוחד.)</small>
            <span class="password-input">
                <input type="password" class="input-text" name="password" id="reg_password" autocomplete="new-password" required />
                <span class="show-password-input"></span>
            </span>
        </p>

        <!-- אימות סיסמה -->
        <p class="form-row form-row-wide">            
             <label for="reg_password2"><?php esc_html_e('אימות סיסמה','woocommerce'); ?> <span class="required">*</span></label>
             <span class="password-input">
              <input type="password" class="input-text" name="password2" id="reg_password2" required />
              <span class="show-password-input"></span>
           </span> 
        </p>

      <!-- 6) מספר טלפון -->
      <p class="form-row form-row-wide">
        <label for="reg_phone"><?php esc_html_e('מספר טלפון','woocommerce'); ?> <span class="required">*</span></label>
        <input type="tel" class="input-text" name="phone" id="reg_phone"
               value="<?php echo !empty($_POST['phone']) ? esc_attr($_POST['phone']) : ''; ?>" required />
      </p>

      <!-- 7) שם חברה -->
      <p class="form-row form-row-wide">
        <label for="reg_company"><?php esc_html_e('שם חברה','woocommerce'); ?></label>
        <input type="text" class="input-text" name="company" id="reg_company"
               value="<?php echo !empty($_POST['company']) ? esc_attr($_POST['company']) : ''; ?>" />
      </p>

      <!-- 8) ח.פ -->
      <p class="form-row form-row-wide">
        <label for="reg_vatid"><?php esc_html_e('ח.פ','woocommerce'); ?></label>
        <input type="text" class="input-text" name="vatid" id="reg_vatid"
               value="<?php echo !empty($_POST['vatid']) ? esc_attr($_POST['vatid']) : ''; ?>" />
      </p>

        <p class="form-row form-row-wide">
            <span class="required">*</span> שדות חובה
        </p>
      <!-- תנאים -->
      <p class="form-row form-row-wide">
        <label class="woocommerce-form__label woocommerce-form__label-for-checkbox">
          <input type="checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox" name="terms" id="reg_terms" value="1"
                 <?php checked( !empty($_POST['terms']), 1 ); ?> />
              <span>
                <?php 
                printf(
                    __('אני מאשר שקראתי את <a href="%s" target="_blank" style="color:#4279D4;">תקנון האתר</a>', 'woocommerce'),
                    esc_url( get_permalink( get_page_by_path('terms') ) )
                );
                ?>
                </span>
        </label>
      </p>

      <?php do_action('woocommerce_register_form_end'); ?>
      <?php wp_nonce_field('woocommerce-register','woocommerce-register-nonce'); ?>
    
      <p class="form-row">
        <button type="submit" class="woocommerce-Button button" name="register">
            <?php esc_html_e('יצירת חשבון','woocommerce'); ?>
        </button>
        </p>
    </form>

    </div>

    <?php
    return ob_get_clean();
});

/**
 * ולידציה בצד שרת
 */
add_action('woocommerce_register_post', function($username, $email, $errors){
    // שמות
    if ( empty($_POST['first_name']) ) $errors->add('first_name_error', __('יש למלא שם פרטי.', 'woocommerce'));
    if ( empty($_POST['last_name']) )  $errors->add('last_name_error',  __('יש למלא שם משפחה.', 'woocommerce'));

    // טלפון
    if ( empty($_POST['phone']) ) {
        $errors->add('phone_error', __('יש למלא מספר טלפון.', 'woocommerce'));
    }

    // אימות סיסמה
    if ( isset($_POST['password'], $_POST['password2']) && $_POST['password'] !== $_POST['password2'] ) {
        $errors->add('password_mismatch', __('הסיסמאות אינן תואמות.', 'woocommerce'));
    }

    // כללי סיסמה
    if ( ! empty($_POST['password']) ) {
        $pw = (string) $_POST['password'];
        if (
            strlen($pw) < 8 ||
            preg_match('/\s/', $pw) ||
            !preg_match('/[A-Z]/', $pw) ||
            !preg_match('/[a-z]/', $pw) ||
            !preg_match('/[0-9]/', $pw) ||
            !preg_match('/[\W_]/', $pw)
        ) {
            $errors->add('password_strength', __('הסיסמה חייבת להיות לפחות 8 תווים, ללא רווחים, לכלול אות גדולה, אות קטנה, מספר וסימן מיוחד.', 'woocommerce'));
        }
    }

    // תנאים
    if ( empty($_POST['terms']) ) {
        $errors->add('terms_error', __('יש לאשר את תקנון האתר.', 'woocommerce'));
    }

    return $errors;
}, 10, 3);

/**
 * שמירת נתוני משתמש לאחר יצירה
 */
add_action('woocommerce_created_customer', function($customer_id){
    if ( isset($_POST['first_name']) ) update_user_meta($customer_id, 'first_name',       sanitize_text_field($_POST['first_name']));
    if ( isset($_POST['last_name']) )  update_user_meta($customer_id, 'last_name',        sanitize_text_field($_POST['last_name']));
    if ( isset($_POST['phone']) )      update_user_meta($customer_id, 'billing_phone',    sanitize_text_field($_POST['phone']));
    if ( isset($_POST['company']) )    update_user_meta($customer_id, 'billing_company',  sanitize_text_field($_POST['company']));
    if ( isset($_POST['vatid']) )      update_user_meta($customer_id, 'billing_vat_id',   sanitize_text_field($_POST['vatid']));
});

/**
 * טעינת JS+CSS לעמוד ההרשמה/החשבון כאשר לא מחוברים
 */
add_action('wp_enqueue_scripts', function () {
    if ( is_page(302) ) {
        wp_enqueue_script(
            'wc-register-validate',
            get_stylesheet_directory_uri() . '/assets/js/wc-register-validate.js',
            [],
            '1.1',
            true
        );
    }
});

// אחרי הרשמה → להפנות ל"אזור אישי"
add_filter('woocommerce_registration_redirect', function ($redirect) {
    return wc_get_page_permalink('myaccount'); // או: wc_get_account_endpoint_url('dashboard')
});

/**
 * Admin User Profile: show extra fields
 */
add_action('show_user_profile', 'cg_admin_extra_customer_fields');
add_action('edit_user_profile', 'cg_admin_extra_customer_fields');
function cg_admin_extra_customer_fields( $user ) {
    // נתוני מטא
    $phone   = get_user_meta($user->ID, 'billing_phone', true);
    $company = get_user_meta($user->ID, 'billing_company', true);
    $vatid   = get_user_meta($user->ID, 'billing_vat_id', true);
    ?>
    <h2><?php esc_html_e('שדות לקוח נוספים (WooCommerce)', 'woocommerce'); ?></h2>
    <table class="form-table" role="presentation">
        <tr>
            <th><label for="billing_phone"><?php esc_html_e('מספר טלפון', 'woocommerce'); ?></label></th>
            <td>
                <input type="text" name="billing_phone" id="billing_phone" value="<?php echo esc_attr($phone); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="billing_company"><?php esc_html_e('שם חברה', 'woocommerce'); ?></label></th>
            <td>
                <input type="text" name="billing_company" id="billing_company" value="<?php echo esc_attr($company); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="billing_vat_id"><?php esc_html_e('ח.פ', 'woocommerce'); ?></label></th>
            <td>
                <input type="text" name="billing_vat_id" id="billing_vat_id" value="<?php echo esc_attr($vatid); ?>" class="regular-text" />
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Admin User Profile: save extra fields
 */
add_action('personal_options_update', 'cg_admin_save_extra_customer_fields');
add_action('edit_user_profile_update', 'cg_admin_save_extra_customer_fields');
function cg_admin_save_extra_customer_fields( $user_id ) {
    if ( ! current_user_can('edit_user', $user_id) ) return;

    if ( isset($_POST['billing_phone']) )
        update_user_meta($user_id, 'billing_phone', sanitize_text_field($_POST['billing_phone']));

    if ( isset($_POST['billing_company']) )
        update_user_meta($user_id, 'billing_company', sanitize_text_field($_POST['billing_company']));

    if ( isset($_POST['billing_vat_id']) )
        update_user_meta($user_id, 'billing_vat_id', sanitize_text_field($_POST['billing_vat_id']));
}


/**
 * My Account → Account details: show extra fields
 */
add_action('woocommerce_edit_account_form', function () {
    $user_id = get_current_user_id();
    $phone   = get_user_meta($user_id, 'billing_phone', true);
    $company = get_user_meta($user_id, 'billing_company', true);
    $vatid   = get_user_meta($user_id, 'billing_vat_id', true);
    ?>
    <fieldset>
        <legend><?php esc_html_e('פרטי חשבון נוספים', 'woocommerce'); ?></legend>

        <p class="form-row form-row-wide">
            <label for="account_billing_phone"><?php esc_html_e('מספר טלפון', 'woocommerce'); ?> <span class="required">*</span></label>
            <input type="tel" class="input-text" name="account_billing_phone" id="account_billing_phone" value="<?php echo esc_attr($phone); ?>" />
        </p>

        <p class="form-row form-row-wide">
            <label for="account_billing_company"><?php esc_html_e('שם חברה', 'woocommerce'); ?></label>
            <input type="text" class="input-text" name="account_billing_company" id="account_billing_company" value="<?php echo esc_attr($company); ?>" />
        </p>

        <p class="form-row form-row-wide">
            <label for="account_billing_vat_id"><?php esc_html_e('ח.פ', 'woocommerce'); ?></label>
            <input type="text" class="input-text" name="account_billing_vat_id" id="account_billing_vat_id" value="<?php echo esc_attr($vatid); ?>" />
        </p>
    </fieldset>
    <?php
});

/**
 * My Account → Account details: validate & save
 */
add_action('woocommerce_save_account_details_errors', function( $errors, $user ){
    // טלפון חובה (התאם לצורך)
    if ( isset($_POST['account_billing_phone']) ) {
        $phone = trim( (string) $_POST['account_billing_phone'] );
        if ( $phone === '' ) {
            $errors->add('billing_phone_error', __('יש למלא מספר טלפון.', 'woocommerce'));
        } else {
            // בדיקה בסיסית: לפחות 7 ספרות
            if ( preg_match_all('/\d/', $phone) < 7 ) {
                $errors->add('billing_phone_invalid', __('מספר טלפון לא תקין.', 'woocommerce'));
            }
        }
    }
}, 10, 2);

add_action('woocommerce_save_account_details', function( $user_id ){
    if ( isset($_POST['account_billing_phone']) )
        update_user_meta($user_id, 'billing_phone', sanitize_text_field($_POST['account_billing_phone']));

    if ( isset($_POST['account_billing_company']) )
        update_user_meta($user_id, 'billing_company', sanitize_text_field($_POST['account_billing_company']));

    if ( isset($_POST['account_billing_vat_id']) )
        update_user_meta($user_id, 'billing_vat_id', sanitize_text_field($_POST['account_billing_vat_id']));
});
