<?php
/**
 * Database activation / deactivation.
 *
 * @package AQS
 */

defined('ABSPATH') || exit;

class AQS_Activator {

    public static function activate() {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset = $wpdb->get_charset_collate();

        $table_responses = $wpdb->prefix . AQS_TABLE_RESPONSES;
        $sql_responses   = "CREATE TABLE IF NOT EXISTS {$table_responses} (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            full_name VARCHAR(150) NOT NULL,
            email VARCHAR(150) NOT NULL,
            phone VARCHAR(30) NULL,
            answers LONGTEXT NOT NULL,
            axis_scores LONGTEXT NOT NULL,
            total_score DECIMAL(5,2) NOT NULL,
            classification VARCHAR(50) NOT NULL,
            ip_address VARCHAR(45) NULL,
            created_at DATETIME NOT NULL,
            contacted TINYINT(1) DEFAULT 0
        ) {$charset};";
        dbDelta($sql_responses);

        $table_contacts = $wpdb->prefix . AQS_TABLE_CONTACTS;
        $sql_contacts   = "CREATE TABLE IF NOT EXISTS {$table_contacts} (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            response_id BIGINT UNSIGNED NOT NULL,
            message TEXT NULL,
            preferred_contact_method VARCHAR(50) NULL,
            status VARCHAR(20) DEFAULT 'pending',
            created_at DATETIME NOT NULL,
            FOREIGN KEY (response_id) REFERENCES {$table_responses}(id) ON DELETE CASCADE
        ) {$charset};";
        dbDelta($sql_contacts);

        add_option('aqs_version', AQS_VERSION);
        add_option('aqs_rate_limit', array());
    }

    public static function deactivate() {
        // Cleanup transient data only; keep tables for data retention.
        delete_option('aqs_rate_limit');
    }

    public static function drop_tables() {
        global $wpdb;
        $table_contacts  = $wpdb->prefix . AQS_TABLE_CONTACTS;
        $table_responses = $wpdb->prefix . AQS_TABLE_RESPONSES;
        $wpdb->query("DROP TABLE IF EXISTS {$table_contacts}");
        $wpdb->query("DROP TABLE IF EXISTS {$table_responses}");
    }
}
