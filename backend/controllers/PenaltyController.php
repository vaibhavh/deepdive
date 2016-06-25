<?php

/**
 * Penalty controller
 * @author Prashant Swami <prashant.s@infinitylabs.in>
 */

namespace backend\controllers;

//namespace backend\components;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use \yii\mongodb\Connection;

class PenaltyController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['logout', 'index', 'add-daily-data', 'weekly-data', 'add-penalty-point-master'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex() {
        $connection = Yii::$app->mongodb;
        $database = $connection->getDatabase('deepdive');
        $collection = $database->getCollection('week_master');
        $tables = $collection->find(['status' => 0], ['table_name']);
        foreach ($tables as $table) {
            $table_name = $table['table_name'];
        }
        $collection = $database->getCollection('weekdayPenalty');
        $cursor = $collection->find();
        $data = array();
        foreach ($cursor as $doc) {
            echo "<pre/>";
            print_r($doc);
        }
    }

    public function getBuiltPenalty() {
        $db = Yii::$app->db_rjil;
        $sql = "SELECT * FROM tbl_built_penalty_points limit 10";
        $command = $db->createCommand($sql);
        $penelty_points = $command->queryAll();
        $data = array();
        if (!empty($penelty_points)) {
            foreach ($penelty_points as $penelty) {
                $data[$penelty['hostname']] = [
                    'ios_compliance_status' => $penelty['ios_compliance_status'],
                    'bgp_available' => $penelty['bgp_available'],
                    'isis_available' => $penelty['isis_available'],
                    'resilent_status' => $penelty['resilent_status'],
                ];
            }
        }
    }

    public function actionAddDailyData() {
        ini_set("memory_limit", "-1");
        error_reporting(E_ALL);
        ini_set("display_errors", 1);
        ini_set('max_execution_time', 86400);
        $auditPoints = $this->getAuditPoints();
        $ipslaRecords = $this->getIpslaRecords();
        $packetDrop = $this->getPacketDrop();
        $latency = $this->geLatency();

        $day = date('D');

        $db = Yii::$app->db_rjil;
        $sql = "SELECT * FROM tbl_built_penalty_points WHERE date(created_date)=date(now())";
        $command = $db->createCommand($sql);
        $penelty_points = $command->queryAll();
        $connection = new \MongoClient(Yii::$app->mongodb->dsn);
        $database = $connection->deepdive;
        $collection = $database->week_master;
        $date = date("Y_m_d");
        $table_name = '';

        if ($day == 'Mon') {
            $collection->update([], ['$set' => ['status' => 1]]);
            $table_name = "weekday_penalty_" . $date;
            $collection->insert(['table_name' => "weekday_penalty_" . $date, 'status' => 0, 'date' => date('Y:m:d')]);
        } else {
            $tables = $collection->find(['status' => 0], ['table_name']);
            foreach ($tables as $table) {
                $table_name = $table['table_name'];
            }
        }

        if (!empty($table_name)) {
            $collection = $database->$table_name;
            $data = array();
            if (!empty($penelty_points)) {
                foreach ($penelty_points as $penelty_point) {
                    $is_exist = $collection->find(['hostname' => $penelty_point['hostname'], 'created_date' => date("Y:m:d")], ['hostname']);
                    $hostname = '';
                    foreach ($is_exist as $exist) {
                        $hostname = $exist['hostname'];
                    }

                    if (empty($hostname)) {
                        $packet_drop = 0;
                        $latency_point = 0;

                        $data = [
                            'hostname' => $penelty_point['hostname'],
                            'loopback0' => $penelty_point['loopback0'],
                            'device_type' => $penelty_point['device_type'],
                            'ios_compliance_status' => (int) $penelty_point['ios_compliance_status'],
                            'ios_current_version' => $penelty_point['ios_current_version'],
                            'ios_built_version' => $penelty_point['ios_built_version'],
                            'bgp_available' => (int) $penelty_point['bgp_available'],
                            'isis_available' => (int) $penelty_point['isis_available'],
                            'resilent_status' => (int) $penelty_point['resilent_status'],
                        ];
                        $data['crc'] = 0;
                        $data['output_errors'] = 0;
                        $data['interface_resets'] = 0;
                        $data['interface_resets'] = 0;
                        $data['power'] = 0;
                        $data['optical_power'] = 0;
                        $data['module_temperature'] = 0;
                        $data['packetloss'] = 0;
                        $data['latency'] = 0;

                        if (isset($ipslaRecords[$penelty_point['hostname']])) {
                            $ipsla_record = $ipslaRecords[$penelty_point['hostname']];
                            $data['crc'] = (int) $ipsla_record['crc'];
                            $data['output_errors'] = (int) $ipsla_record['output_errors'];
                            $data['input_errors'] = (int) $ipsla_record['input_errors'];
                            $data['interface_resets'] = (int) $ipsla_record['interface_resets'];
                            $data['power'] = (int) $ipsla_record['power'];
                            $data['optical_power'] = (int) $ipsla_record['optical_power'];
                            $data['module_temperature'] = (int) $ipsla_record['module_temperature'];
                        }
                        if (isset($packetDrop[$penelty_point['hostname']]))
                            $data['packetloss'] = (int) $packetDrop[$penelty_point['hostname']];
                        if (isset($latency[$penelty_point['hostname']]))
                            $data['latency'] = (int) $latency[$penelty_point['hostname']];
                        $data['audit_penalty'] = 0;
                        if (isset($auditPoints[$penelty_point['loopback0']]))
                            $data['audit_penalty'] = (int) $auditPoints[$penelty_point['loopback0']];
                        $data['table_name'] = $table_name;
                        $data['created_date'] = date("Y:m:d");
                        $collection->insert($data);
                        $data = array();
                    } else {
                        echo $penelty_point['hostname'] . " is already exist<br>";
                    }
                }
            }
        }
        die("done");
    }

    public function actionWeeklyData() {
        ini_set("memory_limit", "-1");
        error_reporting(E_ALL);
        ini_set("display_errors", 1);
        ini_set('max_execution_time', 86400);
        $connection = new \MongoClient(Yii::$app->mongodb->dsn);
        $database = $connection->deepdive;
        $collection = $database->week_master;
        $tables = $collection->find(['status' => 0], ['table_name']);
        foreach ($tables as $table) {
            $table_name = $table['table_name'];
        }

        $collection = $database->$table_name;
        $details = array();
        $pipeline = [
            ['$limit' => 40000],
            [
                '$group' => [
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
                ],
            ],
        ];
        $options = ['allowDiskUse' => true];
        $data = $collection->aggregate($pipeline, $options);
//        echo "<pre/>", print_r($data);
//        die;
//        $data = $collection->group(
//                ['hostname' => true, 'loopback0' => true], ["ios_compliance_status" => 0, 'bgp_available' => 0, 'isis_available' => 0, 'resilent_status' => 0,
//            'device_type' => 0, 'crc' => 0, 'input_errors' => 0, 'output_errors' => 0, 'interface_resets' => 0, 'power' => 0, 'optical_power' => 0, 'module_temperature' => 0, 'packetloss' => 0, 'latency' => 0, 'audit_penalty' => 0], //
//                new \MongoCode('function(doc, prev) {
//            prev.ios_compliance_status += obj . ios_compliance_status;
//            prev.bgp_available += obj . bgp_available;
//            prev.isis_available += obj . isis_available;
//            prev.resilent_status += obj . resilent_status;
//            prev.device_type = obj . device_type;
//            prev.crc += obj . crc;
//            prev.input_errors += obj . input_errors;
//            prev.output_errors += obj . output_errors;
//            prev.interface_resets += obj . interface_resets;
//            prev.power += obj . power;
//            prev.optical_power += obj . optical_power;
//            prev.packetloss += obj . packetloss;
//            prev.audit_penalty += obj . audit_penalty;
//            prev.latency += obj . latency;
//            prev.module_temperature += obj . module_temperature;
//        }')
//        );

        $details = array();
        $collection = $database->week_penalty_master;
        if (!empty($data)) {
            foreach ($data['result'] as $value) {

                if (!empty($value)) {
                    $is_exist = $collection->find(['hostname' => $value['_id']['hostname'], 'created_at' => date('Y:m:d')], ['hostname']);
                    $hostname = '';
                    foreach ($is_exist as $exist) {
                        $hostname = $exist['hostname'];
                    }
                    if (empty($hostname)) {
                        $device_type = "CSS";
                        if ($value['device_type'] == 'PAR')
                            $device_type = "AG1";
                        if (in_array($value['device_type'], ['CSS', 'AG1']))
                            $device_type = $value['device_type'];
                        $data = [
                            'hostname' => $value['_id']['hostname'],
                            'loopback0' => $value['_id']['loopback0'],
                            'device_type' => $device_type,
                            'ios_compliance_status' => (int) $value['ios_compliance_status'],
                            'bgp_available' => (int) $value['bgp_available'],
                            'isis_available' => (int) $value['isis_available'],
                            'resilent_status' => (int) $value['resilent_status'],
                            'crc' => (int) $value['crc'],
                            'input_errors' => (int) $value['input_errors'],
                            'output_errors' => (int) $value['output_errors'],
                            'interface_resets' => (int) $value['interface_resets'],
                            'power' => (int) $value['power'],
                            'optical_power' => (int) $value['optical_power'],
                            'module_temperature' => (int) $value['module_temperature'],
                            'packetloss' => (int) $value['packetloss'],
                            'audit_penalty' => (int) $value['audit_penalty'],
                            'latency' => (int) $value['latency'],
                            'table_name' => $table_name,
                            'created_at' => date('Y:m:d'),
                        ];

                        $collection->insert($data);
                    } else {
                        echo $value['_id']['hostname'] . " is already exist<br>";
                    }
                }
            }
        }
        die("done");
    }

    public function getIpslaRecords() {
        $db = Yii::$app->db_rjil;
        $sql = "SELECT * FROM dd_ipsla_errors WHERE substring(host_name,9,3) IN ('ESR','PAR') AND date(created_at)=date(now())";
        $command = $db->createCommand($sql);
        $ipsla_points = $command->queryAll();

        $data = array();
        if (!empty($ipsla_points)) {
            foreach ($ipsla_points as $ipsla_point) {
                $device_type = substr($ipsla_point['host_name'], 8, 3);
                $type = '';
                switch ($device_type) {
                    case "ESR":
                        $type = "CSS";
                        break;
                    case "PAR":
                        $type = "AG1";
                        break;
                }
                if (!empty($type)) {

                    $data[$ipsla_point['host_name']] = [
                        'crc' => $ipsla_point['crc'],
                        'input_errors' => $ipsla_point['input_errors'],
                        'output_errors' => $ipsla_point['output_errors'],
                        'interface_resets' => $ipsla_point['interface_resets'],
                        'optical_power' => $ipsla_point['optical_power'],
                        'module_temperature' => $ipsla_point['module_temperature'],
                        'power' => $ipsla_point['power'],
                    ];
                }
            }
        }
        return $data;
    }

    public function getPacketDrop() {
        $db = Yii::$app->db_rjil;
        $sql = "select count(*) as count,host_name from dd_ipsla_packet_drop WHERE host_name!='' AND date(created_at)=date(now()) group by host_name";
        $command = $db->createCommand($sql);
        $ipsla_points = $command->queryAll();

        $data = array();
        if (!empty($ipsla_points)) {
            foreach ($ipsla_points as $value) {
                $data[$value['host_name']] = $value['count'];
            }
        }
        return $data;
    }

    public function geLatency() {
        $db = Yii::$app->db_rjil;
        $sql = "select count(*) as count,host_name from dd_ipsla_latency WHERE host_name!='' AND date(created_at)=date(now()) group by host_name";
        $command = $db->createCommand($sql);
        $ipsla_latency_points = $command->queryAll();
        $data = array();
        if (!empty($ipsla_latency_points)) {
            foreach ($ipsla_latency_points as $value) {
                $data[$value['host_name']] = $value['count'];
            }
        }
        return $data;
    }

    public function getIntegratedDevices() {
        $db = Yii::$app->db_rjil;
        $sql = "select im.hostname from tbl_assigned_sites as t INNER JOIN tbl_ip_master AS im ON (t.site_id = im.id)
                WHERE t.status = '1' AND (im.status = 1 OR im.status = '1') AND substring(im.hostname, 9,3) IN ('PAR','ESR') LIMIT 10";
        $command = $db->createCommand($sql);
        $integrated_devices = $command->queryAll();
        $data = array();
        foreach ($integrated_devices as $integrated) {
            $device_type = substr($integrated['hostname'], 8, 3);
            $type = '';
            switch ($device_type) {
                case "ESR":
                    $type = "CSS";
                    break;
                case "PAR":
                    $type = "AG1";
                    break;
            }
            if (!empty($type)) {
                $integrated['device_type'] = $type;
                $data[] = $integrated;
            }
        }
        return $data;
    }

    public function getAuditPoints() {
        $db = Yii::$app->db_rjil;
        $sql = "select penalty_counts,loopback0 from tbl_audit_penalty_points WHERE date(created_dt)=date(now())";
        $command = $db->createCommand($sql);
        $auditResults = $command->queryAll();
        $auditPenalty = 0;
        $data = array();
        if (!empty($auditResults)) {
            foreach ($auditResults as $auditResult) {
                $penalty_counts = $auditResult['penalty_counts'];
                $penalty_counts = explode(":::::", $penalty_counts);
                if (!empty($penalty_counts)) {
                    foreach ($penalty_counts as $penalty_count) {
                        if (preg_match("/failed/", $penalty_count)) {
                            $penalty_count = explode("::", $penalty_count);
                            $auditPenalty = $penalty_count[1];
                        }
                    }
                }
                if (!empty($auditPenalty)) {
                    $data[$auditResult['loopback0']] = $auditPenalty;
                }
            }
        }
        return $data;
    }

    public function actionAddPenaltyPointMaster() {
        $connection = new \MongoClient(Yii::$app->mongodb->dsn);
        $database = $connection->deepdive;
        $collection = $database->penaltyPointMaster;
        $dataArr = array();
        $dataArr[0] = array('section' => 'IPSLA', 'device_type' => 'CSS', 'subsection' => 'IPSLA Performance', 'rule' => 'packetloss', 'frequency' => '5 min', 'points' => '100', 'created_at' => '2016-06-24 12:00:00', 'modified_at' => '0000-00-00 00:00:00', 'created_by' => '1', 'modified_by' => '0', 'is_deleted' => '0', 'is_active' => '1');
        $dataArr[1] = array('section' => 'IPSLA', 'device_type' => 'AG1', 'subsection' => 'IPSLA Performance', 'rule' => 'packetloss', 'frequency' => '5 min', 'points' => '100', 'created_at' => '2016-06-24 12:00:00', 'modified_at' => '0000-00-00 00:00:00', 'created_by' => '1', 'modified_by' => '0', 'is_deleted' => '0', 'is_active' => '1');
        $dataArr[2] = array('section' => 'IPSLA', 'device_type' => 'CSS', 'subsection' => 'IPSLA Performance', 'rule' => 'latency', 'frequency' => '5 min', 'points' => '100', 'created_at' => '2016-06-24 12:00:00', 'modified_at' => '0000-00-00 00:00:00', 'created_by' => '1', 'modified_by' => '0', 'is_deleted' => '0', 'is_active' => '1');
        $dataArr[3] = array('section' => 'IPSLA', 'device_type' => 'AG1', 'subsection' => 'IPSLA Performance', 'rule' => 'latency', 'frequency' => '5 min', 'points' => '100', 'created_at' => '2016-06-24 12:00:00', 'modified_at' => '0000-00-00 00:00:00', 'created_by' => '1', 'modified_by' => '0', 'is_deleted' => '0', 'is_active' => '1');
        $dataArr[4] = array('section' => 'Interface Errors', 'device_type' => 'CSS', 'subsection' => 'Link Quality & Stability', 'rule' => 'crc', 'frequency' => '24 hours', 'points' => '100', 'created_at' => '2016-06-24 12:00:00', 'modified_at' => '0000-00-00 00:00:00', 'created_by' => '1', 'modified_by' => '0', 'is_deleted' => '0', 'is_active' => '1');
        $dataArr[5] = array('section' => 'Interface Errors', 'device_type' => 'AG1', 'subsection' => 'Link Quality & Stability', 'rule' => 'crc', 'frequency' => '24 hours', 'points' => '100', 'created_at' => '2016-06-24 12:00:00', 'modified_at' => '0000-00-00 00:00:00', 'created_by' => '1', 'modified_by' => '0', 'is_deleted' => '0', 'is_active' => '1');
        $dataArr[6] = array('section' => 'Interface Errors', 'device_type' => 'CSS', 'subsection' => 'Link Quality & Stability', 'rule' => 'input_errors', 'frequency' => '24 hours', 'points' => '100', 'created_at' => '2016-06-24 12:00:00', 'modified_at' => '0000-00-00 00:00:00', 'created_by' => '1', 'modified_by' => '0', 'is_deleted' => '0', 'is_active' => '1');
        $dataArr[7] = array('section' => 'Interface Errors', 'device_type' => 'AG1', 'subsection' => 'Link Quality & Stability', 'rule' => 'input_errors', 'frequency' => '24 hours', 'points' => '100', 'created_at' => '2016-06-24 12:00:00', 'modified_at' => '0000-00-00 00:00:00', 'created_by' => '1', 'modified_by' => '0', 'is_deleted' => '0', 'is_active' => '1');
        $dataArr[8] = array('section' => 'Interface Errors', 'device_type' => 'CSS', 'subsection' => 'Link Quality & Stability', 'rule' => 'output_errors', 'frequency' => '24 hours', 'points' => '100', 'created_at' => '2016-06-24 12:00:00', 'modified_at' => '0000-00-00 00:00:00', 'created_by' => '1', 'modified_by' => '0', 'is_deleted' => '0', 'is_active' => '1');
        $dataArr[9] = array('section' => 'Interface Errors', 'device_type' => 'AG1', 'subsection' => 'Link Quality & Stability', 'rule' => 'output_errors', 'frequency' => '24 hours', 'points' => '100', 'created_at' => '2016-06-24 12:00:00', 'modified_at' => '0000-00-00 00:00:00', 'created_by' => '1', 'modified_by' => '0', 'is_deleted' => '0', 'is_active' => '1');
        $dataArr[10] = array('section' => 'Interface Errors', 'device_type' => 'CSS', 'subsection' => 'Link Quality & Stability', 'rule' => 'interface_resets', 'frequency' => '24 hours', 'points' => '100', 'created_at' => '2016-06-24 12:00:00', 'modified_at' => '0000-00-00 00:00:00', 'created_by' => '1', 'modified_by' => '0', 'is_deleted' => '0', 'is_active' => '1');
        $dataArr[11] = array('section' => 'Interface Errors', 'device_type' => 'AG1', 'subsection' => 'Link Quality & Stability', 'rule' => 'interface_resets', 'frequency' => '24 hours', 'points' => '100', 'created_at' => '2016-06-24 12:00:00', 'modified_at' => '0000-00-00 00:00:00', 'created_by' => '1', 'modified_by' => '0', 'is_deleted' => '0', 'is_active' => '1');
        $dataArr[12] = array('section' => 'Interface Errors', 'device_type' => 'CSS', 'subsection' => 'Link Quality & Stability', 'rule' => 'module_temperature', 'frequency' => '24 hours', 'points' => '100', 'created_at' => '2016-06-24 12:00:00', 'modified_at' => '0000-00-00 00:00:00', 'created_by' => '1', 'modified_by' => '0', 'is_deleted' => '0', 'is_active' => '1');
        $dataArr[13] = array('section' => 'Interface Errors', 'device_type' => 'AG1', 'subsection' => 'Link Quality & Stability', 'rule' => 'module_temperature', 'frequency' => '24 hours', 'points' => '100', 'created_at' => '2016-06-24 12:00:00', 'modified_at' => '0000-00-00 00:00:00', 'created_by' => '1', 'modified_by' => '0', 'is_deleted' => '0', 'is_active' => '1');
        $dataArr[14] = array('section' => 'Interface Errors', 'device_type' => 'CSS', 'subsection' => 'Link Quality & Stability', 'rule' => 'optical_power', 'frequency' => '24 hours', 'points' => '100', 'created_at' => '2016-06-24 12:00:00', 'modified_at' => '0000-00-00 00:00:00', 'created_by' => '1', 'modified_by' => '0', 'is_deleted' => '0', 'is_active' => '1');
        $dataArr[15] = array('section' => 'Interface Errors', 'device_type' => 'AG1', 'subsection' => 'Link Quality & Stability', 'rule' => 'optical_power', 'frequency' => '24 hours', 'points' => '100', 'created_at' => '2016-06-24 12:00:00', 'modified_at' => '0000-00-00 00:00:00', 'created_by' => '1', 'modified_by' => '0', 'is_deleted' => '0', 'is_active' => '1');
        $dataArr[16] = array('section' => 'Interface Errors', 'device_type' => 'CSS', 'subsection' => 'Link Quality & Stability', 'rule' => 'power', 'frequency' => '24 hours', 'points' => '100', 'created_at' => '2016-06-24 12:00:00', 'modified_at' => '0000-00-00 00:00:00', 'created_by' => '1', 'modified_by' => '0', 'is_deleted' => '0', 'is_active' => '1');
        $dataArr[17] = array('section' => 'Interface Errors', 'device_type' => 'AG1', 'subsection' => 'Link Quality & Stability', 'rule' => 'power', 'frequency' => '24 hours', 'points' => '100', 'created_at' => '2016-06-24 12:00:00', 'modified_at' => '0000-00-00 00:00:00', 'created_by' => '1', 'modified_by' => '0', 'is_deleted' => '0', 'is_active' => '1');
        $dataArr[18] = array('section' => 'Configuration Audit', 'device_type' => 'CSS', 'subsection' => 'PvB', 'rule' => 'priority', 'frequency' => '24 hours', 'points' => '100', 'created_at' => '2016-06-24 12:00:00', 'modified_at' => '0000-00-00 00:00:00', 'created_by' => '1', 'modified_by' => '0', 'is_deleted' => '0', 'is_active' => '1');
        $dataArr[19] = array('section' => 'Configuration Audit', 'device_type' => 'AG1', 'subsection' => 'PvB', 'rule' => 'priority', 'frequency' => '24 hours', 'points' => '100', 'created_at' => '2016-06-24 12:00:00', 'modified_at' => '0000-00-00 00:00:00', 'created_by' => '1', 'modified_by' => '0', 'is_deleted' => '0', 'is_active' => '1');
        $dataArr[20] = array('section' => 'Configuration Audit', 'device_type' => 'CSS', 'subsection' => 'Security Compliance(GCT 20.7)', 'rule' => 'audit_penalty', 'frequency' => '1 week', 'points' => '500', 'created_at' => '2016-06-24 12:00:00', 'modified_at' => '0000-00-00 00:00:00', 'created_by' => '1', 'modified_by' => '0', 'is_deleted' => '0', 'is_active' => '1');
        $dataArr[21] = array('section' => 'Configuration Audit', 'device_type' => 'AG1', 'subsection' => 'Security Compliance(GCT 20.7)', 'rule' => 'audit_penalty', 'frequency' => '1 week', 'points' => '500', 'created_at' => '2016-06-24 12:00:00', 'modified_at' => '0000-00-00 00:00:00', 'created_by' => '1', 'modified_by' => '0', 'is_deleted' => '0', 'is_active' => '1');
        $dataArr[22] = array('section' => 'Resiliency', 'device_type' => 'CSS', 'subsection' => 'Resiliency', 'rule' => 'bgp_available', 'frequency' => '24 hours', 'points' => '500', 'created_at' => '2016-06-24 12:00:00', 'modified_at' => '0000-00-00 00:00:00', 'created_by' => '1', 'modified_by' => '0', 'is_deleted' => '0', 'is_active' => '1');
        $dataArr[23] = array('section' => 'Resiliency', 'device_type' => 'AG1', 'subsection' => 'Resiliency', 'rule' => 'bgp_available', 'frequency' => '24 hours', 'points' => '1000', 'created_at' => '2016-06-24 12:00:00', 'modified_at' => '0000-00-00 00:00:00', 'created_by' => '1', 'modified_by' => '0', 'is_deleted' => '0', 'is_active' => '1');
        $dataArr[24] = array('section' => 'Resiliency', 'device_type' => 'CSS', 'subsection' => 'Resiliency', 'rule' => 'isis_available', 'frequency' => '24 hours', 'points' => '500', 'created_at' => '2016-06-24 12:00:00', 'modified_at' => '0000-00-00 00:00:00', 'created_by' => '1', 'modified_by' => '0', 'is_deleted' => '0', 'is_active' => '1');
        $dataArr[25] = array('section' => 'Resiliency', 'device_type' => 'AG1', 'subsection' => 'Resiliency', 'rule' => 'isis_available', 'frequency' => '24 hours', 'points' => '1000', 'created_at' => '2016-06-24 12:00:00', 'modified_at' => '0000-00-00 00:00:00', 'created_by' => '1', 'modified_by' => '0', 'is_deleted' => '0', 'is_active' => '1');
        $dataArr[26] = array('section' => 'Resiliency', 'device_type' => 'CSS', 'subsection' => 'Resiliency', 'rule' => 'resilent_status', 'frequency' => '24 hours', 'points' => '1000', 'created_at' => '2016-06-24 12:00:00', 'modified_at' => '0000-00-00 00:00:00', 'created_by' => '1', 'modified_by' => '0', 'is_deleted' => '0', 'is_active' => '1');
        $dataArr[27] = array('section' => 'Resiliency', 'device_type' => 'AG1', 'subsection' => 'Resiliency', 'rule' => 'resilent_status', 'frequency' => '24 hours', 'points' => '5000', 'created_at' => '2016-06-24 12:00:00', 'modified_at' => '0000-00-00 00:00:00', 'created_by' => '1', 'modified_by' => '0', 'is_deleted' => '0', 'is_active' => '1');
        $dataArr[28] = array('section' => 'IOS & SMU Compliance', 'device_type' => 'CSS', 'subsection' => 'iOS', 'rule' => 'ios_compliance_status', 'frequency' => '24 hours', 'points' => '1000', 'created_at' => '2016-06-24 12:00:00', 'modified_at' => '0000-00-00 00:00:00', 'created_by' => '1', 'modified_by' => '0', 'is_deleted' => '0', 'is_active' => '1');
        $dataArr[29] = array('section' => 'IOS & SMU Compliance', 'device_type' => 'AG1', 'subsection' => 'iOS', 'rule' => 'ios_compliance_status', 'frequency' => '24 hours', 'points' => '1000', 'created_at' => '2016-06-24 12:00:00', 'modified_at' => '0000-00-00 00:00:00', 'created_by' => '1', 'modified_by' => '0', 'is_deleted' => '0', 'is_active' => '1');
        foreach ($dataArr as $mydata) {
            $collection->insert($mydata);
        }
    }

}
