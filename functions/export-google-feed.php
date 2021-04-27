<?php

/**
 * Export Google product feed
 * Render export options
 *
 * @return html
 */
function sbwc_opf_google_feed_export()
{
?>

    <div id="sbwc_google_feed_export_options">

        <!-- product language select -->
        <div class="sbwc-opf-input-cont">
            <label for="product-lang-gf"><?php echo __('Product language'); ?></label>
            <?php $langs = pll_languages_list(); ?>
            <select name="product-lang-gf" id="product-lang-gf">
                <?php foreach ($langs as $lang) : ?>
                    <option value="<?php echo $lang; ?>"><?php echo strtoupper($lang); ?></option>
                <?php endforeach; ?>
            </select>
            <span class="sbwc-opf-help"><?php echo __('Select which language you would like to export products for'); ?></span>
        </div>

        <!-- specific product ids -->
        <div class="sbwc-opf-input-cont">
            <label for="product-ids-gf"><?php echo __('Product IDs'); ?></label>
            <select name="product-ids-gf[]" id="product-ids-gf" multiple="multiple"></select>
            <span class="sbwc-opf-help"><?php echo __('Select product IDs you would like to include'); ?></span>
        </div>

        <!-- product category select -->
        <div class="sbwc-opf-input-cont">
            <label for="cat-select-gf"><?php echo __('Product categories'); ?></label>
            <select name="cat-select-gf[]" id="cat-select-gf" multiple="multiple"></select>
            <span class="sbwc-opf-help"><?php echo __('Select product categories you would like to export'); ?></span>
        </div>

        <!-- include skus -->
        <div class="sbwc-opf-input-cont">
            <label for="include-skus-gf"><?php echo __('Include SKUs'); ?></label>
            <select name="include-skus-gf[]" id="include-skus-gf" multiple="multiple"></select>
            <span class="sbwc-opf-help"><?php echo __('Select SKUs you would like to include'); ?></span>
        </div>

        <!-- include product titles -->
        <div class="sbwc-opf-input-cont">
            <label for="include-titles-gf"><?php echo __('Include titles'); ?></label>
            <select name="include-titles-gf[]" id="include-titles-gf" multiple="multiple"></select>
            <span class="sbwc-opf-help"><?php echo __('Select product titles you would like to include'); ?></span>
        </div>

        <div class="sbwc-opf-input-cont" id="sbwc-opf-gf-fetched" style="display: none;">

        </div>

        <!-- process export -->
        <div class="sbwc-opf-input-cont">
            <button id="sbwc-opf-fetch-gf" class="button button-primary" title="<?php echo __('Fetch products based on filters'); ?>"><?php echo __('Fetch'); ?></button>

            <form action="" method="post">
                <input type="hidden" name="gf-lang-code" id="gf-lang-code" value="">
                <input type="hidden" name="gf-lang" id="gf-lang" value="">
                <button id="sbwc-opf-export-gf" disabled type="submit" name="gf-prod-ids" value=" class=" button button-primary" title="<?php echo __('Preform fetch before attempting to export'); ?>"><?php echo __('Export'); ?></button>
            </form>

        </div>

        <?php
        if (isset($_POST['gf-prod-ids'])) {

            // retrieve submitted prod ids
            $prod_ids = $_POST['gf-prod-ids'];
            $prod_ids = explode(',', $prod_ids);

            // print_r($_POST);


            // setup csv headers
            $gheaders = [
                'id',
                'title',
                'description',
                'link',
                'image_link',
                'additional_image_link',
                'availability',
                'availability_date',
                'sale_price_effective_date',
                'price',
                'sale_price',
                'product_type',
                'google_product_category',
                'brand',
                'mpn',
                'identifier_exists',
                'condition',
                'is_bundle',
                'age_group',
                'color',
                'gender',
                'material',
                'pattern',
                'size',
                'item_group_id',
                'shipping',
                'size_type',
                'size_system',
                'shipping_label',
                'adult',
                'multipack',
                'mobile_link',
                'custom_label_0',
                'custom_label_1',
                'custom_label_2',
                'custom_label_3',
                'custom_label_4',
                'unit_pricing_measure',
                'expiration_date',
                'energy_efficiency_class',
                'promotion_id',
                'adwords_redirect'
            ];

            // setup initial array
            $gfeed_exp_array = [
                $gheaders
            ];

            // product loop to get/generate required export data
            foreach ($prod_ids as $id) {

                // get product object
                $product = wc_get_product($id);

                // id
                $id = ($product->get_sku()) ? $product->get_sku() : $id;

                // title
                $title = $product->get_title();

                // description
                $description = ($product->get_short_description()) ? $product->get_short_description() : '';

                // link
                $link = $product->get_permalink();

                // image link
                $img_id = $product->get_image_id();
                $image_link = wp_get_attachment_image_url($img_id, 'full');

                // additional image(s) link
                $gall_ids = $product->get_gallery_image_ids();
                $additional_image_link = '';
                if (!empty($gall_ids) && is_array($gall_ids)) {
                    $count = count($gall_ids);
                    $counter = 1;
                    foreach ($gall_ids as $gid) {
                        if ($counter < $count) {
                            $additional_image_link .= wp_get_attachment_image_url($gid, 'full') . ',';
                        } else {
                            $additional_image_link .= wp_get_attachment_image_url($gid, 'full');
                        }
                        $counter++;
                    }
                } else {
                    $additional_image_link = '';
                }

                // availability
                $availability = 'in stock';

                // availability date - empty
                $availability_date = '';

                // sale price effective date - empty
                $sale_price_effective_date = '';

                // price
                $price = ($product->get_regular_price()) ? $product->get_regular_price() : $product->get_price();

                // sale price
                $sale_price = ($product->get_sale_price()) ? $product->get_sale_price() : '';

                // product type
                $product_terms = get_the_terms($id, 'product_cat');

                if ($product_terms) {
                    foreach ($product_terms as $term_obj) {
                        if (!empty($term_obj->parent)) {
                            $product_type = $term_obj->name;
                        }
                    }
                }

                // google product category
                $google_product_category = 100;

                // brand
                $brand = get_bloginfo();

                // mpn
                $mpn = ($product->get_sku()) ? $product->get_sku() : $id;

                // identifier exists
                $identifier_exists = 'yes';

                // condition
                $condition = 'new';

                // is bundle
                $is_bundle = 'no';

                // age group - empty
                $age_group = '';

                // color - empty
                $color = '';

                // gender - empty
                $gender = '';

                // material - empty
                $material = '';

                // pattern - empty
                $patern = '';

                // size - empty
                $size = '';

                // item group id
                if ($product->get_parent_id()) {
                    $product_id = $product->get_parent_id();
                    $product    = wc_get_product($product_id);
                } else {
                    $product_id = $id;
                }
                $item_group_id = substr(md5($product_id . $product->post_title), 0, 16);

                // shipping
                $shipping = 0;

                // size type - empty
                $size_type = '';

                // size system - empty
                $size_system = '';

                // shipping label - empty
                $shipping_label = '';

                // adult
                $adult = 'no';

                // multipack - empty
                $multipack = '';

                // mobile link - empty
                $mobile_link = '';

                // custom_label_0 => language
                $custom_label_0 = $_POST['gf-lang'];

                // custom_label_1 => language code
                $custom_label_1 = $_POST['gf-lang-code'];

                // custom_label_2
                $custom_label_2 = $id;

                // custom_label_3 => category names
                foreach ($product_terms as $t_obj) {
                    $custom_label_3[] = $t_obj->name;
                }
                $custom_label_3 = implode(', ', $custom_label_3);

                // custom_label_4 - empty
                $custom_label_4 = '';

                // unit pricing measure - empty
                $unit_pricing_measure = '';

                // expiration date - empty
                $expiration_date = '';

                // energy efficiency class - empty
                $energy_efficiency_class = '';

                // promotion id - empty
                $promotion_id = '';

                // adwords redirect - empty
                $adwords_redirect = '';

                // setup per product data
                $g_prod_data = [
                    'id'                        => $id,
                    'title'                     => $title,
                    'description'               => $description,
                    'link'                      => $link,
                    'image_link'                => $image_link,
                    'additional_image_link'     => $additional_image_link,
                    'availability'              => $availability,
                    'availability_date'         => $availability_date,
                    'sale_price_effective_date' => $sale_price_effective_date,
                    'price'                     => $price,
                    'sale_price'                => $sale_price,
                    'product_type'              => $product_type,
                    'google_product_category'   => $google_product_category,
                    'brand'                     => $brand,
                    'mpn'                       => $mpn,
                    'identifier_exists'         => $identifier_exists,
                    'condition'                 => $condition,
                    'is_bundle'                 => $is_bundle,
                    'age_group'                 => $age_group,
                    'color'                     => $color,
                    'gender'                    => $gender,
                    'material'                  => $material,
                    'pattern'                   => $patern,
                    'size'                      => $size,
                    'item_group_id'             => $item_group_id,
                    'shipping'                  => $shipping,
                    'size_type'                 => $size_type,
                    'size_system'               => $size_system,
                    'shipping_label'            => $shipping_label,
                    'adult'                     => $adult,
                    'multipack'                 => $multipack,
                    'mobile_link'               => $mobile_link,
                    'custom_label_0'            => $custom_label_0,
                    'custom_label_1'            => $custom_label_1,
                    'custom_label_2'            => $custom_label_2,
                    'custom_label_3'            => $custom_label_3,
                    'custom_label_4'            => $custom_label_4,
                    'unit_pricing_measure'      => $unit_pricing_measure,
                    'expiration_date'           => $expiration_date,
                    'energy_efficiency_class'   => $energy_efficiency_class,
                    'promotion_id'              => $promotion_id,
                    'adwords_redirect'          => $adwords_redirect,
                ];

                // push to export array
                array_push($gfeed_exp_array, $g_prod_data);
            }

            // perform export
            // export
            $file_name = 'google_feed_export_' . strtotime('now') . '_' . strtoupper($_POST['gf-lang-code']) . '.csv';
            $file_path = SBWC_PO_PATH . 'exports/google/' . $file_name;
            $file_url  = SBWC_PO_URL . 'exports/google/' . $file_name;
            $csv_file  = fopen($file_path, 'w');

            /**
             * loop through array  
             */
            foreach ($gfeed_exp_array as $line) {
                /**
                 * default php csv handler 
                 **/
                fputcsv($csv_file, $line);
            }
            fclose($csv_file);

        ?>
            <a id="sbwc-opf-dl-google-csv" href="<?php echo $file_url; ?>" download="<?php echo $file_name; ?>">Download CSV</a>

            <script>
                jQuery(function($) {

                    $('.sbwc-opf-nav-link').each(function(index, element) {
                        var target = $(this).data('target');
                        if (target === 'sbwc-export-google') {
                            $(this).addClass('sbwc-opf-active');
                        } else {
                            $(this).removeClass('sbwc-opf-active');
                        }
                    });

                    $('.sbwc-opf-nav-data').each(function(index, element) {
                        var data_id = $(this).attr('id');

                        if (data_id === 'sbwc-export-google') {
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
