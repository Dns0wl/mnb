<?php

if (!defined('ABSPATH')) {
    exit;
}

class HW_DNS_Manual_Admin
{
    private static $instance = null;

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        add_action('init', [$this, 'register_post_type']);
        add_action('admin_menu', [$this, 'register_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('admin_post_hw_dns_manual_create', [$this, 'handle_create_manual']);
        add_filter('manage_hw_dns_manual_posts_columns', [$this, 'add_columns']);
        add_action('manage_hw_dns_manual_posts_custom_column', [$this, 'render_columns'], 10, 2);
    }

    public function register_post_type()
    {
        $labels = [
            'name' => __('Manual Papers', 'hw-dns-manual'),
            'singular_name' => __('Manual Paper', 'hw-dns-manual'),
        ];

        $args = [
            'labels' => $labels,
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'supports' => ['title'],
            'capability_type' => 'post',
        ];

        register_post_type('hw_dns_manual', $args);
    }

    public function register_menu()
    {
        add_menu_page(
            __('Manual Paper', 'hw-dns-manual'),
            __('Manual Paper', 'hw-dns-manual'),
            'manage_options',
            'hw-dns-manual',
            [$this, 'render_admin_page'],
            'dashicons-media-document'
        );
    }

    public function enqueue_assets($hook)
    {
        if ($hook !== 'toplevel_page_hw-dns-manual') {
            return;
        }

        wp_enqueue_style(
            'hw-dns-manual-admin',
            HW_DNS_MANUAL_PLUGIN_URL . 'assets/css/admin.css',
            [],
            HW_DNS_MANUAL_VERSION
        );

        wp_enqueue_script(
            'hw-dns-manual-admin',
            HW_DNS_MANUAL_PLUGIN_URL . 'assets/js/admin.js',
            ['jquery'],
            HW_DNS_MANUAL_VERSION,
            true
        );

        wp_localize_script('hw-dns-manual-admin', 'HWManual', [
            'nonce' => wp_create_nonce('hw_dns_manual_nonce'),
            'createUrl' => admin_url('admin-post.php'),
        ]);
    }

    public function render_admin_page()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to access this page', 'hw-dns-manual'));
        }

        $query = new WP_Query([
            'post_type' => 'hw_dns_manual',
            'posts_per_page' => -1,
            'orderby' => 'date',
            'order' => 'DESC',
        ]);
        ?>
        <div class="wrap hw-dns-manual-wrap">
            <h1 class="hw-title">HW DNS Manual</h1>
            <?php if (isset($_GET['message']) && $_GET['message'] === 'created') : ?>
                <div class="notice notice-success"><p><?php esc_html_e('Manual created successfully.', 'hw-dns-manual'); ?></p></div>
            <?php elseif (isset($_GET['message']) && $_GET['message'] === 'error') : ?>
                <div class="notice notice-error"><p><?php esc_html_e('Unable to create manual. Please check the fields and try again.', 'hw-dns-manual'); ?></p></div>
            <?php endif; ?>

            <button class="button button-primary hw-add-manual" id="hw-add-manual"><?php esc_html_e('Add New Manual', 'hw-dns-manual'); ?></button>

            <table class="wp-list-table widefat fixed striped hw-manual-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Customer Name', 'hw-dns-manual'); ?></th>
                        <th><?php esc_html_e('Purchase Channel', 'hw-dns-manual'); ?></th>
                        <th><?php esc_html_e('Purchase Date', 'hw-dns-manual'); ?></th>
                        <th><?php esc_html_e('Actions', 'hw-dns-manual'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($query->have_posts()) : ?>
                    <?php while ($query->have_posts()) : $query->the_post();
                        $manual_id = get_the_ID();
                        $customer_name = get_post_meta($manual_id, '_hw_customer_name', true);
                        $purchase_channel = get_post_meta($manual_id, '_hw_purchase_channel', true);
                        $purchase_date = get_post_meta($manual_id, '_hw_purchase_date', true);
                    ?>
                        <tr>
                            <td><?php echo esc_html($customer_name); ?></td>
                            <td><?php echo esc_html($purchase_channel); ?></td>
                            <td><?php echo esc_html($purchase_date); ?></td>
                            <td class="hw-actions">
                                <a class="button" href="<?php echo esc_url($this->get_pdf_url($manual_id, false)); ?>" target="_blank"><?php esc_html_e('View', 'hw-dns-manual'); ?></a>
                                <a class="button" href="<?php echo esc_url($this->get_pdf_url($manual_id, true)); ?>"><?php esc_html_e('Download', 'hw-dns-manual'); ?></a>
                            </td>
                        </tr>
                    <?php endwhile; wp_reset_postdata(); ?>
                <?php else : ?>
                    <tr><td colspan="4"><?php esc_html_e('No manuals available yet.', 'hw-dns-manual'); ?></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div id="hw-manual-modal" class="hw-modal" style="display:none;">
            <div class="hw-modal-content">
                <span class="hw-close">&times;</span>
                <h2><?php esc_html_e('Manual Data', 'hw-dns-manual'); ?></h2>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="hw-manual-form">
                    <input type="hidden" name="action" value="hw_dns_manual_create">
                    <?php wp_nonce_field('hw_dns_manual_nonce', '_hw_dns_manual_nonce'); ?>
                    <div class="hw-field">
                        <label for="hw_customer_name"><?php esc_html_e('Customer Name', 'hw-dns-manual'); ?>*</label>
                        <input type="text" name="customer_name" id="hw_customer_name" required>
                    </div>
                    <div class="hw-field">
                        <label><?php esc_html_e('Purchase Date', 'hw-dns-manual'); ?>*</label>
                        <div class="hw-date-row">
                            <label class="hw-inline"><input type="radio" name="date_mode" value="now" checked> <?php esc_html_e('Now', 'hw-dns-manual'); ?></label>
                            <label class="hw-inline"><input type="radio" name="date_mode" value="choose"> <?php esc_html_e('Choose', 'hw-dns-manual'); ?></label>
                        </div>
                        <input type="date" name="purchase_date" id="hw_purchase_date" class="hw-date-input" style="display:none;" pattern="\d{2}/\d{2}/\d{2}">
                        <p class="description"><?php esc_html_e('Format: DD/MM/YY when choosing manually.', 'hw-dns-manual'); ?></p>
                    </div>
                    <div class="hw-field">
                        <label for="hw_purchase_channel"><?php esc_html_e('Purchase Channel', 'hw-dns-manual'); ?>*</label>
                        <select name="purchase_channel" id="hw_purchase_channel" required>
                            <option value=""><?php esc_html_e('Select channel', 'hw-dns-manual'); ?></option>
                            <option value="Marketplace"><?php esc_html_e('Marketplace', 'hw-dns-manual'); ?></option>
                            <option value="Customer Service"><?php esc_html_e('Customer Service', 'hw-dns-manual'); ?></option>
                            <option value="Boutique"><?php esc_html_e('Boutique', 'hw-dns-manual'); ?></option>
                            <option value="Website"><?php esc_html_e('Website', 'hw-dns-manual'); ?></option>
                        </select>
                    </div>
                    <div class="hw-field" id="hw-marketplace-wrapper" style="display:none;">
                        <label for="hw_marketplace_source"><?php esc_html_e('Marketplace Source', 'hw-dns-manual'); ?>*</label>
                        <select name="marketplace_source" id="hw_marketplace_source">
                            <option value=""><?php esc_html_e('Select marketplace', 'hw-dns-manual'); ?></option>
                            <option value="Tiktok Shop"><?php esc_html_e('Tiktok Shop', 'hw-dns-manual'); ?></option>
                            <option value="Shopee"><?php esc_html_e('Shopee', 'hw-dns-manual'); ?></option>
                            <option value="Tokopedia"><?php esc_html_e('Tokopedia', 'hw-dns-manual'); ?></option>
                        </select>
                    </div>
                    <div class="hw-field">
                        <button type="submit" class="button button-primary hw-submit"><?php esc_html_e('Submit', 'hw-dns-manual'); ?></button>
                    </div>
                </form>
            </div>
        </div>
        <?php
    }

