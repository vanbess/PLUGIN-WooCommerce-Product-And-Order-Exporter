jQuery(document).ready(function ($) {

    // *******************
    // NAV LINKS ON CLICK
    // *******************
    $('.sbwc-opf-nav-link').each(function (index, element) {

        $(this).on('click', function () {

            $('.sbwc-opf-nav-link').each(function (index, element) {
                $(this).removeClass('sbwc-opf-active');
            });
            $(this).addClass('sbwc-opf-active');

            var target = $(this).data('target');

            $('.sbwc-opf-nav-data').each(function (index, element) {
                var data_id = $(this).attr('id');

                if (data_id === target) {
                    $(this).removeClass('hidden');
                } else {
                    $(this).addClass('hidden');
                }
            });
        });
    });

});