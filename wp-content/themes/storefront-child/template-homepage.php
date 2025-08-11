<?php
/**
 * The template for displaying the homepage.
 *
 * This page template will display any functions hooked into the `homepage` action.
 * By default this includes a variety of product displays and the page content itself. To change the order or toggle these components
 * use the Homepage Control plugin.
 * https://wordpress.org/plugins/homepage-control/
 *
 * Template name: Homepage
 *
 * @package storefront
 */

get_header();

$post_id = get_the_ID();
?>

<div class="cg-home" dir="rtl">

<?php if ( have_rows('home_sections', $post_id) ) : ?>
  <?php while ( have_rows('home_sections', $post_id) ) : the_row(); ?>

    <?php if ( get_row_layout() === 'hero_section' ) : ?>
      <?php if ( have_rows('hero_slides') ) : ?>
      <section class="cg-hero">
        <div class="cg-hero__slider js-hero-slider">
          <?php while ( have_rows('hero_slides') ) : the_row();
            $img_d = get_sub_field('image_desktop');
            $img_m = get_sub_field('image_mobile');
            $d_url = is_array($img_d) ? $img_d['url'] : $img_d;
            $m_url = is_array($img_m) ? $img_m['url'] : $img_m;
            $label_text = get_sub_field('label_text');
            $label_bg   = get_sub_field('label_bg_color') ?: '#2c7be5';
            $title      = get_sub_field('title');
            $body       = get_sub_field('body');
            $button     = get_sub_field('button');
          ?>
          <article class="cg-hero__slide">
            <picture class="cg-hero__bg" aria-hidden="true">
              <?php if ($m_url): ?><source media="(max-width: 768px)" srcset="<?php echo esc_url($m_url); ?>"><?php endif; ?>
              <?php if ($d_url): ?><img src="<?php echo esc_url($d_url); ?>" alt=""><?php endif; ?>
            </picture>
            <div class="cg-hero__inner container">
              <?php if ($label_text): ?>
                <div class="cg-hero__label" style="--label-bg: <?php echo esc_attr($label_bg); ?>;"><?php echo esc_html($label_text); ?></div>
              <?php endif; ?>
              <?php if ($title): ?><h1 class="cg-hero__title"><?php echo esc_html($title); ?></h1><?php endif; ?>
              <?php if ($body): ?><div class="cg-hero__text"><?php echo wp_kses_post($body); ?></div><?php endif; ?>
              <?php if (!empty($button['url'])) :
                $target = $button['target'] ?: '_self'; ?>
                <a class="cg-btn cg-btn--primary" href="<?php echo esc_url($button['url']); ?>" target="<?php echo esc_attr($target); ?>">
                  <?php echo esc_html($button['title'] ?: __('לפרטים', 'storefront-child')); ?>
                </a>
              <?php endif; ?>
            </div>
          </article>
          <?php endwhile; ?>
        </div>
      </section>
      <?php endif; ?>
    <?php endif; ?>

    <?php if ( get_row_layout() === 'best_sellers_section' ) : 
      $best_title = get_sub_field('title');
      $products   = get_sub_field('products');
      if ( $products ) : ?>
      <section class="cg-best-sellers section">
        <div class="container">
          <?php if ($best_title): ?><div class="section__head"><h2 class="section__title"><?php echo esc_html($best_title); ?></h2></div><?php endif; ?>
          <div class="cg-products">
            <?php foreach ( $products as $p ) :
              $pid      = is_object($p) ? $p->ID : (int) $p;
              $product  = wc_get_product( $pid );
              if ( ! $product ) continue;
              $url      = get_permalink( $pid );
              $img_html = get_the_post_thumbnail( $pid, 'woocommerce_thumbnail', ['class'=>'cg-product__img','loading'=>'lazy'] );
              $title    = get_the_title( $pid );
              $price    = $product->get_price_html();
              $btn_url  = $product->add_to_cart_url();
              $btn_txt  = $product->add_to_cart_text();
              $btn_cls  = 'button add_to_cart_button ajax_add_to_cart product_type_' . $product->get_type();
            ?>
            <article class="cg-product">
              <a class="cg-product__link" href="<?php echo esc_url($url); ?>" aria-label="<?php echo esc_attr($title); ?>"></a>
              <div class="cg-product__media">
                <?php echo $img_html ?: '<div class="cg-product__placeholder"></div>'; ?>
                <a href="<?php echo esc_url($btn_url); ?>"
                   data-product_id="<?php echo esc_attr($pid); ?>"
                   class="cg-product__cta <?php echo esc_attr($btn_cls); ?>"><?php echo esc_html($btn_txt); ?></a>
              </div>
              <h3 class="cg-product__title"><?php echo esc_html($title); ?></h3>
              <div class="cg-product__price"><?php echo wp_kses_post($price); ?></div>
            </article>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
      <?php endif; endif; ?>

    <?php if ( get_row_layout() === 'double_banners_section' ) : 
      if ( have_rows('promo_banners') ) : ?>
      <section class="cg-double-banners section">
        <div class="container grid-2">
          <?php while ( have_rows('promo_banners') ) : the_row();
            $bg     = get_sub_field('bg_image');
            $title  = get_sub_field('title');
            $text   = get_sub_field('text');
            $printer= get_sub_field('printer_image');
            $link   = get_sub_field('link');
            $bg_url = is_array($bg) ? $bg['url'] : $bg;
            $p_url  = is_array($printer) ? $printer['url'] : $printer;
            $href   = $link['url'] ?? '#'; $target = $link['target'] ?? '_self';
          ?>
          <article class="cg-banner" style="--bg:url('<?php echo esc_url($bg_url); ?>')">
            <a class="cg-banner__overlay" href="<?php echo esc_url($href); ?>" target="<?php echo esc_attr($target); ?>"></a>
            <div class="cg-banner__content">
              <?php if ($title): ?><h3 class="cg-banner__title"><?php echo esc_html($title); ?></h3><?php endif; ?>
              <?php if ($text): ?><p class="cg-banner__text"><?php echo wp_kses_post($text); ?></p><?php endif; ?>
            </div>
            <?php if ($p_url): ?><img class="cg-banner__img" src="<?php echo esc_url($p_url); ?>" alt="" loading="lazy"><?php endif; ?>
          </article>
          <?php endwhile; ?>
        </div>
      </section>
      <?php endif; endif; ?>

    <?php if ( get_row_layout() === 'testimonials_section' ) :
      $t_title = get_sub_field('title');
      if ( have_rows('testimonials') ) : ?>
      <section class="cg-testimonials section">
        <div class="container">
          <?php if ($t_title): ?><h2 class="section__title"><?php echo esc_html($t_title); ?></h2><?php endif; ?>
          <div class="cg-testimonials__list js-testimonials">
            <?php while ( have_rows('testimonials') ) : the_row();
              $avatar = get_sub_field('avatar');
              $name   = get_sub_field('name');
              $role   = get_sub_field('role');
              $quote  = get_sub_field('quote');
              $rate   = (int)(get_sub_field('rating') ?: 5);
              $av_url = is_array($avatar) ? $avatar['url'] : $avatar;
            ?>
            <article class="cg-testimonial">
              <?php if ($av_url): ?><img class="cg-testimonial__avatar" src="<?php echo esc_url($av_url); ?>" alt="<?php echo esc_attr($name ?: ''); ?>" loading="lazy"><?php endif; ?>
              <div class="cg-testimonial__body">
                <div class="cg-testimonial__meta">
                  <strong class="cg-testimonial__name"><?php echo esc_html($name); ?></strong>
                  <?php if ($role): ?><span class="cg-testimonial__role"><?php echo esc_html($role); ?></span><?php endif; ?>
                </div>
                <div class="cg-testimonial__rating" aria-label="<?php echo esc_attr(sprintf(__('דירוג %d מתוך 5', 'storefront-child'), $rate)); ?>">
                  <?php for($i=1;$i<=5;$i++): ?><span class="star <?php echo $i <= $rate ? 'is-on':''; ?>" aria-hidden="true">★</span><?php endfor; ?>
                </div>
                <blockquote class="cg-testimonial__quote"><?php echo wp_kses_post($quote); ?></blockquote>
              </div>
            </article>
            <?php endwhile; ?>
          </div>
        </div>
      </section>
      <?php endif; endif; ?>

    <?php if ( get_row_layout() === 'news_icons_section' ) :
      $nl_title = get_sub_field('newsletter_title');
      $nl_text  = get_sub_field('newsletter_text');
      $shortcode= get_sub_field('newsletter_form_shortcode');
      $icons_bg = get_sub_field('icon_panel_bg');
      $bg_url   = is_array($icons_bg) ? $icons_bg['url'] : $icons_bg; ?>
      <section class="cg-news-icons section">
        <div class="container grid-2">
          <div class="cg-news">
            <?php if ($nl_title): ?><h3 class="section__subtitle"><?php echo esc_html($nl_title); ?></h3><?php endif; ?>
            <?php if ($nl_text): ?><p class="section__text"><?php echo wp_kses_post($nl_text); ?></p><?php endif; ?>
            <div class="cg-news__form">
              <?php echo $shortcode ? do_shortcode($shortcode) : '<!-- Set CF7 shortcode -->'; ?>
            </div>
          </div>
          <aside class="cg-icon-panel" style="--panel-bg:url('<?php echo esc_url($bg_url); ?>')">
            <?php if ( have_rows('icon_panel_items') ) : ?>
              <ul class="cg-icon-panel__list">
                <?php while ( have_rows('icon_panel_items') ) : the_row();
                  $icon = get_sub_field('icon'); $label = get_sub_field('label'); $link = get_sub_field('link');
                  $iurl = is_array($icon) ? $icon['url'] : $icon; $href = $link['url'] ?? '#'; $target = $link['target'] ?? '_self'; ?>
                  <li class="cg-icon-panel__item">
                    <a href="<?php echo esc_url($href); ?>" target="<?php echo esc_attr($target); ?>">
                      <?php if ($iurl): ?><img src="<?php echo esc_url($iurl); ?>" alt="" loading="lazy"><?php endif; ?>
                      <span><?php echo esc_html($label); ?></span>
                    </a>
                  </li>
                <?php endwhile; ?>
              </ul>
            <?php endif; ?>
          </aside>
        </div>
      </section>
    <?php endif; ?>

    <?php if ( get_row_layout() === 'categories_section' ) :
      if ( have_rows('categories') ) : ?>
      <section class="cg-categories section">
        <div class="container grid-5">
          <?php while ( have_rows('categories') ) : the_row();
            $img = get_sub_field('image'); $name = get_sub_field('name'); $link = get_sub_field('link');
            $url = $link['url'] ?? '#'; $target = $link['target'] ?? '_self'; $iurl = is_array($img) ? $img['url'] : $img; ?>
            <a class="cg-category" href="<?php echo esc_url($url); ?>" target="<?php echo esc_attr($target); ?>">
              <?php if ($iurl): ?><img class="cg-category__img" src="<?php echo esc_url($iurl); ?>" alt="<?php echo esc_attr($name ?: ''); ?>" loading="lazy"><?php endif; ?>
              <span class="cg-category__name"><?php echo esc_html($name); ?></span>
            </a>
          <?php endwhile; ?>
        </div>
      </section>
      <?php endif; endif; ?>

    <?php if ( get_row_layout() === 'clients_section' ) :
      $title = get_sub_field('title');
      if ( have_rows('clients') ) : ?>
      <section class="cg-logos section">
        <div class="container">
          <?php if ($title): ?><h3 class="section__subtitle"><?php echo esc_html($title); ?></h3><?php endif; ?>
          <div class="cg-logos__grid">
            <?php while ( have_rows('clients') ) : the_row();
              $logo = get_sub_field('logo'); $url = get_sub_field('link');
              $lurl = is_array($logo) ? $logo['url'] : $logo; $href = $url['url'] ?? '#'; $target = $url['target'] ?? '_self'; ?>
              <a class="cg-logo" href="<?php echo esc_url($href); ?>" target="<?php echo esc_attr($target); ?>">
                <?php if ($lurl): ?><img src="<?php echo esc_url($lurl); ?>" alt="" loading="lazy"><?php endif; ?>
              </a>
            <?php endwhile; ?>
          </div>
        </div>
      </section>
      <?php endif; endif; ?>

    <?php if ( get_row_layout() === 'partners_section' ) :
      $title = get_sub_field('title');
      if ( have_rows('partners') ) : ?>
      <section class="cg-logos section">
        <div class="container">
          <?php if ($title): ?><h3 class="section__subtitle"><?php echo esc_html($title); ?></h3><?php endif; ?>
          <div class="cg-logos__grid">
            <?php while ( have_rows('partners') ) : the_row();
              $logo = get_sub_field('logo'); $url = get_sub_field('link');
              $lurl = is_array($logo) ? $logo['url'] : $logo; $href = $url['url'] ?? '#'; $target = $url['target'] ?? '_self'; ?>
              <a class="cg-logo" href="<?php echo esc_url($href); ?>" target="<?php echo esc_attr($target); ?>">
                <?php if ($lurl): ?><img src="<?php echo esc_url($lurl); ?>" alt="" loading="lazy"><?php endif; ?>
              </a>
            <?php endwhile; ?>
          </div>
        </div>
      </section>
      <?php endif; endif; ?>

  <?php endwhile; ?>
<?php endif; ?>

</div>

<?php get_footer(); ?>
