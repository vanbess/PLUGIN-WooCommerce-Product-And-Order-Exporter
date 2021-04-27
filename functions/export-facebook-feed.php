<?php

/**
 * Export Facebook product feed
 */

function sbwc_opf_export_fb_feed()
{ ?>

    <div id="sbwc_facebook_feed_export_options">

        <!-- product language select -->
        <div class="sbwc-opf-input-cont">
            <label for="product-lang-fb"><?php echo __('Product language'); ?></label>
            <?php $langs = pll_languages_list(); ?>
            <select name="product-lang-fb" id="product-lang-fb">
                <?php foreach ($langs as $lang) : ?>
                    <option value="<?php echo $lang; ?>"><?php echo strtoupper($lang); ?></option>
                <?php endforeach; ?>
            </select>
            <span class="sbwc-opf-help"><?php echo __('Select which language you would like to export products for'); ?></span>
        </div>

        <!-- specific product ids -->
        <div class="sbwc-opf-input-cont">
            <label for="product-ids-fb"><?php echo __('Product IDs'); ?></label>
            <select name="product-ids-fb[]" id="product-ids-fb" multiple="multiple"></select>
            <span class="sbwc-opf-help"><?php echo __('Select product IDs you would like to include'); ?></span>
        </div>

        <!-- product category select -->
        <div class="sbwc-opf-input-cont">
            <label for="cat-select-fb"><?php echo __('Product categories'); ?></label>
            <select name="cat-select-fb[]" id="cat-select-fb" multiple="multiple"></select>
            <span class="sbwc-opf-help"><?php echo __('Select product categories you would like to export'); ?></span>
        </div>

        <!-- include skus -->
        <div class="sbwc-opf-input-cont">
            <label for="include-skus-fb"><?php echo __('Include SKUs'); ?></label>
            <select name="include-skus-fb[]" id="include-skus-fb" multiple="multiple"></select>
            <span class="sbwc-opf-help"><?php echo __('Select SKUs you would like to include'); ?></span>
        </div>

        <!-- include product titles -->
        <div class="sbwc-opf-input-cont">
            <label for="include-titles-fb"><?php echo __('Include titles'); ?></label>
            <select name="include-titles-fb[]" id="include-titles-fb" multiple="multiple"></select>
            <span class="sbwc-opf-help"><?php echo __('Select product titles you would like to include'); ?></span>
        </div>

        <div class="sbwc-opf-input-cont" id="sbwc-opf-fb-fetched" style="display: none;">

        </div>

        <!-- process export -->
        <div class="sbwc-opf-input-cont">
            <button id="sbwc-opf-fetch-fb" class="button button-primary" title="<?php echo __('Fetch products based on filters'); ?>"><?php echo __('Fetch'); ?></button>

            <form action="" method="post">
                <button id="sbwc-opf-export-fb" disabled type="submit" name="fb-prod-ids" value="" class="button button-primary" title="<?php echo __('Perform fetch before attempting to export'); ?>"><?php echo __('Export'); ?></button>
            </form>
        </div>

        <?php
        if (isset($_POST['fb-prod-ids'])) {

            // get product ids submitted for export 
            $exp_ids = $_POST['fb-prod-ids'];
            $id_arr = explode(',', $exp_ids);

            // build CSV header array
            $fbfeed_csv_heads = array(
                'id',
                'title',
                'description',
                'availability',
                'condition',
                'price',
                'link',
                'image_link',
                'brand',
                'fb_product_category',
                'google_product_category',
                'sale_price',
                'sale_price_effective_date',
                'item_group_id',
                'visibility',
                'additional_image_link',
                'custom_label_0',
                'custom_label_1',
                'custom_label_2',
                'custom_label_3',
                'custom_label_4',
            );

            // setup initial csv export data
            $fb_export_data = [
                $fbfeed_csv_heads
            ];

            // loop through order ids and get required order data
            foreach ($id_arr as $id) :

                // get product object
                $product = wc_get_product($id);

                // get product lang
                $prod_lang = pll_get_post_language($id);

                // id
                $id = ($product->get_sku()) ? $product->get_sku() : $id;

                // title
                $title = $product->get_title();

                // description
                $description = $product->get_short_description();

                // availability
                $availability = $product->get_stock_status();

                // condition
                $condition = 'New';

                // price
                $price = ($product->get_regular_price()) ? $product->get_regular_price() : $product->get_price();

                // link
                $link = $product->get_permalink();

                // image link
                $image_id = $product->get_image_id();
                $image_link = wp_get_attachment_image_url($image_id);

                // brand
                $brand = get_bloginfo();

                // facebook product category
                $fb_product_category = '';

                // google product category
                $google_product_category = '';

                // sale price
                $sale_price = ($product->get_sale_price()) ? $product->get_sale_price() : '';

                // sale price effective date (empty)
                $sale_price_effective_date = '';

                // item group id
                if ($product->get_parent_id()) {
                    $product_id = $product->get_parent_id();
                    $product    = wc_get_product($product_id);
                } else {
                    $product_id = $id;
                }
                $item_group_id = substr(md5($product_id . $product->post_title), 0, 16);

                // visibility (empty)
                $visibility = '';

                // additional image link
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

                // custom label 0 (empty)
                $custom_label_0 = '';

                // custom label 1 (empty)
                $custom_label_1 = '';

                // custom label 2 (empty)
                $custom_label_2 = '';

                // custom label 3 (empty)
                $custom_label_3 = '';

                // custom label 4 (empty)
                $custom_label_4 = '';


                // build array
                $fb_prod_data_arr = [
                    'id'                        => $id,
                    'title'                     => $title,
                    'description'               => $description,
                    'availability'              => $availability,
                    'condition'                 => $condition,
                    'price'                     => $price,
                    'link'                      => $link,
                    'image_link'                => $image_link,
                    'brand'                     => $brand,
                    'fb_product_category'       => $fb_product_category,
                    'google_product_category'   => $google_product_category,
                    'sale_price'                => $sale_price,
                    'sale_price_effective_date' => $sale_price_effective_date,
                    'item_group_id'             => $item_group_id,
                    'visibility'                => $visibility,
                    'additional_image_link'     => $additional_image_link,
                    'custom_label_0'            => $custom_label_0,
                    'custom_label_1'            => $custom_label_1,
                    'custom_label_2'            => $custom_label_2,
                    'custom_label_3'            => $custom_label_3,
                    'custom_label_4'            => $custom_label_4,
                ];

                // push
                array_push($fb_export_data, $fb_prod_data_arr);

            endforeach;

            // export
            $file_name = 'facebook_export_' . strtotime('now') . '_' . strtoupper($prod_lang) . '.csv';
            $file_path = SBWC_PO_PATH . 'exports/fb/' . $file_name;
            $file_url  = SBWC_PO_URL . 'exports/fb/' . $file_name;
            $csv_file  = fopen($file_path, 'w');
            /**
             * loop through array  
             */
            foreach ($fb_export_data as $line) {
                /**
                 * default php csv handler 
                 **/
                fputcsv($csv_file, $line);
            }
            fclose($csv_file);

        ?>
            <a id="sbwc-opf-dl-fb-csv" href="<?php echo $file_url; ?>" download="<?php echo $file_name; ?>">Download CSV</a>

            <script>
                jQuery(function($) {

                    $('.sbwc-opf-nav-link').each(function(index, element) {
                        var target = $(this).data('target');
                        if (target === 'sbwc-export-facebook') {
                            $(this).addClass('sbwc-opf-active');
                        } else {
                            $(this).removeClass('sbwc-opf-active');
                        }
                    });

                    $('.sbwc-opf-nav-data').each(function(index, element) {
                        var data_id = $(this).attr('id');

                        if (data_id === 'sbwc-export-facebook') {
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