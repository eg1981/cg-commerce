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

      <!-- 3) דואר אלקטרוני -->
      <p class="form-row form-row-wide">
        <label for="reg_email"><?php esc_html_e('דואר אלקטרוני','woocommerce'); ?> <span class="required">*</span></label>
        <input type="email" class="input-text" name="email" id="reg_email" autocomplete="email"
               value="<?php echo isset($_POST['email']) ? esc_attr( wp_unslash($_POST['email']) ) : ''; ?>" required />
      </p>

      <!-- 4) סיסמה -->
      <p class="form-row form-row-wide">
        <label for="reg_password"><?php esc_html_e('סיסמה','woocommerce'); ?> <span class="required">*</span></label>
        <small>(יש לכלול לפחות 8 תווים ללא רווחים, מספרים ואותיות באנגלית, לפחות אות אחת גדולה וסימן מיוחד.)</small>
        <span class="password-input">
            <input type="password" class="input-text" name="password" id="reg_password" autocomplete="new-password" required />
            <span class="show-password-input"></span>
        </span>
      </p>

      <!-- 5) אימות סיסמה -->
      <p class="form-row form-row-wide">
        <label for="reg_password2"><?php esc_html_e('אימות סיסמה','woocommerce'); ?> <span class="required">*</span></label>
        <span class="password-input">
          <input type="password" class="input-text" name="password2" id="reg_password2" required />
          <span class="show-password-input"></span>
        </span>
      </p>

      <!-- 6) מספר טלפון (שדה ליבה: billing_phone) -->
      <p class="form-row form-row-wide">
        <label for="reg_billing_phone"><?php esc_html_e('מספר טלפון','woocommerce'); ?> <span class="required">*</span></label>
        <input type="tel" class="input-text" name="billing_phone" id="reg_billing_phone"
               value="<?php echo !empty($_POST['billing_phone']) ? esc_attr($_POST['billing_phone']) : ''; ?>" required />
      </p>

      <!-- 7) שם חברה (שדה ליבה: billing_company) -->
      <p class="form-row form-row-wide">
        <label for="reg_billing_company"><?php esc_html_e('שם חברה','woocommerce'); ?></label>
        <input type="text" class="input-text" name="billing_company" id="reg_billing_company"
               value="<?php echo !empty($_POST['billing_company']) ? esc_attr($_POST['billing_company']) : ''; ?>" />
      </p>

      <!-- 8) ח.פ (מותאם אישית; אפשר להשאיר כ-billing_vat_id) -->
      <p class="form-row form-row-wide">
        <label for="reg_billing_vat_id"><?php esc_html_e('ח.פ','woocommerce'); ?></label>
        <input type="text" class="input-text" name="billing_vat_id" id="reg_billing_vat_id"
               value="<?php echo !empty($_POST['billing_vat_id']) ? esc_attr($_POST['billing_vat_id']) : ''; ?>" />
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

    // טלפון (שדה ליבה)
    if ( empty($_POST['billing_phone']) ) {
        $errors->add('billing_phone_error', __('יש למלא מספר טלפון.', 'woocommerce'));
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
 * משתמש במפתחות המקוריים של Woo: billing_*
 */
add_action('woocommerce_created_customer', function($customer_id){
    if ( isset($_POST['first_name']) )       update_user_meta($customer_id, 'first_name',        sanitize_text_field($_POST['first_name']));
    if ( isset($_POST['last_name']) )        update_user_meta($customer_id, 'last_name',         sanitize_text_field($_POST['last_name']));
    if ( isset($_POST['billing_phone']) )    update_user_meta($customer_id, 'billing_phone',     sanitize_text_field($_POST['billing_phone']));
    if ( isset($_POST['billing_company']) )  update_user_meta($customer_id, 'billing_company',   sanitize_text_field($_POST['billing_company']));
    if ( isset($_POST['billing_vat_id']) )   update_user_meta($customer_id, 'billing_vat_id',    sanitize_text_field($_POST['billing_vat_id']));
});

add_filter('woocommerce_billing_fields', function($fields){
  if ( isset($fields['billing_phone']) ) $fields['billing_phone']['required'] = true;
  return $fields;
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


//product
add_action( 'wp', function () {
    //if ( is_product() ) {
        // Storefront מחבר את הסיידבר ל-storefront_sidebar
        remove_action( 'storefront_sidebar', 'storefront_get_sidebar', 10 );
        // למקרה ויש חיבור דרך ה-hook של WooCommerce:
        remove_action( 'woocommerce_sidebar', 'storefront_get_sidebar', 10 );
        remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
    //}
}, 20 );

remove_action('woocommerce_single_product_summary','woocommerce_template_single_price',10);
add_action('woocommerce_before_add_to_cart_button','woocommerce_template_single_price',5);

remove_action('woocommerce_single_product_summary','woocommerce_template_single_meta',40);
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sku', 5 );
function woocommerce_template_single_sku(){
    global $product;
    echo '<div class="item-sku">מק"ט: '.$product->get_sku().'</div>';
}

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 21 );
add_action( 'woocommerce_single_product_summary', 'add_review_ask', 30 );
add_action( 'woocommerce_after_add_to_cart_button', 'under_add_to_cart', 10 );

function add_review_ask(){
    global $product;
    ?>
        <div class="review-ask">
            <div class="review-add">
                <a href="#reviews" class="woocommerce-review-link" rel="nofollow"><span>הוסף חוות דעת</span></a>
            </div>
            <div class="ask">
                <a href="#" rel="nofollow"><span>שאלו אותנו על מוצר זה<br/>השאירו פרטים ונחזור בהקדם</span></a>
            </div>            
        </div>
    <?php
}

function under_add_to_cart(){
    global $product;
    ?>
        <div class="under-add-to-cart">
            <div class="wish-add">
                <?php echo do_shortcode('[ti_wishlists_addtowishlist]'); ?>
            </div>
            <div class="wish-add share">
                <a href="#"><span>שיתוף</span></a>
            </div>                    
        </div>
        <div class="compare-add">
            <?php echo do_shortcode('[br_compare_button]'); ?>
        </div>          
    <?php
}

add_action('woocommerce_before_add_to_cart_quantity','btn_qty_wrap',1);
add_action('woocommerce_after_add_to_cart_quantity','btn_qty_wrap_end',10);

function btn_qty_wrap(){
    echo '<div class="qty_wrap">';
    echo '<div class="qty-title">כמות</div>';
    echo '<div class="btn_qty_wrap">';
}

function btn_qty_wrap_end(){
    echo '</div></div>';
}

// עוטף את שדה הכמות בכפתורים
add_action( 'woocommerce_before_add_to_cart_quantity', function() {
    echo '<button type="button" class="qty-btn minus">-</button>';
});
add_action( 'woocommerce_after_add_to_cart_quantity', function() {
    echo '<button type="button" class="qty-btn plus">+</button>';
},9);

// "החל מ" במוצרי וריאציות + תמיכת מבצע
add_filter('woocommerce_variable_price_html', function( $price_html, $product ) {

    if ( ! $product instanceof WC_Product_Variable ) {
        return $price_html;
    }

    // כל המחירים של הווריאציות (לפי הגדרות מס)
    $prices = $product->get_variation_prices( true ); // true = כולל מיון לפי מחיר מוצג

    if ( empty( $prices['price'] ) ) {
        return $price_html;
    }

    // המינימומים
    $min_price        = current( $prices['price'] );          // המחיר בפועל (כולל מבצע אם יש)
    $min_regular      = ! empty( $prices['regular_price'] ) ? current( $prices['regular_price'] ) : $min_price;
    $min_sale_prices  = ! empty( $prices['sale_price'] ) ? array_filter( $prices['sale_price'] ) : [];
    $min_sale         = $min_sale_prices ? current( $min_sale_prices ) : 0;

    // תצוגה: אם יש מבצע אמיתי (מחיר מבצע נמוך ממחיר רגיל)
    if ( $min_sale && $min_sale < $min_regular ) {
        $html = sprintf(
            /* translators: starting-from price with sale */
            __('החל מ %1$s %2$s', 'your-textdomain'),
            '<del>' . wc_price( $min_regular ) . '</del>',
            '<ins>' . wc_price( $min_sale ) . '</ins>'
        );
    } else {
        $html = sprintf(
            /* translators: starting-from price */
            __('החל מ %s', 'your-textdomain'),
            wc_price( $min_price )
        );
    }

    // עטיפה בקלאס סטנדרטי של ווקומרס
    return '<span class="price">' . $html . '</span>';

}, 10, 2);

add_action('wp_footer', function () {
  if ( ! is_product() ) return; ?>
  <script>
  (function($){
    var $form = $('form.variations_form');
    if(!$form.length) return;

    // אלמנט המחיר הראשי (תמיכה ברוב התבניות)
    var $mainPrice = $('.summary .price, .product .summary p.price').first();
    if(!$mainPrice.length) return;

    var defaultHtml = $mainPrice.html(); // "החל מ …" ברירת מחדל

    // כשנמצאת וריאציה → החלפת המחיר הראשי במחיר הווריאציה (כולל מבצע)
    $form.on('found_variation', function(e, variation){
      if (variation && variation.price_html) {
        $mainPrice.html(variation.price_html);
      }
    });

    // איפוס בחירה → החזרת "החל מ …"
    $form.on('reset_data hide_variation', function(){
      $mainPrice.html(defaultHtml);
    });
  })(jQuery);
  </script>
  <?php
});


add_filter( 'woocommerce_product_tabs', 'eran_acf_custom_tab' );
function eran_acf_custom_tab( $tabs ) {

    // רק אם יש תוכן בשדה – נוסיף את הטאב
        $tabs['extra_info'] = array(
            'title'    => __( 'תמיכה והורדות', 'your-textdomain' ),
            'priority' => 50,
            'callback' => 'acf_custom_tab_content'
        );

    return $tabs;
}

function acf_custom_tab_content() {
    global $product;

    $driver = get_field('driver', $product->get_id());
    $guide = get_field('guide', $product->get_id());
    $admin_email = get_option( 'admin_email' );

    if ( $driver || $guide ) {
        echo '<div class="title">הורדות</div>';
    }

    if ( $driver ) {
        echo '<div class="acf-product-tab">';
            echo '<div>הורדה של דרייברים</div>';
            echo '<a href="'.$driver['url'].'" target="_blank">הורדה של דרייברים</a>';
        echo '</div>';
    }

    if ( $guide ) {
        echo '<div class="acf-product-tab">';
            echo '<div>הורדה של מדריכים</div>';
            echo '<a href="'.$guide['url'].'" target="_blank">הורדה של מדריכים</a>';
        echo '</div>';
    }    

    echo '<div class="acf-product-tab">';
        echo '<div>תמיכה טכנית</div>';
        echo '<div class="sub">כתובת הדוא"ל של התמיכה הטכנית:</div>';
        echo '<a href="mailto:'.$admin_email.'">'.$admin_email.'</a>';
    echo '</div>';
}

//review tab
remove_action( 'woocommerce_review_before','woocommerce_review_display_gravatar',10);
remove_action( 'woocommerce_review_before_comment_meta','woocommerce_review_display_rating',10);

add_action( 'woocommerce_before_variations_form', 'variation_title', 10 );
function variation_title(){
    echo '<div class="variation_title">הגדר את האפשרויות שלך:</div>';
}

//category
add_action('woocommerce_before_shop_loop','shop_loop_side_wrap',5);
add_action('woocommerce_before_shop_loop','shop_loop_filter',30);
add_action('woocommerce_before_shop_loop','shop_loop_side_wrap_end',40);
add_action('woocommerce_after_shop_loop','shop_loop_wrap_end',5);
add_action('woocommerce_shop_loop_header','filter_selected_area',50);

function shop_loop_side_wrap(){
    echo '<div class="shop_loop_wrap"><div class="shop_loop_side_wrap">';
}

function shop_loop_side_wrap_end(){
    echo '</div>';
}

function shop_loop_wrap_end(){
    echo '</div>';
}

function shop_loop_filter(){
    echo '<div class="filter-title">סינון לפי:</div>';
    echo do_shortcode('[br_filters_group group_id=338]');
}

function filter_selected_area(){
    echo '<div class="filter_selected_area">';
    echo do_shortcode('[br_filter_single filter_id=340]');
    echo '</div>';
}

//category header
add_action('woocommerce_shop_loop_header','shop_header_wrap',5);
add_action('woocommerce_shop_loop_header','shop_header_img',15);
add_action('woocommerce_shop_loop_header','shop_header_wrap_end',20);

function shop_header_wrap(){
    echo '<div class="shop_header_wrap">';
}

function shop_header_wrap_end(){
    echo '</div>';
}

function shop_header_img(){
    if ( is_product_category() ) {
        $term = get_queried_object(); // הקטגוריה הנוכחית
        if ( $term && isset( $term->term_id ) ) {
            $thumbnail_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
            if ( $thumbnail_id ) {
                // תמונה בגודל של WooCommerce (רספונסיבי)
                echo '<div class="cat-img">'.wp_get_attachment_image( $thumbnail_id, 'woocommerce_thumbnail', false, ['alt' => esc_attr( $term->name )]).'</div>';
            }
        }
    }
}

if (!defined('ABSPATH')) exit;

/**
 * Get pre-VAT total from the live cart.
 */
function eg_wc_get_pre_vat_from_cart() {
	if ( ! function_exists('WC') || ! WC()->cart ) return 0;
	$t = WC()->cart->get_totals(); // ['total'] and ['total_tax']
	$pre = (float) ($t['total'] ?? 0) - (float) ($t['total_tax'] ?? 0);
	return max(0, $pre);
}

/**
 * Output a table row in Cart/Checkout totals before the final order total.
 */
function eg_wc_output_pre_vat_row() {
	if ( ! function_exists('WC') || ! WC()->cart ) return;
	$pre = eg_wc_get_pre_vat_from_cart();
	echo '<li class="eg-pre-vat">' .
		 '<label>' . esc_html__('מחיר לפני מע״מ', 'woocommerce') . '</label>' .
		 '<span data-title="' . esc_attr__('מחיר לפני מע״מ', 'woocommerce') . '">' . wc_price($pre) . '</span>' .
		 '</li>';
}
// Cart page totals table
//add_action('woocommerce_cart_totals_before_order_total', 'eg_wc_output_pre_vat_row', 9);
// Checkout review-order table
//add_action('woocommerce_review_order_before_order_total', 'eg_wc_output_pre_vat_row', 9);

/**
 * Inject into order totals array (Thank you page, My Account > Orders, Emails).
 */
add_filter('woocommerce_get_order_item_totals', function($totals, $order/*, $tax_display*/) {
	if ( ! $order instanceof WC_Order ) return $totals;
	$pre = max(0, (float) $order->get_total() - (float) $order->get_total_tax());
	$insert = [
		'eg_pre_vat' => [
			'label' => 'מחיר לפני מע״מ',
			'value' => wc_price($pre, ['currency' => $order->get_currency()]),
		]
	];

	$new = [];
	foreach ($totals as $key => $row) {
		// הוסף את "מחיר לפני מע״מ" לפני ה-order_total
		if ($key === 'order_total') {
			$new += $insert;
		}
		$new[$key] = $row;
	}
	return $new;
}, 10, 2);


//הוספת + - לכמות בסך הקניות
/** +/- בכמות + עדכון סל אוטומטי + הסתרת כפתור "עדכון סל" (Cart בלבד) **/

// עטיפה וכפתורים סביב שדה הכמות
add_action('woocommerce_before_quantity_input_field', function () {
	if (!is_cart()) return;
	echo '<div class="eg-qty-wrap">';
	echo '<button type="button" class="eg-qty-btn minus" aria-label="הפחת כמות">−</button>';
}, 9);

add_action('woocommerce_after_quantity_input_field', function () {
	if (!is_cart()) return;
	echo '<button type="button" class="eg-qty-btn plus" aria-label="הוסף כמות">+</button>';
	echo '</div>';
}, 9);

// JS: לוגיקת +/− ועדכון סל אוטומטי (debounce)
add_action('wp_footer', function () {
	if (!is_cart()) return; ?>
<script>
(function($){
	"use strict";

	function decimals(step){
		step = String(step || 1);
		return (step.indexOf('.')>-1) ? (step.length - step.indexOf('.') - 1) : 0;
	}

	function clamp(val, min, max){
		if(isNaN(val)) val = 0;
		if(isFinite(min)) val = Math.max(val, min);
		if(isFinite(max)) val = Math.min(val, max);
		return val;
	}

	function refreshBtnStates($wrap){
		var $in = $wrap.find('input.qty');
		var val = parseFloat($in.val());
		var min = parseFloat($in.attr('min'));
		var max = parseFloat($in.attr('max'));
		$wrap.find('.minus').prop('disabled', isFinite(min) && val <= min);
		$wrap.find('.plus').prop('disabled', isFinite(max) && val >= max);
	}

	var updateTimer=null;
	function autoUpdate(){
		var $btn = $('button[name="update_cart"]');
		if($btn.length){
			$btn.prop('disabled', false);
			$btn.trigger('click');
		}
	}

	// לחיצה על +/−
	$(document).on('click', '.eg-qty-btn', function(){
		var $wrap = $(this).closest('.eg-qty-wrap');
		var $in   = $wrap.find('input.qty');
		var step  = parseFloat($in.attr('step')) || 1;
		var min   = parseFloat($in.attr('min'));
		var max   = parseFloat($in.attr('max'));
		var val   = parseFloat(($in.val()||'').replace(',', '.')) || 0;
		val += $(this).hasClass('plus') ? step : -step;
		val = clamp(val, min, max);
		var dec = decimals(step);
		$in.val( dec ? val.toFixed(dec) : String(parseInt(val,10)) ).trigger('change');
		refreshBtnStates($wrap);
	});

	// שינוי ידני => עדכון סל בהשהיה קצרה
	$(document).on('change keyup', '.woocommerce-cart-form input.qty', function(){
		var $wrap = $(this).closest('.eg-qty-wrap');
		refreshBtnStates($wrap);
		clearTimeout(updateTimer);
		updateTimer = setTimeout(autoUpdate, 500);
	});

	// אתחול מצבי כפתורים בטעינה
	$(function(){
		$('.eg-qty-wrap').each(function(){ refreshBtnStates($(this)); });
	});
})(jQuery);
</script>
<?php });


/**
 * Cart: "שמור לאחר כך" (TI Wishlist) -> ואז הסרה מהסל
 * דורש תוסף: TI WooCommerce Wishlist (TemplateInvaders)
 */

if (!defined('ABSPATH')) exit;

/* 1) מוסיף לינק "שמור לאחר כך" ליד קישור ההסרה של כל שורה בסל,
      ובנוסף מייצר כפתור Wishlist סמוי בעזרת ה־shortcode של TI */
add_filter('woocommerce_cart_item_remove_link', function ($link_html, $cart_item_key) {
	if (!is_cart()) return;

	$cart = WC()->cart->get_cart();
	if (empty($cart[$cart_item_key])) return $link_html;

	$item         = $cart[$cart_item_key];
	$product_id   = (int) ($item['product_id'] ?? 0);
	$variation_id = (int) ($item['variation_id'] ?? 0);

	// כפתור TI Wishlist סמוי (מייצר URL+nonce הנכונים של התוסף)
	$atts = ' product_id="'.$product_id.'"';
	if ($variation_id) $atts .= ' variation_id="'.$variation_id.'"';
	$hidden_wl_btn = '<span class="eg-hidden-wl" style="display:none">'
	               . do_shortcode('[ti_wishlists_addtowishlist'.$atts.' /]')
	               . '</span>';

	$save_btn = '<a href="#" class="eg-save-for-later" data-cart-key="'.esc_attr($cart_item_key).'" aria-label="שמור לאחר כך">'
	          . 'שמור לאחר כך</a>';

	return $link_html . $save_btn . $hidden_wl_btn;
}, 10, 2);

/* 2) סטייל קטן (רשות) */
add_action('wp_head', function () {
	if (!is_cart()) return;
	echo '<style>
		.woocommerce-cart a.eg-save-for-later { font-size:.9em; text-decoration:underline; }
		.woocommerce-cart a.eg-save-for-later.disabled { opacity:.5; pointer-events:none; }
	</style>';
});

/* 3) JS: לחיצה על "שמור לאחר כך" => טריגר לכפתור TI הסמוי => בהצלחה מסירים מהסל */
add_action('wp_footer', function () {
	if (!is_cart()) return; ?>
<script>
jQuery(function($){
	$(document).on('click', '.eg-save-for-later', function(e){
		e.preventDefault();
		var $btn = $(this);
		if ($btn.hasClass('disabled')) return;
		$btn.addClass('disabled');

		var $row = $btn.closest('tr.cart_item');
		var $wl  = $row.find('.eg-hidden-wl .tinvwl_add_to_wishlist_button, .eg-hidden-wl a.tinvwl_add_to_wishlist_button, .eg-hidden-wl button.tinvwl_add_to_wishlist_button');

		if (!$wl.length){
			console.warn('Wishlist button not found in row');
			$btn.removeClass('disabled');
			return;
		}

		// מפעיל את כפתור ה-Wishlist של התוסף (AJAX שלהם)
		$wl.trigger('click');

		// מחכים לסיום – בודקים שהכפתור קיבל מצב "נוסף"
		var tries = 0, tick = setInterval(function(){
			tries++;
			if ($wl.hasClass('tinvwl-product-in-list') || $wl.hasClass('tinvwl-added') || tries > 40) {
				clearInterval(tick);
				if (tries <= 40) {
					// מסיר מהסל את אותה שורה (כמו לחיצה על ה-X)
					var $remove = $row.find('.product-remove a.remove');
					if ($remove.length) { $remove.trigger('click'); }
				} else {
					// ככל הנראה נדרש התחברות/הרשאות – משחרר את הכפתור
					$btn.removeClass('disabled');
				}
			}
		}, 150);
	});
});
</script>
<?php });

/* ===== USD helper ===== */
function eg_get_usd_rate() {
	// ערך משדה ACF גלובלי (Options). שנה לפי המקום שהגדרת.
	$rate = (float) get_field('rate_ex', 'option');
	if (!$rate) { $rate = 3.35; } // fallback
	return $rate;
}
function eg_usd_amount($amount) {
	$rate = eg_get_usd_rate();
	if (!$rate || $amount <= 0) return '';
	$usd = $amount / $rate;
	return '$' . number_format($usd, 2);
}
function eg_usd_suffix_pair($regular_display, $sale_display = null) {
	// אם יש מחיר מבצע קטן מהרגיל – מציגים "לפני → אחרי", אחרת רק רגיל
	if ($sale_display && $sale_display > 0 && $sale_display < $regular_display) {
		return ' <span class="eg-usd-price"><span class="eg-usd-regular">'.eg_usd_amount($regular_display).'</span> <span class="eg-usd-sale">'.eg_usd_amount($sale_display).'</span></span>';
	}
	return ' <span class="eg-usd-price">'.eg_usd_amount($regular_display).'</span>';
}

/* ===== קטגוריות + עמוד מוצר (מחיר בסיסי) ===== */
add_filter('woocommerce_get_price_html', function($html, $product){
	if (!$product instanceof WC_Product) return $html;

	// מוצרים משתנים (טווחים)
	if ( $product->is_type('variable') ) {
		$display_incl = ('incl' === get_option('woocommerce_tax_display_shop')); // true => מחיר לתצוגה כולל מע"מ לפי ההגדרות
		$reg_min  = (float) $product->get_variation_regular_price('min', $display_incl);
		$reg_max  = (float) $product->get_variation_regular_price('max', $display_incl);
		$sale_min = (float) $product->get_variation_sale_price('min',  $display_incl);
		$sale_max = (float) $product->get_variation_sale_price('max',  $display_incl);

		//$range_reg  = ($reg_min === $reg_max) ? eg_usd_amount($reg_min) : eg_usd_amount($reg_min) . '–' . eg_usd_amount($reg_max);
        $range_reg  = ($reg_min === $reg_max) ? eg_usd_amount($reg_min) : eg_usd_amount($reg_min);

		if ( $product->is_on_sale() && $sale_min > 0 ) {
			//$range_sale = ($sale_min === $sale_max) ? eg_usd_amount($sale_min) : eg_usd_amount($sale_min) . '–' . eg_usd_amount($sale_max);
            $range_sale = ($sale_min === $sale_max) ? eg_usd_amount($sale_min) : eg_usd_amount($sale_min);
			$html .= ' <span class="eg-usd-price"><span class="eg-usd-regular">'.$range_reg.'</span><span class="eg-usd-sale">'.$range_sale.'</span></span>';
		} else {
			$html .= ' <span class="eg-usd-price">'.$range_reg.'</span>';
		}
		return $html;
	}

	// מוצרים פשוטים/חיצוניים/מקובצים – לפי מחיר לתצוגה (מכבד כולל/לא כולל מע"מ)
	$reg  = wc_get_price_to_display($product, ['price' => (float) $product->get_regular_price()]);
	$sale = $product->get_sale_price() ? wc_get_price_to_display($product, ['price' => (float) $product->get_sale_price()]) : 0;

	return $html . eg_usd_suffix_pair($reg, $sale ?: null);
}, 30, 2);

/* ===== וריאציות: מחיר משתנה בדינמיקה ===== */
add_filter('woocommerce_available_variation', function($data, $product, $variation){
	if (!$variation instanceof WC_Product_Variation) return $data;

	$reg  = wc_get_price_to_display($variation, ['price' => (float) $variation->get_regular_price()]);
	$sale = $variation->get_sale_price() ? wc_get_price_to_display($variation, ['price' => (float) $variation->get_sale_price()]) : 0;

	$data['price_html'] .= eg_usd_suffix_pair($reg, $sale ?: null);
	return $data;
}, 30, 3);

// functions.php (בתבנית-בת או בתוסף ייעודי)
add_action( 'wp', function () {

    // 1) מסיר את בלוק התשלום המלא (שכולל גם כפתור) מהמיקום הדיפולטי
    remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );

    // 2) מציג רק את שיטות התשלום מיד אחרי פרטי הלקוח
    add_action( 'woocommerce_checkout_after_customer_details', function () {

        if ( ! is_checkout() ) return;

        $gateways = WC()->payment_gateways()->get_available_payment_gateways();

        // מגדיר gateway נבחר לפי סשן/דיפולט
        WC()->payment_gateways()->set_current_gateway( $gateways );

        echo '<div class="wc-payment-methods-only">';
        echo '<h3>' . esc_html__( 'שיטת תשלום', 'woocommerce' ) . '</h3>';

        if ( ! empty( $gateways ) ) {
            echo '<ul class="wc_payment_methods payment_methods methods">';
            foreach ( $gateways as $gateway ) {
                // תבנית הלייסט-אייטם של כל Gateway (כולל הרדיו והשדות)
                wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
            }
            echo '</ul>';
        } else {
            // הודעה כשאין שערים זמינים
            echo '<div class="woocommerce-info">' .
                 wp_kses_post( apply_filters(
                     'woocommerce_no_available_payment_methods_message',
                     __( 'אין אמצעי תשלום זמינים עבור הכתובת/מטבע הנוכחיים.', 'woocommerce' )
                 ) ) .
                 '</div>';
        }

        echo '</div>';
    }, 20 );

    // 3) מחזיר באזור הסיכום רק את כפתור ההזמנה (פלוס תנאים ו־nonce)
    add_action( 'woocommerce_checkout_order_review', function () {

        if ( ! is_checkout() ) return;

        echo '<div class="wc-place-order-only">';

        // תנאי שימוש/פרטיות (אם מופעל בהגדרות)
        wc_get_template( 'checkout/terms.php' );

        do_action( 'woocommerce_review_order_before_submit' );

        $order_button_text = apply_filters( 'woocommerce_order_button_text', __( 'Place order', 'woocommerce' ) );

        echo apply_filters(
            'woocommerce_order_button_html',
            '<button type="submit" class="button alt" name="woocommerce_checkout_place_order" id="place_order"
                value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '">'
                . esc_html( $order_button_text ) .
            '</button>'
        );

        do_action( 'woocommerce_review_order_after_submit' );

        // nonce של תהליך הצ'קאאוט
        wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' );

        echo '</div>';

    }, 20 );
}, 99 );

//הזזת שיטות משלוח בקופה
function eran_checkout_shipping_methods_html() {
    if ( ! function_exists('WC') || ! is_checkout() ) return '';
    if ( ! WC()->cart || ! WC()->cart->needs_shipping() || ! WC()->cart->show_shipping() ) return '';

    // תאימות: קבלת החבילות
    $packages = method_exists( WC()->shipping, 'get_packages' )
        ? WC()->shipping->get_packages()
        : WC()->shipping()->get_packages();

    $chosen_methods        = WC()->session->get( 'chosen_shipping_methods', array() );
    $has_calculated        = WC()->customer ? WC()->customer->has_calculated_shipping() : false;

    ob_start();
    echo '<section id="eg-shipping-methods" class="checkout-shipping-methods">';
    echo '<h3>' . esc_html__( 'שיטת משלוח', 'woocommerce' ) . '</h3>';

    foreach ( $packages as $i => $package ) {
        $available_methods    = isset( $package['rates'] ) ? $package['rates'] : array();
        $chosen_method        = isset( $chosen_methods[ $i ] ) ? $chosen_methods[ $i ] : '';
        $formatted_destination = WC()->countries->get_formatted_address( $package['destination'], ', ' );

        // *** חשוב: מעבירים גם package_name, index ו־has_calculated_shipping ***
        $package_name = apply_filters(
            'woocommerce_shipping_package_name',
            sprintf( _x( 'Shipping %d', 'shipping packages', 'woocommerce' ), $i + 1 ),
            $i,
            $package
        );

        wc_get_template( 'cart/cart-shipping.php', array(
            'package'                   => $package,
            'available_methods'         => $available_methods,
            'show_package_details'      => count( $packages ) > 1,
            'show_shipping_calculator'  => false,
            'package_index'             => $i,     // חלק מהגרסאות
            'index'                     => $i,     // חלק אחר
            'chosen_method'             => $chosen_method,
            'formatted_destination'     => $formatted_destination,
            'has_calculated_shipping'   => $has_calculated,
            'package_name'              => $package_name, // מונע ה־Notice
        ) );
    }

    echo '</section>';
    return ob_get_clean();
}

// מדפיס לפני כותרת הסיכום
add_action( 'woocommerce_checkout_before_order_review_heading', function () {
    echo eran_checkout_shipping_methods_html();
}, 12 );

// מסתיר כפילות של שורות משלוח בסיכום
add_filter( 'woocommerce_cart_totals_shipping_html', function ( $html ) {
    return is_checkout() ? '' : $html;
}, 10 );

// רענון AJAX לפרגמנט
add_filter( 'woocommerce_update_order_review_fragments', function ( $fragments ) {
    $fragments['#eg-shipping-methods'] = eran_checkout_shipping_methods_html();
    return $fragments;
});

add_filter('woocommerce_shipping_package_name', function($name){
    return '<span class="screen-reader-text">'.esc_html($name).'</span>';
}, 10);

// קובע 5 תוצאות ו־5 עמודות ל־Related Products
add_filter('woocommerce_output_related_products_args', function($args){
    $args['posts_per_page'] = 5; // כמה מוצרים להציג
    $args['columns']        = 1; // כמה עמודות ברשת
    // אופציונלי: $args['orderby'] = 'rand'; // סדר אקראי
    return $args;
}, 20);
