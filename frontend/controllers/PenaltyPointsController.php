<?php

namespace frontend\controllers;

use Yii;
use frontend\models\PenaltyPoints;
use frontend\models\PenaltyPointsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PenaltyPointsController implements the CRUD actions for PenaltyPoints model.
 */
class PenaltyPointsController extends Controller {

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
        $searchModel = new PenaltyPointsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel,
        ]);
    }

    /**
     * Lists all PenaltyPoints models.
     * @return mixed
     */
    public function actionPoints() {
        error_reporting(E_ALL);
        ini_set("display_errors", 1);
        $pointsModel = new PenaltyPointsSearch();

        $dataProvider = $pointsModel->getData(Yii::$app->request->queryParams);
        return $this->render('index', [
                    'dataProvider' => $dataProvider['data'],
                    'date' => $dataProvider['date'],
                    'searchModel' => $pointsModel,
        ]);
    }
    
    /**
     * Lists all PenaltyPoints models.
     * @return mixed
     */
    public function actionGraph($id)
    {   
        /*$db = Yii::$app->db_rjil;
        $sql = "SELECT `modified_sapid` FROM `ndd_host_name` WHERE is_deleted = 0 AND host_name = '".$id."'";
        $command = $db->createCommand($sql);
        $deviceSapid = $command->queryRow();*/
        
        $sectionPoints = array('IPSLA' => 5000, 'Interface Errors' => 1000, 'Resiliency' => 2000);
        $subSectionPoints = array('IPSLA' => array('Packet Loss' => 3000, 'Jitter' => 500, 'Latency' => 1500), 
                                  'Interface Errors' => array('Crc' => 100, 'Input Errors' => 100, 'Output Errors' => 200,
                                                              'Interface Reset' => 100, 'SFP Module Temperature' => 200,
                                                              'Optical Power' => 100, 'Buffer Consumption' => 100,'Power Error' => 100), 
                                  'Resiliency' => array('1 bgp available' => 1000, '1 isis available' => 500, 'Repair path not available' => 500));
        $sectionPointsData = PenaltyPoints::getSectionData($sectionPoints);
        $subSectionPointsData = PenaltyPoints::getSubSectionDrilldownData($subSectionPoints);

        return $this->renderAjax('graph', [
            'sectionData' => $sectionPointsData,
            'subSectionData' => $subSectionPointsData,
        ]);
    }

    /**
     * Displays a single PenaltyPoints model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new PenaltyPoints model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new PenaltyPoints();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing PenaltyPoints model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing PenaltyPoints model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the PenaltyPoints model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PenaltyPoints the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = PenaltyPoints::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
