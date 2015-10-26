<?php

use \app\components\Form;
use \app\components\ActiveForm;
use app\components\Html;

/**
 * @var \prime\models\Project $project
 * @var \prime\models\forms\projects\Share $model
 */

$this->title = \Yii::t('app', 'Share {projectTitle} with:', [
    'projectTitle' => $model->project->title
]);
?>

<div class="col-xs-12">
    <?php
    $form = ActiveForm::begin([
        'id' => 'share-project',
        'method' => 'POST',
        "type" => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => [
            'showLabels' => true,
            'defaultPlaceholder' => false
        ]
    ]);

    echo \app\components\Form::widget([
        'form' => $form,
        'model' => $model,
        'columns' => 1,
        "attributes" => [
            'userIds' => [
                'type' => Form::INPUT_WIDGET,
                'widgetClass' => \kartik\widgets\Select2::class,
                'options' => [
                    'data' => $model->userOptions,
                    'options' => [
                    'multiple' => true
                        ]
                ]
            ],
            'permission' => [
                'label' => \Yii::t('app', 'Permission'),
                'type' => Form::INPUT_DROPDOWN_LIST,
                'items' => $model->permissionOptions
            ],
            'actions' => [
                'type' => Form::INPUT_RAW,
                'value' =>
                    Html::submitButton(\Yii::t('app', 'Submit'), ['class' => 'btn btn-primary col-xs-12'])
            ]
        ]
    ]);

    $form->end();
    ?>
</div>

