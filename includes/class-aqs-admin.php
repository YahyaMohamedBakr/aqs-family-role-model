<?php
/**
 * Admin pages: results, contact requests, stats, export.
 *
 * @package AQS
 */

defined('ABSPATH') || exit;

class AQS_Admin {

    public function __construct() {
        add_action('admin_menu', array($this, 'add_menu_pages'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }

    public function add_menu_pages() {
        add_menu_page(
            __('مقياس الأسرة القدوة', 'aqs-family-role-model'),
            __('مقياس الأسرة', 'aqs-family-role-model'),
            'manage_options',
            'aqs-dashboard',
            array($this, 'page_dashboard'),
            'dashicons-feedback',
            30
        );

        add_submenu_page(
            'aqs-dashboard',
            __('النتائج', 'aqs-family-role-model'),
            __('النتائج', 'aqs-family-role-model'),
            'manage_options',
            'aqs-results',
            array($this, 'page_results')
        );

        add_submenu_page(
            'aqs-dashboard',
            __('طلبات التواصل', 'aqs-family-role-model'),
            __('طلبات التواصل', 'aqs-family-role-model'),
            'manage_options',
            'aqs-contacts',
            array($this, 'page_contacts')
        );

        add_submenu_page(
            'aqs-dashboard',
            __('الإحصائيات', 'aqs-family-role-model'),
            __('الإحصائيات', 'aqs-family-role-model'),
            'manage_options',
            'aqs-stats',
            array($this, 'page_stats')
        );

        add_submenu_page(
            'aqs-dashboard',
            __('الإعدادات', 'aqs-family-role-model'),
            __('الإعدادات', 'aqs-family-role-model'),
            'manage_options',
            'aqs-settings',
            array($this, 'page_settings')
        );
    }

    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'aqs-') === false) {
            return;
        }

        wp_enqueue_style(
            'aqs-admin',
            AQS_URL . 'assets/css/aqs-survey.css',
            array(),
            AQS_VERSION
        );

        wp_enqueue_script(
            'chartjs',
            'https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js',
            array(),
            '4.4.7',
            true
        );
        wp_script_add_data('chartjs', 'integrity', 'sha384-vsrfeLOOY6KuIYKDlmVH5UiBmgIdB1oEf7p01YgWHuqmOHfZr374+odEv96n9tNC');
        wp_script_add_data('chartjs', 'crossorigin', 'anonymous');

        wp_enqueue_script(
            'aqs-admin',
            AQS_URL . 'assets/js/aqs-admin.js',
            array('chartjs'),
            AQS_VERSION,
            true
        );

