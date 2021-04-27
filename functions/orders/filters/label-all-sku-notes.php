<?php

/**
 * Does some sort of filtering on product skus and returns sku and qty json
 *
 * @param object $order
 * @return json
 */
function sbwc_opf_label_sku_notes($order)
{
    $sku = "";
    $product_count = (is_array($order->get_items())) ? count($order->get_items()) : 0;

    foreach ($order->get_items() as $order_item_id => $item) :

        $product = $item->get_product();

        if ($product) :

            // if item count equal to 1
            if ($order->get_item_count() === 1) :

                if (stripos($product->get_sku(), ",") !== FALSE) :

                    $parsedOrdersSkusString = $product->get_sku();
                    $parsedOrdersSkusString = rtrim($parsedOrdersSkusString);
                    $parsedOrdersSkusString = str_replace(', ', ',', $parsedOrdersSkusString);
                    $parsedOrdersSkusWithQuantityArray = explode(',', $parsedOrdersSkusString);

                    array_walk($parsedOrdersSkusWithQuantityArray, 'rtrim');

                    foreach ($parsedOrdersSkusWithQuantityArray as $skuWithQuantityString) :

                        $skuQuantityOpeningBracketPosition = strpos($skuWithQuantityString, '(');
                        $skuQuantityClosingBracketPosition = strpos($skuWithQuantityString, ')');
                        $skuQuantity = substr($skuWithQuantityString, $skuQuantityOpeningBracketPosition + 1, $skuQuantityClosingBracketPosition - $skuQuantityOpeningBracketPosition - 1);
                        $skuQuantity = intval(rtrim($skuQuantity));
                        $actualSku = substr($skuWithQuantityString, 0, $skuQuantityOpeningBracketPosition);
                        $actualSku = rtrim($actualSku);
                        $sku .= $actualSku . "(" . (int)$skuQuantity * ((int)$item['qty'] + (int)$order->get_qty_refunded_for_item($order_item_id)) . "), ";

                    endforeach;
                else :
                    $sku = $product->get_sku() . "(" . $order->get_remaining_refund_items() . ")";
                endif;

            // if item count > 1
            else :
                if (((int)$item['qty'] + (int)$order->get_qty_refunded_for_item($order_item_id)) > 0) :

                    if (stripos($product->get_sku(), ",") !== FALSE) :

                        $parsedOrdersSkusString = $product->get_sku();
                        $parsedOrdersSkusString = rtrim($parsedOrdersSkusString);
                        $parsedOrdersSkusString = str_replace(', ', ',', $parsedOrdersSkusString);
                        $parsedOrdersSkusWithQuantityArray = explode(',', $parsedOrdersSkusString);

                        array_walk($parsedOrdersSkusWithQuantityArray, 'rtrim');

                        foreach ($parsedOrdersSkusWithQuantityArray as $skuWithQuantityString) :

                            $skuQuantityOpeningBracketPosition = strpos($skuWithQuantityString, '(');
                            $skuQuantityClosingBracketPosition = strpos($skuWithQuantityString, ')');
                            $skuQuantity = substr($skuWithQuantityString,$skuQuantityOpeningBracketPosition + 1,$skuQuantityClosingBracketPosition - $skuQuantityOpeningBracketPosition - 1);
                            $skuQuantity = intval(rtrim($skuQuantity));
                            $actualSku = substr($skuWithQuantityString, 0, $skuQuantityOpeningBracketPosition);
                            $actualSku = rtrim($actualSku);
                            $sku .= $actualSku . "(" . (int)$skuQuantity * ((int)$item['qty'] + (int)$order->get_qty_refunded_for_item($order_item_id)) . "), ";
                            
                        endforeach;
                    else :
                        $sku .= $product->get_sku() . "(" . ((int)$item['qty'] + (int)$order->get_qty_refunded_for_item($order_item_id)) . "), ";
                    endif;
                endif;
            endif; // order item count
        else :
            $sku .= "NO PRODUCT";
        endif; // if product
    endforeach;

    $product_count = count(explode(",", rtrim($sku, ", ")));
    return '{' . $product_count . '}' . rtrim($sku, ", ");
}
