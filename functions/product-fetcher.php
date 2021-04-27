<?php

/**
 * Fetches product based on supplied filters
 */
add_action('wp_ajax_sbwc_opf_fetch_products', 'sbwc_opf_fetch_products');
add_action('wp_ajax_nopriv_sbwc_opf_fetch_products', 'sbwc_opf_fetch_products');
function sbwc_opf_fetch_products()
{
    check_ajax_referer('gimme some exports');

    // variables
    $ids    = $_POST['prod_ids'];
    $cats   = $_POST['prod_cats'];
    $skus   = $_POST['prod_skus'];
    $titles = $_POST['prod_titles'];

    // query submitted cats
    if ($cats) :
        $catq = new WP_Query([
            'post_type' => "product",
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'term_id',
                    'terms'    => $cats,
                    'compare' => 'IN'
                ),
            ),
        ]);

        if ($catq->have_posts()) :
            while ($catq->have_posts()) : $catq->the_post();
                $fin_ids[] = intval($catq->post->ID);
            endwhile;
            wp_reset_postdata();
        endif;
    endif;

    // if ids present
    if ($ids) :
        foreach ($ids as $id) :
            $fin_ids[] = intval($id);
        endforeach;
    endif;

    // if skus present
    if ($skus) :
        foreach ($skus as $id) :
            $fin_ids[] = intval($id);
        endforeach;
    endif;

    // if titles present
    if ($titles) :
        foreach ($titles as $id) :
            $fin_ids[] = intval($id);
        endforeach;
    endif;

    // final, filtered product id array
    $fin_ids = array_unique($fin_ids);

    // loop through filtered prod ids and get some general info for user's reference
    $counter = 0;
    foreach ($fin_ids as $id) :

        // get img src
        $thumbnail_id = get_post_thumbnail_id($id);
        $thumb_src = wp_get_attachment_image_url($thumbnail_id);

        // get title
        $title = get_the_title($id);

        // get sku
        $sku = get_post_meta($id, '_sku', true);

        $prod_data[$counter] = [
            'prod_id' => $id,
            'thumb_src' => $thumb_src,
            'title' => $title,
            'sku' => $sku
        ];
        $counter++;
    endforeach;

    // return data
    if ($prod_data) :
        wp_send_json_success($prod_data);
    else :
        wp_send_json_error('prod data retrieval failed.');
    endif;

    wp_die();
}
