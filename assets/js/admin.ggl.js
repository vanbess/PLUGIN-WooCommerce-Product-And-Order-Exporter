jQuery(document).ready(function ($) {

    // select2
    $('#cat-select-gf, #product-ids-gf, #include-skus-gf, #include-titles-gf').select2();

    // *****************************
    // SET CURRENT PRODUCT LANGUAGE
    // *****************************
    var curr_lang = $('select#product-lang-gf option:selected').val();
    $('select#product-lang-gf').attr('current-lang', curr_lang);

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
        $('#cat-select-gf option, #product-ids-gf option, #include-skus-gf option, #include-titles-gf option').each(function (index, element) {
            $(element).remove();
        });

        var cats   = response.data.cats;
        var prods  = response.data.prods;
        var skus   = response.data.skus;
        var titles = response.data.titles;
        var lang   = response.data.lang;

        // set language options for export
        $('#gf-lang-code').val(lang.code);
        $('#gf-lang').val(lang.name);

        // set product categories for selected language
        $.each(cats, function (cat_id, cat_name) {
            $('#cat-select-gf').append('<option value="' + cat_id + '">' + cat_name + '</option>');
        });

        // set product ids for selected product ids
        $.each(prods, function (id_no, id_act) {
            $('#product-ids-gf').append('<option value="' + id_act + '">' + id_act + '</option>');
        });

        // set product skus
        $.each(skus, function (sku_id, sku) {
            $('#include-skus-gf').append('<option value="' + sku['prod_id'] + '">' + sku['prod_sku'] + '</option>');
        });

        // set product titles
        $.each(titles, function (tit_id, title) {
            $('#include-titles-gf').append('<option value="' + title['prod_id'] + '">' + title['prod_title'] + '</option>');
        });
    });

    // ****************************************************
    // FETCH UPDATED PRODUCT ATTRIBUTES ON LANGUAGE CHANGE
    // ****************************************************
    $('select#product-lang-gf').change(function (e) {
        e.preventDefault();

        var data = {
            'action': 'get_product_per_language',
            '_ajax_nonce': $('#ajax_nonce').val(),
            'lang': $(this).val()
        };
        $.post(ajaxurl, data, function (response) {

            // clear all current options if any
            $('#cat-select-gf option, #product-ids-gf option, #include-skus-gf option, #include-titles-gf option').each(function (index, element) {
                $(element).remove();
            });

            var cats   = response.data.cats;
            var prods  = response.data.prods;
            var skus   = response.data.skus;
            var titles = response.data.titles;
            var lang   = response.data.lang;

            // set language options for export
            $('#gf-lang-code').val(lang.code);
            $('#gf-lang').val(lang.name);

            // set product categories for selected language
            $.each(cats, function (cat_id, cat_name) {
                $('#cat-select-gf').append('<option value="' + cat_id + '">' + cat_name + '</option>');
            });

            // set product ids for selected product ids
            $.each(prods, function (id_no, id_act) {
                $('#product-ids-gf').append('<option value="' + id_act + '">' + id_act + '</option>');
            });

            // set product skus
            $.each(skus, function (sku_id, sku) {
                $('#include-skus-gf').append('<option value="' + sku['prod_id'] + '">' + sku['prod_sku'] + '</option>');
            });

            // set product titles
            $.each(titles, function (tit_id, title) {
                $('#include-titles-gf').append('<option value="' + title['prod_id'] + '">' + title['prod_title'] + '</option>');
            });
        });
    });

    // **************************************************************************************************************
    // FETCH SAMPLE PRODUCT EXPORT LIST PRIOR TO EXPORT (NOT REQUIRED BUT HANDY TO CHECK FILTER RESULTS ARE CORRECT)
    // **************************************************************************************************************
    $('#sbwc-opf-fetch-gf').click(function (e) {
        e.preventDefault();

        var data = {
            'action'     : 'sbwc_opf_fetch_products',
            '_ajax_nonce': $('#ajax_nonce').val(),
            'prod_ids'   : $('#product-ids-gf').val(),
            'prod_cats'  : $('#cat-select-gf').val(),
            'prod_skus'  : $('#include-skus-gf').val(),
            'prod_titles': $('#include-titles-gf').val(),
        };

        $.post(ajaxurl, data, function (response) {
            var prod_data = response.data;
            var prod_ids  = []

            $('div#sbwc-opf-gf-fetched').show();
            $('div#sbwc-opf-gf-fetched').empty();

            $.each(prod_data, function (indexInArray, data) {

                prod_ids.push(data['prod_id']);

                var append = '<div class="sbwc_opf_fetch_preview">';
                append += '<img src="' + data['thumb_src'] + '">';
                append += '<span>' + data['title'] + '</span>';
                append += '<span>' + data['sku'] + '</span>';
                append += '</div>';

                $('div#sbwc-opf-gf-fetched').append(append);
            });

            $('button#sbwc-opf-export-gf').val(prod_ids);
            $('button#sbwc-opf-export-gf').attr('disabled', false);
            $('button#sbwc-opf-export-gf').attr('title', '');

        });

    });
});