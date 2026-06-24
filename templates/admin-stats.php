<?php
/**
 * Admin statistics view with Chart.js.
 *
 * @package AQS
 */

defined('ABSPATH') || exit;
?>
<div class="wrap aqs-admin-wrap">
    <div class="aqs-admin-header">
        <h1><?php esc_html_e('الإحصائيات', 'aqs-family-role-model'); ?></h1>
    </div>

    <div class="aqs-stats-grid">
        <div class="aqs-stat-card">
            <h3><?php esc_html_e('إجمالي المشاركات', 'aqs-family-role-model'); ?></h3>
            <div class="aqs-stat-number"><?php echo intval($total); ?></div>
        </div>
        <div class="aqs-stat-card">
            <h3><?php esc_html_e('متوسط النتيجة', 'aqs-family-role-model'); ?></h3>
            <div class="aqs-stat-number"><?php echo esc_html($avg_score); ?></div>
        </div>
    </div>

    <div class="aqs-chart-grid">
        <div class="aqs-chart-container">
            <h3><?php esc_html_e('توزيع التصنيفات', 'aqs-family-role-model'); ?></h3>
            <canvas id="aqs-dist-chart" height="250"></canvas>
        </div>
        <div class="aqs-chart-container">
            <h3><?php esc_html_e('متوسط النتيجة لكل محور', 'aqs-family-role-model'); ?></h3>
            <canvas id="aqs-axis-chart" height="250"></canvas>
        </div>
    </div>

    <script>
    window.aqsAdminData = <?php echo wp_json_encode(array(
        'distLabels' => $dist_labels,
        'distCounts' => $dist_counts,
        'distColors' => $dist_colors,
        'axisLabels' => $axis_labels,
        'axisAvgs'   => $axis_avgs,
    )); ?>;
    </script>
</div>
