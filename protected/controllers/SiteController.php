<?php
    namespace app\controllers;
    use app\components\Controller;

    class SiteController extends Controller
    {
        public function accessRules() {
            $rules = [
				[
					'allow',
					'actions' => ['index']
				]
			];
			return array_merge($rules, parent::accessRules());
        }

        public function actionIndex()
        {
            return $this->render('index');
        }
    }