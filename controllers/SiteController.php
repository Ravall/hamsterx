<?php

namespace app\controllers;

use app\models\Requests\TableSortRequest;
use app\models\VideoContent;
use app\models\PageableContent\Services\ContentPageService;
use Yii;
use yii\web\Controller;

final class SiteController extends Controller
{
    /**
     * @var ContentPageService
     */
    private $contentPageService;
    /**
     * @var TableSortRequest
     */
    private $actionIndexRequest;

    public function __construct(
        $id,
        $module,
        ContentPageService $contentPageService,
        TableSortRequest $request,
        $config = []
    ) {
        $this->contentPageService = $contentPageService;
        $this->actionIndexRequest = $request;
        parent::__construct($id, $module, $config);
    }

    public function actionIndex()
    {
        $this->actionIndexRequest->load(Yii::$app->request->get(), '');

        if (!$this->actionIndexRequest->validate()) {
            return $this->render('errors', [
                'errors' => $this->actionIndexRequest->errors
            ]);
        }

        $sortedPage = $this->actionIndexRequest->getSortedPage();
        $pageContent = $this->contentPageService->getContentPage(VideoContent::find(), $sortedPage);

        return $this->render('index', [
            'sortedPage' => $sortedPage,
            'pageContent' => $pageContent
        ]);
    }

}
