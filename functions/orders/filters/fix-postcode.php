<?php

/**
 * Filters and returns proper order shipping post codes
 *
 * @param object $order
 * @return void
 */
function sbwc_opf_fix_order_post_code($order)
{
    $postcode = $order->get_shipping_postcode();

    if ($order->get_shipping_country() == "JP") {
        $postcode = (stripos($order->get_shipping_postcode(), "-") !== FALSE) ? $order->get_shipping_postcode() : substr_replace($order->get_shipping_postcode(), "-", 3, 0);
    }

    if ($order->get_shipping_country() == "NL") {
        $postcode = (stripos(trim($order->get_shipping_postcode()), " ") !== FALSE) ? trim($order->get_shipping_postcode()) : substr_replace(trim($order->get_shipping_postcode()), " ", 4, 0);
    }

    if ($order->get_shipping_country() == "SE") {
        $postcode = str_replace(" ", "", trim($order->get_shipping_postcode()));
    }

    return $postcode;
}
