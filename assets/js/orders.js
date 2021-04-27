jQuery(document).ready(function ($) {

    // SETUP SELECT2
    $('#order-ids').select2({
        placeholder: "please select..."
    });

    // LOAD INITIAL LIST OF ORDERS
    var data = {
        'action': 'sbwc_opf_fetch_filter_orders',
        '_ajax_nonce': $('#ajax_nonce').val(),
        'initial_filter': true
    };
    $.post(ajaxurl, data, function (response) {

        var order_ids = response.data;

        $.each(order_ids, function (i, order_id) {
            $('#order-ids').append('<option value="' + order_id + '">' + order_id + '</option>');
        });
    });

    // LOAD FILTERED LIST OF ORDERS BASED ON STATUS CHANGE
    $('select#status').change(function (e) {
        e.preventDefault();

        var status = $(this).val();

        var data = {
            'action': 'sbwc_opf_fetch_filter_orders',
            '_ajax_nonce': $('#ajax_nonce').val(),
            'status': status,
            'status_filter': true
        };
        $.post(ajaxurl, data, function (response) {

            $('#order-ids').empty();

            var order_ids = response.data;

            $.each(order_ids, function (i, order_id) {
                $('#order-ids').append('<option value="' + order_id + '">' + order_id + '</option>');
            });
        });
    });

    // FETCH FILTERED ORDERS
    $('button#sbwc-opf-fetch-orders').click(function (e) {
        e.preventDefault();

        // vars 
        var status = $('select#status').val();
        var order_ids = $('select#order-ids').val();
        var date_from = $('input#order-date-from').val();
        var date_to = $('input#order-date-to').val();
        var from_id = $('select#from-order-id').val();

        var data = {
            'action': 'sbwc_opf_fetch_filter_orders',
            '_ajax_nonce': $('#ajax_nonce').val(),
            'status': status,
            'order_ids': order_ids,
            'date_from': date_from,
            'date_to': date_to,
            'from_id': from_id,
            'fetch': true
        };
        $.post(ajaxurl, data, function (response) {

            var order_ids = [];

            $('table#sbwc-opf-fetched-orders-table > tbody').empty();

            $.each(response.data, function (order_id, data) {
                order_ids.push(order_id);

                var order_info = '<tr>';
                order_info += '<td>' + order_id + '</td>';
                order_info += '<td>' + data.order_no + '</td>';
                order_info += '<td>' + data.date + '</td>';
                order_info += '<td>' + data.client + '</td>';
                order_info += '<td>' + data.client_loc + '</td>';
                order_info += '<td>' + data.value + '</td>';
                order_info += '</tr>';

                $('table#sbwc-opf-fetched-orders-table > tbody').append(order_info);
            });

            $('table#sbwc-opf-fetched-orders-table').show();
            $('#order-exp-ids').val(order_ids);
            $('#order-exp-ids').attr('disabled', false);
            $('#order-exp-ids').attr('title', '');
        });
    });


});