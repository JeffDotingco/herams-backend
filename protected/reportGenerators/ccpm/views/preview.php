<?php

use app\components\Html;
use yii\helpers\ArrayHelper;
use app\components\Form;

/**
 * @var \prime\models\ar\UserData $userData
 * @var \yii\web\View $this
 * @var \prime\reportGenerators\ccpm\Generator $generator
 * @var \prime\interfaces\ProjectInterface $project
 * @var \prime\interfaces\SignatureInterface $signature
 * @var \prime\interfaces\ResponseCollectionInterface $responses
 */

$generator = $this->context;

$scores = [
    '1.1' => $generator->calculateScore($responses, [67825 => ['q111', 'q112', 'q114', 'q118', 'q113', 'q115', 'q119', 'q116', 'q117'], 22814 => ['q111', 'q112', 'q113', 'q114', 'q115', 'q116']], 'average'),
    '1.1.1' => $generator->calculateScore($responses, [67825 => ['q111'], 22814 => []]),
    '1.1.2' => $generator->calculateScore($responses, [67825 => ['q112'], 22814 => ['q111']]),
    '1.1.3' => $generator->calculateScore($responses, [67825 => ['q114'], 22814 => ['q112']]),
    '1.1.4' => $generator->calculateScore($responses, [67825 => [], 22814 => ['q113']]),
    '1.1.5' => $generator->calculateScore($responses, [67825 => ['q118'], 22814 => ['q114']]),
    '1.1.6' => $generator->calculateScore($responses, [67825 => ['q113'], 22814 => []]),
    '1.1.7' => $generator->calculateScore($responses, [67825 => ['q115'], 22814 => ['q115']]),
    '1.1.8' => $generator->calculateScore($responses, [67825 => ['q119'], 22814 => ['q116']]),
    '1.1.9' => $generator->calculateScore($responses, [67825 => ['q116'], 22814 => []]),
    '1.1.10' => $generator->calculateScore($responses, [67825 => ['q117'], 22814 => []]),
    '1.2' => $generator->calculateScore($responses, [67825 => ['q121', 'q122', 'q123'], 22814 => ['q121', 'q122', 'q123']], 'average'),
    '1.2.1' => $generator->calculateScore($responses, [67825 => ['q121'], 22814 => []]),
    '1.2.2' => $generator->calculateScore($responses, [67825 => ['q122'], 22814 => ['q121']]),
    '1.2.3' => $generator->calculateScore($responses, [67825 => [], 22814 => ['q122']]),
    '1.2.4' => $generator->calculateScore($responses, [67825 => ['q123'], 22814 => ['q123']]),
    '2.1' => $generator->calculateScore($responses, [67825 => ['q211', 'q212', 'q213'], 22814 => ['q211', 'q212', 'q213']], 'average'),
    '2.1.1' => $generator->calculateScore($responses, [67825 => ['q211'], 22814 => ['q211']]),
    '2.1.2' => $generator->calculateScore($responses, [67825 => ['q212'], 22814 => ['q212']]),
    '2.1.3' => $generator->calculateScore($responses, [67825 => ['q213'], 22814 => ['q213']]),
    '2.2' => $generator->calculateScore($responses, [67825 => ['q221', 'q222[1]', 'q222[2]', 'q222[3]', 'q222[4]', 'q222[5]', 'q223[1]', 'q223[2]', 'q223[3]', 'q223[4]', 'q223[5]', 'q223[6]', 'q223[7]', 'q223[8]'], 22814 => ['q221', 'q222[1]', 'q222[2]', 'q222[3]', 'q222[4]', 'q222[5]', 'q223[1]', 'q223[2]', 'q223[3]', 'q223[4]', 'q223[5]', 'q223[6]', 'q223[7]', 'q223[8]']], 'average'),
    '2.2.1' => $generator->calculateScore($responses, [67825 => ['q221'], 22814 => ['q221']]),
    '2.2.2' => $generator->calculateScore($responses, [67825 => ['q222[1]'], 22814 => ['q222[1]']]),
    '2.2.3' => $generator->calculateScore($responses, [67825 => ['q222[2]'], 22814 => ['q222[2]']]),
    '2.2.4' => $generator->calculateScore($responses, [67825 => ['q222[3]'], 22814 => ['q222[3]']]),
    '2.2.5' => $generator->calculateScore($responses, [67825 => ['q222[4]'], 22814 => ['q222[4]']]),
    '2.2.6' => $generator->calculateScore($responses, [67825 => ['q222[5]'], 22814 => ['q222[5]']]),
    '2.2.7' => $generator->calculateScore($responses, [67825 => ['q223[1]'], 22814 => ['q223[1]']]),
    '2.2.8' => $generator->calculateScore($responses, [67825 => ['q223[2]'], 22814 => ['q223[2]']]),
    '2.2.9' => $generator->calculateScore($responses, [67825 => ['q223[3]'], 22814 => ['q223[3]']]),
    '2.2.10' => $generator->calculateScore($responses, [67825 => ['q223[4]'], 22814 => ['q223[4]']]),
    '2.2.11' => $generator->calculateScore($responses, [67825 => ['q223[5]'], 22814 => ['q223[5]']]),
    '2.2.12' => $generator->calculateScore($responses, [67825 => ['q223[6]'], 22814 => ['q223[6]']]),
    '2.2.13' => $generator->calculateScore($responses, [67825 => ['q223[7]'], 22814 => ['q223[7]']]),
    '2.2.14' => $generator->calculateScore($responses, [67825 => ['q223[8]'], 22814 => ['q223[8]']]),
    '2.3' => $generator->calculateScore($responses, [67825 => ['q231'], 22814 => ['q231']], 'average'),
    '2.3.1' => $generator->calculateScore($responses, [67825 => ['q231'], 22814 => ['q231']]),
    '3.1' => $generator->calculateScore($responses, [67825 => ['q311', 'q314', 'q312', 'q313', 'q315[1]', 'q315[2]', 'q315[3]', 'q315[4]', 'q315[5]', 'q315[6]', 'q315[7]', 'q315[8]', 'q316', 'q317', 'q318'], 22814 => ['q311', 'q312']], 'average'),
    '3.1.1' => $generator->calculateScore($responses, [67825 => ['q311'], 22814 => []]),
    '3.1.2' => $generator->calculateScore($responses, [67825 => ['q314'], 22814 => ['q311']]),
    '3.1.3' => $generator->calculateScore($responses, [67825 => ['q312'], 22814 => []]),
    '3.1.4' => $generator->calculateScore($responses, [67825 => ['q313'], 22814 => []]),
    '3.1.5' => $generator->calculateScore($responses, [67825 => ['q315[1]'], 22814 => []]),
    '3.1.6' => $generator->calculateScore($responses, [67825 => ['q315[2]'], 22814 => []]),
    '3.1.7' => $generator->calculateScore($responses, [67825 => ['q315[3]'], 22814 => []]),
    '3.1.8' => $generator->calculateScore($responses, [67825 => ['q315[4]'], 22814 => []]),
    '3.1.9' => $generator->calculateScore($responses, [67825 => ['q315[5]'], 22814 => []]),
    '3.1.10' => $generator->calculateScore($responses, [67825 => ['q315[6]'], 22814 => []]),
    '3.1.11' => $generator->calculateScore($responses, [67825 => ['q315[7]'], 22814 => []]),
    '3.1.12' => $generator->calculateScore($responses, [67825 => ['q315[8]'], 22814 => []]),
    '3.1.13' => $generator->calculateScore($responses, [67825 => ['q316'], 22814 => []]),
    '3.1.14' => $generator->calculateScore($responses, [67825 => ['q317'], 22814 => ['q312']]),
    '3.1.15' => $generator->calculateScore($responses, [67825 => ['q318'], 22814 => []]),
    '3.2' => $generator->calculateScore($responses, [67825 => ['q321', 'q322'], 22814 => ['q321']], 'average'),
    '3.2.1' => $generator->calculateScore($responses, [67825 => ['q321'], 22814 => []]),
    '3.2.2' => $generator->calculateScore($responses, [67825 => ['q322'], 22814 => ['q321']]),
    '3.3' => $generator->calculateScore($responses, [67825 => ['q331', 'q332', 'q333', 'q334'], 22814 => ['q331', 'q332', 'q333']], 'average'),
    '3.3.1' => $generator->calculateScore($responses, [67825 => ['q331'], 22814 => ['q331']]),
    '3.3.2' => $generator->calculateScore($responses, [67825 => ['q332'], 22814 => ['q332']]),
    '3.3.3' => $generator->calculateScore($responses, [67825 => ['q333'], 22814 => []]),
    '3.3.4' => $generator->calculateScore($responses, [67825 => ['q334'], 22814 => ['q333']]),
    '4.1' => $generator->calculateScore($responses, [67825 => ['q411'], 22814 => ['q411']], 'average'),
    '4.1.1' => $generator->calculateScore($responses, [67825 => ['q411'], 22814 => ['q411']]),
    '4.2' => $generator->calculateScore($responses, [67825 => ['q421'], 22814 => ['q421']], 'average'),
    '4.2.1' => $generator->calculateScore($responses, [67825 => ['q421'], 22814 => ['q421']]),
    '5' => $generator->calculateScore($responses, [67825 => ['q51', 'q52', 'q53', 'q54', 'q55', 'q56'], 22814 => ['q51', 'q52', 'q53']], 'average'),
    '5.1.1' => $generator->calculateScore($responses, [67825 => ['q51'], 22814 => ['q52']]),
    '5.1.2' => $generator->calculateScore($responses, [67825 => ['q52'], 22814 => []]),
    '5.1.3' => $generator->calculateScore($responses, [67825 => ['q53'], 22814 => []]),
    '5.1.4' => $generator->calculateScore($responses, [67825 => ['q54'], 22814 => []]),
    '5.1.5' => $generator->calculateScore($responses, [67825 => ['q55'], 22814 => ['q51']]),
    '5.1.6' => $generator->calculateScore($responses, [67825 => ['q56'], 22814 => ['q53']]),
    '6' => $generator->calculateScore($responses, [67825 => ['q61', 'q62', 'q63', 'q64', 'q65', 'q66'], 22814 => ['q61', 'q62']], 'average'),
    '6.1.1' => $generator->calculateScore($responses, [67825 => ['q61'], 22814 => []]),
    '6.1.2' => $generator->calculateScore($responses, [67825 => ['q62'], 22814 => []]),
    '6.1.3' => $generator->calculateScore($responses, [67825 => ['q63'], 22814 => ['q61']]),
    '6.1.4' => $generator->calculateScore($responses, [67825 => ['q64'], 22814 => ['q62']]),
    '6.1.5' => $generator->calculateScore($responses, [67825 => ['q65'], 22814 => []]),
    '7' => $generator->calculateScore($responses, [67825 => ['q71', 'q72'], 22814 => ['q71', 'q72']], 'average'),
    '7.1.1' => $generator->calculateScore($responses, [67825 => ['q71'], 22814 => ['q71']]),
    '7.1.2' => $generator->calculateScore($responses, [67825 => ['q72'], 22814 => ['q72']]),
];

