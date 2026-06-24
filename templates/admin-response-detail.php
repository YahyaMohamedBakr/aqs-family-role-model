<?php
/**
 * Admin response detail view.
 *
 * @package AQS
 */

defined('ABSPATH') || exit;
?>
<div class="wrap aqs-admin-wrap">
    <div class="aqs-admin-header">
        <h1><?php esc_html_e('تفاصيل النتيجة', 'aqs-family-role-model'); ?></h1>
        <a href="<?php echo esc_url(admin_url('admin.php?page=aqs-results')); ?>" class="button">
            &larr; <?php esc_html_e('العودة للنتائج', 'aqs-family-role-model'); ?>
        </a>
    </div>

    <!-- User Info -->
    <div class="aqs-stats-grid" style="margin-bottom: 24px;">
        <div class="aqs-stat-card">
            <h3><?php esc_html_e('الاسم', 'aqs-family-role-model'); ?></h3>
            <div style="font-size: 1.1rem; font-weight: 700; color: #1a1a2e;"><?php echo esc_html($response['full_name']); ?></div>
        </div>
        <div class="aqs-stat-card">
            <h3><?php esc_html_e('البريد الإلكتروني', 'aqs-family-role-model'); ?></h3>
            <div style="font-size: 1rem; color: #4b5563;"><?php echo esc_html($response['email']); ?></div>
        </div>
        <div class="aqs-stat-card">
            <h3><?php esc_html_e('رقم الجوال', 'aqs-family-role-model'); ?></h3>
            <div style="font-size: 1rem; color: #4b5563;"><?php echo esc_html($response['phone'] ?: '-'); ?></div>
        </div>
        <div class="aqs-stat-card">
            <h3><?php esc_html_e('تاريخ الإرسال', 'aqs-family-role-model'); ?></h3>
            <div style="font-size: 1rem; color: #4b5563;"><?php echo esc_html($response['created_at']); ?></div>
        </div>
    </div>

    <!-- Score Overview -->
    <div class="aqs-table-wrap" style="padding: 24px; margin-bottom: 24px;">
        <div style="display: flex; align-items: center; gap: 32px; flex-wrap: wrap;">
            <div style="text-align: center;">
                <div style="width: 100px; height: 100px; border-radius: 50%; background: var(--gradient-primary, linear-gradient(135deg, #2090b0, #107080)); display: flex; align-items: center; justify-content: center; margin: 0 auto 8px;">
                    <span style="color: #fff; font-size: 1.8rem; font-weight: 900;"><?php echo esc_html($response['total_score']); ?></span>
                </div>
                <div style="font-size: 0.85rem; color: #9ca3af;">/ 100</div>
            </div>
            <div>
                <div style="font-size: 1.2rem; font-weight: 800; color: #1a1a2e; margin-bottom: 4px;">
                    <?php echo $classification ? esc_html($classification['label']) : esc_html($response['classification']); ?>
                </div>
                <?php if ($classification) : ?>
                    <p style="color: #4b5563; font-size: 0.9rem; line-height: 1.7; max-width: 500px; margin: 0;"><?php echo esc_html($classification['msg']); ?></p>
                <?php endif; ?>
                <div style="margin-top: 8px;">
                    <span style="font-size: 0.8rem; color: #9ca3af;"><?php esc_html_e('IP:', 'aqs-family-role-model'); ?> <?php echo esc_html($response['ip_address'] ?: '-'); ?></span>
                    <span style="font-size: 0.8rem; color: #9ca3af; margin-right: 16px;"><?php esc_html_e('تم التواصل:', 'aqs-family-role-model'); ?> <?php echo $response['contacted'] ? __('نعم', 'aqs-family-role-model') : __('لا', 'aqs-family-role-model'); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Axis Breakdown -->
    <div class="aqs-table-wrap" style="padding: 24px; margin-bottom: 24px;">
        <h2 style="font-size: 1rem; font-weight: 700; color: #1a1a2e; margin: 0 0 16px;"><?php esc_html_e('تفصيل النتائج حسب المحاور', 'aqs-family-role-model'); ?></h2>
        <?php foreach ($axes as $key => $axis) :
            $score  = isset($axis_scores[$key]) ? intval($axis_scores[$key]) : 0;
            $pct    = min(100, round(($score / 25) * 100));
            $color  = $pct >= 80 ? '#10b981' : ($pct >= 60 ? '#f59e0b' : '#ef4444');
        ?>
            <div style="margin-bottom: 16px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                    <span style="font-weight: 700; font-size: 0.9rem; color: #1a1a2e;"><?php echo esc_html($axis['title']); ?></span>
                    <span style="font-size: 0.85rem; color: #6b7280; font-weight: 600;"><?php echo esc_html($score); ?>/25</span>
                </div>
                <div style="height: 8px; background: #e5e7eb; border-radius: 9999px; overflow: hidden;">
                    <div style="height: 100%; width: <?php echo intval($pct); ?>%; background: <?php echo esc_attr($color); ?>; border-radius: 9999px; transition: width 0.5s;"></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- All Questions & Answers -->
    <div class="aqs-table-wrap" style="padding: 24px; margin-bottom: 24px;">
        <h2 style="font-size: 1rem; font-weight: 700; color: #1a1a2e; margin: 0 0 16px;"><?php esc_html_e('الإجابات التفصيلية', 'aqs-family-role-model'); ?></h2>
        <?php $qnum = 1; ?>
        <?php foreach ($axes as $key => $axis) : ?>
            <div style="margin-bottom: 24px;">
                <h3 style="font-size: 0.95rem; font-weight: 700; color: #2090b0; margin: 0 0 12px; padding-bottom: 8px; border-bottom: 2px solid #e0f4fa;">
                    <?php echo esc_html($axis['title']); ?>
                    <span style="font-weight: 400; color: #6b7280;"> — <?php echo esc_html($axis['sub']); ?></span>
                </h3>

                <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                    <thead>
                        <tr style="background: #f9fafb;">
                            <th style="padding: 8px 12px; text-align: right; font-weight: 700; color: #374151; border-bottom: 2px solid #e5e7eb; width: 40px;">#</th>
                            <th style="padding: 8px 12px; text-align: right; font-weight: 700; color: #374151; border-bottom: 2px solid #e5e7eb;"><?php esc_html_e('السؤال', 'aqs-family-role-model'); ?></th>
                            <th style="padding: 8px 12px; text-align: center; font-weight: 700; color: #374151; border-bottom: 2px solid #e5e7eb; width: 100px;"><?php esc_html_e('الإجابة', 'aqs-family-role-model'); ?></th>
                            <th style="padding: 8px 12px; text-align: center; font-weight: 700; color: #374151; border-bottom: 2px solid #e5e7eb; width: 60px;"><?php esc_html_e('الدرجة', 'aqs-family-role-model'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($questions[$key] as $qi => $question) :
                            $qid    = $key . '_q' . $qi;
                            $answer = isset($answers[$qid]) ? intval($answers[$qid]) : 0;
                            $label  = isset($options[$answer]) ? $options[$answer] : '-';
                        ?>
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 10px 12px; color: #9ca3af; font-weight: 600;"><?php echo intval($qnum); ?></td>
                            <td style="padding: 10px 12px; color: #1a1a2e; line-height: 1.6;"><?php echo esc_html($question); ?></td>
                            <td style="padding: 10px 12px; text-align: center; font-weight: 600; color: <?php echo $answer >= 4 ? '#10b981' : ($answer >= 3 ? '#f59e0b' : '#ef4444'); ?>;"><?php echo esc_html($label); ?></td>
                            <td style="padding: 10px 12px; text-align: center; font-weight: 700; color: #374151;"><?php echo intval($answer); ?></td>
                        </tr>
                        <?php $qnum++; endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Contact Requests -->
    <?php if (!empty($contacts)) : ?>
    <div class="aqs-table-wrap" style="padding: 24px;">
        <h2 style="font-size: 1rem; font-weight: 700; color: #1a1a2e; margin: 0 0 16px;"><?php esc_html_e('طلبات التواصل', 'aqs-family-role-model'); ?></h2>
        <?php foreach ($contacts as $contact) :
            $status_class = 'aqs-status-' . $contact['status'];
        ?>
        <div style="background: #f9fafb; border-radius: 12px; padding: 16px; margin-bottom: 12px; border: 1px solid #e5e7eb;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; flex-wrap: wrap; gap: 8px;">
                <span style="font-size: 0.8rem; color: #9ca3af;"><?php echo esc_html($contact['created_at']); ?></span>
                <span style="font-size: 0.8rem; color: #6b7280;"><?php esc_html_e('وسيلة التواصل:', 'aqs-family-role-model'); ?> <?php echo esc_html($contact['preferred_contact_method'] ?: '-'); ?></span>
                <span class="aqs-status <?php echo esc_attr($status_class); ?>"><?php echo esc_html($contact['status']); ?></span>
            </div>
            <?php if ($contact['message']) : ?>
                <p style="color: #4b5563; font-size: 0.85rem; line-height: 1.7; margin: 0;"><?php echo esc_html($contact['message']); ?></p>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <p style="margin-top: 24px;">
        <a href="<?php echo esc_url(admin_url('admin.php?page=aqs-results')); ?>" class="button">
            &larr; <?php esc_html_e('العودة للنتائج', 'aqs-family-role-model'); ?>
        </a>
    </p>
</div>
