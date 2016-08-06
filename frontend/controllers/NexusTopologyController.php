<?php

namespace frontend\controllers;

use Yii;
use frontend\models\nexustopology;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\bootstrap\Modal;
use app\models\Model;
//use yii\components\CHelper;
use \CHelper;

/**
 * nexusTopologyController implements the CRUD actions for nexusTopology model.
 */
class NexusTopologyController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all PenaltyPoints models.
     * @return mixed
     */
    public function actionIndex() {
        error_reporting(E_ALL);
        ini_set("display_errors", 1);
        $searchModel = new PenaltyPointsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'dataProvider' => $dataProvider['data'],
                    'date' => $dataProvider['date'],
                    'searchModel' => $searchModel,
                    'export' => $dataProvider['export'],
        ]);
    }
}