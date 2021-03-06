<?php


namespace prime\widgets\chart;


use prime\interfaces\HeramsResponseInterface;
use prime\objects\HeramsSubject;
use prime\traits\SurveyHelper;
use prime\widgets\element\Element;
use SamIT\LimeSurvey\Interfaces\SurveyInterface;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use function iter\take;

class Chart extends Element
{
    public const TYPE_DOUGHNUT = 'doughnut';
    public const TYPE_BAR = 'bar';
    use SurveyHelper;

    /** @var iterable */
    public $data = [];
    public $code;

    public $type = self::TYPE_DOUGHNUT;

    /** @var SurveyInterface */
    public $survey;

    /** @var ?string The title to use, if not set will fall back to retrieving it from the survey */
    public $title;

    /**
     * @var bool whether to skip multiple choice / ranking questions with no answer
     */
    public $skipEmpty = false;

    public $map;

    public $colors = [
        'A1' => 'green',
        'A2' => 'orange',
        'A3' => 'red',
        'A4' => 'gray'
    ];

    protected function getMap()
    {
        try {
            $question = $this->findQuestionByCode($this->code);
            switch ($question->getDimensions()) {
                case 0:
                    $map = $this->getAnswers($this->code);
                    break;
                case 1:
                    $map = $this->getAnswers($question->getTitle());
                    break;
                default:
                    die('unknown' . $question->getDimensions());
            }
        } catch (\InvalidArgumentException $e) {
            switch($this->code) {
                case 'subjectAvailabilityBucket':
                case 'availability':
                case 'fullyAvailable':
                    return $this->getAnswers($this->code);
                case 'causes':
                    $expr = strtr($this->element->project->getMap()->getSubjectExpression(), ['$' => 'x$']);
                    foreach($this->survey->getGroups() as $group) {
                        foreach($group->getQuestions() as $question) {
                            if (preg_match($expr, $question->getTitle())) {
                                return $this->getAnswers($question->getTitle());
                            }
                        }
                    }
                    return [];
                default:
                    $map = [];
            }

        }
        return array_merge($this->map ?? [], $map);
    }
    /**
     * @param HeramsResponseInterface[]|HeramsSubject[] $responses
     * @return array
     */
    protected function getDataSet(iterable $responses): array
    {
        // Check question type.
        try {
            $question = $this->findQuestionByCode($this->code);
            switch ($question->getDimensions()) {
                case 0:
                    // Single choice
                    return $this->getCounts($responses, $this->code);

                case 1:
                    // Ranking or multiple choice.
                    return $this->getCounts($responses, $this->code);
                default:
                    die('unknown' . $question->getDimensions());
            }
        } catch (\InvalidArgumentException $e) {
            // If the question is not set, it could be an abstract property.
            $getter = 'get'. ucfirst($this->code);

            // Call this method on each response.
            $counts = [];
            foreach($responses as $response) {
                $value = $response->$getter();
                if (!$this->skipEmpty || !empty($value)) {
                    if (is_scalar($value)) {
                        $counts[$value] = ($counts[$value] ?? 0) + 1;
                    } else {
                        foreach($value as $subValue) {
                            $counts[$subValue] = ($counts[$subValue] ?? 0) + 1;
                        }
                    }

                }

            }
            ksort($counts);
            return $counts;
        }
    }

    private function applyMapping(array $map, array $counts)
    {
        $result = [];

        foreach($map as $key => $label) {
            if ($this->skipEmpty && !array_key_exists($key, $counts)) {
                continue;
            }

            $result[$label] = $counts[$key] ?? null;
            unset($counts[$key]);
        }

        foreach($counts as $key => $value) {
            $result[$key] = $value;
        }


        return $result;
    }


    /**
     * @param HeramsResponseInterface[] $responses
     * @param string[] $codes
     */
    private function getCounts(iterable $responses, string $code, int $top = 3): array
    {
        $result = [];
        foreach($responses as $response) {
            $value = $response->getValueForCode($code);
            if (!empty($value)) {
                if (is_array($value)) {
                    foreach(take($top, $value) as $answer) {
                        $result[$answer] = ($result[$answer] ?? 0) + 1;
                    }
                } else {
                    $result[$value] = ($result[$value] ?? 0) + 1;
                }
            } elseif (!$this->skipEmpty) {
                $result[""] = ($result[""] ?? 0) + 1;
            }
        }
        ksort($result);
        return $result;
    }

    public function run()
    {
        $this->registerClientScript();


        $map = $this->getMap();
        $unmappedData = $this->getDataSet($this->data);

        $dataSet = $this->applyMapping($map, $unmappedData);

        $pointCount = count($dataSet);
        if ($pointCount > 30) {
            $this->type = self::TYPE_BAR;
        }

        $colors = [];

        $colorMap = $this->colors;

        foreach($unmappedData as $code => $count) {
            $colors[] = $colorMap[strtr($code, ['-' => '_'])] ?? '#000000';
        }

        $config = [
            'type' => $this->type,
            'data' => [
                'datasets' => [
                    [
                        'data' => array_values($dataSet),
                        'backgroundColor' => $colors
                    ]
                ],
                'labels' => array_keys($dataSet)
            ],
            'options' => [
                'layout' => [
                    'padding' => [
//                        'right' => 50
                    ]
                ],
                'scales' => [
                    'xAxes' => [
                        [
                            'display' => false,
                        ]
                    ]
                ],
                'elements' => [
                    'center' => [
                        'text' => new JsExpression('(chart) => {
                            let data = chart.data.datasets[0].data;
                            for (k in  chart.data.datasets[0]._meta) {
                                let meta = chart.data.datasets[0]._meta[k];
                                let total = meta.data.reduce((sum, elem) => {
                                   return elem.hidden ? sum : sum + data[elem._index];
                                }, 0);
                                return total;
                            }
                            
                            
                        }')
                    ]
                ],
                'legend' => [
                    'position' => 'right',
                    'display' => $this->type === self::TYPE_DOUGHNUT,
                    'labels' => [
                        'boxWidth' => 15
                    ]
                ],
                'cutoutPercentage' => 80,

                'title' => [
                    'display' => $this->type === self::TYPE_DOUGHNUT,
                    'text' => $this->title ?? $this->getTitleFromCode($this->code)
                ],
                'responsive' => true,
                'maintainAspectRatio' => false,
                'tooltips' => [
                    'callbacks' => [
                        'label' => new JsExpression('function(item, data) { 
                        console.log(this, item, data);
                            let value = data.datasets[item.datasetIndex].data[item.index];
                            let label = data.labels[item.index] || "";
                            let meta = this._chart.data.datasets[0]._meta;
                            for (let key in meta) {
                                let sum = meta[key].data.reduce((sum, elem) => {
                                    return elem.hidden ? sum : sum + data.datasets[item.datasetIndex].data[elem._index]
                               
                               
                                }, 0);
                                let percentage = Math.round(100 * value / sum) + "%";
                                return `${label}: ${value} (${percentage})`;
                            }
                        }'),

                    ]

                ]
            ]
        ];
        $jsConfig = Json::encode($config);


        echo Html::tag('canvas', '', [
            'id' => "{$this->getId()}-canvas"
        ]);

        $canvasId = Json::encode("{$this->getId()}-canvas");
        $this->view->registerJs(<<<JS
        (function() {
            let ctx = document.getElementById($canvasId).getContext('2d');
            let chart = new Chart(ctx, $jsConfig);
            
        })();
JS
        );
        parent::run();
    }

    protected function registerClientScript()
    {
       $this->view->registerAssetBundle(ChartBundle::class);



    }


}
