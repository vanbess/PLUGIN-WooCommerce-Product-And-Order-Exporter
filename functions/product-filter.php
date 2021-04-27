<?php

/**
 * Filters products prior to export
 */

use Josantonius\LanguageCode\LanguageCode;

add_action('wp_ajax_get_product_per_language', 'get_product_per_language');
add_action('wp_ajax_nopriv_get_product_per_language', 'get_product_per_language');

/**
 * AJAX to retrieve product term ids based on specific PLL language
 *
 * @return json
 */
function get_product_per_language()
{
    check_ajax_referer('gimme some exports');

    // get submitted lang
    $lang = $_POST['lang'];

    // get actual language
    $lang_name = LanguageCode::getLanguageFromCode($lang);

    $lang_data = [
        'code' => $lang,
        'name' => $lang_name
    ];

    // get all products
    $products = wc_get_products([
        'limit' => -1,
        'status' => 'publish'
    ]);

    // get relevant product ids based on specified language
    if ($products) :
        foreach ($products as $product) :
            $filtered_prod_ids[] = pll_get_post($product->id, $lang);
        endforeach;
    endif;

    // get associated terms
    $terms = get_terms(['object_ids' => $filtered_prod_ids]);

    foreach ($terms as $term) :
        if ($term->taxonomy == 'product_cat') :
            $filtered_terms[$term->term_id] = $term->name;
        endif;
    endforeach;

    // get skus
    foreach ($filtered_prod_ids as $prod_id) :
        $product_data = wc_get_product($prod_id);
        if ($product_data->get_sku() !== '') :
            $skus[] = [
                'prod_id' => $product_data->get_id(),
                'prod_sku' => $product_data->get_sku()
            ];
        endif;
    endforeach;

    // get titles
    foreach ($filtered_prod_ids as $prod_id) :
        $product_data = wc_get_product($prod_id);
        $titles[] = [
            'prod_id' => $prod_id,
            'prod_title' => $product_data->get_title()
        ];
    endforeach;

    // return data
    if (!empty($filtered_prod_ids) && !empty($filtered_terms)) :
        $data['prods'] = $filtered_prod_ids;
        $data['cats'] = $filtered_terms;
        $data['skus'] = $skus;
        $data['titles'] = $titles;
        $data['lang'] = $lang_data;
        wp_send_json_success($data, 'success');
    else :
        $data = [];
        wp_send_json_error($data, 'failure');
    endif;

    wp_die();
}
