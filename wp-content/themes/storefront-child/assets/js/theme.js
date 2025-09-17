jQuery(document).ready(function($){
  $('.stop-header-m').slick({
        slidesToShow: 1,
		slidesToScroll:1,
		rtl: true,
		infinite: true,
        autoplay:4000,
        arrows: true,
        dots: false,        
  });
});

//Home
(function($){

    // HERO Slick
    var $hero = $('.js-hero-slider');
    if ($hero.length && $.fn.slick){
      $hero.slick({
        rtl: true,
        autoplay: false,
        autoplaySpeed: 4500,
        speed: 600,
        dots: false,
        arrows: true,
        adaptiveHeight: false
      });
    }

    // Testimonials (optional slider if many)
    var $t = $('.js-testimonials');
    if ($t.length && $t.children().length > 3 && $.fn.slick){
      $t.slick({
        rtl: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        dots: true,
        responsive: [
          {breakpoint: 1100, settings: {slidesToShow: 2}},
          {breakpoint: 768,  settings: {slidesToShow: 1}}
        ]
      });
    }

    // Newsletter toggle: if there's a checkbox, require it before submit
    $('.cg-news__form form').each(function(){
      var $form = $(this);
      var $submit = $form.find('input[type="submit"], button[type="submit"]').first();
      var $chk = $form.find('input[type="checkbox"]').first();
      if ($chk.length) {
        var set = function(){
          var ok = $chk.is(':checked');
          $form.toggleClass('is-disabled', !ok);
          $submit.prop('disabled', !ok);
        };
        set();
        $chk.on('change', set);
      }
    });


document.addEventListener('click', function (e) {
  const btn = e.target.closest('.qty-btn');           // מזהה רק כפתורי +/-
  if (!btn) return;

  const wrap  = btn.closest('.btn_qty_wrap');         // העטיפה שלך
  const input = wrap ? wrap.querySelector('.quantity input.qty') : null;
  if (!input) return;

  const step = parseFloat(input.step) || 1;
  const min  = input.min !== '' ? parseFloat(input.min) : 1;
  const max  = input.max !== '' ? parseFloat(input.max) : Infinity;

  // קריאה בטוחה + תמיכה בפסיק עשרוני
  let val = parseFloat(String(input.value).replace(',', '.'));
  if (isNaN(val)) val = min;

  if (btn.classList.contains('plus'))  val += step;
  if (btn.classList.contains('minus')) val -= step;

  // גבולות + יישור ל-step
  val = Math.max(min, Math.min(val, max));
  val = Number((Math.round(val / step) * step).toFixed(6));

  input.value = val;

  // טריגרים כדי שווקומרס/וריאציות/מחיר יתעדכנו
  input.dispatchEvent(new Event('change', { bubbles: true }));
  input.dispatchEvent(new Event('input',  { bubbles: true }));
});

})(jQuery);

jQuery(function($){

    function updateMiniCartCountAttr() {
        $('.site-header-cart .count').each(function(){
            var count = $(this).text().replace(/\D/g, '');
            $(this).attr('data-count', count);
        });
    }

    // להריץ בהתחלה
    updateMiniCartCountAttr();

    // להריץ בכל פעם שהסל מתעדכן
    $(document.body).on('wc_fragments_refreshed wc_fragments_loaded added_to_cart', function () {
        updateMiniCartCountAttr();
    });

});


/* Back to top – behavior only (jQuery) */
(function ($) {
  var SHOW_AFTER = 300;   // כמה לגלול עד שהכפתור מוצג
  var ANIM_MS    = 400;   // זמן הגלילה לראש

  $(function () {
    var $btn = $('#backToTop');
    if (!$btn.length) return;

    $btn.hide(); // מוסתר בתחילה

    function toggle() {
      var reduce  = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      var visible = $(window).scrollTop() > SHOW_AFTER;

      if (reduce) {
        visible ? $btn.show() : $btn.hide();
      } else {
        visible ? $btn.stop(true, true).fadeIn(150)
                : $btn.stop(true, true).fadeOut(150);
      }
    }

    $(window).on('scroll.backToTop', toggle);
    toggle(); // סטטוס התחלתי

    $btn.on('click', function (e) {
      e.preventDefault();
      var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      if (reduce) {
        $('html, body').stop(true).scrollTop(0);
      } else {
        $('html, body').stop(true).animate({ scrollTop: 0 }, ANIM_MS);
      }
    });
  });
})(jQuery);

