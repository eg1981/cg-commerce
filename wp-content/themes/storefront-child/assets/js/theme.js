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
