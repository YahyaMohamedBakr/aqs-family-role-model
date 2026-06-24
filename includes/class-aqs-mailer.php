<?php
/**
 * Email notifications.
 *
 * @package AQS
 */

defined('ABSPATH') || exit;

class AQS_Mailer {

    public static function notify_team($response_id, $user_data, $result, $contact_message = '') {
        $to = get_option('admin_email');
        if (!$to) {
            return false;
        }

        $classifications = aqs_get_classifications();
        $class_key       = $result['classification'];
        $class_label     = isset($classifications[$class_key]) ? $classifications[$class_key]['label'] : $class_key;

        $subject = sprintf(
            __('[مقياس الأسرة القدوة] نتيجة جديدة - %s - %s', 'aqs-family-role-model'),
            $user_data['full_name'],
            $result['total_score'] . '/100'
        );

        $body = self::build_team_email_body($user_data, $result, $class_label, $contact_message);

        $from_name  = get_bloginfo('name');
        $from_email = $to;
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $from_name . ' <' . $from_email . '>',
        );

        return wp_mail($to, $subject, $body, $headers);
    }

    public static function send_confirmation($user_data, $result) {
        $email = sanitize_email($user_data['email']);
        if (!is_email($email)) {
            return false;
        }

        $classifications = aqs_get_classifications();
        $class_key       = $result['classification'];
        $class_label     = isset($classifications[$class_key]) ? $classifications[$class_key]['label'] : $class_key;

        $subject = __('شكراً لمشاركتكم في مقياس الأسرة القدوة', 'aqs-family-role-model');

        $body = self::build_user_email_body($user_data, $result, $class_label);

        $from_name  = get_bloginfo('name');
        $from_email = get_option('admin_email');
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $from_name . ' <' . $from_email . '>',
        );

        return wp_mail($email, $subject, $body, $headers);
    }

    private static function build_team_email_body($user_data, $result, $class_label, $contact_message = '') {
        $site_name = esc_html(get_bloginfo('name'));
        $date      = esc_html(current_time('Y-m-d H:i'));
        $name      = esc_html($user_data['full_name']);
        $email     = esc_html($user_data['email']);
        $phone     = esc_html($user_data['phone']);
        $score     = esc_html($result['total_score']);
        $label     = esc_html($class_label);

        $body = '<!DOCTYPE html>';
        $body .= '<html dir="rtl">';
        $body .= '<head><meta charset="UTF-8"></head>';
        $body .= '<body style="font-family: Cairo, sans-serif; padding: 24px; background: #f9fafb;">';
        $body .= '<div style="max-width: 600px; margin: 0 auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.08);">';
        $body .= '<div style="background: linear-gradient(135deg, #2090b0 0%, #107080 100%); padding: 24px; text-align: center;">';
        $body .= '<h1 style="color: #fff; margin: 0; font-size: 1.4rem;">' . $site_name . '</h1>';
        $body .= '<p style="color: rgba(255,255,255,0.85); margin: 8px 0 0;">نتيجة جديدة - مقياس الأسرة القدوة</p>';
        $body .= '</div>';
        $body .= '<div style="padding: 24px;">';
        $body .= '<table style="width:100%; border-collapse: collapse;">';
        $body .= '<tr><td style="padding: 8px; font-weight: 700;">الاسم</td><td style="padding: 8px;">' . $name . '</td></tr>';
        $body .= '<tr><td style="padding: 8px; font-weight: 700;">البريد</td><td style="padding: 8px;">' . $email . '</td></tr>';
        $body .= '<tr><td style="padding: 8px; font-weight: 700;">الهاتف</td><td style="padding: 8px;">' . $phone . '</td></tr>';
        $body .= '<tr><td style="padding: 8px; font-weight: 700;">النتيجة</td><td style="padding: 8px;">' . $score . '/100</td></tr>';
        $body .= '<tr><td style="padding: 8px; font-weight: 700;">التصنيف</td><td style="padding: 8px;">' . $label . '</td></tr>';
        $body .= '<tr><td style="padding: 8px; font-weight: 700;">التاريخ</td><td style="padding: 8px;">' . $date . '</td></tr>';
        $body .= '</table>';

        if (!empty($contact_message)) {
            $body .= '<hr style="margin: 20px 0; border: none; border-top: 1px solid #e5e7eb;">';
            $body .= '<h3 style="color: #f0a010;">رسالة من المستخدم</h3>';
            $body .= '<p>' . nl2br(esc_html($contact_message)) . '</p>';
        }

        $body .= '<hr style="margin: 20px 0; border: none; border-top: 1px solid #e5e7eb;">';
        $body .= '<p style="font-size: 0.85rem; color: #9ca3af; text-align: center;">تم الإرسال بواسطة مقياس الأسرة القدوة</p>';
        $body .= '</div></div></body></html>';

        return $body;
    }

    private static function build_user_email_body($user_data, $result, $class_label) {
        $site_name = esc_html(get_bloginfo('name'));
        $home_url  = esc_url(home_url('/'));
        $name      = esc_html($user_data['full_name']);
        $score     = esc_html($result['total_score']);
        $label     = esc_html($class_label);
        $message   = esc_html($result['message']);

        $body = '<!DOCTYPE html>';
        $body .= '<html dir="rtl">';
        $body .= '<head><meta charset="UTF-8"></head>';
        $body .= '<body style="font-family: Cairo, sans-serif; padding: 24px; background: #f9fafb;">';
        $body .= '<div style="max-width: 600px; margin: 0 auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.08);">';
        $body .= '<div style="background: linear-gradient(135deg, #2090b0 0%, #107080 100%); padding: 24px; text-align: center;">';
        $body .= '<h1 style="color: #fff; margin: 0; font-size: 1.2rem;">شكراً لمشاركتكم</h1>';
        $body .= '</div>';
        $body .= '<div style="padding: 24px; text-align: center;">';
        $body .= '<p style="font-size: 1.1rem; font-weight: 700;">' . $name . '</p>';
        $body .= '<p>نتيجة مقياس الأسرة القدوة الخاصة بكم:</p>';
        $body .= '<div style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, #2090b0 0%, #107080 100%); display: flex; align-items: center; justify-content: center; margin: 20px auto;">';
        $body .= '<span style="color: #fff; font-size: 2rem; font-weight: 900;">' . $score . '</span>';
        $body .= '</div>';
        $body .= '<p style="font-size: 1.2rem; font-weight: 700; color: #2090b0;">' . $label . '</p>';
        $body .= '<p style="color: #4b5563; line-height: 1.8;">' . $message . '</p>';
        $body .= '<a href="' . $home_url . '" style="display: inline-block; margin-top: 20px; padding: 12px 28px; background: linear-gradient(135deg, #2090b0 0%, #107080 100%); color: #fff; text-decoration: none; border-radius: 12px; font-weight: 600;">زيارة الموقع</a>';
        $body .= '</div>';
        $body .= '<div style="background: #f9fafb; padding: 16px; text-align: center;">';
        $body .= '<p style="font-size: 0.8rem; color: #9ca3af; margin: 0;">' . $site_name . '</p>';
        $body .= '</div>';
        $body .= '</div></body></html>';

        return $body;
    }
}
