<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ArrayDataProvider;
use yii\data\ActiveDataProvider;

/**
 * ContactForm is the model behind the contact form.
 */
class PenaltyTopTen extends Model {

    public $fromDate;
    public $toDate;
    public $scenario;
    public $circle;
    public $device;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            // name, email, subject and body are required
            [['fromDate', 'toDate', 'scenario'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'fromDate' => 'From Date',
            'toDate' => 'To Date',
            'scenario' => 'Scenario',
            'circle' => 'Circle',
            'device' => 'Device Type',
        ];
    }

    public static function getCircleData() {
        $db = Yii::$app->db_rjil;
        $sql = "SELECT `circle_code`,`circle_name` FROM `tbl_circle_master`";
        $command = $db->createCommand($sql);
        $circleData = $command->queryAll();
        $circleMasterData = [];
        $circleMasterData[''] = 'Circle';
        foreach ($circleData as $myCircle) {
            $circleMasterData[$myCircle['circle_name']] = $myCircle['circle_name'];
        }
        return $circleMasterData;
    }

    public function getCircleWiseData($circle = '', $fromDate = '', $toDate = '', $params = [], $report = false) {
        $sql = "select site_sap_id FROM tbl_site_master as t INNER JOIN tbl_circle_master as tu ON(t.circle_id=tu.id) "
                . "WHERE tu.circle_name='$circle' AND LENGTH(site_sap_id)<20 AND LENGTH(site_sap_id)>15";
        $results = Yii::$app->db_rjil->createCommand($sql)->queryAll();
        $sapids = array();
        if (!empty($results)) {
            foreach ($results as $key => $value) {
                $sapids[] = $value['site_sap_id'];
            }
        }

        if (!empty($sapids)) {
            $data = $this->groupPenaltyData("week_penalty_master", $fromDate, $toDate, $sapids, '', $report);
            //$data = $this->getPenaltyData($sapids, $fromDate, $toDate);
            //$penaltyPointsProvider = new ArrayDataProvider(['allModels' => $data]);
            $penaltyPointsProvider[] = new ArrayDataProvider([
                'allModels' => $data,
                'pagination' => ['pageSize' => 20],
                'sort' => [
                    'attributes' => ['total'],
                    'defaultOrder' => [
                        'total' => SORT_DESC,
                    ]]
            ]);
            $penaltyPointsProvider[] = new ArrayDataProvider([
                'allModels' => $data,
                'pagination' => ['pageSize' => 100000],
                'sort' => [
                    'attributes' => ['total'],
                    'defaultOrder' => [
                        'total' => SORT_DESC,
                    ]]
            ]);
            $this->load($params);

            if (!$report)
                $penaltyPointsProvider = $penaltyPointsProvider[0];

            return $penaltyPointsProvider;
        } else {
            return array();
        }
    }

