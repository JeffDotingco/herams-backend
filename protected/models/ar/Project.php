<?php
declare(strict_types=1);

namespace prime\models\ar;

use prime\components\LimesurveyDataProvider;
use prime\interfaces\HeramsResponseInterface;
use prime\models\ActiveRecord;
use prime\models\permissions\Permission;
use prime\objects\HeramsCodeMap;
use prime\objects\HeramsSubject;
use SamIT\LimeSurvey\Interfaces\SurveyInterface;
use SamIT\Yii2\VirtualFields\VirtualFieldBehavior;
use SamIT\Yii2\VirtualFields\VirtualFieldQueryBehavior;
use yii\base\NotSupportedException;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\validators\BooleanValidator;
use yii\validators\NumberValidator;
use yii\validators\RangeValidator;
use yii\validators\RequiredValidator;
use yii\validators\SafeValidator;
use yii\validators\StringValidator;
use yii\validators\UniqueValidator;
use function iter\filter;

/**
 * Class Tool
 * @property int $id
 * @property int $base_survey_eid
 * @property string $title
 * @property string $visibility
 * @property Page[] $pages
 * @property int $status
 * @property Workspace[] $workspaces
 * @property-read SurveyInterface $survey
 */
class Project extends ActiveRecord {
    public const VISIBILITY_PUBLIC = 'public';
    public const VISIBILITY_PRIVATE = 'private';
    public const VISIBILITY_HIDDEN = 'hidden';
    public const STATUS_ONGOING = 0;
    public const STATUS_BASELINE = 1;
    public const STATUS_TARGET = 2;
    public const STATUS_EMERGENCY_SPECIFIC = 3;

    const PROGRESS_ABSOLUTE = 'absolute';
    const PROGRESS_PERCENTAGE = 'percentage';

    public function statusText(): string
    {
        return $this->statusOptions()[$this->status];
    }

    public function isHidden(): bool
    {
        return $this->visibility === self::VISIBILITY_HIDDEN;
    }

    public function visibilityOptions()
    {
        return [
            self::VISIBILITY_HIDDEN => 'Hidden, this project is only visible to people with permissions',
            self::VISIBILITY_PUBLIC => 'Public, anyone can view this project',
            self::VISIBILITY_PRIVATE => 'Private, this project is visible on the map and in the list, but people need permission to view it'
        ];
    }
    public static function find()
    {
        $result = new ActiveQuery(self::class);
        $result->attachBehaviors([
            VirtualFieldQueryBehavior::class => [
                'class' => VirtualFieldQueryBehavior::class
            ]
        ]);
        return $result;
    }


    public function init()
    {
        parent::init();
        $this->typemap = [
            'A1' => 'Primary',
            'A2' => 'Primary',
            'A3' => 'Secondary',
            'A4' => 'Secondary',
            'A5' => 'Tertiary',
            'A6' => 'Tertiary',
            "" => 'Other',
        ];

        $this->overrides = [];
        $this->status = self::STATUS_ONGOING;
    }

    public function statusOptions()
    {
        return [
            self::STATUS_ONGOING => 'Ongoing',
            self::STATUS_BASELINE => 'Baseline',
            self::STATUS_TARGET => 'Target',
            self::STATUS_EMERGENCY_SPECIFIC => 'Emergency specific'
        ];
    }

    public function attributeLabels()
    {
        return [
            'base_survey_eid' => \Yii::t('app', 'Survey')
        ];
    }

    public function attributeHints()
    {
        return [
            'name_code' => \Yii::t('app', 'Question code containing the name (case sensitive)'),
            'type_code' => \Yii::t('app', 'Question code containing the type (case sensitive)'),
            'typemap' => \Yii::t('app', 'Map facility types for use in the world map'),
            'status' => \Yii::t('app','Project status is shown on the world map')
        ];
    }

    /**
     * @return LimesurveyDataProvider
     */
    protected function limesurveyDataProvider() {
        return app()->limesurveyDataProvider;
    }

    /**
     * @return \SamIT\LimeSurvey\Interfaces\SurveyInterface
     */
    public function getSurvey(): SurveyInterface
    {
        return $this->limesurveyDataProvider()->getSurvey($this->base_survey_eid);
    }

    public function dataSurveyOptions()
    {
        $existing = Project::find()->select('base_survey_eid')->indexBy('base_survey_eid')->column();

        $surveys = filter(function($details) use ($existing) {
            return $this->base_survey_eid == $details['sid'] || !isset($existing[$details['sid']]);
        }, $this->limesurveyDataProvider()->listSurveys());

        $result = ArrayHelper::map($surveys, 'sid', function ($details) use ($existing) {
                return $details['surveyls_title'] . (($details['active'] == 'N') ? " (INACTIVE)" : "");
        });

        return $result;
    }

