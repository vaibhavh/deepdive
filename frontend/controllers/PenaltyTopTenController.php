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
use app\models\Model;
//use yii\components\CHelper;
use \CHelper;

/**
 * PenaltyTopTenController implements the CRUD actions for PenaltyTopTen model.
 */
class PenaltyTopTenController extends Controller {

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
        $model = new PenaltyTopTen();
        $pointsModel = new PenaltyPointsSearch();
        $circle = '';
        $circleData = [];
        $fromDate = $toDate = '';
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $data = Yii::$app->request->post();
            if (!empty($data['PenaltyTopTen'])) {
                $data = $data['PenaltyTopTen'];
                $fromDate = $this->getFormatedDate($data['fromDate']);
                $toDate = $this->getFormatedDate($data['toDate']);
                $circle = $data['circle'];
                $deviceType = $data['device'];
                if (!empty($data['circle'])) {
                    $result = $model->getCircleWiseData($circle, $fromDate, $toDate, Yii::$app->request->queryParams);
                }
                if (!empty($data['circle'])) {
                    $result = $model->getDeviceTypeWiseData($deviceType, $fromDate, $toDate, Yii::$app->request->queryParams);
                }
            }
        }
        $circleMasterData = $model->getCircleData();
        return $this->render('index', [
                    'model' => $model,
                    'pointsModel' => $model,
                    'circleMasterData' => $circleMasterData,
                    'dataProvider' => $result,
                    'date' => $fromDate,
                    'toDate' => $toDate,
//                    'toDate' => $circle,
        ]);
    }

    /**
     * Lists all PenaltyPoints models.
     * @return mixed
     */
    public function actionGraph() {
        $model = new PenaltyTopTen();
        $deviceDetails = [];
        if (!empty($_REQUEST['id'])) {
            $id = $_REQUEST['id'];
            $id = explode("&", $id);
            $host_name = $id[0];
            $fromDate = str_replace("fromDate=", "", $id[1]);
            $todate = str_replace("todate=", "", $id[2]);
            $data = $model->groupPenaltyData("week_penalty_master", $fromDate, $todate, [], $host_name);

            if (!empty($data)) {
                $details = $data[$host_name];
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
                $deviceDetails = ['hostname' => $details['hostname'], 'loopback0' => $details['loopback0'], 'date' => $fromDate, 'todate' => $todate];
            }
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
        if (($model = PenaltyTopTen::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function getFormatedDate($date = '') {
        $date = str_replace(':', "-", $date);
        $date = new \DateTime($date);
        $date = $date->format('Y-m-d');
        return $date;
    }

}