    public function handle_create_manual()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to perform this action', 'hw-dns-manual'));
        }

        if (!isset($_POST['_hw_dns_manual_nonce']) || !wp_verify_nonce($_POST['_hw_dns_manual_nonce'], 'hw_dns_manual_nonce')) {
            $this->redirect_with_message('error');
        }

        $customer_name = sanitize_text_field($_POST['customer_name'] ?? '');
        $date_mode = sanitize_text_field($_POST['date_mode'] ?? '');
        $purchase_channel = sanitize_text_field($_POST['purchase_channel'] ?? '');
        $marketplace = sanitize_text_field($_POST['marketplace_source'] ?? '');
        $purchase_date = '';

        if (!$customer_name || !$purchase_channel || !$date_mode) {
            $this->redirect_with_message('error');
        }

        if ($date_mode === 'now') {
            $purchase_date = current_time('d/m/y');
        } else {
            $raw_date = sanitize_text_field($_POST['purchase_date'] ?? '');
            if (!$raw_date) {
                $this->redirect_with_message('error');
            }
            $timestamp = strtotime($raw_date);
            $purchase_date = $timestamp ? date('d/m/y', $timestamp) : date('d/m/y');
        }

        $channel_label = $purchase_channel;
        if ('Marketplace' === $purchase_channel && $marketplace) {
            $channel_label .= ' - ' . $marketplace;
        }

        $post_id = wp_insert_post([
            'post_type' => 'hw_dns_manual',
            'post_status' => 'publish',
            'post_title' => sprintf(__('Manual - %s', 'hw-dns-manual'), $customer_name),
        ], true);

        if (is_wp_error($post_id)) {
            $this->redirect_with_message('error');
        }

        update_post_meta($post_id, '_hw_customer_name', $customer_name);
        update_post_meta($post_id, '_hw_purchase_channel', $channel_label);
        update_post_meta($post_id, '_hw_purchase_date', $purchase_date);

        $this->redirect_with_message('created');
    }

    public function add_columns($columns)
    {
        $columns['hw_customer'] = __('Customer Name', 'hw-dns-manual');
        $columns['hw_channel'] = __('Purchase Channel', 'hw-dns-manual');
        $columns['hw_date'] = __('Purchase Date', 'hw-dns-manual');
        return $columns;
    }

    public function render_columns($column, $post_id)
    {
        switch ($column) {
            case 'hw_customer':
                echo esc_html(get_post_meta($post_id, '_hw_customer_name', true));
                break;
            case 'hw_channel':
                echo esc_html(get_post_meta($post_id, '_hw_purchase_channel', true));
                break;
            case 'hw_date':
                echo esc_html(get_post_meta($post_id, '_hw_purchase_date', true));
                break;
        }
    }

    private function get_pdf_url($manual_id, $download = false)
    {
        return add_query_arg([
            'action' => 'hw_dns_manual_pdf',
            'manual_id' => $manual_id,
            'download' => $download ? 1 : 0,
        ], admin_url('admin-post.php'));
    }

    private function redirect_with_message($message)
    {
        $target = menu_page_url('hw-dns-manual', false);
        if (!$target) {
            $target = admin_url('admin.php?page=hw-dns-manual');
        }

        $redirect_url = add_query_arg('message', $message, $target);

        if (!wp_safe_redirect($redirect_url)) {
            wp_redirect($redirect_url);
        }

        exit;
    }
}
