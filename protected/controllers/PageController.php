<?php


namespace prime\controllers;


use prime\actions\DeleteAction;
use prime\components\Controller;
use prime\controllers\page\Create;
use prime\controllers\page\Update;
use prime\models\ar\Page;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class PageController extends Controller
{
    public $layout = 'admin';

    public function actions()
    {
        return [
            'update' => Update::class,
            'create' => Create::class,
            'delete' => [
                'class' => DeleteAction::class,
                'query' => Page::find()
            ]
        ];
    }

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['delete']
                    ]
                ],
            ]
        );
    }
}