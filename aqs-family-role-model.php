<?php
/**
 * Plugin Name:     مقياس الأسرة القدوة
 * Plugin URI:      https://goodwaty.org.sa
 * Description:     استبيان تفاعلي "مقياس الأسرة القدوة" يقيس مدى كون الأسرة قدوة عبر 4 محاور
 * Version:         1.0.0
 * Author:          مركز قيمة وقدوة للتدريب
 * Author URI:      https://goodwaty.org.sa
 * Text Domain:     aqs-family-role-model
 * Domain Path:     /languages
 * Requires PHP:    8.1
 * Requires WP:     6.0
 */

defined('ABSPATH') || exit;

define('AQS_VERSION', '1.0.0');
define('AQS_FILE', __FILE__);
define('AQS_PATH', plugin_dir_path(__FILE__));
define('AQS_URL', plugin_dir_url(__FILE__));
define('AQS_BASENAME', plugin_basename(__FILE__));

define('AQS_TABLE_RESPONSES', 'aqs_responses');
define('AQS_TABLE_CONTACTS', 'aqs_contact_requests');

/**
 * Activation hook
 */
function aqs_activate() {
    require_once AQS_PATH . 'includes/class-aqs-activator.php';
    AQS_Activator::activate();
}
register_activation_hook(__FILE__, 'aqs_activate');

/**
 * Deactivation hook
 */
function aqs_deactivate() {
    require_once AQS_PATH . 'includes/class-aqs-activator.php';
    AQS_Activator::deactivate();
}
register_deactivation_hook(__FILE__, 'aqs_deactivate');

/**
 * Bootstrap
 */
add_action('plugins_loaded', 'aqs_init');

function aqs_init() {
    require_once AQS_PATH . 'data/survey-questions.php';
    require_once AQS_PATH . 'includes/class-aqs-survey.php';
    require_once AQS_PATH . 'includes/class-aqs-shortcode.php';
    require_once AQS_PATH . 'includes/class-aqs-ajax-handlers.php';
    require_once AQS_PATH . 'includes/class-aqs-mailer.php';

    new AQS_Shortcode();

    if (is_admin()) {
        require_once AQS_PATH . 'includes/class-aqs-admin.php';
        new AQS_Admin();
    }
}

/**
 * Enqueue frontend assets
 */
add_action('wp_enqueue_scripts', 'aqs_enqueue_assets');

function aqs_enqueue_assets() {
    global $post;
    if (!is_a($post, 'WP_Post') || !has_shortcode($post->post_content, 'aqs_survey')) {
        return;
    }

    wp_enqueue_style(
        'aqs-survey',
        AQS_URL . 'assets/css/aqs-survey.css',
        array(),
        AQS_VERSION
    );

    wp_enqueue_script(
        'alpinejs',
        'https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js',
        array(),
        '3.14.8',
        array('strategy' => 'defer')
    );
    wp_script_add_data('alpinejs', 'integrity', 'sha384-X9kJyAubVxnP0hcA+AMMs21U445qsnqhnUF8EBlEpP3a42Kh/JwWjlv2ZcvGfphb');
    wp_script_add_data('alpinejs', 'crossorigin', 'anonymous');

    wp_enqueue_script(
        'aqs-survey',
        AQS_URL . 'assets/js/aqs-survey.js',
        array(),
        AQS_VERSION,
        true
    );

    wp_localize_script('aqs-survey', 'aqs_ajax', array(
        'ajaxurl'  => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('aqs_nonce'),
        'rate_sec' => __('ثانية', 'aqs-family-role-model'),
    ));
}

/**
 * Load text domain
 */
add_action('init', 'aqs_load_textdomain');

function aqs_load_textdomain() {
    load_plugin_textdomain('aqs-family-role-model', false, dirname(AQS_BASENAME) . '/languages');
}
