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

    public function getCircleWiseData($circle = '', $fromDate = '', $toDate = '', $params = []) {
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
            $data = $this->getPenaltyData($sapids, $fromDate, $toDate);
            $penaltyPointsProvider = new ArrayDataProvider(['allModels' => $data]);

            $penaltyPointsProvider = new ArrayDataProvider([
                'allModels' => $data,
                'pagination' => ['pageSize' => 0],
                'sort' => [
                    'attributes' => ['total'],
                    'defaultOrder' => [
                        'total' => SORT_DESC,
                    ]]
            ]);
            $this->load($params);
            return $penaltyPointsProvider;
        } else {
            return array();
        }
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

    public function groupPenaltyData($table_name = '', $fromDate = '', $toDate = '', $sapids = [], $request_hostname = '') {
        $penltySearch = new PenaltyPointsSearch;
        $connection = new \MongoClient(Yii::$app->mongodb->dsn);
        $database = $connection->deepdive;
        $collection = $database->$table_name;

        $pipeline = array();
        $match = '';
        $toDate = "2016-07-10";
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
            $details = $penltySearch->setPoints($data['result']);
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
        if (!empty($data)) {
            rsort($total);

            $topTenPenalties = array_slice($total, 0, 10);
            if (!empty($data)) {
                foreach ($data as $key => $dataDtl) {
                    if (in_array($dataDtl['total'], $topTenPenalties)) {
                        $topTenDevices[$key] = $dataDtl;
                    }
                    if (count($topTenDevices) == 10) {
                        break;
                    }
                }
            }
        }
        return $topTenDevices;
    }

    public function getDeviceTypeWiseData($device_type = '', $fromDate = '', $toDate = '', $params = []) {
        $penltySearch = new PenaltyPointsSearch;
        $connection = new \MongoClient(Yii::$app->mongodb->dsn);
        $database = $connection->deepdive;
        $collection = $database->week_penalty_master;

        $pipeline = array();
        $match = '';
        $toDate = "2016-07-10";
        $match = [];
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
            $details = $penltySearch->setPoints($data['result']);
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
        if (!empty($data)) {
            rsort($total);

            $topTenPenalties = array_slice($total, 0, 10);
            if (!empty($data)) {
                foreach ($data as $key => $dataDtl) {
                    if (in_array($dataDtl['total'], $topTenPenalties)) {
                        $topTenDevices[$key] = $dataDtl;
                    }
                    if (count($topTenDevices) == 10) {
                        break;
                    }
                }
            }
        }
        return $topTenDevices;
    }

}
