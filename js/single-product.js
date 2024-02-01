(function ($) {
  $('.material__options label').on('click', function () {
    $('.material__options label').removeClass('checked');
    $(this).addClass('checked');
  });
  $('.frame-color__options label').on('click', function () {
    $('.frame-color__options label').removeClass('checked');
    $(this).addClass('checked');
  });

  $('.order__button').on('click', function () {
    let material = $('.material__options .checked input').val();
    let frameColor = $('.frame-color__options .checked input').val();
    $('.info__material .material__text').text(material);
    $('.info__frame-color .frame-color__text').text(frameColor);

    $('input[type="hidden"][name="material"]').val(material);
    $('input[type="hidden"][name="frame-color"]').val(frameColor);
    $('input[type="hidden"][name="product-name"]').val($('.info__product-name').text());
    $('input[type="hidden"][name="product-sku"]').val($('.info__product-sku').text().split(': ')[1]);

    // Move close button to modal
    const moveBtnInterval = setInterval(() => {
      const orderFormHeader = $('#orderForm .order-form__header');
      const closeBtn = $('.mfp-ready .mfp-close');
      if (orderFormHeader.length > 0 && closeBtn.length > 0) {
        $('.order-form__header').append($('.mfp-ready .mfp-close'));
        clearInterval(moveBtnInterval);
      }
    }, 200);
  });
})(jQuery);
