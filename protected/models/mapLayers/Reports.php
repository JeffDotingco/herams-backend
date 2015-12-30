<?php

namespace prime\models\mapLayers;

use app\queries\ProjectQuery;
use prime\models\ar\ProjectCountry;
use prime\models\ar\Report;
use prime\models\Country;
use prime\models\MapLayer;
use prime\models\search\Project;
use prime\objects\ProjectCollection;
use yii\web\Controller;
use yii\web\JsExpression;
use yii\web\View;

class Reports extends MapLayer
{
    protected $projectQuery;

    public function __construct(ProjectQuery $projectQuery, $config = [])
    {
        $this->projectQuery = $projectQuery;
        parent::__construct($config);
    }


    public function init()
    {
        $this->allowPointSelect = true;
        $this->joinBy = ['ISO_3_CODE', 'iso_3'];
        $this->name = \Yii::t('app', 'Reports');
        $this->showInLegend = true;
        $this->addPointEventHandler('select', new JsExpression("function(e){select(this, 'reports'); return false;}"));
        parent::init();
    }

    protected function prepareData()
    {
        $this->data = array_map(function($country) {
             return ['iso_3' => $country, 'id' => $country];
        //}, Project::find()->innerJoinWith(['reports'])->select('country_iso_3')->column());
        }, $this->projectQuery->select('country_iso_3')->column());
    }
}