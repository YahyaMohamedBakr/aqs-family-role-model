<?php
/**
 * Uninstall handler - cleanup custom DB tables.
 * Checks aqs_preserve_data option before dropping tables.
 *
 * @package AQS
 */

defined('WP_UNINSTALL_PLUGIN') || exit;

$preserve = get_option('aqs_preserve_data', '1');

if ('0' === $preserve) {
    global $wpdb;

    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}aqs_contact_requests");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}aqs_responses");

    delete_option('aqs_version');
    delete_option('aqs_rate_limit');
    delete_option('aqs_preserve_data');
}
