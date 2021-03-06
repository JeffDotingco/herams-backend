<?php

use kartik\widgets\ActiveForm;

/**
 * @var \prime\models\ar\Workspace $workspace
 * @var \prime\models\forms\Share $model
 */
$this->params['breadcrumbs'][] = [
    'label' => \Yii::t('app', 'Admin dashboard'),
    'url' => ['/admin']
];
$this->params['breadcrumbs'][] = [
    'label' => \Yii::t('app', 'Projects'),
    'url' => ['/project']
];
$this->params['breadcrumbs'][] = [
    'label' => \Yii::t('app', 'Workspaces for {project}', [
        'project' => $workspace->project->title
    ]),
    'url' => ['project/workspaces', 'id' => $workspace->project->id]
];
$this->title = \Yii::t('app', 'Share workspace {workspace}', ['workspace' => $workspace->title]);
$this->params['breadcrumbs'][] = $this->title;




?>
<div class="col-xs-12">
    <?php
    $form = ActiveForm::begin([
        'method' => 'POST',
        "type" => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => [
            'showLabels' => true,
            'defaultPlaceholder' => false
        ]
    ]);

    echo $model->renderForm($form);
    ?>
    <div class="col-xs-offset-11"><button type="submit" class="btn btn-primary">Share</button></div>
    <?php
    $form->end();
    ?>
    <h2><?=\Yii::t('app', 'Already shared with')?></h2>
    <?php
    echo $model->renderTable();
    ?>
</div>

