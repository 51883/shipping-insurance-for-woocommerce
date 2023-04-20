jQuery(document).ready(function ($) {
    $('body').on('change', '#shipping_insurance', function () {
        $('body').trigger('update_checkout');
    });
});
