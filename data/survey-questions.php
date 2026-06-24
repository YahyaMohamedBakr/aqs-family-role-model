<?php
/**
 * Survey questions configuration.
 *
 * @package AQS
 */

defined('ABSPATH') || exit;

function aqs_get_axes() {
    return array(
        'axis_1' => array(
            'title' => __('التأصيل القيمي والنمذجة السلوكية', 'aqs-family-role-model'),
            'sub'   => __('البناء الداخلي', 'aqs-family-role-model'),
        ),
        'axis_2' => array(
            'title' => __('التماسك الأسري والمرونة النفسية', 'aqs-family-role-model'),
            'sub'   => __('بيئة العلاقات', 'aqs-family-role-model'),
        ),
        'axis_3' => array(
            'title' => __('الأسرة في العالم الرقمي', 'aqs-family-role-model'),
            'sub'   => __('المواطنة الرقمية', 'aqs-family-role-model'),
        ),
        'axis_4' => array(
            'title' => __('الأثر المجتمعي وتوجهات الرؤية', 'aqs-family-role-model'),
            'sub'   => __('المجتمع الحيوي', 'aqs-family-role-model'),
        ),
    );
}

function aqs_get_questions() {
    return array(
        'axis_1' => array(
            __('نحرص على تطبيق السلوك الإيجابي أمام الأبناء قبل أن نطلبه منهم', 'aqs-family-role-model'),
            __('نربط المواقف اليومية بالقيم الإسلامية بطريقة غير متكلفة', 'aqs-family-role-model'),
            __('نعتمد على الحوار والإقناع في غرس السلوكيات بدلاً من الأوامر الجافة والتلقين', 'aqs-family-role-model'),
            __('نتجنب التناقض بين ما نأمر به الأبناء وبين ما نمارسه نحن كآباء وأمهات', 'aqs-family-role-model'),
            __('يلتزم جميع أفراد الأسرة بقواعد واضحة وموحدة تنظم شؤون حياتهم اليومية ومتفق عليها بين أفرادها', 'aqs-family-role-model'),
        ),
        'axis_2' => array(
            __('نخصص وقتاً يومياً للحوار الأسري الحر بعيداً عن المشتتات التقنية', 'aqs-family-role-model'),
            __('نُدير الخلافات الزوجية والأسرية بعيداً عن مسامع الأبناء وبطريقة تحفظ الاحترام', 'aqs-family-role-model'),
            __('نوفر للأبناء بيئة أمنة تتيح لهم مصارحتنا بأخطائهم دون خوف من العقاب القاسي', 'aqs-family-role-model'),
            __('نتعامل مع الأزمات الطارئة بروح الفريق الواحد والمرونة العالية', 'aqs-family-role-model'),
            __('نوزع المسؤوليات المنزلية بعدل بين أفراد الأسرة لتعزيز روح الانتماء والمشاركة', 'aqs-family-role-model'),
        ),
        'axis_3' => array(
            __('نمَتلك اتفاقية واضحة لأوقات وأماكن استخدام الأجهزة الذكية داخل المنزل', 'aqs-family-role-model'),
            __('نناقش المحتوى الرقمي الذي يشاهده الأبناء بوعي ونحلله نقدياً لتعزيز حصانتهم الذاتية', 'aqs-family-role-model'),
            __('نحرص كوالدين على ضبط أوقات استخدامنا لهواتفنا لنكون قدوة لأبنائنا في الاستخدام المتوازن للتقنية', 'aqs-family-role-model'),
            __('نُوجّه الأبناء لاستخدام التقنية وأدوات الذكاء الاصطناعي في تطوير مهاراتهم النافعة', 'aqs-family-role-model'),
            __('نحترم خصوصية الأبناء الرقمية مع توفير رقابة واعية مبنية على الثقة المتبادلة', 'aqs-family-role-model'),
        ),
        'axis_4' => array(
            __('نمَارس سلوكيات المحافظة على البيئة وترشيد الاستهلاك والموارد كجزء من مسؤوليتنا', 'aqs-family-role-model'),
            __('نشجع أبناءنا على المشاركة في المبادرات التطوعية وخدمة المجتمع المحيط', 'aqs-family-role-model'),
            __('نربط طموحات الأبناء الدراسية والمهنية باحتياجات التنمية بالمملكة العربية السعودية ورؤيتها المستقبلية', 'aqs-family-role-model'),
            __('نحافظ على علاقات إيجابية وفعالة مع الجيران والأقارب', 'aqs-family-role-model'),
            __('نغرس في الأبناء مبادئ التسامح واحترام التنوع والتعايش السلمي مع الآخرين', 'aqs-family-role-model'),
        ),
    );
}

function aqs_get_options() {
    return array(
        5 => __('دائماً', 'aqs-family-role-model'),
        4 => __('غالباً', 'aqs-family-role-model'),
        3 => __('أحياناً', 'aqs-family-role-model'),
        2 => __('نادراً', 'aqs-family-role-model'),
        1 => __('أبداً', 'aqs-family-role-model'),
    );
}

function aqs_get_classifications() {
    return array(
        'role_model'        => array(
            'label' => __('أسرة قدوة', 'aqs-family-role-model'),
            'color' => 'var(--primary)',
            'bg'    => 'var(--primary-lighter)',
            'min'   => 80,
            'max'   => 100,
            'msg'   => __('تهانينا! أنتم أسرة قدوة ونموذج يُحتذى به. استمرّوا على هذا النهج في بناء جيل واعٍ بقيمه ومبادئه.', 'aqs-family-role-model'),
        ),
        'on_the_way'        => array(
            'label' => __('في الطريق إلى أن تكون قدوة', 'aqs-family-role-model'),
            'color' => 'var(--secondary)',
            'bg'    => 'var(--secondary-lighter)',
            'min'   => 60,
            'max'   => 79,
            'msg'   => __('أنتم تسيرون في الاتجاه الصحيح! مع بعض التركيز على بعض الجوانب يمكنكم أن تصبحوا أسرة قدوة. ننصحكم بطلب استشارة لتحديد أولويات التطوير.', 'aqs-family-role-model'),
        ),
        'needs_consultation' => array(
            'label' => __('تحتاج إلى جلسات علاجية واستشارات', 'aqs-family-role-model'),
            'color' => '#f59e0b',
            'bg'    => '#fef3c7',
            'min'   => 0,
            'max'   => 59,
            'msg'   => __('شكراً لثقتكم واهتمامكم بتطوير أسرتكم. نتفهم التحديات التي تواجهونها، ونحن هنا لدعمكم. فريقنا مستعد لتقديم استشارات مخصصة تساعدكم على بناء أسرة أقوى وأكثر تماسكاً. لا تترددوا في التواصل معنا.', 'aqs-family-role-model'),
        ),
    );
}
