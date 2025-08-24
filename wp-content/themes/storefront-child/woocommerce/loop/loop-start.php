<?php
/**
 * Product Loop Start
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/loop-start.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$term_id  = get_queried_object_id();
$taxonomy = 'product_cat';
$terms    = get_terms([
    'taxonomy'    => $taxonomy,
    'hide_empty'  => false,
    'parent'      => get_queried_object_id()
]);
?>
<div class="loop_wrap_start">
    <?php
        if(count($terms)){
			echo '<div class="sub-cats"><div class="container grid-5">';
				foreach ( $terms as $term ){
					$image = get_field('sub_cat_icon', $term->taxonomy . '_' . $term->term_id);
					if(!$image){
						$image = get_term_meta($term->term_id, 'thumbnail_id', true);
					}
					echo '<a href="'.$term->slug.'" class="cg-category">';
						echo '<div class="category__img">';
							if($image)
								echo '<img src="'.wp_get_attachment_image_url($image, 'full').'" alt="'.$term->name.'">';
							else
								echo '<img src="'.wc_placeholder_img_src().'" alt="'.$term->name.'">';
                        echo '</div>';
                        echo '<div class="cg-category__name">'.$term->name.'</div>';						
					echo '</a>';
				}
			echo '</div></div>';
		}
    ?>
    <ul class="products columns-<?php echo esc_attr( wc_get_loop_prop( 'columns' ) ); ?>">
