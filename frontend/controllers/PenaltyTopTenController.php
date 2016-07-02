<?php

namespace frontend\controllers;

use Yii;
use frontend\models\PenaltyPoints;
use frontend\models\PenaltyPointsSearch;
use frontend\models\PenaltyTopTen;
use frontend\models\PenaltyTopTenSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\bootstrap\Modal;

/**
 * PenaltyTopTenController implements the CRUD actions for PenaltyTopTen model.
 */
class PenaltyTopTenController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
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
    public function actionIndex()
    {
        $model = new PenaltyTopTen();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                Yii::$app->session->setFlash('success', 'Thank');
                return $this->refresh();
        }
        
        return $this->render('index', [
            'model' => $model,
        ]);
        
    }
    
    /**
     * Lists all PenaltyPoints models.
     * @return mixed
     */
//    public function actionGraph()
//    {
//        $sectionPoints = array('IPSLA' => 5000, 'Interface Errors' => 1000, 'Resiliency' => 2000);
//        $subSectionPoints = array('IPSLA' => array('Packet Loss' => 3000, 'Jitter' => 500, 'Latency' => 1500), 
//                                  'Interface Errors' => array('Crc' => 100, 'Input Errors' => 100, 'Output Errors' => 200,
//                                                              'Interface Reset' => 100, 'SFP Module Temperature' => 200,
//                                                              'Optical Power' => 100, 'Buffer Consumption' => 100,'Power Error' => 100), 
//                                  'Resiliency' => array('1 bgp available' => 1000, '1 isis available' => 500, 'Repair path not available' => 500));
//        $sectionPointsData = PenaltyPoints::getSectionData($sectionPoints);
//        $subSectionPointsData = PenaltyPoints::getSubSectionDrilldownData($subSectionPoints);
//
//        return $this->renderAjax('graph', [
//            'sectionData' => $sectionPointsData,
//            'subSectionData' => $subSectionPointsData,
//        ]);
//    }

    /**
     * Displays a single PenaltyPoints model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new PenaltyPoints model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PenaltyTopTen();

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
    public function actionUpdate($id)
    {
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
    public function actionDelete($id)
    {
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
    protected function findModel($id)
    {
        if (($model = PenaltyTopTen::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