    public function getDeviceWiseData($deviceType, $fromDate, $toDate, $params = [], $report = false) {
        $data = $this->getDeviceTypeWiseData($deviceType, $fromDate, $toDate, $params, $report);

        $penaltyPointsProvider[] = new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => ['pageSize' => 20],
            'sort' => [
                'attributes' => ['total'],
                'defaultOrder' => [
                    'total' => SORT_DESC,
                ]]
        ]);

        $penaltyPointsProvider[] = new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => ['pageSize' => 100000],
            'sort' => [
                'attributes' => ['total'],
                'defaultOrder' => [
                    'total' => SORT_DESC,
                ]]
        ]);
        $this->load($params);

        if (!$report)
            $penaltyPointsProvider = $penaltyPointsProvider[0];

        return $penaltyPointsProvider;
    }

    public function getPenaltyData($sapids = [], $fromDate = '', $toDate = '') {
        $connection = new \MongoClient(Yii::$app->mongodb->dsn);
        $database = $connection->deepdive;
        $collection = $database->week_master;
        $tables = $collection->find(['status' => 0], ['table_name']);
        foreach ($tables as $table) {
            $table_name = $table['table_name'];
        }
        $weekData = $this->groupPenaltyData("week_penalty_master", $fromDate, $toDate, $sapids);
        return $weekData;
    }

    public function groupPenaltyData($table_name = '', $fromDate = '', $toDate = '', $sapids = [], $request_hostname = '', $report = false) {
        $penltySearch = new PenaltyPointsSearch;
        $connection = new \MongoClient(Yii::$app->mongodb->dsn);
        $database = $connection->deepdive;
        $collection = $database->$table_name;

        $pipeline = array();
        $match = '';
        $toDate = "2016-07-18";
        $match = [];
        if (!empty($sapids)) {
            $match['sapid'] = ['$in' => $sapids];
        }
        if (!empty($fromDate) && !empty($toDate)) {
            $match['created_at'] = ['$gte' => $fromDate, '$lte' => $toDate];
        }
        if (!empty($request_hostname)) {
            $match['hostname'] = trim($request_hostname);
        }
        if (!empty($match))
            $pipeline[]['$match'] = $match;


        $pipeline[]['$group'] = [
            '_id' => ['hostname' => '$hostname', 'loopback0' => '$loopback0'],
            'ios_compliance_status' => ['$sum' => '$ios_compliance_status'],
            'bgp_available' => ['$sum' => '$bgp_available'],
            'isis_available' => ['$sum' => '$isis_available'],
            'resilent_status' => ['$sum' => '$resilent_status'],
            'device_type' => ['$first' => '$device_type'],
            'crc' => ['$sum' => '$crc'],
            'input_errors' => ['$sum' => '$input_errors'],
            'output_errors' => ['$sum' => '$output_errors'],
            'interface_resets' => ['$sum' => '$interface_resets'],
            'power' => ['$sum' => '$power'],
            'optical_power' => ['$sum' => '$optical_power'],
            'packetloss' => ['$sum' => '$packetloss'],
            'audit_penalty' => ['$sum' => '$audit_penalty'],
            'latency' => ['$sum' => '$latency'],
            'module_temperature' => ['$sum' => '$module_temperature'],
            'sapid' => ['$first' => '$sapid'],
            'total' => ['$sum' => ['$add' => ['$ios_compliance_status', '$bgp_available', '$isis_available', '$resilent_status', '$crc', '$input_errors', '$output_errors', '$power', '$optical_power', '$packetloss', '$audit_penalty', '$latency', '$module_temperature']]],
        ];
        $options = ['allowDiskUse' => true];
        $data = $collection->aggregate($pipeline);
        if (isset($data['result']) && !empty($data['result'])) {
            $details = (!$report) ? $penltySearch->setPoints($data['result']) : $data['result'];
            $total = [];
            $data = [];
            if (!empty($details)) {
                foreach ($details as $dataDtl) {
                    $host_name = $dataDtl['_id']['hostname'];
                    $loopback0 = $dataDtl['_id']['loopback0'];
                    unset($dataDtl['_id']);
                    $dataDtl['hostname'] = $host_name;
                    $dataDtl['loopback0'] = $loopback0;
                    $dataDtl['total'] = $dataDtl['total'];
                    if (!empty($request_hostname)) {
                        $data[$host_name] = $dataDtl;
                    } else {
                        $data[$host_name . "&fromDate=" . $fromDate . "&todate=" . $toDate] = $dataDtl;
                    }

                    $total[] = $dataDtl['total'];
                }
            }
        }
        $topTenDevices = [];
        if (!empty($data) && !empty($total)) {
            rsort($total);
            $topTenPenalties = (!$report) ? array_slice($total, 0, 10) : $total;
            if (!empty($data)) {
                foreach ($data as $key => $dataDtl) {
                    if (in_array($dataDtl['total'], $topTenPenalties)) {
                        $topTenDevices[$key] = $dataDtl;
                    }
                    if (!$report) {
                        if (count($topTenDevices) == 10) {
                            break;
                        }
                    }
                }
            }
        } else {
            Yii::$app->session->setFlash('error', 'No Data Found!');
        }
        return $topTenDevices;
    }

    public function getDeviceTypeWiseData($device_type = '', $fromDate = '', $toDate = '', $params = [], $report = false) {
        $device_type = ($device_type == 'PAR') ? 'AG1' : (($device_type == 'ESR') ? 'CSS' : '');
        $penltySearch = new PenaltyPointsSearch;
        $connection = new \MongoClient(Yii::$app->mongodb->dsn);
        $database = $connection->deepdive;
        $collection = $database->week_penalty_master;

        $pipeline = array();
        $match = '';
        $toDate = "2016-07-18";
        $match = [];
        if (!empty($fromDate) && !empty($toDate)) {
            $match['created_at'] = ['$gte' => $fromDate, '$lte' => $toDate];
        }
        if (!empty($device_type)) {
            $match['device_type'] = trim($device_type);
        }
        if (!empty($match))
            $pipeline[]['$match'] = $match;


        $pipeline[]['$group'] = [
            '_id' => ['hostname' => '$hostname', 'loopback0' => '$loopback0'],
            'ios_compliance_status' => ['$sum' => '$ios_compliance_status'],
            'bgp_available' => ['$sum' => '$bgp_available'],
            'isis_available' => ['$sum' => '$isis_available'],
            'resilent_status' => ['$sum' => '$resilent_status'],
            'device_type' => ['$first' => '$device_type'],
            'crc' => ['$sum' => '$crc'],
            'input_errors' => ['$sum' => '$input_errors'],
            'output_errors' => ['$sum' => '$output_errors'],
            'interface_resets' => ['$sum' => '$interface_resets'],
            'power' => ['$sum' => '$power'],
            'optical_power' => ['$sum' => '$optical_power'],
            'packetloss' => ['$sum' => '$packetloss'],
            'audit_penalty' => ['$sum' => '$audit_penalty'],
            'latency' => ['$sum' => '$latency'],
            'module_temperature' => ['$sum' => '$module_temperature'],
            'sapid' => ['$first' => '$sapid'],
            'total' => ['$sum' => ['$add' => ['$ios_compliance_status', '$bgp_available', '$isis_available', '$resilent_status', '$crc', '$input_errors', '$output_errors', '$power', '$optical_power', '$packetloss', '$audit_penalty', '$latency', '$module_temperature']]],
        ];
        $options = ['allowDiskUse' => true];
        $data = $collection->aggregate($pipeline);
        if (isset($data['result']) && !empty($data['result'])) {
            $details = (!$report) ? $penltySearch->setPoints($data['result']) : $data['result'];
            $total = [];
            $data = [];
            if (!empty($details)) {
                foreach ($details as $dataDtl) {
                    $host_name = $dataDtl['_id']['hostname'];
                    $loopback0 = $dataDtl['_id']['loopback0'];
                    unset($dataDtl['_id']);
                    $dataDtl['hostname'] = $host_name;
                    $dataDtl['loopback0'] = $loopback0;
                    $dataDtl['total'] = $dataDtl['total'];
                    $data[$host_name . "&fromDate=" . $fromDate . "&todate=" . $toDate] = $dataDtl;
                    $total[] = $dataDtl['total'];
                }
            }
        }
        $topTenDevices = [];
        if (!empty($data) && !empty($total)) {
            rsort($total);
            $topTenPenalties = (!$report) ? array_slice($total, 0, 10) : $total;
            if (!empty($data)) {
                foreach ($data as $key => $dataDtl) {
                    if (in_array($dataDtl['total'], $topTenPenalties)) {
                        $topTenDevices[$key] = $dataDtl;
                    }
                    if (!$report) {
                        if (count($topTenDevices) == 10) {
                            break;
                        }
                    }
                }
            }
        } else {
            Yii::$app->session->setFlash('error', 'No Data Found!');
        }
        return $topTenDevices;
    }

}
