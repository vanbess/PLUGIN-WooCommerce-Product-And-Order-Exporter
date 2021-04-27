<?php

/**
 * Attaches order country to shipping method acronym pre export
 *
 * @param object $order
 * @return void
 */
function sbwc_opf_add_country_to_shipping($order)
{
    $country = $order->get_shipping_country();
    $shipping_method = $order->get_shipping_method();
    $payment_method = $order->get_payment_method();

    if ((stripos($shipping_method, "DHL") !== false) || (stripos($shipping_method, "Expedited") !== false)) {
        return "DHL-" . $country;
    }

    if (stripos($payment_method, "cod") !== false) {
        return "COD-" . $country;
    }

    return $country;
}
