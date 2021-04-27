jQuery(document).ready(function ($) {
    
    // select2
    $('#cat-select-pr, #product-ids-pr, #include-skus-pr, #include-titles-pr').select2();

    // *****************************
    // SET CURRENT PRODUCT LANGUAGE
    // *****************************
    var curr_lang = $('select#product-lang-pr option:selected').val();
    $('select#product-lang-pr').attr('current-lang', curr_lang);

    // *********************************
    // FETCH INITIAL PRODUCT ATTRIBUTES
    // *********************************
    var data = {
        'action': 'get_product_per_language',
        '_ajax_nonce': $('#ajax_nonce').val(),
        'lang': curr_lang
    };
    $.post(ajaxurl, data, function (response) {

        // clear all current options if any
        $('#cat-select-pr option, #product-ids-pr option, #include-skus-pr option, #include-titles-pr option').each(function (index, element) {
            $(element).remove();
        });

        var cats   = response.data.cats;
        var prods  = response.data.prods;
        var skus   = response.data.skus;
        var titles = response.data.titles;

        // set product categories for selected language
        $.each(cats, function (cat_id, cat_name) {
            $('#cat-select-pr').append('<option value="' + cat_id + '">' + cat_name + '</option>');
        });

        // set product ids for selected product ids
        $.each(prods, function (id_no, id_act) {
            $('#product-ids-pr').append('<option value="' + id_act + '">' + id_act + '</option>');
        });

        // set product skus
        $.each(skus, function (sku_id, sku) {
            $('#include-skus-pr').append('<option value="' + sku['prod_id'] + '">' + sku['prod_sku'] + '</option>');
        });

        // set product titles
        $.each(titles, function (tit_id, title) {
            $('#include-titles-pr').append('<option value="' + title['prod_id'] + '">' + title['prod_title'] + '</option>');
        });
    });

    // ****************************************************
    // FETCH UPDATED PRODUCT ATTRIBUTES ON LANGUAGE CHANGE
    // ****************************************************
    $('select#product-lang-pr').change(function (e) {
        e.preventDefault();

        var data = {
            'action': 'get_product_per_language',
            '_ajax_nonce': $('#ajax_nonce').val(),
            'lang': $(this).val()
        };
        $.post(ajaxurl, data, function (response) {

            // clear all current options if any
            $('#cat-select-pr option, #product-ids-pr option, #include-skus-pr option, #include-titles-pr option').each(function (index, element) {
                $(element).remove();
            });

            var cats   = response.data.cats;
            var prods  = response.data.prods;
            var skus   = response.data.skus;
            var titles = response.data.titles;

            // set product categories for selected language
            $.each(cats, function (cat_id, cat_name) {
                $('#cat-select-pr').append('<option value="' + cat_id + '">' + cat_name + '</option>');
            });

            // set product ids for selected product ids
            $.each(prods, function (id_no, id_act) {
                $('#product-ids-pr').append('<option value="' + id_act + '">' + id_act + '</option>');
            });

            // set product skus
            $.each(skus, function (sku_id, sku) {
                $('#include-skus-pr').append('<option value="' + sku['prod_id'] + '">' + sku['prod_sku'] + '</option>');
            });

            // set product titles
            $.each(titles, function (tit_id, title) {
                $('#include-titles-pr').append('<option value="' + title['prod_id'] + '">' + title['prod_title'] + '</option>');
            });
        });
    });

    // **************************************************************************************************************
    // FETCH SAMPLE PRODUCT EXPORT LIST PRIOR TO EXPORT (NOT REQUIRED BUT HANDY TO CHECK FILTER RESULTS ARE CORRECT)
    // **************************************************************************************************************
    $('#sbwc-opf-fetch-pr').click(function (e) {
        e.preventDefault();

        var data = {
            'action': 'sbwc_opf_fetch_products',
            '_ajax_nonce': $('#ajax_nonce').val(),
            'prod_ids': $('#product-ids-pr').val(),
            'prod_cats': $('#cat-select-pr').val(),
            'prod_skus': $('#include-skus-pr').val(),
            'prod_titles': $('#include-titles-pr').val(),
        };

        $.post(ajaxurl, data, function (response) {
            var prod_data = response.data;
            var prod_ids = []

            $('div#sbwc-opf-pr-fetched').show();
            $('div#sbwc-opf-pr-fetched').empty();

            $.each(prod_data, function (indexInArray, data) {

                prod_ids.push(data['prod_id']);

                var append = '<div class="sbwc_opf_fetch_preview">';
                append += '<img src="' + data['thumb_src'] + '">';
                append += '<span>' + data['title'] + '</span>';
                append += '<span>' + data['sku'] + '</span>';
                append += '</div>';

                $('div#sbwc-opf-pr-fetched').append(append);
            });

            $('button#sbwc-opf-export-pr').val(prod_ids);
            $('button#sbwc-opf-export-pr').attr('disabled', false);
            $('button#sbwc-opf-export-pr').attr('title', '');

        });

    });
});