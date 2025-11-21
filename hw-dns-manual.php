<?php
/**
 * Plugin Name: HW DNS Manual
 * Description: Generate lightweight Manual Paper PDFs with customer, purchase channel, and purchase date details on a branded background.
 * Version: 1.0.0
 * Author: ChatGPT
 */

if (!defined('ABSPATH')) {
    exit;
}

define('HW_DNS_MANUAL_VERSION', '1.0.0');
define('HW_DNS_MANUAL_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('HW_DNS_MANUAL_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once HW_DNS_MANUAL_PLUGIN_DIR . 'includes/class-hw-dns-manual-admin.php';
require_once HW_DNS_MANUAL_PLUGIN_DIR . 'includes/class-hw-dns-manual-pdf.php';

add_action('plugins_loaded', function () {
    HW_DNS_Manual_Admin::get_instance();
    HW_DNS_Manual_PDF::get_instance();
});
