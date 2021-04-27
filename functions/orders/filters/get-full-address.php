<?php

/**
 * Queries and return properly filtererd order shipping address
 *
 * @param object $order
 * @return void
 */
function sbwc_opf_get_full_order_address($order)
{
    if (!empty(trim($order->get_shipping_address_2()))) {
        if ($order->get_shipping_country() == "JP") {
            $address = trim($order->get_shipping_address_1() . ', ' . $order->get_shipping_address_2());
        } else {
            $address = trim($order->get_shipping_address_1() . ', ' . $order->get_shipping_address_2());
        }
    } else {
        $address = trim($order->get_shipping_address_1());
    }

    $search_chars = array("－", "・");
    $fix_chars    = array("-", "·");
    $address      = str_replace($search_chars, $fix_chars, $address);

    return $address;
}
