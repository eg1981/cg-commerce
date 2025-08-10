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