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
  $(function(){

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

  });
})(jQuery);