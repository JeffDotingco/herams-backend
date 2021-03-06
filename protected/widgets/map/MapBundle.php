<?php


namespace prime\widgets\map;


use yii\web\AssetBundle;

class MapBundle extends AssetBundle
{

    public $depends = [
        LeafletBundle::class,

    ];

    public $sourcePath = __DIR__ . '/assets';

    public $js = [
        [
            'https://cdn.jsdelivr.net/npm/chroma-js@2.0.2/chroma.min.js',
            'integrity' => 'sha256-A6e6m2HRvOpsUi37pgdyPYK2rbumr3kp6WcvGUMQ5Bc=',
            'crossorigin' => 'anonymous'
        ],
    ];
    public $css = [
        'map.css'
    ];

}