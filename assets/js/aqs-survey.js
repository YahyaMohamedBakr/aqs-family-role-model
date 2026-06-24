/**
 * AQS Survey - Alpine.js
 * Interactive multi-step survey with scoring.
 */
document.addEventListener('alpine:init', () => {
    Alpine.data('aqsSurvey', () => ({
        // Steps: 'info' -> 'survey' -> 'result' -> 'contact'
        step: 'info',
        currentAxis: 0,
        axes: [],
        questions: [],
        options: [],
        classifications: {},
        axisKeys: [],

        // User data
        fullName: '',
        email: '',
        phone: '',

        // Answers: { axis_1_q0: 5, axis_1_q1: 4, ... }
        answers: {},

        // Validation errors
        errors: {},

        // Results
        result: null,
        responseId: 0,
        submitting: false,
        contactSubmitting: false,
        contactSuccess: false,

        // Contact form
        contactMessage: '',
        preferredMethod: '',

        // Computed
        get totalQuestions() {
            let count = 0;
            for (const key of this.axisKeys) {
                count += (this.questions[key] || []).length;
            }
            return count;
        },

        get answeredCount() {
            let count = 0;
            for (const key of this.axisKeys) {
                const qs = this.questions[key] || [];
                for (let i = 0; i < qs.length; i++) {
                    const qid = key + '_q' + i;
                    if (this.answers[qid] >= 1 && this.answers[qid] <= 5) {
                        count++;
                    }
                }
            }
            return count;
        },

        get progressPercent() {
            if (this.totalQuestions === 0) return 0;
            return Math.round((this.answeredCount / this.totalQuestions) * 100);
        },

        get currentAxisKey() {
            return this.axisKeys[this.currentAxis] || '';
        },

        get currentAxisQuestions() {
            const key = this.currentAxisKey;
            const qs = this.questions[key] || [];
            const axes = this.axes;
            return qs.map((q, i) => ({
                id: key + '_q' + i,
                text: q,
                number: this.getGlobalQuestionNumber(key, i),
            }));
        },

        get globalQuestionStart() {
            let count = 0;
            for (let a = 0; a < this.currentAxis; a++) {
                const key = this.axisKeys[a];
                count += (this.questions[key] || []).length;
            }
            return count + 1;
        },

        get currentAxisTitle() {
            const key = this.currentAxisKey;
            return this.axes[key] ? this.axes[key].title : '';
        },

        get currentAxisSub() {
            const key = this.currentAxisKey;
            return this.axes[key] ? this.axes[key].sub : '';
        },

        get isLastAxis() {
            return this.currentAxis >= this.axisKeys.length - 1;
        },

        get isFirstAxis() {
            return this.currentAxis === 0;
        },

        get formattedResultCircle() {
            if (!this.result) return {};
            const pct = this.result.total_score;
            return {
                '--aqs-percent': pct + '%',
            };
        },

        get classificationColor() {
            if (!this.result) return '';
            return this.result.color;
        },

        get classificationBg() {
            if (!this.result) return '';
            return this.result.bg;
        },

        get axisBreakdown() {
            if (!this.result || !this.result.axis_scores) return [];
            const keys = this.axisKeys;
            return keys.map(key => {
                const maxPerAxis = 25;
                const score = this.result.axis_scores[key] || 0;
                const pct = Math.round((score / maxPerAxis) * 100);
                return {
                    name: this.axes[key] ? this.axes[key].title : key,
                    score: score,
                    percent: Math.min(100, pct),
                };
            });
        },

        init() {
            this.axes = window.aqsData ? window.aqsData.axes : {};
            this.questions = window.aqsData ? window.aqsData.questions : {};
            this.options = window.aqsData ? window.aqsData.options : {};
            this.classifications = window.aqsData ? window.aqsData.classifications : {};
            this.axisKeys = Object.keys(this.axes);

            // Pre-fill answers with 0
            for (const key of this.axisKeys) {
                const qs = this.questions[key] || [];
                for (let i = 0; i < qs.length; i++) {
                    this.answers[key + '_q' + i] = 0;
                }
            }
        },

        getGlobalQuestionNumber(axisKey, qIndex) {
            let num = 0;
            for (const key of this.axisKeys) {
                if (key === axisKey) {
                    return num + qIndex + 1;
                }
                num += (this.questions[key] || []).length;
            }
            return num + qIndex + 1;
        },

        selectOption(questionId, value) {
            this.answers[questionId] = parseInt(value);
        },

        hasError(field) {
            return this.errors[field] !== undefined;
        },

        getError(field) {
            return this.errors[field] || '';
        },

        nextAxis() {
            if (this.isLastAxis) {
                this.submitSurvey();
            } else {
                this.currentAxis++;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },

        prevAxis() {
            if (this.currentAxis > 0) {
                this.currentAxis--;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },

        startSurvey() {
            this.errors = {};
            let hasError = false;

            if (!this.fullName.trim()) {
                this.errors.fullName = 'الاسم مطلوب';
                hasError = true;
            }

            if (!this.email.trim()) {
                this.errors.email = 'البريد الإلكتروني مطلوب';
                hasError = true;
            } else if (!this.isValidEmail(this.email)) {
                this.errors.email = 'البريد الإلكتروني غير صحيح';
                hasError = true;
            }

            if (hasError) return;

            this.step = 'survey';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        },

        submitSurvey() {
            this.submitting = true;
            this.errors = {};

            const formData = new FormData();
            formData.append('action', 'aqs_submit_survey');
            formData.append('nonce', aqs_ajax.nonce);
            formData.append('full_name', this.fullName);
            formData.append('email', this.email);
            formData.append('phone', this.phone);

            for (const [key, val] of Object.entries(this.answers)) {
                formData.append('answers[' + key + ']', val);
            }

            fetch(aqs_ajax.ajaxurl, {
                method: 'POST',
                body: formData,
            })
            .then(res => res.json())
            .then(data => {
                this.submitting = false;
                if (data.success) {
                    this.result = data.data.result;
                    this.responseId = data.data.response_id;
                    this.step = 'result';
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                    if (data.data && data.data.errors) {
                        this.errors = data.data.errors;
                    }
                    if (data.data && data.data.message) {
                        alert(data.data.message);
                    }
                }
            })
            .catch(() => {
                this.submitting = false;
                alert('حدث خطأ في الاتصال. يرجى المحاولة مرة أخرى.');
            });
        },

        showContactForm() {
            this.step = 'contact';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        submitContact() {
            this.contactSubmitting = true;

            const formData = new FormData();
            formData.append('action', 'aqs_submit_contact');
            formData.append('nonce', aqs_ajax.nonce);
            formData.append('response_id', this.responseId);
            formData.append('message', this.contactMessage);
            formData.append('preferred_method', this.preferredMethod);

            fetch(aqs_ajax.ajaxurl, {
                method: 'POST',
                body: formData,
            })
            .then(res => res.json())
            .then(data => {
                this.contactSubmitting = false;
                if (data.success) {
                    this.contactSuccess = true;
                } else {
                    alert(data.data && data.data.message ? data.data.message : 'حدث خطأ');
                }
            })
            .catch(() => {
                this.contactSubmitting = false;
                alert('حدث خطأ في الاتصال');
            });
        },

        resetSurvey() {
            this.step = 'info';
            this.currentAxis = 0;
            this.fullName = '';
            this.email = '';
            this.phone = '';
            this.answers = {};
            this.errors = {};
            this.result = null;
            this.responseId = 0;
            this.contactMessage = '';
            this.preferredMethod = '';
            this.contactSuccess = false;

            // Re-initialize answers
            for (const key of this.axisKeys) {
                const qs = this.questions[key] || [];
                for (let i = 0; i < qs.length; i++) {
                    this.answers[key + '_q' + i] = 0;
                }
            }

            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
    }));
});
