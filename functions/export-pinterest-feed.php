<?php

/**
 * Export Pinterest product feed
 */

function sbwc_opf_pinterest_feed_export()
{ ?>
    <div id="sbwc_pinterest_feed_export_options">

        <!-- product language select -->
        <div class="sbwc-opf-input-cont">
            <label for="product-lang-pr"><?php echo __('Product language'); ?></label>
            <?php $langs = pll_languages_list(); ?>
            <select name="product-lang-pr" id="product-lang-pr">
                <?php foreach ($langs as $lang) : ?>
                    <option value="<?php echo $lang; ?>"><?php echo strtoupper($lang); ?></option>
                <?php endforeach; ?>
            </select>
            <span class="sbwc-opf-help"><?php echo __('Select which language you would like to export products for'); ?></span>
        </div>

        <!-- specific product ids -->
        <div class="sbwc-opf-input-cont">
            <label for="product-ids-pr"><?php echo __('Product IDs'); ?></label>
            <select name="product-ids-pr[]" id="product-ids-pr" multiple="multiple"></select>
            <span class="sbwc-opf-help"><?php echo __('Select product IDs you would like to include'); ?></span>
        </div>

        <!-- product category select -->
        <div class="sbwc-opf-input-cont">
            <label for="cat-select-pr"><?php echo __('Product categories'); ?></label>
            <select name="cat-select-pr[]" id="cat-select-pr" multiple="multiple"></select>
            <span class="sbwc-opf-help"><?php echo __('Select product categories you would like to export'); ?></span>
        </div>

        <!-- include skus -->
        <div class="sbwc-opf-input-cont">
            <label for="include-skus-pr"><?php echo __('Include SKUs'); ?></label>
            <select name="include-skus-pr[]" id="include-skus-pr" multiple="multiple"></select>
            <span class="sbwc-opf-help"><?php echo __('Select SKUs you would like to include'); ?></span>
        </div>

        <!-- include product titles -->
        <div class="sbwc-opf-input-cont">
            <label for="include-titles-pr"><?php echo __('Include titles'); ?></label>
            <select name="include-titles-pr[]" id="include-titles-pr" multiple="multiple"></select>
            <span class="sbwc-opf-help"><?php echo __('Select product titles you would like to include'); ?></span>
        </div>

        <div class="sbwc-opf-input-cont" id="sbwc-opf-pr-fetched" style="display: none;">

        </div>

        <!-- process export -->
        <div class="sbwc-opf-input-cont">
            <button id="sbwc-opf-fetch-pr" class="button button-primary" title="<?php echo __('Fetch products based on filters'); ?>"><?php echo __('Fetch'); ?></button>

            <form action="" method="post">
                <button id="sbwc-opf-export-pr" disabled type="submit" name="pr-prod-ids" value="" class="button button-primary" title="<?php echo __('Perform fetch before attempting to export'); ?>"><?php echo __('Export'); ?></button>
            </form>
        </div>

        <?php
        if (isset($_POST['pr-prod-ids'])) {

            // get product ids submitted for export 
            $exp_ids = $_POST['pr-prod-ids'];
            $id_arr = explode(',', $exp_ids);

            // build CSV header array
            $prfeed_csv_heads = array(
                'id',
                'item_group_id',
                'title',
                'description',
                'link',
                'image_link',
                'price',
                'availability',
                'condition',
                'google_product_category',
                'product_type',
                'additional_image_link',
                'sale_price',
                'brand',
                'gender',
                'age_group',
                'size',
                'size_type',
                'shipping',
                'custom_label_0',
                'adwords_redirect',
            );

            // setup initial csv export data
            $pr_export_data = [
                $prfeed_csv_heads
            ];

            // loop through order ids and get required order data
            foreach ($id_arr as $id) :

                // get product object
                $product = wc_get_product($id);

                // get product lang
                $prod_lang = pll_get_post_language($id);

                // id
                $id = ($product->get_sku()) ? $product->get_sku() : $id;

                // item group id
                if ($product->get_parent_id()) {
                    $product_id = $product->get_parent_id();
                    $product    = wc_get_product($product_id);
                } else {
                    $product_id = $id;
                }
                $item_group_id = substr(md5($product_id . $product->post_title), 0, 16);

                // title
                $title = $product->get_title();

                // description
                $description = $product->get_short_description();

                // link
                $link = $product->get_permalink();

                // image link
                $image_id = $product->get_image_id();
                $image_link = wp_get_attachment_image_url($image_id);

                // price
                $price = ($product->get_regular_price()) ? $product->get_regular_price() : $product->get_price();

                // availability
                $availability = $product->get_stock_status();

                // condition
                $condition = 'New';

                // google product category (empty)
                $google_product_category = '';

                // product type
                $product_terms = get_the_terms($id, 'product_cat');

                if ($product_terms) {
                    foreach ($product_terms as $term_obj) {
                        if (!empty($term_obj->parent)) {
                            $product_type = $term_obj->name;
                        }
                    }
                }

                // get first gallery image url if present for additional_image_link
                $gall_ids = $product->get_gallery_image_ids();
                $additional_image_link = '';
                if (!empty($gall_ids) && is_array($gall_ids)) {
                    $count = count($gall_ids);
                    $counter = 1;
                    foreach ($gall_ids as $gid) {
                        if($counter<$count){
                            $additional_image_link .= wp_get_attachment_image_url($gid, 'full') . ',';
                        }else{
                            $additional_image_link .= wp_get_attachment_image_url($gid, 'full');
                        }
                        $counter++;
                    }
                } else {
                    $additional_image_link = '';
                }

                // sale price
                $sale_price = ($product->get_sale_price()) ? $product->get_sale_price() : 'N/A';

                // brand
                $brand = get_bloginfo();

                // gender
                $gender = '';

                // age group
                $age_group = '';

                // size
                $size = '';

                // size type
                $size_type = '';

                // shipping
                $shipping = '';

                // custom label 1
                $custom_label_1 = 'Bestseller';

                // adwords redirect
                $adwords_redirect = $link . '?utm_source=Pinterest&utm_campaign=shopping';

                // build array
                $prod_data_arr = [
                    'id'                      => $id,
                    'item_group_id'           => $item_group_id,
                    'title'                   => $title,
                    'description'             => $description,
                    'link'                    => $link,
                    'image_link'              => $image_link,
                    'price'                   => $price,
                    'availability'            => $availability,
                    'condition'               => $condition,
                    'google_product_category' => $google_product_category,
                    'product_type'            => $product_type,
                    'additional_image_link'   => $additional_image_link,
                    'sale_price'              => $sale_price,
                    'brand'                   => $brand,
                    'gender'                  => $gender,
                    'age_group'               => $age_group,
                    'size'                    => $size,
                    'size_type'               => $size_type,
                    'shipping'                => $shipping,
                    'custom_label_0'          => $custom_label_1,
                    'adwords_redirect'        => $adwords_redirect,
                ];

                // push
                array_push($pr_export_data, $prod_data_arr);

            endforeach;

            // export
            $file_name = 'pinterest_export_' . strtotime('now') . '_' . strtoupper($prod_lang) . '.csv';
            $file_path = SBWC_PO_PATH . 'exports/pr/' . $file_name;
            $file_url  = SBWC_PO_URL . 'exports/pr/' . $file_name;
            $csv_file  = fopen($file_path, 'w');
            /**
             * loop through array  
             */
            foreach ($pr_export_data as $line) {
                /**
                 * default php csv handler 
                 **/
                fputcsv($csv_file, $line);
            }
            fclose($csv_file);

        ?>
            <a id="sbwc-opf-dl-pinterest-csv" href="<?php echo $file_url; ?>" download="<?php echo $file_name; ?>">Download CSV</a>

            <script>
                jQuery(function($) {

                    $('.sbwc-opf-nav-link').each(function(index, element) {
                        var target = $(this).data('target');
                        if (target === 'sbwc-export-pinterest') {
                            $(this).addClass('sbwc-opf-active');
                        } else {
                            $(this).removeClass('sbwc-opf-active');
                        }
                    });

                    $('.sbwc-opf-nav-data').each(function(index, element) {
                        var data_id = $(this).attr('id');

                        if (data_id === 'sbwc-export-pinterest') {
                            $(this).removeClass('hidden');
                        } else {
                            $(this).addClass('hidden');
                        }
                    });
                });
            </script>

        <?php }
        ?>

    </div>
<?php }

?>