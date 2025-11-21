<?php

if (!defined('ABSPATH')) {
    exit;
}

class HW_DNS_Manual_PDF
{
    private static $instance = null;
    private $background_url = 'https://hayuwidyas.com/wp-content/uploads/2025/11/MNB-L_Cust_HW-BG.png';

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        add_action('admin_post_hw_dns_manual_pdf', [$this, 'output_pdf']);
    }

    public function output_pdf()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to view this PDF', 'hw-dns-manual'));
        }

        $manual_id = isset($_GET['manual_id']) ? absint($_GET['manual_id']) : 0;
        $download = !empty($_GET['download']);

        if (!$manual_id) {
            wp_die(__('Manual not found.', 'hw-dns-manual'));
        }

        $customer_name = get_post_meta($manual_id, '_hw_customer_name', true);
        $purchase_channel = get_post_meta($manual_id, '_hw_purchase_channel', true);
        $purchase_date = get_post_meta($manual_id, '_hw_purchase_date', true);

        $pdf_content = $this->generate_pdf($customer_name, $purchase_channel, $purchase_date);

        if (is_wp_error($pdf_content)) {
            wp_die($pdf_content->get_error_message());
        }

        $filename = sanitize_file_name('manual-' . $manual_id . '.pdf');

        header('Content-Type: application/pdf');
        header('Content-Length: ' . strlen($pdf_content));
        if ($download) {
            header('Content-Disposition: attachment; filename="' . $filename . '"');
        } else {
            header('Content-Disposition: inline; filename="' . $filename . '"');
        }

        echo $pdf_content;
        exit;
    }

    private function generate_pdf($customer_name, $purchase_channel, $purchase_date)
    {
        if (!class_exists('FPDF')) {
            require_once HW_DNS_MANUAL_PLUGIN_DIR . 'includes/fpdf.php';
        }

        $background_path = $this->get_background_path();

        try {
            $pdf = new FPDF('P', 'mm', 'A5');
            $pdf->SetMargins(15, 20, 15);
            $pdf->SetAutoPageBreak(false);
            $pdf->AddPage();

            if ($background_path && file_exists($background_path)) {
                $pdf->Image($background_path, 0, 0, 148, 210); // Full A5 background
            }

            $pdf->SetFont('Arial', '', 12);
            $pdf->SetTextColor(17, 17, 17);
            $pdf->SetXY(20, 110);
            $pdf->Cell(0, 10, sprintf(__('Customer Name: %s', 'hw-dns-manual'), $customer_name));
            $pdf->Ln(12);
            $pdf->SetX(20);
            $pdf->Cell(0, 10, sprintf(__('Purchase Channel: %s', 'hw-dns-manual'), $purchase_channel));
            $pdf->Ln(12);
            $pdf->SetX(20);
            $pdf->Cell(0, 10, sprintf(__('Purchase Date: %s', 'hw-dns-manual'), $purchase_date));

            return $pdf->Output('S');
        } catch (Exception $e) {
            return new WP_Error('pdf_error', $e->getMessage());
        }
    }

    private function get_background_path()
    {
        $local_path = $this->get_cached_background_path();
        if ($local_path && file_exists($local_path)) {
            return $local_path;
        }

        $downloaded = $this->download_background();
        return $downloaded ? $downloaded : null;
    }

    private function download_background()
    {
        if (!$this->background_url) {
            return null;
        }

        $destination = $this->get_cached_background_path(true);
        if (!$destination) {
            return null;
        }

        $response = wp_remote_get($this->background_url, ['timeout' => 15]);
        if (is_wp_error($response)) {
            return null;
        }

        $code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);

        if ($code !== 200 || empty($body)) {
            return null;
        }

        if (file_put_contents($destination, $body) === false) {
            return null;
        }

        return $destination;
    }

    private function get_cached_background_path($create = false)
    {
        $uploads = wp_upload_dir();
        if (!empty($uploads['error'])) {
            return null;
        }

        $base_dir = trailingslashit($uploads['basedir']) . 'hw-dns-manual/';

        if (!is_dir($base_dir) && $create) {
            wp_mkdir_p($base_dir);
        }

        $path = $base_dir . 'background.png';
        if (!$create && !file_exists($path)) {
            // As a final fallback, allow a bundled asset if present.
            $bundled = HW_DNS_MANUAL_PLUGIN_DIR . 'assets/background.png';
            return file_exists($bundled) ? $bundled : null;
        }

        return $path;
    }
}
