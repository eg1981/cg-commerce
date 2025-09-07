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

    $('body').on('click', '.site-header-cart a.cart-contents', function(e){
        $(this).parent().parent().find('.widget_shopping_cart').toggleClass('active');
    });
  }
})(jQuery);