    public function getWorkspaces()
    {
        return $this->hasMany(Workspace::class, ['tool_id' => 'id'])->inverseOf('project');
    }

    public function getTypemapAsJson()
    {
        return Json::encode($this->typemap, JSON_PRETTY_PRINT);
    }

    public function getOverridesAsJson()
    {
        return Json::encode($this->overrides, JSON_PRETTY_PRINT);
    }

    public function setTypemapAsJson($value)
    {
        $this->typemap = Json::decode($value);
    }

    public function setOverridesAsJson(string $value)
    {
        $this->overrides = array_filter(Json::decode($value));
    }


    public function rules()
    {
        return [
            [[
                'title', 'base_survey_eid'
            ], RequiredValidator::class],
            [['title'], StringValidator::class],
            [['title'], UniqueValidator::class],
            [['base_survey_eid'], RangeValidator::class, 'range' => array_keys($this->dataSurveyOptions())],
            [['hidden'], BooleanValidator::class],
            [['latitude', 'longitude'], NumberValidator::class, 'integerOnly' => false],
            [['typemapAsJson', 'overridesAsJson'], SafeValidator::class],
            [['status'], RangeValidator::class, 'range' => array_keys($this->statusOptions())],
            [['visibility'], RangeValidator::class, 'range' => array_keys($this->visibilityOptions())]
        ];
    }

    public function behaviors()
    {
        return [
            'virtualFields' => [
                'class' => VirtualFieldBehavior::class,
                'virtualFields' => [
                    'workspaceCount' => [
                        VirtualFieldBehavior::CAST => VirtualFieldBehavior::CAST_INT,
                        VirtualFieldBehavior::GREEDY => Workspace::find()->limit(1)->select('count(*)')
                            ->where(['tool_id' => new Expression(self::tableName() . '.[[id]]')]),
                        VirtualFieldBehavior::LAZY => static function(self $model): int {
                            return (int) $model->getWorkspaces()->count();
                        }
                    ],
                    'facilityCount' => [
                        VirtualFieldBehavior::CAST => VirtualFieldBehavior::CAST_INT,
                        VirtualFieldBehavior::GREEDY => Response::find()->andWhere([
                                'workspace_id' => Workspace::find()->select('id')
                                    ->where(['tool_id' => new Expression(self::tableName() . '.[[id]]')]),
                            ])->addParams([':path' => '$.facilityCount'])->
                        select(new Expression('coalesce(json_unquote(json_extract([[overrides]], :path)), count(distinct [[hf_id]]))')),
                        VirtualFieldBehavior::LAZY => static function(self $model): int {
                            if ($model->workspaceCount === 0) {
                                return 0;
                            }
                            return (int) ($model->getOverride('facilityCount')
                                ?? $model->getResponses()->count(new Expression('DISTINCT [[hf_id]]')));
                        }
                    ],
                    'responseCount' => [
                        VirtualFieldBehavior::CAST => VirtualFieldBehavior::CAST_INT,
                        VirtualFieldBehavior::GREEDY => Response::find()->andWhere([
                            'workspace_id' => Workspace::find()->select('id')
                                ->where(['tool_id' => new Expression(self::tableName() . '.[[id]]')]),
                        ])->addParams([':path' => '$.responseCount'])->
                        select(new Expression('coalesce(json_unquote(json_extract([[overrides]], :path)), count(*))'))
                        ,
                        VirtualFieldBehavior::LAZY => static function(self $model): int {
                            if ($model->workspaceCount === 0) {
                                return 0;
                            }
                            return (int)($model->getOverride('responseCount') ?? $model->getResponses()->count());
                        }
                    ],
                    'contributorCount' => [
                        VirtualFieldBehavior::CAST => VirtualFieldBehavior::CAST_INT,
                        VirtualFieldBehavior::LAZY => static function(self $model): int {
                            $result = $model->getOverride('contributorCount');
                            if (!isset($result)) {
                                if ($model->workspaceCount === 0) {
                                    return 0;
                                }
                                $result = Permission::find()->where([
                                    'target' => Workspace::class,
                                    'target_id' => $model->getWorkspaces()->select('id'),
                                    'source' => User::class,
                                    'permission' => [
                                        Permission::PERMISSION_WRITE,
                                        Permission::PERMISSION_ADMIN
                                    ]
                                ])  ->distinct()
                                    ->select('source_id')
                                    ->count();

                            }
                            return (int) $result;
                        }
                    ]
                ]
            ]
        ];
    }


    public function  getResponses(): ActiveQuery
    {
        return $this->hasMany(Response::class, ['workspace_id' => 'id'])->via('workspaces');
    }


