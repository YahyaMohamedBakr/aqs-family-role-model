<?php
/**
 * AJAX handlers for survey submission and contact requests.
 *
 * @package AQS
 */

defined('ABSPATH') || exit;

class AQS_Ajax_Handlers {

    public static function init() {
        add_action('wp_ajax_aqs_submit_survey', array(__CLASS__, 'submit_survey'));
        add_action('wp_ajax_nopriv_aqs_submit_survey', array(__CLASS__, 'submit_survey'));

        add_action('wp_ajax_aqs_submit_contact', array(__CLASS__, 'submit_contact'));
        add_action('wp_ajax_nopriv_aqs_submit_contact', array(__CLASS__, 'submit_contact'));

        add_action('wp_ajax_aqs_export_csv', array(__CLASS__, 'export_csv'));
    }

    public static function submit_survey() {
        check_ajax_referer('aqs_nonce', 'nonce');

        $ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';

        if (!AQS_Survey::rate_limit_check($ip)) {
            wp_send_json_error(array(
                'message' => __('تم تجاوز الحد المسموح من المحاولات. يرجى الانتظار 10 دقائق.', 'aqs-family-role-model'),
            ));
        }

        $data = array(
            'full_name' => isset($_POST['full_name']) ? sanitize_text_field(wp_unslash($_POST['full_name'])) : '',
            'email'     => isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '',
            'answers'   => isset($_POST['answers']) ? $_POST['answers'] : array(),
        );

        $data['answers'] = self::sanitize_answers($data['answers']);

        $errors = AQS_Survey::validate_submission($data);
        if (!empty($errors)) {
            wp_send_json_error(array('errors' => $errors));
        }

        $result  = AQS_Survey::calculate_score($data['answers']);
        $class   = AQS_Survey::classify($result['total']);
        $classifications = aqs_get_classifications();

        global $wpdb;
        $table_responses = $wpdb->prefix . AQS_TABLE_RESPONSES;

        $wpdb->insert(
            $table_responses,
            array(
                'full_name'      => $data['full_name'],
                'email'          => $data['email'],
                'phone'          => isset($_POST['phone']) ? sanitize_text_field(wp_unslash($_POST['phone'])) : '',
                'answers'        => wp_json_encode($data['answers']),
                'axis_scores'    => wp_json_encode($result['axis_scores']),
                'total_score'    => $result['total'],
                'classification' => $class,
                'ip_address'     => $ip,
                'created_at'     => current_time('mysql'),
            ),
            array('%s', '%s', '%s', '%s', '%s', '%f', '%s', '%s', '%s')
        );

        $response_id = $wpdb->insert_id;

        AQS_Mailer::send_confirmation(
            array(
                'full_name' => $data['full_name'],
                'email'     => $data['email'],
            ),
            array(
                'total_score'   => $result['total'],
                'classification' => $class,
                'message'       => $classifications[$class]['msg'],
            )
        );

        AQS_Mailer::notify_team($response_id, $data, array(
            'total_score'    => $result['total'],
            'classification' => $class,
        ));

        wp_send_json_success(array(
            'response_id' => $response_id,
            'result'      => array(
                'total_score'    => $result['total'],
                'axis_scores'    => $result['axis_scores'],
                'classification' => $class,
                'label'          => $classifications[$class]['label'],
                'color'          => $classifications[$class]['color'],
                'bg'             => $classifications[$class]['bg'],
                'message'        => $classifications[$class]['msg'],
            ),
        ));
    }

    public static function submit_contact() {
        check_ajax_referer('aqs_nonce', 'nonce');

        $ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';
        if (!AQS_Survey::rate_limit_check($ip)) {
            wp_send_json_error(array(
                'message' => __('تم تجاوز الحد المسموح من المحاولات. يرجى الانتظار 10 دقائق.', 'aqs-family-role-model'),
            ));
        }

        $response_id = isset($_POST['response_id']) ? intval($_POST['response_id']) : 0;
        $message     = isset($_POST['message']) ? sanitize_textarea_field(wp_unslash($_POST['message'])) : '';
        $method      = isset($_POST['preferred_method']) ? sanitize_text_field(wp_unslash($_POST['preferred_method'])) : '';

        if (!$response_id) {
            wp_send_json_error(array('message' => __('معرّف الاستجابة مطلوب', 'aqs-family-role-model')));
        }

        global $wpdb;
        $table_contacts = $wpdb->prefix . AQS_TABLE_CONTACTS;

        $wpdb->insert(
            $table_contacts,
            array(
                'response_id'             => $response_id,
                'message'                 => $message,
                'preferred_contact_method' => $method,
                'created_at'              => current_time('mysql'),
            ),
            array('%d', '%s', '%s', '%s')
        );

        $table_responses = $wpdb->prefix . AQS_TABLE_RESPONSES;
        $user_data       = $wpdb->get_row($wpdb->prepare(
            "SELECT full_name, email, total_score, classification FROM {$table_responses} WHERE id = %d",
            $response_id
        ));

        if ($user_data) {
            AQS_Mailer::notify_team(
                $response_id,
                array(
                    'full_name' => $user_data->full_name,
                    'email'     => $user_data->email,
                    'phone'     => '',
                ),
                array(
                    'total_score'    => $user_data->total_score,
                    'classification' => $user_data->classification,
                ),
                $message
            );
        }

        wp_send_json_success(array(
            'message' => __('تم إرسال طلب التواصل بنجاح. سيتواصل معكم فريقنا قريباً.', 'aqs-family-role-model'),
        ));
    }

    public static function export_csv() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        check_admin_referer('aqs_export_csv');

        global $wpdb;
        $table_responses = $wpdb->prefix . AQS_TABLE_RESPONSES;
        $rows            = $wpdb->get_results("SELECT * FROM {$table_responses} ORDER BY created_at DESC", ARRAY_A);

        $classifications = aqs_get_classifications();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=aqs-responses-' . date('Y-m-d') . '.csv');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($output, array(
            'م', 'الاسم', 'البريد الإلكتروني', 'الهاتف', 'النتيجة',
            'التصنيف', 'تاريخ الإرسال', 'تم التواصل',
        ));

        foreach ($rows as $row) {
            $class_label = isset($classifications[$row['classification']])
                ? $classifications[$row['classification']]['label']
                : $row['classification'];

            fputcsv($output, array(
                $row['id'],
                $row['full_name'],
                $row['email'],
                $row['phone'],
                $row['total_score'],
                $class_label,
                $row['created_at'],
                $row['contacted'] ? 'نعم' : 'لا',
            ));
        }

        fclose($output);
        exit;
    }

    private static function sanitize_answers($answers) {
        if (!is_array($answers)) {
            return array();
        }

        $sanitized = array();
        foreach ($answers as $key => $value) {
            $sanitized[sanitize_text_field($key)] = intval($value);
        }

        return $sanitized;
    }
}

AQS_Ajax_Handlers::init();
