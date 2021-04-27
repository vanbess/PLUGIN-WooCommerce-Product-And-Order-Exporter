<?php

/**
 * Filters orders prior to export
 */

// fetch and return initial order IDs
add_action('wp_ajax_sbwc_opf_fetch_filter_orders', 'sbwc_opf_fetch_filter_orders');
add_action('wp_ajax_nopriv_sbwc_opf_fetch_filter_orders', 'sbwc_opf_fetch_filter_orders');
function sbwc_opf_fetch_filter_orders()
{

    check_ajax_referer('gimme some exports');

    // ORDER FILTER BY STATUS
    if (isset($_POST['status_filter'])) :

        $orders = wc_get_orders(['posts_per_page' => -1, 'post_status' => $_POST['status']]);

        foreach ($orders as $order) :
            $order_ids[] = $order->id;
        endforeach;

        if ($order_ids) :
            wp_send_json_success($order_ids);
        else :
            wp_send_json_error();
        endif;

    endif;

    // INITIAL ORDER FILTER
    if (isset($_POST['initial_filter'])) :

        $orders = wc_get_orders(['posts_per_page' => -1]);

        foreach ($orders as $order) :
            $order_ids[] = $order->id;
        endforeach;

        if ($order_ids) :
            wp_send_json_success($order_ids);
        else :
            wp_send_json_error();
        endif;

    endif;

    // FETCH
    if (isset($_POST['fetch'])) :

        // vars
        $ids       = $_POST['order_ids'];
        $date_from = $_POST['date_from'];
        $date_to   = $_POST['date_to'];
        $from_id   = $_POST['from_id'];

        // status, date to and date from
        $orders = wc_get_orders(
            [
                'posts_per_page' => -1,
                'post_status'    => $_POST['status'],
                'date_before'    => $date_to,
                'date_after'     => $date_from
            ]
        );

        // push found ids to array
        foreach ($orders as $order) :
            $order_ids[] = $order->id;
        endforeach;

        // if order ids defined, combine with initial order ids array
        if (!empty($ids)) :
            $order_ids = array_merge($order_ids, $ids);
        endif;

        // if from id present
        if (!empty($from_id)) :
            foreach ($order_ids as $id) :
                if ($id >= $from_id) :
                    $from_ids[] = $id;
                endif;
            endforeach;
        endif;

        // fetch relevant order data for export for $from_ids
        if ($from_ids) :
            foreach ($from_ids as $id) :

                $order_data   = wc_get_order($id);
                $date         = $order_data->get_date_created();
                $value        = $order_data->get_total('view');
                $order_client = $order_data->get_billing_first_name() . ' ' . $order_data->get_billing_last_name();
                $client_loc   = $order_data->get_billing_city() . ', ' . $order_data->get_billing_country();
                $order_no     = $order_data->get_order_number();

                $from_id_data[$id] = [
                    'date'       => date('j F Y', strtotime($date)),
                    'value'      => $value,
                    'client'     => $order_client,
                    'client_loc' => $client_loc,
                    'order_no'   => $order_no
                ];

            endforeach;
        endif;

        // fetch relevant order data for export for $order_ids
        if ($order_ids) :
            foreach ($order_ids as $id) :

                $order_data   = wc_get_order($id);
                $date         = $order_data->get_date_created();
                $value        = $order_data->get_total('view');
                $order_client = $order_data->get_billing_first_name() . ' ' . $order_data->get_billing_last_name();
                $client_loc   = $order_data->get_billing_city() . ', ' . $order_data->get_billing_country();
                $order_no     = $order_data->get_order_number();

                $order_id_data[$id] = [
                    'date'       => date('j F Y', strtotime($date)),
                    'value'      => $value,
                    'client'     => $order_client,
                    'client_loc' => $client_loc,
                    'order_no'   => $order_no
                ];

            endforeach;
        endif;

        // if from id arr, else order id arr
        if ($from_id_data) :
            wp_send_json_success($from_id_data);
        elseif ($order_id_data) :
            wp_send_json_success($order_id_data);
        else :
            wp_send_json_error();
        endif;

    endif;

    wp_die();
}
