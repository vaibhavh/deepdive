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
    public function actionGraph($id) {
//        if (!empty($id)) {
//
//            echo "<pre/>", print_r($id);
//            die;
//        }

        $pointsModel = new PenaltyPointsSearch();
        $data = $pointsModel->getData(Yii::$app->request->queryParams, $id);
        $ipsla = $interface_resets = $resiliency = 0;
        if (!empty($data['details'])) {
            $details = $data['details'][$id];
            $ipsla = $details['packetloss'] + $details['latency'];
            $interface_resets = $details['crc'] + $details['input_errors'] + $details['output_errors'] + $details['interface_resets'] + $details['module_temperature'] + $details['optical_power'] + $details['power'];
            $resiliency = $details['bgp_available'] + $details['isis_available'] + $details['resilent_status'];
            $subSectionPoints = array(
                'IPSLA' => array(
                    'Packet Loss' => (isset($details['packetloss']) ? $details['packetloss'] : 0),
                    'Latency' => $details['latency']
                ),
                'Interface Errors' => array(
                    'Crc' => $details['crc'],
                    'Input Errors' => $details['input_errors'],
                    'Output Errors' => $details['output_errors'],
                    'Interface Reset' => $details['interface_resets'],
                    'SFP Module Temperature' => $details['module_temperature'],
                    'Optical Power' => $details['optical_power'],
                    'Power Error' => $details['power']),
                'Resiliency' => array(
                    '1 bgp available' => $details['bgp_available'],
                    '1 isis available' => $details['isis_available'],
                    'Repair path not available' => $details['resilent_status']),
                'Configuration Audit' => array(
                    'Configuration Audit' => $details['audit_penalty']),
                'IOS & SMU Compliance' => array(
                    'IOS & SMU Compliance' => $details['ios_compliance_status'])
            );
            $deviceDetails = ['hostname' => $details['hostname'], 'loopback0' => $details['loopback0'], 'date' => $data['date']];
        }

        $sectionPoints = array('IPSLA' => $ipsla, 'Interface Errors' => $interface_resets, 'Resiliency' => $resiliency, 'Configuration Audit' => $details['audit_penalty'], 'IOS & SMU Compliance' => $details['ios_compliance_status']);
        $sectionPointsData = PenaltyPoints::getSectionData($sectionPoints);
        $subSectionPointsData = PenaltyPoints::getSubSectionDrilldownData($subSectionPoints);

        return $this->renderAjax('graph', [
                    'sectionData' => $sectionPointsData,
                    'subSectionData' => $subSectionPointsData,
                    'deviceDetails' => $deviceDetails
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