    public function getTypeCounts()
    {
        if (null !== $result = $this->getOverride('typeCounts')) {
            return $result;
        }
        \Yii::beginProfile(__FUNCTION__);
        $map = is_array($this->typemap) ? $this->typemap : [];
        // Always have a mapping for the empty / unknown value.
        if (!empty($map) && !isset($map[HeramsResponseInterface::UNKNOWN_VALUE])) {
            $map[HeramsResponseInterface::UNKNOWN_VALUE] = "Unknown";
        }
        // Initialize counts
        $counts = [];
        foreach($map as $key => $value) {
            $counts[$value] = 0;
        }

        $query = $this->getResponses()
            ->groupBy([
                "json_unquote(json_extract([[data]], '$.{$this->getMap()->getType()}'))"
            ])
            ->select([
                'count' => 'count(*)',
                'type' => "json_unquote(json_extract([[data]], '$.{$this->getMap()->getType()}'))",
            ])
             ->indexBy('type')
            ->asArray();

        foreach($query->column() as $type => $count) {
            if (empty($map)) {
                $counts[$type] = ($counts[$type] ?? 0) + 1;
            } elseif (isset($map[$type])) {
                $counts[$map[$type]]++;
            } else {
                $counts[$map[HeramsResponseInterface::UNKNOWN_VALUE]]++;
            }
        }

        \Yii::endProfile(__FUNCTION__);
        return $counts;
    }

    public function getFunctionalityCounts(): array
    {
        $query = $this->getResponses()
            ->groupBy([
                "json_unquote(json_extract([[data]], '$.{$this->getMap()->getFunctionality()}'))"
            ])
            ->select([
                'count' => 'count(*)',
                'functionality' => "json_unquote(json_extract([[data]], '$.{$this->getMap()->getFunctionality()}'))",
            ])
            ->indexBy('functionality')
            ->orderBy('functionality')
            ->asArray();

        $map = [
            'A1' => \Yii::t('app', 'Full'),
            'A2' => \Yii::t('app', 'Partial'),
            'A3' => \Yii::t('app', 'None'),
            HeramsResponseInterface::UNKNOWN_VALUE => \Yii::t('app', 'Unknown'),
        ];

        $result = [];
        foreach($query->column() as $key => $value) {
            $label = isset($map[$key]) ? $map[$key] : $map[HeramsResponseInterface::UNKNOWN_VALUE];
            $result[$label] = ($result[$label] ?? 0) + $value;
        }
        return $result;
    }


    public function getSubjectAvailabilityCounts(): array
    {
        \Yii::beginProfile(__FUNCTION__);
        $counts = [
            HeramsSubject::FULLY_AVAILABLE => 0,
            HeramsSubject::PARTIALLY_AVAILABLE => 0,
            HeramsSubject::NOT_AVAILABLE => 0,
            HeramsSubject::NOT_PROVIDED=> 0,
        ];
        /** @var HeramsResponseInterface $heramsResponse */
        foreach ($this->getResponses()->each() as $heramsResponse)
        {
            foreach ($heramsResponse->getSubjects() as $subject) {
                $subjectAvailability = $subject->getAvailability();
                if (!isset($subjectAvailability, $counts[$subjectAvailability])) {
                    continue;
                }
                $counts[$subjectAvailability]++;
            }
        }
        ksort($counts);
        $map = [
            'A1' => \Yii::t('app', 'Full'),
            'A2' => \Yii::t('app', 'Partial'),
            'A3' => \Yii::t('app', 'None'),
//            'A4' => \Yii::t('app', 'Not normally provided'),
        ];

        $result = [];
        foreach($counts as $key => $value) {
            if (isset($map[$key])) {
                $result[$map[$key]] = $value;
            }
        }

        \Yii::endProfile(__FUNCTION__);
        return $result;
    }

    public function getPermissions()
    {
        return $this->hasMany(Permission::class, ['target_id' => 'id'])
            ->andWhere(['target' => self::class]);
    }

    /**
     * String representation of object
     * @link https://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        throw new NotSupportedException();
    }

    /**
     * Constructs the object
     * @link https://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        throw new NotSupportedException();
    }

    public function getMap(): HeramsCodeMap
    {
        return new HeramsCodeMap();
    }

    public function getPages() {
        return $this->hasMany(Page::class, ['project_id' => 'id'])->andWhere(['parent_id' => null])->orderBy('sort');
    }

    public function getAllPages()
    {
        return $this->hasMany(Page::class, ['project_id' => 'id'])->orderBy('COALESCE([[parent_id]], [[id]])');
    }



    /**
     * @param $name
     * @return mixed|null
     */
    public function getOverride($name)
    {
        return $this->overrides[$name] ?? null;
    }

    public function exportDashboard(): array
    {
        $pages = [];
        foreach ($this->pages as $page) {
            $pages[] = $page->export();
        }
        return $pages;
    }
}
