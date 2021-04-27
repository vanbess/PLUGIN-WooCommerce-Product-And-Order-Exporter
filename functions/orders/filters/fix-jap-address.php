<?php

/**
 * Fixes any potential isses with Japanese shipping address and returns fixed address
 *
 * @param object $order
 * @return void
 */
function sbwc_opf_fix_jap_order_address($order)
{
    if ($order->get_shipping_country() == "JP") {
        $return_address = $order->get_shipping_city() . " " . $order->get_shipping_address_1();
    } else {
        $return_address = $order->get_shipping_address_1();
    }

    $search_chars = array("ー", "－", "・");
    $fix_chars = array("-", "-", "·");
    $return_address_final = trim(str_replace($search_chars, $fix_chars, $return_address));
    return $return_address_final;
}
