<?php

/**
 * Performs order export
 */

// required filters
require SBWC_PO_PATH . 'functions/orders/filters/fix-jap-address.php';
require SBWC_PO_PATH . 'functions/orders/filters/fix-jap-characters.php';
require SBWC_PO_PATH . 'functions/orders/filters/fix-jap-phone.php';
require SBWC_PO_PATH . 'functions/orders/filters/fix-postcode.php';
require SBWC_PO_PATH . 'functions/orders/filters/get-country-with-shipping.php';
require SBWC_PO_PATH . 'functions/orders/filters/get-full-address.php';
require SBWC_PO_PATH . 'functions/orders/filters/get-full-state.php';
require SBWC_PO_PATH . 'functions/orders/filters/get-shipping-full-name.php';
require SBWC_PO_PATH . 'functions/orders/filters/label-all-sku-notes.php';
require SBWC_PO_PATH . 'functions/csv-functions/array2csv.php';
require SBWC_PO_PATH . 'functions/csv-functions/download-send-headers.php';

function sbwc_po_order_options()
{
?>

    <div id="sbwc_order_export_options">

        <!-- order status select -->
        <div class="sbwc-opf-input-cont">
            <label for="status"><?php _e('Order status') ?></label>
            <select name="status" id="status">
                <?php
                // get current order statuses and populate options
                $order_statuses = wc_get_order_statuses();
                foreach ($order_statuses as $key => $name) : ?>
                    <option value="<?php echo $key; ?>"><?php echo __($name); ?></option>
                <?php endforeach; ?>
            </select>
            <span class="sbwc-opf-help"><?php echo __('Select order statuses you would like to export. Select Any to export all order statuses.'); ?></span>
        </div>

        <!-- specific order ids -->
        <div class="sbwc-opf-input-cont">
            <label for="order-ids"><?php echo __('Order IDs'); ?></label>
            <select name="order-ids" id="order-ids" multiple>

            </select>
            <span class="sbwc-opf-help"><?php echo __('Specify a specific set of order IDs to export. Leave empty to export all matching orders.'); ?></span>
        </div>

        <!-- date from -->
        <div class="sbwc-opf-input-cont">
            <label for="order-date-from"><?php echo __('From date'); ?></label>
            <input type="date" name="order-date-from" id="order-date-from">
            <span class="sbwc-opf-help"><?php echo __('Specify start date to export from. Leave empty to disable.'); ?></span>
        </div>

        <!-- date to -->
        <div class="sbwc-opf-input-cont">
            <label for="order-date-to"><?php echo __('To date'); ?></label>
            <input type="date" name="order-date-to" id="order-date-to">
            <span class="sbwc-opf-help"><?php echo __('Specify end date to export to. Leave empty to disable.'); ?></span>
        </div>

        <!-- from order id -->
        <div class="sbwc-opf-input-cont">
            <label for="from-order-id"><?php echo __('From order ID'); ?></label>

            <select name="from-order-id" id="from-order-id">
                <option value=""><?php echo __('please select...'); ?></option>
                <?php
                $orders = wc_get_orders(['posts_per_page' => -1]);
                if ($orders) :
                    foreach ($orders as $obj) : ?>
                        <option value="<?php echo $obj->id; ?>"><?php echo $obj->id; ?></option>
                <?php endforeach;
                endif;
                ?>
            </select>
            <span class="sbwc-opf-help"><?php echo __('Specify order ID to start exporting from. Previous orders will be excluded. Leave empty to disable.'); ?></span>
        </div>

        <!-- fetched -->
        <div class="sbwc-opf-input-cont" id="sbwc-opf-fetched-orders">
            <table id="sbwc-opf-fetched-orders-table" style="display: none;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Order No</th>
                        <th>Date</th>
                        <th>Client</th>
                        <th>Location</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

        <!-- fetch/export -->
        <div class="sbwc-opf-input-cont">
            <button id="sbwc-opf-fetch-orders" class="button button-primary" title="<?php echo __('Fetch filtered orders'); ?>"><?php echo __('Fetch'); ?></button>

            <form action="" method="post">
                <input type="hidden" id="order-exp-ids" name="order-exp-ids" value="">
                <button id="sbwc-opf-export-orders" disabled class="button button-primary" title="<?php echo __('Fetch orders before attempting to export'); ?>"><?php echo __('Export'); ?></button>
            </form>
        </div>

        <?php
        if (isset($_POST['order-exp-ids'])) :

            // get order ids submitted for export 
            $exp_ids = $_POST['order-exp-ids'];
            $id_arr = explode(',', $exp_ids);

            // build CSV header array
            $order_csv_heads = array(
                'ID',
                'Name',
                'Shipping Name',
                'Shipping Street',
                'Shipping Address1',
                'Shipping Address2',
                'Shipping Company',
                'Shipping City',
                'Shipping Zip',
                'Shipping Province',
                'Shipping Country',
                'Shipping Phone',
                'Dialling Code',
                'Lineitem sku',
                'Lineitem quantity',
                'Order Total',
                'Order Currency',
            );

            // setup initial csv export data
            $order_export_data = [
                $order_csv_heads
            ];

            // loop through order ids and get required order data
            foreach ($id_arr as $order_id) :

                // get order object
                $order = wc_get_order($order_id);

                // get user ip so that we can get correct country dialling code for phone number provided
                $user_ip = $order->get_customer_ip_address();
                $url = "https://ipapi.co/$user_ip/country_calling_code/";
                $dialling_code = wp_remote_get($url);

                // get data bits
                $id             = $order->get_order_number();
                $name           = sbwc_opf_get_ship_label_full_name($order);
                $ship_name      = sbwc_opf_get_full_order_address($order);
                $ship_addr_1    = sbwc_opf_fix_jap_order_address($order);
                $ship_addr_2    = $order->get_shipping_address_2();
                $ship_company   = $order->get_shipping_company();
                $ship_city      = $order->get_shipping_city();
                $ship_pcode     = sbwc_opf_fix_order_post_code($order);
                $ship_province  = sbwc_get_full_order_state($order);
                $ship_country   = sbwc_opf_add_country_to_shipping($order);
                $ship_phone     = $order->get_billing_phone();
                $phone_prefix   = $dialling_code['body'];
                $line_item_sku  = sbwc_opf_label_sku_notes($order);
                $line_item_qty  = 1;
                $order_total    = $order->get_total();
                $order_currency = $order->get_currency();

                // build array
                $data_arr = [
                    'ID'                => $id,
                    'Name'              => $name,
                    'Shipping Name'     => $ship_name,
                    'Shipping Address1' => $ship_addr_1,
                    'Shipping Address2' => $ship_addr_2,
                    'Shipping Company'  => $ship_company,
                    'Shipping City'     => $ship_city,
                    'Shipping Zip'      => $ship_pcode,
                    'Shipping Province' => $ship_province,
                    'Shipping Country'  => $ship_country,
                    'Shipping Phone'    => $ship_phone,
                    'Dialling Code'     => $phone_prefix,
                    'Lineitem sku'      => $line_item_sku,
                    'Lineitem quantity' => $line_item_qty,
                    'Order Total'       => $order_total,
                    'Order Currency'    => $order_currency,
                ];

                // push
                array_push($order_export_data, $data_arr);

            endforeach;

            // export
            $file_name = 'order_export_' . strtotime('now') . '.csv';
            $file_path = SBWC_PO_PATH . 'exports/orders/' . $file_name;
            $file_url  = SBWC_PO_URL . 'exports/orders/' . $file_name;
            $csv_file  = fopen($file_path, 'w');
            /**
             * loop through array  
             */
            foreach ($order_export_data as $line) {
                /**
                 * default php csv handler 
                 **/
                fputcsv($csv_file, $line);
            }
            fclose($csv_file);

        ?>
            <a id="sbwc-opf-dl-order-csv" href="<?php echo $file_url; ?>" download="<?php echo $file_name; ?>">Download CSV</a>
        <?php endif;
        ?>

    </div>

<?php }

?>