//mobile header
(function ($) {

  //adding close icon to menu
  $('<div/>', { class: 'menu-close', text: '' }).appendTo('.handheld-navigation');

  $('body').on('click', '.menu-close', function(e){
      $('.main-navigation').removeClass('toggled');
  });

  //filter on catalog
  if ($(window).width() < 991){
    if ($('.shop_loop_wrap .loop_wrap_start').length){
        $('<div/>', { class: 'filter-icon', text: 'סינון לפי' }).appendTo('.loop_wrap_start');
        $('<div/>', { class: 'filter-close', text: '' }).appendTo('.shop_loop_side_wrap');
    }

    $('body').on('click', '.filter-close', function(e){
        $('body').removeClass('filter-show');
    });    

    $('body').on('click', '.filter-icon', function(e){
        $('body').addClass('filter-show');
    });

  }

  if ($(window).width() < 767){

    jQuery(function($){
      var $parent = $('.site-header>.col-full');
      var $el     = $('button.menu-toggle');
      var $login     = $('.header-icons .login-icon');

      if ($parent.length && $el.length){
        var $wrap = $('<div/>', { class: 'header-right' }).prependTo($parent);
        $el.appendTo($wrap);
        $login.appendTo($wrap);
      }
    });

    $('body .dgwt-wcas-search-wrapp').appendTo('.site-header');

    $('.site-header').addClass('active');

    $('body').on('click', '.site-header-cart a.cart-contents', function(e){
        $(this).parent().parent().find('.widget_shopping_cart').toggleClass('active');
    });
    
  }

  //קרא עוד בקטגוריה
  if ($(window).width() < 450){
        const $box = $('.term-description p');
        if ($box.data('rm-ready')) return;
        $box.data('rm-ready', true);

        const moreText = $box.data('more') || 'קרא עוד';
        const lessText = $box.data('less') || 'צמצם';

        // נשתמש בכפתור קיים אם יש, אחרת ניצור אחד אחרי הקופסה
        let $toggle = $box.find('> .rm-toggle').first();
        if (!$toggle.length) {
          $toggle = $('<a type="button" class="rm-toggle" aria-expanded="false"></a>')
            .text(moreText)
            .insertAfter($box);
        } else {
          $toggle.text(moreText).attr('aria-expanded', 'false');
        }

        $toggle.on('click', function () {
          const isActive = $box.toggleClass('active').hasClass('active');
          $toggle.text(isActive ? lessText : moreText)
            .attr('aria-expanded', isActive ? 'true' : 'false');
        });  
  }

  // החלפת הטאבים בדף מוצר לאקורדיון במובייל
  jQuery(function ($) {
    const mq = window.matchMedia('(max-width: 767px)');

    function openPanel($wrap, id, animate){
      const $panel = $wrap.find('#'+id);
      const $head  = $wrap.find('.wc-acc-head[aria-controls="'+id+'"]');
      const $all   = $wrap.find('.wc-tab');

      // סגור אחרים
      $wrap.find('.wc-acc-head').attr('aria-expanded','false');

      if (animate){
        $all.filter('.is-open').stop(true,true).slideUp(200, function(){ $(this).removeClass('is-open'); });
        $panel.stop(true,true).slideDown(200, function(){ $(this).addClass('is-open'); });
      } else {
        $all.hide().removeClass('is-open');
        $panel.show().addClass('is-open');
      }

      // סימון פתוח + סנכרון טאב פעיל לדסקטופ
      $head.attr('aria-expanded','true');
      $wrap.find('.wc-tabs li').removeClass('active').attr('aria-selected', false);
      $wrap.find('.wc-tabs a[aria-controls="'+id+'"]').closest('li')
          .addClass('active').attr('aria-selected', true);
    }

    function buildAccordion($wrap){
      if ($wrap.data('acc-init')) return;
      $wrap.data('acc-init', true);

      // צור כותרות אקורדיון מכל הטאבים
      $wrap.find('.wc-tabs li').each(function(){
        const $a   = $(this).find('a[aria-controls]');
        const id   = $a.attr('aria-controls');
        const text = $.trim($a.text());
        const $p   = $wrap.find('#'+id);
        if (!$p.length) return;

        const $head = $('<button type="button" class="wc-acc-head" aria-expanded="false"></button>')
          .attr('aria-controls', id)
          .append('<span class="wc-acc-title">'+text+'</span>');

        $p.before($head);
      });

      // פתיחה ראשונית: הפעיל אם קיים, אחרת הראשון
      const initialId = $wrap.find('.wc-tabs li.active a').attr('aria-controls')
                      || $wrap.find('.wc-tab').first().attr('id');
      openPanel($wrap, initialId, false);
    }

    function destroyAccordion($wrap){
      if (!$wrap.data('acc-init')) return;
      $wrap.find('.wc-acc-head').remove();
      // החזר להצגת פאנל הטאב הפעיל בלבד (כמו ברירת־המחדל של Woo)
      const id = $wrap.find('.wc-tabs li.active a').attr('aria-controls')
              || $wrap.find('.wc-tab').first().attr('id');
      $wrap.find('.wc-tab').each(function(){
        $(this).toggle($(this).attr('id') === id).removeClass('is-open');
      });
      $wrap.removeData('acc-init');
    }

    function apply(e){
      $('.wc-tabs-wrapper').each(function(){
        e.matches ? buildAccordion($(this)) : destroyAccordion($(this));
      });
    }

    // הפעלה ראשונית + האזנה לשינויים (כולל resize/orientationchange)
    apply(mq);
    if (mq.addEventListener) mq.addEventListener('change', apply);
    else mq.addListener(apply); // תמיכה בדפדפנים ישנים

    // פתיחה באקורדיון
    $(document).on('click', '.wc-acc-head', function(){
      const $wrap = $(this).closest('.wc-tabs-wrapper');
      const id    = $(this).attr('aria-controls');
      const isOpen = $(this).attr('aria-expanded') === 'true';
      if (!isOpen) openPanel($wrap, id, true);
      // אם תרצה לאפשר סגירה מלאה בקליק חוזר: הוצא את התנאי ובצע כאן slideUp לפאנל הנוכחי.
    });

    // סנכרון לחיצה על טאבים (בדסקטופ, וליתר ביטחון גם במובייל)
    $(document).on('click', '.wc-tabs a[aria-controls]', function(e){
      const id   = $(this).attr('aria-controls');
      const $wrap= $(this).closest('.wc-tabs-wrapper');
      if (mq.matches){
        e.preventDefault(); // מונע קפיצת עוגן במובייל
        openPanel($wrap, id, false);
      } else {
        // בדסקטופ – תן ל-Woo לעשות את שלו, אבל נוודא רק את ההצגה
        $wrap.find('.wc-tab').hide();
        $wrap.find('#'+id).show();
      }
    });
  });


})(jQuery);