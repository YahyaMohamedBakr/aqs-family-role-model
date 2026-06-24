<?php
/**
 * Admin contact requests view.
 *
 * @package AQS
 */

defined('ABSPATH') || exit;
$classifications = aqs_get_classifications();
?>
<div class="wrap aqs-admin-wrap">
    <div class="aqs-admin-header">
        <h1><?php esc_html_e('طلبات التواصل', 'aqs-family-role-model'); ?></h1>
    </div>

    <div class="aqs-table-wrap">
        <table class="aqs-table">
            <thead>
                <tr>
                    <th><?php esc_html_e('م', 'aqs-family-role-model'); ?></th>
                    <th><?php esc_html_e('الاسم', 'aqs-family-role-model'); ?></th>
                    <th><?php esc_html_e('النتيجة', 'aqs-family-role-model'); ?></th>
                    <th><?php esc_html_e('الرسالة', 'aqs-family-role-model'); ?></th>
                    <th><?php esc_html_e('وسيلة التواصل', 'aqs-family-role-model'); ?></th>
                    <th><?php esc_html_e('الحالة', 'aqs-family-role-model'); ?></th>
                    <th><?php esc_html_e('التاريخ', 'aqs-family-role-model'); ?></th>
                    <th><?php esc_html_e('إجراء', 'aqs-family-role-model'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)) : ?>
                    <tr><td colspan="8" style="text-align: center; color: #9ca3af;"><?php esc_html_e('لا توجد طلبات تواصل', 'aqs-family-role-model'); ?></td></tr>
                <?php else : ?>
                    <?php foreach ($rows as $row) :
                        $cl = isset($classifications[$row['classification']]) ? $classifications[$row['classification']] : array('label' => '-', 'color' => '#6b7280', 'bg' => '#f3f4f6');
                        $status_class = 'aqs-status-' . $row['status'];
                    ?>
                    <tr>
                        <td><?php echo intval($row['id']); ?></td>
                        <td><strong><?php echo esc_html($row['full_name']); ?></strong></td>
                        <td><span class="aqs-classification-badge" style="background: <?php echo esc_attr($cl['bg'] ?? '#f3f4f6'); ?>; color: <?php echo esc_attr($cl['color'] ?? '#6b7280'); ?>;"><?php echo esc_html($row['total_score']); ?>/100</span></td>
                        <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo esc_html($row['message']); ?></td>
                        <td><?php echo esc_html($row['preferred_contact_method']); ?></td>
                        <td><span class="aqs-status <?php echo esc_attr($status_class); ?>"><?php echo esc_html($row['status']); ?></span></td>
                        <td><?php echo esc_html($row['created_at']); ?></td>
                        <td>
                            <form method="post" style="display: flex; gap: 6px;">
                                <?php wp_nonce_field('aqs_update_contact'); ?>
                                <input type="hidden" name="contact_id" value="<?php echo intval($row['id']); ?>">
                                <select name="status" style="font-size: 0.8rem; padding: 4px 8px;">
                                    <option value="pending" <?php selected($row['status'], 'pending'); ?>><?php esc_html_e('قيد الانتظار', 'aqs-family-role-model'); ?></option>
                                    <option value="contacted" <?php selected($row['status'], 'contacted'); ?>><?php esc_html_e('تم التواصل', 'aqs-family-role-model'); ?></option>
                                    <option value="closed" <?php selected($row['status'], 'closed'); ?>><?php esc_html_e('مغلق', 'aqs-family-role-model'); ?></option>
                                </select>
                                <button type="submit" name="update_status" class="button button-small"><?php esc_html_e('تحديث', 'aqs-family-role-model'); ?></button>
                            </form>
                        </td>
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