        wp_localize_script('aqs-admin', 'aqs_admin', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('aqs_nonce'),
        ));
    }

    public function page_dashboard() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        global $wpdb;
        $table_responses = $wpdb->prefix . AQS_TABLE_RESPONSES;
        $table_contacts  = $wpdb->prefix . AQS_TABLE_CONTACTS;

        $total      = $wpdb->get_var("SELECT COUNT(*) FROM {$table_responses}");
        $avg_score  = $wpdb->get_var("SELECT AVG(total_score) FROM {$table_responses}");
        $contacts   = $wpdb->get_var("SELECT COUNT(*) FROM {$table_contacts}");
        $recent     = $wpdb->get_results("SELECT * FROM {$table_responses} ORDER BY created_at DESC LIMIT 5", ARRAY_A);

        $classifications = aqs_get_classifications();
        $distribution    = array();
        foreach ($classifications as $key => $data) {
            $count = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$table_responses} WHERE classification = %s",
                $key
            ));
            $distribution[$key] = array(
                'label' => $data['label'],
                'count' => intval($count),
                'color' => $data['color'],
            );
        }

        include AQS_PATH . 'templates/admin-dashboard.php';
    }

    public function page_results() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        $view_id = isset($_GET['view']) ? intval($_GET['view']) : 0;
        if ($view_id) {
            $this->response_detail($view_id);
            return;
        }

        global $wpdb;
        $table_responses = $wpdb->prefix . AQS_TABLE_RESPONSES;

        $classification_filter = isset($_GET['classification']) ? sanitize_text_field(wp_unslash($_GET['classification'])) : '';
        $where = '';
        if ($classification_filter && in_array($classification_filter, array_keys(aqs_get_classifications()))) {
            $where = $wpdb->prepare("WHERE classification = %s", $classification_filter);
        }

        $search = isset($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';
        if ($search) {
            $search_term = '%' . $wpdb->esc_like($search) . '%';
            if ($where) {
                $where .= $wpdb->prepare(" AND (full_name LIKE %s OR email LIKE %s)", $search_term, $search_term);
            } else {
                $where .= $wpdb->prepare("WHERE (full_name LIKE %s OR email LIKE %s)", $search_term, $search_term);
            }
        }

        $paged  = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $limit  = 20;
        $offset = ($paged - 1) * $limit;

        $total_items = $wpdb->get_var("SELECT COUNT(*) FROM {$table_responses} {$where}");
        $total_pages = ceil($total_items / $limit);

        $rows = $wpdb->get_results(
            "SELECT * FROM {$table_responses} {$where} ORDER BY created_at DESC LIMIT {$limit} OFFSET {$offset}",
            ARRAY_A
        );

        include AQS_PATH . 'templates/admin-results.php';
    }

    private function response_detail($response_id) {
        global $wpdb;
        $table_responses = $wpdb->prefix . AQS_TABLE_RESPONSES;
        $table_contacts  = $wpdb->prefix . AQS_TABLE_CONTACTS;

        $response = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table_responses} WHERE id = %d",
            $response_id
        ), ARRAY_A);

        if (!$response) {
            echo '<div class="wrap aqs-admin-wrap"><h1>' . esc_html__('النتيجة', 'aqs-family-role-model') . '</h1>';
            echo '<div class="notice notice-error"><p>' . esc_html__('لم يتم العثور على النتيجة', 'aqs-family-role-model') . '</p></div>';
            echo '<a href="' . esc_url(admin_url('admin.php?page=aqs-results')) . '" class="button">&larr; ' . esc_html__('العودة للنتائج', 'aqs-family-role-model') . '</a></div>';
            return;
        }

        $contacts = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table_contacts} WHERE response_id = %d ORDER BY created_at DESC",
            $response_id
        ), ARRAY_A);

        $answers       = json_decode($response['answers'], true);
        $axis_scores   = json_decode($response['axis_scores'], true);
        $axes          = aqs_get_axes();
        $questions     = aqs_get_questions();
        $options       = aqs_get_options();
        $classifications = aqs_get_classifications();
        $classification = isset($classifications[$response['classification']]) ? $classifications[$response['classification']] : null;

        include AQS_PATH . 'templates/admin-response-detail.php';
    }

    public function page_contacts() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        global $wpdb;
        $table_contacts  = $wpdb->prefix . AQS_TABLE_CONTACTS;
        $table_responses = $wpdb->prefix . AQS_TABLE_RESPONSES;

        if (isset($_POST['update_status']) && isset($_POST['contact_id'])) {
            check_admin_referer('aqs_update_contact');

            if (!current_user_can('manage_options')) {
                wp_die('Unauthorized');
            }

            $contact_id = intval($_POST['contact_id']);
            $status     = sanitize_text_field(wp_unslash($_POST['status']));
            $allowed    = array('pending', 'contacted', 'closed');
            if (!in_array($status, $allowed, true)) {
                $status = 'pending';
            }
            $wpdb->update(
                $table_contacts,
                array('status' => $status),
                array('id' => $contact_id),
                array('%s'),
                array('%d')
            );
            echo '<div class="notice notice-success"><p>' . esc_html__('تم تحديث الحالة', 'aqs-family-role-model') . '</p></div>';
        }

        $paged  = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $limit  = 20;
        $offset = ($paged - 1) * $limit;

        $total_items = $wpdb->get_var("SELECT COUNT(*) FROM {$table_contacts}");
        $total_pages = ceil($total_items / $limit);

        $rows = $wpdb->get_results(
            "SELECT c.*, r.full_name, r.total_score, r.classification
            FROM {$table_contacts} c
            LEFT JOIN {$table_responses} r ON c.response_id = r.id
            ORDER BY c.created_at DESC
            LIMIT {$limit} OFFSET {$offset}",
            ARRAY_A
        );

        include AQS_PATH . 'templates/admin-contacts.php';
    }

    public function page_stats() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        global $wpdb;
        $table_responses = $wpdb->prefix . AQS_TABLE_RESPONSES;

        $total      = intval($wpdb->get_var("SELECT COUNT(*) FROM {$table_responses}"));
        $avg_score  = round(floatval($wpdb->get_var("SELECT AVG(total_score) FROM {$table_responses}")), 1);

        $classifications = aqs_get_classifications();
        $dist_labels     = array();
        $dist_counts     = array();
        $dist_colors     = array();

        foreach ($classifications as $key => $data) {
            $count = intval($wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$table_responses} WHERE classification = %s",
                $key
            )));
            $dist_labels[] = $data['label'];
            $dist_counts[] = $count;
            $dist_colors[] = $data['color'];
        }

        $axes = aqs_get_axes();
        $axis_avgs = array();
        $axis_labels = array();

        foreach ($axes as $key => $data) {
            $avg = $wpdb->get_var("SELECT AVG(JSON_EXTRACT(axis_scores, '$.{$key}')) FROM {$table_responses}");
            $axis_avgs[] = round(floatval($avg), 1);
            $axis_labels[] = $data['title'];
        }

        include AQS_PATH . 'templates/admin-stats.php';
    }

    public function page_settings() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        if (isset($_POST['save_aqs_settings'])) {
            check_admin_referer('aqs_save_settings');
            $preserve = isset($_POST['aqs_preserve_data']) ? '1' : '0';
            update_option('aqs_preserve_data', $preserve);
            echo '<div class="notice notice-success"><p>' . esc_html__('تم حفظ الإعدادات', 'aqs-family-role-model') . '</p></div>';
        }

        $preserve_data = get_option('aqs_preserve_data', '1');
        include AQS_PATH . 'templates/admin-settings.php';
    }
}
