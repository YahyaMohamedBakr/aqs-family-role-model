<?php
/**
 * Survey logic: scoring, classification, validation.
 *
 * @package AQS
 */

defined('ABSPATH') || exit;

class AQS_Survey {

    public static function calculate_score($answers) {
        $axes    = aqs_get_axes();
        $total   = 0;
        $scores  = array();

        foreach ($axes as $axis_key => $axis_data) {
            $axis_total = 0;
            $questions  = aqs_get_questions()[$axis_key];

            foreach ($questions as $q_index => $q_text) {
                $qid = $axis_key . '_q' . $q_index;
                $val = isset($answers[$qid]) ? max(1, min(5, intval($answers[$qid]))) : 0;
                $axis_total += $val;
            }

            $scores[$axis_key] = $axis_total;
            $total += $axis_total;
        }

        return array(
            'total'       => min(100, $total),
            'axis_scores' => $scores,
            'max_per_axis' => 25,
        );
    }

    public static function classify($total) {
        $classifications = aqs_get_classifications();
        $total_int       = intval(round($total));

        foreach ($classifications as $key => $data) {
            if ($total_int >= $data['min'] && $total_int <= $data['max']) {
                return $key;
            }
        }

        return 'needs_consultation';
    }

    public static function validate_submission($data) {
        $errors = array();

        $name = isset($data['full_name']) ? trim(sanitize_text_field($data['full_name'])) : '';
        if (empty($name)) {
            $errors['full_name'] = __('الاسم مطلوب', 'aqs-family-role-model');
        }

        $email = isset($data['email']) ? trim(sanitize_email($data['email'])) : '';
        if (empty($email) || !is_email($email)) {
            $errors['email'] = __('البريد الإلكتروني غير صحيح', 'aqs-family-role-model');
        }

        $axes  = aqs_get_axes();
        $questions = aqs_get_questions();
        $answers   = isset($data['answers']) ? $data['answers'] : array();

        $answered_count = 0;
        foreach ($axes as $axis_key => $axis_data) {
            foreach ($questions[$axis_key] as $q_index => $q_text) {
                $qid = $axis_key . '_q' . $q_index;
                if (isset($answers[$qid]) && $answers[$qid] >= 1 && $answers[$qid] <= 5) {
                    $answered_count++;
                }
            }
        }

        $total_questions = 20;
        if ($answered_count < $total_questions) {
            $errors['answers'] = __('يرجى الإجابة على جميع الأسئلة', 'aqs-family-role-model');
        }

        return $errors;
    }

    public static function rate_limit_check($ip) {
        $key    = 'aqs_rate_limit_' . md5($ip);
        $window = 600;
        $max    = 3;

        $attempts = get_transient($key);
        if (false === $attempts) {
            set_transient($key, 1, $window);
            return true;
        }

        if (intval($attempts) >= $max) {
            return false;
        }

        set_transient($key, intval($attempts) + 1, $window);
        return true;
    }
}
