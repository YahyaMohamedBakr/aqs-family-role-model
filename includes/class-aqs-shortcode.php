<?php
/**
 * Shortcode: [aqs_survey]
 *
 * @package AQS
 */

defined('ABSPATH') || exit;

class AQS_Shortcode {

    public function __construct() {
        add_shortcode('aqs_survey', array($this, 'render'));
    }

    public function render() {
        wp_enqueue_style('aqs-survey');
        wp_enqueue_script('alpinejs');
        wp_enqueue_script('aqs-survey');

        $axes       = aqs_get_axes();
        $questions  = aqs_get_questions();
        $options    = aqs_get_options();
        $classifications = aqs_get_classifications();

        ob_start();
        include AQS_PATH . 'templates/survey-form.php';
        return ob_get_clean();
    }
}
