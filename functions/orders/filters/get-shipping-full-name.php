<?php

/**
 * Returns full shipping name for particular order
 *
 * @param object $order
 * @return void
 */
function sbwc_opf_get_ship_label_full_name($order)
{
    $fname   = $order->get_shipping_first_name();
    $lname   = $order->get_shipping_last_name();
    $country = $order->get_shipping_country();

    $search_chars = array("－", "・");
    $fix_chars = array("-", "·");

    if ($country == "TW") {
        $full_name = str_replace($search_chars, $fix_chars, $lname . $fname);
    } else {
        $full_name = str_replace($search_chars, $fix_chars, $fname . ' ' . $lname);
    }

    return $full_name;
}
