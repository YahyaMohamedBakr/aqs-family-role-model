<?php
/**
 * Admin settings view - data preservation on uninstall.
 *
 * @package AQS
 */

defined('ABSPATH') || exit;
?>
<div class="wrap aqs-admin-wrap">
    <div class="aqs-admin-header">
        <h1><?php esc_html_e('الإعدادات', 'aqs-family-role-model'); ?></h1>
    </div>

    <div class="aqs-table-wrap" style="padding: 24px;">
        <form method="post">
            <?php wp_nonce_field('aqs_save_settings'); ?>

            <table class="form-table">
                <tr>
                    <th scope="row" style="font-size: 0.9rem; font-weight: 700; color: #1a1a2e; padding: 16px 0;">
                        <?php esc_html_e('الاحتفاظ بالبيانات عند حذف البلجن', 'aqs-family-role-model'); ?>
                    </th>
                    <td style="padding: 16px 0;">
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 0.9rem; color: #4b5563;">
                            <input type="checkbox" name="aqs_preserve_data" value="1" <?php checked($preserve_data, '1'); ?> style="width: 18px; height: 18px;">
                            <?php esc_html_e('نعم، احتفظ بالبيانات (جداول الاستبيان ونتائج المشاركين) عند حذف البلجن', 'aqs-family-role-model'); ?>
                        </label>
                        <p style="color: #9ca3af; font-size: 0.85rem; margin: 8px 0 0 28px; line-height: 1.6;">
                            <?php esc_html_e('عند إلغاء التحديد: سيتم حذف جميع جداول قاعدة البيانات والنتائج نهائياً عند حذف البلجن. يُنصح بالاحتفاظ بها إذا كنت تخطط لإعادة تثبيت البلجن لاحقاً.', 'aqs-family-role-model'); ?>
                        </p>
                    </td>
                </tr>
            </table>

            <p style="padding-top: 16px;">
                <button type="submit" name="save_aqs_settings" class="button button-primary">
                    <?php esc_html_e('حفظ الإعدادات', 'aqs-family-role-model'); ?>
                </button>
            </p>
        </form>
    </div>
</div>
