<?php

// required functions
include SBWC_PO_PATH . 'functions/product-filter.php';
include SBWC_PO_PATH . 'functions/product-fetcher.php';
include SBWC_PO_PATH . 'functions/orders/order-filter.php';
include SBWC_PO_PATH . 'functions/orders/export-orders.php';
include SBWC_PO_PATH . 'functions/export-facebook-feed.php';
include SBWC_PO_PATH . 'functions/export-google-feed.php';
include SBWC_PO_PATH . 'functions/export-pinterest-feed.php';

/**
 * Sets up admin dashboard for exports
 */
function sbwc_po_feed_export_page()
{
    add_menu_page(
        __('Order/Feed Export', 'woocommerce'),
        'Order/Feed Export',
        'manage_options',
        'sbwc-order-feed-export',
        'sbwc_render_po_feed_export_page',
        'dashicons-randomize',
        25
    );
}

add_action('admin_menu', 'sbwc_po_feed_export_page');

// callback/display function
function sbwc_render_po_feed_export_page()
{ ?>
    <div id="sbwc-order-product-feed-export-cont">
        <div id="sbwc-opf-inner-conter">

            <h2>SBWC Order & Product Feed Export</h2>

            <!-- nav -->
            <ul id="sbwc-opf-nav">
                <li class="sbwc-opf-nav-link sbwc-opf-active" data-target="sbwc-export-orders">
                    <a href="javascript:void(0);">Orders</a>
                </li>
                <li class="sbwc-opf-nav-link" data-target="sbwc-export-google">
                    <a href="javascript:void(0);">Google Feed</a>
                </li>
                <li class="sbwc-opf-nav-link" data-target="sbwc-export-facebook">
                    <a href="javascript:void(0);">Facebook Feed</a>
                </li>
                <li class="sbwc-opf-nav-link" data-target="sbwc-export-pinterest">
                    <a href="javascript:void(0);">Pinterest Feed</a>
                </li>
            </ul>

            <div id="sbwc-opf-nav-content">

                <input type="hidden" id="ajax_nonce" value="<?php echo wp_create_nonce('gimme some exports'); ?>">

                <!-- orders -->
                <div id="sbwc-export-orders" class="sbwc-opf-nav-data">
                    <?php
                    sbwc_po_order_options();
                    ?>
                </div>

                <!-- google -->
                <div id="sbwc-export-google" class="sbwc-opf-nav-data hidden">
                    <?php
                    sbwc_opf_google_feed_export();
                    ?>
                </div>

                <!-- facebook -->
                <div id="sbwc-export-facebook" class="sbwc-opf-nav-data hidden">
                    <?php
                    sbwc_opf_export_fb_feed();
                    ?>
                </div>

                <!-- pinterest -->
                <div id="sbwc-export-pinterest" class="sbwc-opf-nav-data hidden">
                    <?php
                    sbwc_opf_pinterest_feed_export();
                    ?>
                </div>

            </div>
        </div>
    </div>
<?php }

?>