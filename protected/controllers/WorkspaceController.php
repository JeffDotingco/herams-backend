<?php


namespace prime\controllers;


use prime\actions\DeleteAction;
use prime\actions\ExportCsvAction;
use prime\components\Controller;
use prime\controllers\workspace\Configure;
use prime\controllers\workspace\Create;
use prime\controllers\workspace\Download;
use prime\controllers\workspace\Import;
use prime\controllers\workspace\Limesurvey;
use prime\controllers\workspace\Refresh;
use prime\controllers\workspace\Share;
use prime\controllers\workspace\Update;
use prime\models\ar\Workspace;
use prime\models\permissions\Permission;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Request;
use yii\web\User;

class WorkspaceController extends Controller
{
    public $layout = '//admin';

    public function actions()
    {
        return [
            'configure' => Configure::class,
            'export' => [
                'class' => ExportCsvAction::class,
                'subject' => function(Request $request) {
                      return Workspace::findOne(['id' => $request->getQueryParam('id')]);
                },
                'responseIterator' => function(Workspace $workspace) {
                    return $workspace->getResponses()->each();
                },
                'surveyFinder' => function(Workspace $workspace) {
                    return $workspace->project->getSurvey();
                },
                'checkAccess' => function(Workspace $workspace, User $user) {
                    return $user->can(Permission::PERMISSION_EXPORT, $workspace);
                }
            ],
            'limesurvey' => Limesurvey::class,
            'update' => Update::class,
            'create' => Create::class,
            'share' => Share::class,
            'import' => Import::class,
            'refresh' => Refresh::class,
            'delete' => [
                'class' => DeleteAction::class,
                'query' => Workspace::find(),
                'redirect' => function(Workspace $workspace) {
                    return ['/project/workspaces', 'id' => $workspace->tool_id];
                }
            ],

        ];
    }

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(),
            [
                'verb' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'create' => ['get', 'post']
                    ]
                ],
                'access' => [
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ]
                ]
            ]
        );
    }



}