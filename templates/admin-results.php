<?php
/**
 * Admin results list view.
 *
 * @package AQS
 */

defined('ABSPATH') || exit;
$classifications = aqs_get_classifications();
?>
<div class="wrap aqs-admin-wrap">
    <div class="aqs-admin-header">
        <h1><?php esc_html_e('النتائج', 'aqs-family-role-model'); ?></h1>
        <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin-ajax.php?action=aqs_export_csv'), 'aqs_export_csv')); ?>" class="button button-primary">
            <?php esc_html_e('تصدير CSV', 'aqs-family-role-model'); ?>
        </a>
    </div>

    <form method="get" style="margin-bottom: 20px; display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
        <input type="hidden" name="page" value="aqs-results">
        <select name="classification" style="min-width: 150px;">
            <option value=""><?php esc_html_e('جميع التصنيفات', 'aqs-family-role-model'); ?></option>
            <?php foreach ($classifications as $key => $data) : ?>
                <option value="<?php echo esc_attr($key); ?>" <?php selected($classification_filter, $key); ?>><?php echo esc_html($data['label']); ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="s" placeholder="<?php esc_attr_e('بحث...', 'aqs-family-role-model'); ?>" value="<?php echo esc_attr($search); ?>">
        <button type="submit" class="button"><?php esc_html_e('فلترة', 'aqs-family-role-model'); ?></button>
    </form>

    <div class="aqs-table-wrap">
        <table class="aqs-table">
            <thead>
                <tr>
                    <th><?php esc_html_e('م', 'aqs-family-role-model'); ?></th>
                    <th><?php esc_html_e('الاسم', 'aqs-family-role-model'); ?></th>
                    <th><?php esc_html_e('البريد', 'aqs-family-role-model'); ?></th>
                    <th><?php esc_html_e('النتيجة', 'aqs-family-role-model'); ?></th>
                    <th><?php esc_html_e('التصنيف', 'aqs-family-role-model'); ?></th>
                    <th><?php esc_html_e('التاريخ', 'aqs-family-role-model'); ?></th>
                    <th><?php esc_html_e('تفاصيل', 'aqs-family-role-model'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)) : ?>
                    <tr><td colspan="6" style="text-align: center; color: #9ca3af;"><?php esc_html_e('لا توجد نتائج', 'aqs-family-role-model'); ?></td></tr>
                <?php else : ?>
                    <?php foreach ($rows as $i => $row) :
                        $cl = isset($classifications[$row['classification']]) ? $classifications[$row['classification']] : array('label' => $row['classification'], 'color' => '#6b7280', 'bg' => '#f3f4f6');
                    ?>
                    <tr>
                        <td><?php echo intval($row['id']); ?></td>
                        <td><strong><?php echo esc_html($row['full_name']); ?></strong></td>
                        <td><?php echo esc_html($row['email']); ?></td>
                        <td><strong><?php echo esc_html($row['total_score']); ?>/100</strong></td>
                        <td><span class="aqs-classification-badge" style="background: <?php echo esc_attr($cl['bg'] ?? '#f3f4f6'); ?>; color: <?php echo esc_attr($cl['color'] ?? '#6b7280'); ?>;"><?php echo esc_html($cl['label']); ?></span></td>
                        <td><?php echo esc_html($row['created_at']); ?></td>
                        <td><a href="<?php echo esc_url(add_query_arg('view', $row['id'])); ?>" class="button button-small" style="font-size: 0.8rem;"><?php esc_html_e('عرض', 'aqs-family-role-model'); ?></a></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($total_pages > 1) : ?>
    <div class="aqs-pagination">
        <?php
        echo paginate_links(array(
            'base'      => add_query_arg('paged', '%#%'),
            'format'    => '',
            'current'   => $paged,
            'total'     => $total_pages,
            'prev_text' => '&laquo;',
            'next_text' => '&raquo;',
        ));
        ?>
    </div>
    <?php endif; ?>
</div>