$this->beginContent('@app/views/layouts/report.php');
?>
<style>
    <?=file_get_contents(__DIR__ . '/../../base/assets/css/grid.css')?>
    <?php include __DIR__ . '/../../base/assets/css/style.php'; ?>
    .background-good, .background-satisfactory, .background-unsatisfactory, .background-weak {
        font-weight: 600;
    }

    .background-good {
        background-color: #1fc63c;
        color: white;
    }

    .background-satisfactory {
        background-color: #ffe003;
        color: white;
    }

    .background-unsatisfactory {
        background-color: #ff9400;
        color: white;
    }

    .background-weak {
        background-color: red;
        color: white;
    }

    .text-good {
        color: #1fc63c;
    }

    .text-satisfactory {
        color: #ffe003;
    }

    .text-unsatisfactory {
        color: #ff9400;
    }

    .text-weak {
        color: red;
    }
</style>

<div class="container-fluid">
    <?=$this->render('header', ['project' => $project])?>
    <div class="row">
        <h1 class="col-xs-12"><?=$project->getLocality()?></h1>
    </div>
    <?php
    echo \prime\widgets\report\Columns::widget([
        'items' => [
            \Yii::t('ccpm', 'Level : {level}', ['level' => 'National']) . '<br>' . \Yii::t('ccpm', 'Completed on: {completedOn}', ['completedOn' => $signature->getTime()->format('d F - Y')]),
        ],
        'columnsInRow' => 2
    ]);
    ?>
    <hr>
    <div class="row">
        <h1 style="margin-top: 300px; margin-bottom: 300px; text-align: center;"><?=\Yii::t('ccpm', 'Final report')?></h1>
    </div>
