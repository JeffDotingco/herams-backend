<?php

namespace prime\reportGenerators\healthClusterDashboard;

use prime\interfaces\ProjectInterface;
use prime\interfaces\ReportInterface;
use prime\interfaces\ResponseCollectionInterface;
use prime\interfaces\SignatureInterface;
use prime\interfaces\SurveyCollectionInterface;
use prime\interfaces\UserDataInterface;
use prime\models\ar\UserData;
use prime\objects\Report;
use SamIT\LimeSurvey\Interfaces\ResponseInterface;

class Generator extends \prime\reportGenerators\base\Generator
{
    /** @var ResponseInterface */
    public $response;

    /**
     * Return answer to the question title in the response
     * @param $title
     * @return string|null
     */
    public function getQuestionValue($title)
    {
        return isset($this->response->getData()[$title]) ? $this->response->getData()[$title] : null;
    }

    protected function initResponses(ResponseCollectionInterface $responses) {
        $responses = $responses->sort(function(ResponseInterface $r1, ResponseInterface $r2) {
            // Reverse ordered
            return -1 * strcmp($r1->getId(), $r2->getId());
        });

        // Get the first element, we know the collection is traversable.
        foreach($responses as $key => $response) {
            $this->response = $response;
            break;
        }
    }
    /**
     * @param ResponseCollectionInterface $responses
     * @param SignatureInterface $signature
     * @param ProjectInterface $project
     * @param UserDataInterface|null $userData
     * @return string
     */
    public function renderPreview(
        ResponseCollectionInterface $responses,
        SurveyCollectionInterface $surveys,
        ProjectInterface $project,
        SignatureInterface $signature = null,
        UserDataInterface $userData = null
    ) {
        $this->initResponses($responses);
        return $this->view->render('publish', ['userData' => $userData, 'project' => $project, 'signature' => $signature], $this);
    }

    /**
     * This function renders a report.
     * All responses to be used are given as 1 array of Response objects.
     * @param ResponseCollectionInterface $responses
     * @param SurveyCollectionInterface $surveys
     * @param SignatureInterface $signature
     * @param ProjectInterface $project
     * @param UserDataInterface|null $userData
     * @return ReportInterface
     */
    public function render(
        ResponseCollectionInterface $responses,
        SurveyCollectionInterface $surveys,
        ProjectInterface $project,
        SignatureInterface $signature = null,
        UserDataInterface $userData = null
    ) {
        $this->initResponses($responses);
        $stream = \GuzzleHttp\Psr7\stream_for($this->view->render('publish', [
            'userData' => $userData,
            'signature' => $signature,
            'responses' => $responses,
            'project' => $project,
        ], $this));

        $userData = new UserData();
        return new Report($userData, $signature, $stream, $this->className(), $this->getReportTitle($project, $signature));
    }

    /**
     * Returns the title of the Report
     * @return string
     */
    public static function title()
    {
        return \Yii::t('app', 'Health cluster dashboard');
    }
}