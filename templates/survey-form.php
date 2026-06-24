<?php
/**
 * Survey form template - rendered via [aqs_survey] shortcode.
 *
 * @package AQS
 */

defined('ABSPATH') || exit;
?>
<div class="aqs-survey-wrapper"
     x-data="aqsSurvey"
     x-init="init"
     dir="rtl">

    <script>
    window.aqsData = <?php echo wp_json_encode(array(
        'axes'           => $axes,
        'questions'      => $questions,
        'options'        => $options,
        'classifications' => $classifications,
    )); ?>;
    </script>

    <div class="aqs-card">
        <div class="aqs-card-header">
            <h2><?php esc_html_e('مقياس الأسرة القدوة', 'aqs-family-role-model'); ?></h2>
            <p><?php esc_html_e('قياس مدى كون أسرتك قدوة في 4 محاور', 'aqs-family-role-model'); ?></p>
        </div>

        <div class="aqs-card-body">
            <!-- Step: User Info -->
            <div x-show="step === 'info'">
                <div class="aqs-info-box">
                    <p><?php esc_html_e('هذا الاستبيان يساعدكم في تقييم أسرتكم وفق 4 محاور رئيسية: التأصيل القيمي، التماسك الأسري، المواطنة الرقمية، والأثر المجتمعي. يستغرق الاستبيان حوالي 5-7 دقائق.', 'aqs-family-role-model'); ?></p>
                </div>

                <div class="aqs-info-form">
                    <div class="aqs-field" :class="{ 'aqs-field-error': hasError('fullName') }">
                        <label><?php esc_html_e('الاسم الكامل', 'aqs-family-role-model'); ?> <span class="aqs-required">*</span></label>
                        <input type="text" x-model="fullName" placeholder="<?php esc_attr_e('أدخل اسمك الكامل', 'aqs-family-role-model'); ?>">
                        <span x-show="hasError('fullName')" x-text="getError('fullName')" class="aqs-error-text"></span>
                    </div>

                    <div class="aqs-field" :class="{ 'aqs-field-error': hasError('email') }">
                        <label><?php esc_html_e('البريد الإلكتروني', 'aqs-family-role-model'); ?> <span class="aqs-required">*</span></label>
                        <input type="email" x-model="email" placeholder="<?php esc_attr_e('example@domain.com', 'aqs-family-role-model'); ?>">
                        <span x-show="hasError('email')" x-text="getError('email')" class="aqs-error-text"></span>
                    </div>

                    <div class="aqs-field">
                        <label><?php esc_html_e('رقم الجوال (اختياري)', 'aqs-family-role-model'); ?></label>
                        <input type="tel" x-model="phone" placeholder="<?php esc_attr_e('05xxxxxxxx', 'aqs-family-role-model'); ?>">
                    </div>
                </div>

                <div class="aqs-nav">
                    <div></div>
                    <button type="button" class="aqs-btn aqs-btn-primary" @click="startSurvey">
                        <i class="fas fa-arrow-left"></i>
                        <?php esc_html_e('بدء الاستبيان', 'aqs-family-role-model'); ?>
                    </button>
                </div>
            </div>

            <!-- Step: Survey Questions -->
            <div x-show="step === 'survey'" x-cloak>
                <!-- Progress -->
                <div class="aqs-progress">
                    <div class="aqs-progress-bar">
                        <div class="aqs-progress-fill" :style="'width: ' + progressPercent + '%'"></div>
                    </div>
                    <div class="aqs-progress-text">
                        <span><?php esc_html_e('تم الإجابة على', 'aqs-family-role-model'); ?> <span x-text="answeredCount"></span> <?php esc_html_e('من', 'aqs-family-role-model'); ?> <span x-text="totalQuestions"></span></span>
                        <span x-text="'<?php esc_attr_e('السؤال', 'aqs-family-role-model'); ?> ' + globalQuestionStart + ' <?php esc_attr_e('من', 'aqs-family-role-model'); ?> ' + totalQuestions"></span>
                    </div>
                </div>

                <!-- Axis Header -->
                <div style="margin-bottom: 20px;">
                    <h3 class="aqs-axis-title" x-text="currentAxisTitle"></h3>
                    <span class="aqs-axis-sub" x-text="currentAxisSub"></span>
                </div>

                <!-- Questions -->
                <template x-for="(q, index) in currentAxisQuestions" :key="q.id">
                    <div class="aqs-question"
                         :class="{ 'aqs-question-missing': !q.answered && axisErrors.length > 0 }">
                        <div class="aqs-question-text">
                            <span class="aqs-question-number" x-text="q.number"></span>
                            <span x-text="q.text"></span>
                        </div>
                        <div class="aqs-options">
                            <template x-for="(label, score) in options">
                                <div class="aqs-option">
                                    <input type="radio"
                                           :id="q.id + '-' + score"
                                           :name="q.id"
                                           :value="score"
                                           :checked="answers[q.id] == score"
                                           @change="selectOption(q.id, score)">
                                    <label :for="q.id + '-' + score">
                                        <span x-text="label"></span>
                                        <span class="aqs-score-badge" x-text="'(' + score + ')'"></span>
                                    </label>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <!-- Axis Validation Error -->
                <div x-show="axisErrors.length > 0"
                     x-cloak
                     style="background: #fef3c7; border: 1px solid #f59e0b; border-radius: 12px; padding: 16px 20px; margin-top: 20px; display: flex; align-items: flex-start; gap: 12px;">
                    <i class="fas fa-exclamation-triangle" style="color: #d97706; font-size: 1.1rem; margin-top: 2px; flex-shrink: 0;"></i>
                    <div>
                        <strong style="color: #92400e; font-size: 0.9rem;"><?php esc_html_e('يرجى الإجابة على جميع الأسئلة', 'aqs-family-role-model'); ?></strong>
                        <p style="color: #b45309; font-size: 0.85rem; margin: 4px 0 0; line-height: 1.6;">
                            <?php esc_html_e('لم يتم الإجابة على الأسئلة التالية:', 'aqs-family-role-model'); ?>
                            <span x-text="axisErrors.join('، ')"></span>
                        </p>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="aqs-nav">
                    <button type="button"
                            class="aqs-btn aqs-btn-outline"
                            x-show="!isFirstAxis"
                            @click="prevAxis">
                        <i class="fas fa-arrow-right"></i>
                        <?php esc_html_e('السابق', 'aqs-family-role-model'); ?>
                    </button>

                    <div x-show="isFirstAxis"></div>

                    <button type="button"
                            class="aqs-btn aqs-btn-primary"
                            @click="nextAxis"
                            :disabled="submitting">
                        <template x-if="!submitting">
                            <span>
                                <template x-if="!isLastAxis">
                                    <span><i class="fas fa-arrow-left"></i> <?php esc_html_e('التالي', 'aqs-family-role-model'); ?></span>
                                </template>
                                <template x-if="isLastAxis">
                                    <span><i class="fas fa-check"></i> <?php esc_html_e('إرسال', 'aqs-family-role-model'); ?></span>
                                </template>
                            </span>
                        </template>
                        <template x-if="submitting">
                            <span><?php esc_html_e('جارٍ الإرسال...', 'aqs-family-role-model'); ?></span>
                        </template>
                    </button>
                </div>
            </div>

            <!-- Step: Result -->
            <div x-show="step === 'result'" x-cloak>
                <div class="aqs-result" x-data="{ showBreakdown: false }">
                    <!-- Score Circle -->
                    <div class="aqs-result-circle" :style="formattedResultCircle">
                        <div class="aqs-result-circle-inner">
                            <span class="aqs-result-score" x-text="result.total_score"></span>
                            <span class="aqs-result-label">/ 100</span>
                        </div>
                    </div>

                    <!-- Classification Badge -->
                    <div class="aqs-result-badge"
                         :style="{ background: classificationBg, color: classificationColor }"
                         x-text="result.label">
                    </div>

                    <!-- Message -->
                    <p class="aqs-result-message" x-text="result.message"></p>

                    <!-- Axis Breakdown Accordion -->
                    <div class="aqs-axis-breakdown">
                        <button type="button"
                                style="background: none; border: none; cursor: pointer; display: flex; align-items: center; gap: 8px; margin: 0 auto 16px; font-size: 0.9rem; font-weight: 700; color: var(--primary, #2090b0); font-family: var(--font, 'Cairo', sans-serif);"
                                @click="showBreakdown = !showBreakdown">
                            <i class="fas" :class="showBreakdown ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                            <?php esc_html_e('تفصيل النتائج حسب المحاور', 'aqs-family-role-model'); ?>
                        </button>

                        <div x-show="showBreakdown" x-cloak>
                            <template x-for="item in axisBreakdown" :key="item.name">
                                <div class="aqs-axis-item">
                                    <span class="aqs-axis-item-name" x-text="item.name"></span>
                                    <div class="aqs-axis-bar-wrap">
                                        <div class="aqs-axis-bar-fill" :style="'width: ' + item.percent + '%'"></div>
                                    </div>
                                    <span class="aqs-axis-item-score" x-text="item.score + '/25'"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Contact CTA + Reset -->
                    <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
                        <button type="button" class="aqs-btn aqs-btn-primary" @click="showContactForm">
                            <i class="fas fa-comments"></i>
                            <?php esc_html_e('تواصل معنا', 'aqs-family-role-model'); ?>
                        </button>
                        <button type="button" class="aqs-btn aqs-btn-outline" @click="resetSurvey">
                            <i class="fas fa-redo"></i>
                            <?php esc_html_e('إعادة الاستبيان', 'aqs-family-role-model'); ?>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step: Contact Form -->
            <div x-show="step === 'contact'" x-cloak>
                <template x-if="!contactSuccess">
                    <div class="aqs-contact-form">
                        <h3><?php esc_html_e('تواصل مع فريقنا', 'aqs-family-role-model'); ?></h3>
                        <p><?php esc_html_e('يسعدنا التواصل معكم وتقديم الدعم المناسب لأسرتكم.', 'aqs-family-role-model'); ?></p>

                        <div class="aqs-field">
                            <label><?php esc_html_e('رسالتك', 'aqs-family-role-model'); ?></label>
                            <textarea x-model="contactMessage" placeholder="<?php esc_attr_e('اكتب رسالتك هنا...', 'aqs-family-role-model'); ?>"></textarea>
                        </div>

                        <div class="aqs-field">
                            <label><?php esc_html_e('وسيلة التواصل المفضلة', 'aqs-family-role-model'); ?></label>
                            <select x-model="preferredMethod">
                                <option value=""><?php esc_html_e('-- اختر --', 'aqs-family-role-model'); ?></option>
                                <option value="phone"><?php esc_html_e('اتصال هاتفي', 'aqs-family-role-model'); ?></option>
                                <option value="email"><?php esc_html_e('البريد الإلكتروني', 'aqs-family-role-model'); ?></option>
                                <option value="whatsapp"><?php esc_html_e('واتساب', 'aqs-family-role-model'); ?></option>
                            </select>
                        </div>

                        <div class="aqs-nav">
                            <button type="button" class="aqs-btn aqs-btn-outline" @click="step = 'result'">
                                <i class="fas fa-arrow-right"></i>
                                <?php esc_html_e('رجوع', 'aqs-family-role-model'); ?>
                            </button>
                            <button type="button"
                                    class="aqs-btn aqs-btn-primary"
                                    @click="submitContact"
                                    :disabled="contactSubmitting">
                                <template x-if="!contactSubmitting">
                                    <span><i class="fas fa-paper-plane"></i> <?php esc_html_e('إرسال', 'aqs-family-role-model'); ?></span>
                                </template>
                                <template x-if="contactSubmitting">
                                    <span><?php esc_html_e('جارٍ الإرسال...', 'aqs-family-role-model'); ?></span>
                                </template>
                            </button>
                        </div>
                    </div>
                </template>

                <template x-if="contactSuccess">
                    <div class="aqs-success">
                        <div class="aqs-success-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <p class="aqs-success-message">
                            <?php esc_html_e('تم إرسال طلب التواصل بنجاح. سيتواصل معكم فريقنا قريباً.', 'aqs-family-role-model'); ?>
                        </p>
                        <button type="button" class="aqs-btn aqs-btn-primary" style="margin-top: 20px;" @click="resetSurvey">
                            <i class="fas fa-redo"></i>
                            <?php esc_html_e('إعادة الاستبيان', 'aqs-family-role-model'); ?>
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
