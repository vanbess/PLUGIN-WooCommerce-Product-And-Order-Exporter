<?php

/** Plugin Name: SBWC Product Feed & Order Exporter
 * Description: Improved version of Grigor Asatryan's Woo Product Feed and Order Exporter plugin.
 * Author: Werner C. Bessinger
 * Version: 1.0.1
 * Text Domain: sbwc-po-exporter
 */

if (!defined('ABSPATH')) :
    exit();
endif;

define('SBWC_PO_PATH', plugin_dir_path(__FILE__));
define('SBWC_PO_URL', plugin_dir_url(__FILE__));
define('SBWC_TXT_DOM', 'sbwc-po-exporter');

add_action('plugins_loaded', 'sbwc_po_init');
function sbwc_po_init()
{
    // lang
    include SBWC_PO_PATH . 'lang/vendor/autoload.php';

    // scripts
    add_action('admin_enqueue_scripts', 'sbwc_po_feed_scripts');
    function sbwc_po_feed_scripts()
    {
        // enqueue admin js
        wp_enqueue_script('sbwc-po-js', SBWC_PO_URL . 'assets/js/admin.js', ['jquery'], '1.0.1', true);
        wp_enqueue_script('sbwc-po-ggl-js', SBWC_PO_URL . 'assets/js/admin.ggl.js', ['jquery'], '1.0.1', true);
        wp_enqueue_script('sbwc-po-fb-js', SBWC_PO_URL . 'assets/js/admin.fb.js', ['jquery'], '1.0.2', true);
        wp_enqueue_script('sbwc-po-pr-js', SBWC_PO_URL . 'assets/js/admin.pr.js', ['jquery'], '1.0.1', true);
        wp_enqueue_script('sbwc-po-orders', SBWC_PO_URL . 'assets/js/orders.js', ['jquery'], '1.0.1', true);

        // enqueue admin css
        wp_enqueue_style('sbwc-po-css', SBWC_PO_URL . 'assets/css/admin.css', [], '1.0.1', 'all');

        // select2
        wp_enqueue_script('sbwc-po-select2', SBWC_PO_URL . 'assets/js/select2.js', ['jquery'], '4.0.1', true);
        wp_enqueue_style('sbwc-po-css-select2', SBWC_PO_URL . 'assets/css/select2.css', [], '4.0.1', 'all');
    }

    // admin
    include SBWC_PO_PATH . 'functions/admin.php';
}
