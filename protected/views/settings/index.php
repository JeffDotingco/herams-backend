<?php
/** @var \yii\web\View $this */
/** @var \prime\models\forms\user\Settings $settings */

$this->params['subMenu'] = [
    'items' => [
        [
            'label' => \app\components\Html::submitButton(\Yii::t('app', 'Save'), ['form' => 'settings', 'class' => 'btn btn-primary'])
        ],
    ]
];

$form = \kartik\form\ActiveForm::begin([
    'id' => 'settings',
    'method' => 'post',
    'type' => \kartik\form\ActiveForm::TYPE_HORIZONTAL
]);

foreach ($settings->safeAttributes() as $setting) {
    $options = lcfirst($setting) . 'Options';
    if(method_exists($settings, $options)) {
        echo $form->field($settings, $setting)->dropDownList($settings->$options());
    } else {
        echo $form->field($settings, $setting)->textInput();
    }
}
$form->end();
