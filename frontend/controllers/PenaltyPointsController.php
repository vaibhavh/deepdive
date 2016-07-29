<?php

namespace frontend\controllers;

use Yii;
use frontend\models\PenaltyTopTen;
use frontend\models\PenaltyTopTenSearch;
use frontend\models\PenaltyPoints;
use frontend\models\PenaltyPointsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\bootstrap\Modal;
use app\models\Model;
//use yii\components\CHelper;
use \CHelper;

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

    public function actionReports() {
        $model = new PenaltyPoints();
        $topTenModel = new PenaltyTopTen();
        $pointsModel = new PenaltyPointsSearch();
        $circle = '';
        $result = '';
        $circleData = [];
        $fromDate = $toDate = '';
        if ($model->load(Yii::$app->request->post())) {
            $data = Yii::$app->request->post();
            if (!empty($data['PenaltyPoints'])) {
                $data = $data['PenaltyPoints'];
                $fromDate = $this->getFormatedDate($data['fromDate']);
                $toDate = $this->getFormatedDate($data['toDate']);
                $circle = $data['circle'];
                $deviceType = $data['device'];
                if (!empty($data['circle'])) {
                    $result = $topTenModel->getCircleWiseData($circle, $fromDate, $toDate, Yii::$app->request->queryParams, true);
                    $gridData = $result[0];
                    $exportAll = $result[1];
                } else if (!empty($data['device'])) {
                    $result = $topTenModel->getDeviceWiseData($deviceType, $fromDate, $toDate, Yii::$app->request->queryParams, true);
                    $gridData = $result[0];
                    $exportAll = $result[1];
                }
            }
        }
        $circleMasterData = $topTenModel->getCircleData();

        return $this->render('report', [
                    'model' => $model,
                    'pointsModel' => $model,
                    'circleMasterData' => $circleMasterData,
                    'dataProvider' => $gridData,
                    'date' => $fromDate,
                    'toDate' => $toDate,
                    'exportAll' => $exportAll,
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
                    'export' => $dataProvider['penaltyPointsProviderExport'],
        ]);
    }

    /**
     * Lists all PenaltyPoints models.
     * @return mixed
     */
    public function actionGraph($id) {
        $pointsModel = new PenaltyPointsSearch();
        $data = $pointsModel->getData(Yii::$app->request->queryParams, $id);
        $ipsla = $interface_resets = $resiliency = $protocol_stability = $config_audit = 0;
        $sectionPointsData = $subSectionPointsData = $deviceDetails = array();
        if (!empty($id) && !empty($data['details'])) {
            $details = $data['details'][$id];
            $ipsla = $details['packetloss'] + $details['latency'];
            $interface_resets = $details['crc'] + $details['input_errors'] + $details['output_errors'] + $details['interface_resets'] + $details['module_temperature'] + $details['optical_power'] + $details['power'] + $details['device_stability'];
            $resiliency = $details['bgp_available'] + $details['isis_available'] + $details['resilent_status'];
            $protocol_stability = $details['isis_stability_changed'] + $details['bgp_stability_changed'] + $details['bfd_stability_changed'] + $details['ldp_stability_changed'];
            $config_audit = $details['audit_penalty'] + $details['pvb_priority_1'] + $details['pvb_priority_2'] + $details['pvb_priority_3'];
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
                    'Power Error' => $details['power'],
                    'Device Uptime' => $details['device_stability']),
                'Resiliency' => array(
                    '1 bgp available' => $details['bgp_available'],
                    '1 isis available' => $details['isis_available'],
                    'Repair path not available' => $details['resilent_status']),
                'Configuration Audit' => array(
                    'Configuration Audit' => $details['audit_penalty'],
                    'PvB Priority 1' => $details['pvb_priority_1'],
                    'PvB Priority 2' => $details['pvb_priority_2'],
                    'PvB Priority 3' => $details['pvb_priority_3']),
                'IOS & SMU Compliance' => array(
                    'IOS & SMU Compliance' => $details['ios_compliance_status']),
                'Protocol Stability' => array(
                    'ISIS Stability' => $details['isis_stability_changed'],
                    'BGP Stability' => $details['bgp_stability_changed'],
                    'BFD Stability' => $details['bfd_stability_changed'],
                    'LDP Stability' => $details['ldp_stability_changed']),
            );
            $deviceCircle = PenaltyPoints::getCircleForHostname($id);
            $deviceDetails = ['hostname' => $details['hostname'], 'loopback0' => $details['loopback0'], 'date' => $data['date'], 'circle' => $deviceCircle];
            $sectionPoints = array('IPSLA' => $ipsla, 'Interface Errors' => $interface_resets, 'Resiliency' => $resiliency, 'Configuration Audit' => $config_audit, 'IOS & SMU Compliance' => $details['ios_compliance_status'], 'Protocol Stability' => $protocol_stability);
            $sectionPointsData = PenaltyPoints::getSectionData($sectionPoints);
            $subSectionPointsData = PenaltyPoints::getSubSectionDrilldownData($subSectionPoints);
        }

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

    public function actionGetGraphDetails() {
        $db = Yii::$app->db_rjil;
//        $sql = "SELECT * FROM dd_ipsla_errors WHERE substring(host_name,9,3) IN ('ESR','PAR') AND date(created_at)=date(now())";
        $sql = "SELECT * FROM dd_ipsla_errors WHERE substring(host_name,9,3) IN ('ESR','PAR') AND date(created_at)='2016-06-28' limit 1";
        $command = $db->createCommand($sql);
        $ipsla_points = $command->queryAll();
        echo "<pre/>", print_r($ipsla_points);
        die;
        $model = new PenaltyPointsSearch();
        $model->getGraph();
    }

    public function getFormatedDate($date = '') {
        $date = str_replace(':', "-", $date);
        $date = new \DateTime($date);
        $date = $date->format('Y-m-d');
        return $date;
    }

    public function downloadData() {
        
    }

}