</div>

<div class="container-fluid">
    <?=$this->render('header', ['project' => $project])?>
    <div class="row">
        <div class="col-xs-12">
        <h2><?=\Yii::t('ccpm', 'Overall response rate')?><span style="font-size: 0.5em; margin-left: 50px;">(<?=Yii::t('ccpm', 'Based on the number of organizations tat are part of the cluster')?></span></h2>
        </div>
    </div>
    <?php
    $responseRates = $generator->getResponseRates($responses);
    ?>
    <?=\prime\widgets\report\GraphWithNumbers::widget(['total' => $responseRates['total']['total1'], 'part' => $responseRates['total']['responses'], 'view' => $this])?>
    <?php
    $graphWidth = 3;
    echo \prime\widgets\report\Columns::widget([
        'items' => [
            \prime\widgets\report\GraphWithNumbers::widget(['total' => $responseRates[1]['total1'], 'part' => $responseRates[1]['responses'], 'title' => Yii::t('ccpm', 'International NGOs'), 'graphWidth' => $graphWidth, 'view' => $this]),
            \prime\widgets\report\GraphWithNumbers::widget(['total' => $responseRates[2]['total1'], 'part' => $responseRates[2]['responses'], 'title' => Yii::t('ccpm', 'National NGOs'), 'graphWidth' => $graphWidth, 'view' => $this]),
            \prime\widgets\report\GraphWithNumbers::widget(['total' => $responseRates[3]['total1'], 'part' => $responseRates[3]['responses'], 'title' => Yii::t('ccpm', 'UN Agencies'), 'graphWidth' => $graphWidth, 'view' => $this]),
            \prime\widgets\report\GraphWithNumbers::widget(['total' => $responseRates[4]['total1'], 'part' => $responseRates[4]['responses'], 'title' => Yii::t('ccpm', 'National Authorities'), 'graphWidth' => $graphWidth, 'view' => $this]),
            \prime\widgets\report\GraphWithNumbers::widget(['total' => $responseRates[5]['total1'], 'part' => $responseRates[5]['responses'], 'title' => Yii::t('ccpm', 'Donors'), 'graphWidth' => $graphWidth, 'view' => $this]),
            \prime\widgets\report\GraphWithNumbers::widget(['total' => $responseRates[6]['total1'], 'part' => $responseRates[6]['responses'], 'title' => Yii::t('ccpm', 'Other'), 'graphWidth' => $graphWidth, 'view' => $this]),
        ],
        'columnsInRow' => 2
    ]);

    ?>
</div>

    <div class="container-fluid">
        <?=$this->render('header', ['project' => $project])?>
        <div class="row">
            <div class="col-xs-12">
                <h2><?=\Yii::t('ccpm', 'Overall response rate 2')?><span style="font-size: 0.5em; margin-left: 50px;">(<?=Yii::t('ccpm', 'Based on the number of organizations tat are part of the cluster')?></span></h2>
            </div>
        </div>
        <?php
        $responseRates = $generator->getResponseRates($responses);
        ?>
        <?=\prime\widgets\report\GraphWithNumbers::widget(['total' => $responseRates['total']['total2'], 'part' => $responseRates['total']['responses'], 'view' => $this])?>
        <?php
        $graphWidth = 3;
        echo \prime\widgets\report\Columns::widget([
            'items' => [
                \prime\widgets\report\GraphWithNumbers::widget(['total' => $responseRates[1]['total2'], 'part' => $responseRates[1]['responses'], 'title' => Yii::t('ccpm', 'International NGOs'), 'graphWidth' => $graphWidth, 'view' => $this]),
                \prime\widgets\report\GraphWithNumbers::widget(['total' => $responseRates[2]['total2'], 'part' => $responseRates[2]['responses'], 'title' => Yii::t('ccpm', 'National NGOs'), 'graphWidth' => $graphWidth, 'view' => $this]),
                \prime\widgets\report\GraphWithNumbers::widget(['total' => $responseRates[3]['total2'], 'part' => $responseRates[3]['responses'], 'title' => Yii::t('ccpm', 'UN Agencies'), 'graphWidth' => $graphWidth, 'view' => $this]),
                \prime\widgets\report\GraphWithNumbers::widget(['total' => $responseRates[4]['total2'], 'part' => $responseRates[4]['responses'], 'title' => Yii::t('ccpm', 'National Authorities'), 'graphWidth' => $graphWidth, 'view' => $this]),
                \prime\widgets\report\GraphWithNumbers::widget(['total' => $responseRates[5]['total2'], 'part' => $responseRates[5]['responses'], 'title' => Yii::t('ccpm', 'Donors'), 'graphWidth' => $graphWidth, 'view' => $this]),
                \prime\widgets\report\GraphWithNumbers::widget(['total' => $responseRates[6]['total2'], 'part' => $responseRates[6]['responses'], 'title' => Yii::t('ccpm', 'Other'), 'graphWidth' => $graphWidth, 'view' => $this]),
            ],
            'columnsInRow' => 2
        ]);

        ?>
    </div>

<div class="container-fluid">
    <?=$this->render('header', ['project' => $project])?>
    <div class="row">
        <h2 class="col-xs-12"><?=\Yii::t('ccpm', 'Overall Performance')?></h2>
    </div>
    <?php

    $performanceStatusBlockColumns = [
        'items' => [
            [
                'content' => \Yii::t('ccpm', 'Score') . '<hr>> 75 %<br>51 % - 75 %<br>26 % - 50 %<br>< 26 %',
                'width' => 6
            ],
            [
                'content' => \Yii::t('ccpm', 'Performance status') . '<hr><span class="text-good">' . \Yii::t('ccpm', 'Good') . '</span><br><span class="text-satisfactory">' . \Yii::t('ccpm', 'Satisfactory') . '</span><br><span class="text-unsatisfactory">' . \Yii::t('ccpm', 'Unsatisfactory') . '</span><br><span class="text-weak">' . \Yii::t('ccpm', 'Weak') . '</span>',
                'width' => 6
            ]
        ],
        'columnsInRow' => 12
    ];

    $performanceStatusBlock =
        '<div class="col-xs-12" style="border: 1px solid black; padding-top: 15px; padding-bottom: 15px;">' . \prime\widgets\report\Columns::widget($performanceStatusBlockColumns) . '</div>';

    echo \prime\widgets\report\Columns::widget([
        'items' => [
            [
                'content' => $performanceStatusBlock,
                'width' => 4
            ],
            [
                'content' => $this->render('performanceStatusTable', ['generator' => $generator, 'scores' => $scores]),
                'width' => 8
            ],
        ],
        'columnsInRow' => 12
    ]);
    ?>
</div>

<?=$this->render('functionsAndReview', ['generator' => $generator, 'scores' => $scores, 'project' => $project, 'userData' => $userData])?>

<div class="container-fluid">
    <?=$this->render('header', ['project' => $project])?>
    <div class="row">
        <h2 class="col-xs-12"><?=\Yii::t('ccpm', 'Comments')?></h2>
    </div>

    <?=\prime\widgets\report\Comments::widget([
        'comments' => [
            \Yii::t('ccpm', 'General') => $generator->getQuestionValues($responses, [67825 => [], 22814 => ['q014']], function($value){return !empty($value);}),
            \Yii::t('ccpm', 'Supporting service delivery') => $generator->getQuestionValues($responses, [67825 => ['q124'], 22814 => ['q124']], function($value){return !empty($value);}),
            \Yii::t('ccpm', 'Informing strategic decision-making of the Humanitarian Coordinator/Humanitarian Country Team') => $generator->getQuestionValues($responses, [67825 => ['q232'], 22814 => ['q232']], function($value){return !empty($value);}),
            \Yii::t('ccpm', 'Planning and strategy development') => $generator->getQuestionValues($responses, [67825 => ['q335'], 22814 => ['q334']], function($value){return !empty($value);}),
            \Yii::t('ccpm', 'Advocacy') => $generator->getQuestionValues($responses, [67825 => ['q422'], 22814 => ['q422']], function($value){return !empty($value);}),
            \Yii::t('ccpm', 'Monitoring and reporting on implementation of cluster strategy and results') => $generator->getQuestionValues($responses, [67825 => ['q57'], 22814 => ['q54']], function($value){return !empty($value);}),
            \Yii::t('ccpm', 'Preparedness for recurrent disasters') => $generator->getQuestionValues($responses, [67825 => ['q66'], 22814 => ['q63']], function($value){return !empty($value);}),
            \Yii::t('ccpm', 'Accountability to affected populations') => $generator->getQuestionValues($responses, [67825 => ['q73'], 22814 => ['q73']], function($value){return !empty($value);}),
            \Yii::t('ccpm', 'Others') => $generator->getQuestionValues($responses, [67825 => ['q81'], 22814 => ['q81']], function($value){return !empty($value);})
        ]
    ])?>
</div>
<?php $this->endContent(); ?>