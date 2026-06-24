<?php
/**
 * Admin dashboard view.
 *
 * @package AQS
 */

defined('ABSPATH') || exit;
?>
<div class="wrap aqs-admin-wrap">
    <div class="aqs-admin-header">
        <h1><?php esc_html_e('مقياس الأسرة القدوة', 'aqs-family-role-model'); ?></h1>
        <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin-ajax.php?action=aqs_export_csv'), 'aqs_export_csv')); ?>" class="button button-primary">
            <i class="fas fa-download"></i> <?php esc_html_e('تصدير CSV', 'aqs-family-role-model'); ?>
        </a>
    </div>

    <div class="aqs-stats-grid">
        <div class="aqs-stat-card">
            <h3><?php esc_html_e('إجمالي المشاركات', 'aqs-family-role-model'); ?></h3>
            <div class="aqs-stat-number"><?php echo intval($total); ?></div>
        </div>
        <div class="aqs-stat-card">
            <h3><?php esc_html_e('متوسط النتيجة', 'aqs-family-role-model'); ?></h3>
            <div class="aqs-stat-number"><?php echo esc_html($avg_score ? number_format($avg_score, 1) : '0.0'); ?></div>
        </div>
        <div class="aqs-stat-card">
            <h3><?php esc_html_e('طلبات التواصل', 'aqs-family-role-model'); ?></h3>
            <div class="aqs-stat-number"><?php echo intval($contacts); ?></div>
        </div>
    </div>

    <div class="aqs-chart-grid">
        <?php foreach ($distribution as $key => $dist) : ?>
            <div class="aqs-stat-card" style="margin-bottom: 0;">
                <h3><?php echo esc_html($dist['label']); ?></h3>
                <div class="aqs-stat-number" style="font-size: 1.5rem;"><?php echo intval($dist['count']); ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <div style="margin-top: 28px;">
        <h2 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 16px;"><?php esc_html_e('آخر المشاركات', 'aqs-family-role-model'); ?></h2>
        <div class="aqs-table-wrap">
            <table class="aqs-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e('الاسم', 'aqs-family-role-model'); ?></th>
                        <th><?php esc_html_e('البريد', 'aqs-family-role-model'); ?></th>
                        <th><?php esc_html_e('النتيجة', 'aqs-family-role-model'); ?></th>
                        <th><?php esc_html_e('التصنيف', 'aqs-family-role-model'); ?></th>
                        <th><?php esc_html_e('التاريخ', 'aqs-family-role-model'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recent)) : ?>
                        <tr><td colspan="5" style="text-align: center; color: #9ca3af;"><?php esc_html_e('لا توجد مشاركات بعد', 'aqs-family-role-model'); ?></td></tr>
                    <?php else : ?>
                        <?php foreach ($recent as $row) :
                            $cl = isset($classifications[$row['classification']]) ? $classifications[$row['classification']] : array('label' => $row['classification'], 'color' => '#6b7280');
                        ?>
                        <tr>
                            <td><strong><?php echo esc_html($row['full_name']); ?></strong></td>
                            <td><?php echo esc_html($row['email']); ?></td>
                            <td><strong><?php echo esc_html($row['total_score']); ?>/100</strong></td>
                            <td><span class="aqs-classification-badge" style="background: <?php echo esc_attr($cl['bg'] ?? '#f3f4f6'); ?>; color: <?php echo esc_attr($cl['color'] ?? '#6b7280'); ?>;"><?php echo esc_html($cl['label']); ?></span></td>
                            <td><?php echo esc_html($row['created_at']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
