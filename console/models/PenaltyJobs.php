<?php

/**
 * Penalty controller
 * @author Prashant Swami <prashant.s@infinitylabs.in>
 */

namespace console\models;

use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use \yii\mongodb\Connection;
use frontend\models\PenaltyPointsSearch;
use common\models\CommonUtility;
use common\models\CHelper;

class PenaltyJobs {

    public static function fetchDailyData() {
        echo "\nStart collection at " . date("Y-m-d H:i:s");
        ini_set("memory_limit", "-1");
        error_reporting(E_ALL);
        ini_set("display_errors", 1);
        ini_set('max_execution_time', 86400);
        $auditPoints = self::getAuditPoints();
        $ipslaRecords = self::getIpslaRecords();
        $packetDrop = self::getPacketDrop();
        $latency = self::geLatency();
        $NipVsShowrun = self::getEnvironmentPenalty();
        $devicePerformance = self::getDevicePerformance();
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
            $collection->update([], ['$set' => ['status' => 1]], ['multiple' => true]);
            $table_name = "weekday_penalty_" . $date;
            $collection->insert(['table_name' => "weekday_penalty_" . $date, 'status' => 0, 'date' => date('Y-m-d')]);
        } else {
            $tables = $collection->find(['status' => 0], ['table_name']);
            foreach ($tables as $table) {
                $table_name = $table['table_name'];
            }
        }
        $created_date = date('Y-m-d');
        if (!empty($table_name)) {
            $collection = $database->$table_name;
            $data = array();
            if (!empty($penelty_points)) {
                foreach ($penelty_points as $penelty_point) {
                    $is_exist = $collection->find(['hostname' => $penelty_point['hostname'], 'created_date' => $created_date], ['hostname']);
                    $hostname = '';
                    foreach ($is_exist as $exist) {
                        $hostname = $exist['hostname'];
                    }
                    if (empty($hostname)) {
                        $packet_drop = 0;
                        $latency_point = 0;
                        if (empty($penelty_point['modified_sapid'])) {
                            $sql = "select modified_sapid FROM ndd_host_name WHERE host_name='{$penelty_point['hostname']}'";
                            $command = $db->createCommand($sql);
                            $sapid_details = $command->queryOne();
                            $penelty_point['modified_sapid'] = $sapid_details['modified_sapid'];
                        }
                        $data = [
                            'hostname' => $penelty_point['hostname'],
                            'loopback0' => $penelty_point['loopback0'],
                            'sapid' => $penelty_point['modified_sapid'],
                            'device_type' => $penelty_point['device_type'],
                            'ios_compliance_status' => (int) $penelty_point['ios_compliance_status'],
                            'ios_current_version' => $penelty_point['ios_current_version'],
                            'ios_built_version' => $penelty_point['ios_built_version'],
                            'bgp_available' => (int) $penelty_point['bgp_available'],
                            'isis_available' => (int) $penelty_point['isis_available'],
                            'resilent_status' => (int) $penelty_point['resilent_status'],
                            'isis_stability_changed' => (int) $penelty_point['isis_stability_changed'],
                            'ldp_stability_changed' => (int) $penelty_point['ldp_stability_changed'],
                            'bfd_stability_changed' => (int) $penelty_point['bfd_stability_changed'],
                            'bgp_stability_changed' => (int) $penelty_point['bgp_stability_changed'],
                            'device_stability' => (int) $penelty_point['device_stability'],
                            'pvb_priority_1' => (int) $penelty_point['pvb_priority1'],
                            'pvb_priority_2' => (int) $penelty_point['pvb_priority2'],
                            'pvb_priority_3' => (int) $penelty_point['pvb_priority3'],
                        ];
                        $data['crc'] = 0;
                        $data['input_errors'] = 0;
                        $data['output_errors'] = 0;
                        $data['interface_resets'] = 0;
                        $data['power'] = 0;
                        $data['optical_power'] = 0;
                        $data['module_temperature'] = 0;
                        $data['packetloss'] = 0;
                        $data['latency'] = 0;
                        $data['nip_vs_showrun'] = 0;
                        $data['syslog'] = 0;
                        $data['buffer_consumption'] = 0;
                        $data['cpu_utilization'] = 0;
                        $data['memory_utilization'] = 0;
                        $data['core_dump'] = 0;

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

                        if (!empty($NipVsShowrun) && isset($NipVsShowrun[$penelty_point['loopback0']])) {
                            $data['nip_vs_showrun'] = (int) $NipVsShowrun[$penelty_point['loopback0']]['nip_vs_showrun'];
                            $data['syslog'] = (int) $NipVsShowrun[$penelty_point['loopback0']]['syslog'];
                        }

                        if (isset($devicePerformance[$penelty_point['hostname']])) {
                            $device_record = $devicePerformance[$penelty_point['hostname']];
                            $data['buffer_consumption'] = (int) $device_record['buffer_consumption'];
                            $data['cpu_utilization'] = (int) $device_record['cpu_utilization'];
                            $data['memory_utilization'] = (int) $device_record['memory_utilization'];
                            $data['core_dump'] = (int) $device_record['core_dump'];
                        }
                        $data['table_name'] = $table_name;
                        $data['created_date'] = $created_date;

                        $collection->insert($data);
                        $data = array();
                    } else {
                        echo $penelty_point['hostname'] . " is already exist<br>";
                    }
                }
            }
        }
        echo "\nEnd collection at " . date("Y-m-d H:i:s");
        die("done");
    }

    public static function fetchWeeklyData() {
        echo "\nStart weekly collection at " . date("Y-m-d H:i:s");
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
        $limitValue = 10000;
        $offsetValue = 0;
        for ($i = 0; $i < 10; $i++) {
            $pipeline = array();
            $data = array();
            $collection = $database->$table_name;
            $pipeline = [
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
                        'sapid' => ['$first' => '$sapid'],
                        'isis_stability_changed' => ['$sum' => '$isis_stability_changed'],
                        'ldp_stability_changed' => ['$sum' => '$ldp_stability_changed'],
                        'bfd_stability_changed' => ['$sum' => '$bfd_stability_changed'],
                        'bgp_stability_changed' => ['$sum' => '$bgp_stability_changed'],
                        'device_stability' => ['$sum' => '$device_stability'],
                        'pvb_priority_1' => ['$sum' => '$pvb_priority_1'],
                        'pvb_priority_2' => ['$sum' => '$pvb_priority_2'],
                        'pvb_priority_3' => ['$sum' => '$pvb_priority_3'],
                        'buffer_consumption' => ['$sum' => '$buffer_consumption'],
                        'cpu_utilization' => ['$sum' => '$cpu_utilization'],
                        'memory_utilization' => ['$sum' => '$memory_utilization'],
                        'core_dump' => ['$sum' => '$core_dump'],
                    ],
                ],
                ['$limit' => $limitValue],
                ['$skip' => $offsetValue],
            ];


            $options = ['allowDiskUse' => true];
            $data = $collection->aggregate($pipeline);
            if (isset($data['result']) && !empty($data['result'])) {
                $details = array();
                $collection = $database->week_penalty_master;
                foreach ($data['result'] as $value) {
                    if (!empty($value)) {
                        $is_exist = $collection->find(['hostname' => $value['_id']['hostname'], 'created_at' => date('Y:m:d')], ['hostname']);
                        $hostname = '';
                        foreach ($is_exist as $exist) {
                            $hostname = $exist['hostname'];
                        }
                        if (empty($hostname)) {
                            //echo $value['_id']['hostname']."\n";
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
                                'isis_stability_changed' => (int) $value['isis_stability_changed'],
                                'ldp_stability_changed' => (int) $value['ldp_stability_changed'],
                                'bfd_stability_changed' => (int) $value['bfd_stability_changed'],
                                'bgp_stability_changed' => (int) $value['bgp_stability_changed'],
                                'device_stability' => (int) $value['device_stability'],
                                'pvb_priority_1' => (int) $value['pvb_priority_1'],
                                'pvb_priority_2' => (int) $value['pvb_priority_2'],
                                'pvb_priority_3' => (int) $value['pvb_priority_3'],
                                'buffer_consumption' => (int) $value['$buffer_consumption'],
                                'cpu_utilization' => (int) $value['$cpu_utilization'],
                                'memory_utilization' => (int) $value['$memory_utilization'],
                                'core_dump' => (int) $value['$core_dump'],
                                'table_name' => $table_name,
                                'sapid' => $value['sapid'],
                                'created_at' => date('Y-m-d'),
                            ];
                            $collection->insert($data);
                        } else {
                            echo $value['_id']['hostname'] . " is already exist \n <br>";
                        }
                    }
                }
            } else {
                echo "Data Not found";
                break;
            }

            $offsetValue = $limitValue;
            $limitValue = $limitValue + 10000;
        }


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

        echo "\nEnd weekly collection at " . date("Y-m-d H:i:s");
        die("done");
    }

    public static function getIpslaRecords() {
        $db = Yii::$app->db_rjil;
//        $sql = "SELECT * FROM dd_ipsla_errors WHERE substring(host_name,9,3) IN ('ESR','PAR') AND date(created_at)=date(now())";
        $sql = "SELECT * FROM dd_ipsla_errors WHERE substring(host_name,9,3) IN ('ESR','PAR','AAR','CCR','CSR') AND date(created_at)=date(now())";
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

    public static function getPacketDrop() {
        $db = Yii::$app->db_rjil;
//        $sql = "select count(*) as count,host_name from dd_ipsla_packet_drop WHERE host_name!='' AND date(created_at)=date(now()) group by host_name";
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

    public static function geLatency() {
        $db = Yii::$app->db_rjil;
//        $sql = "select count(*) as count,host_name from dd_ipsla_latency WHERE host_name!='' AND date(created_at)=date(now()) group by host_name";
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

    public static function getIntegratedDevices() {
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

    public static function getAuditPoints() {
        $db = Yii::$app->db_rjil;
        //$sql = "select penalty_counts,loopback0 from tbl_audit_penalty_points WHERE date(created_dt)=date(now())";
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

    public static function actionAddPenaltyPointMaster() {
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

    public static function exportData() {
        error_reporting(E_ALL);
        ini_set("display_errros", 1);
        ini_set('max_execution_time', 86400);
        ini_set("memory_limit", "-1");
        echo "\nStart collecting data : " . date("Y:m:d H:i:s");
        @exec("rm -r /var/www/html/deepdive/uploads/*");
        $basePath = \Yii::getAlias('@app') . DIRECTORY_SEPARATOR . '../uploads' . DIRECTORY_SEPARATOR;
        $pointsModel = new PenaltyPointsSearch();
        $dataProvider = $pointsModel->getData([]);
        $data = array();
        if (!empty($dataProvider)) {
            $dataProvider = $dataProvider['data']->allModels;
            $dataProvider = array_chunk($dataProvider, 40000);
            $header = ['hostname', 'loopback0', 'Sapid', 'device_type', 'ios_compliance_status', 'bgp_available', 'isis_available', 'resilent_status', 'crc', 'input_errors',
                'output_errors', 'interface_resets', 'power', 'optical_power', 'packetloss', 'audit_penalty', 'latency', 'module_temperature', 'isis_stability_changed', 'ldp_stability_changed',
                'bfd_stability_changed', 'bgp_stability_changed', 'device_stability', 'pvb_priority_1', 'pvb_priority_2', 'pvb_priority_3',
                'buffer_consumption', 'cpu_utilization', 'memory_utilization', 'core_dump', 'total'];
            if (!empty($dataProvider)) {
                $id = 1;
                foreach ($dataProvider as $key => $dataPro) {
                    $data['Sheet_' . $id]['header'] = $header;
                    $data['Sheet_' . $id]['rows'] = $dataPro;
                    $id++;
                }
            }
            echo "\nEnd collecting data : " . date("Y:m:d H:i:s");
            echo "\nStart Create excel sheet on server : " . date("Y:m:d H:i:s");

            CommonUtility::generateExcelMultipleTabOnServer($data, 'penalty_point_summary.xls', $basePath);
            echo "\nEnd create excel sheet on server : " . date("Y:m:d H:i:s");
            $fileName = "uploads/penalty_point_summary.zip";
            echo "\nStart create zip File on server : " . date("Y:m:d H:i:s");
            self::CreateZip($fileName);
            echo "\nEnd create zip File on server : " . date("Y:m:d H:i:s");
        }
        $fileName = "penalty_point_summary.zip";
        echo "\nStart Send Mail : " . date("Y:m:d H:i:s");
        self::sendEmail($basePath . $fileName, $fileName);
        echo "\nEnd Send Mail : " . date("Y:m:d H:i:s");
        die("Done");
    }

    public static function sendEmail($attachment_path = '', $file_name = '') {
        error_reporting(E_ALL);
        ini_set("display_errros", 1);
        ini_set('max_execution_time', 86400);
        ini_set("memory_limit", "-1");
        ini_set("message_size_limit", 1024000000000000);
        if (file_exists($attachment_path)) {
            $fromDate = date('d-m-Y', strtotime('-6 day', strtotime(date("d-m-Y"))));
            $toDate = date("d-m-Y");
            $cc = $to = array();
            $to[] = array("email" => "prashant.s@infinitylabs.in", "name" => "Prashant Swami");
            $to[] = array("email" => "pm@infinitylabs.in", "name" => "PM");
            $to[] = array("email" => "vaibhav.h@infinitylabs.in", "name" => "Vaibhav Harihar");
            $to[] = array("email" => "kpanse@cisco.com", "name" => "krishnaji Panse");
        $from = "support@rjilauto.com";
        $from_name = "RJILAuto Team";
            $subject = "Penalty Point report from $fromDate to $toDate";
            $message = "Dear All<br/>";
            $message .= "<p></p>";
            $message .= "<p style='padding-left:5em '>Please find attached the report for penalty point for all devices for the period of $fromDate to $toDate</p>";
            $message .= "<p>From,<br/>RJIL Auto Team<p/>";
            $message .= "<p>***This is an auto generated email. PLEASE DO NOT REPLY TO THIS EMAIL.***</p>";
            $isSent = CommonUtility::sendmailWithAttachment($to, "Prashant", $from, $from_name, $subject, $message, $attachment_path, $file_name, $cc = '');

            var_dump($isSent);
            if ($isSent) {
                echo "Mail Sent successfully";
            } else {
                echo "Mail Not Sent";
            };
    }
    }

    public static function getEnvironmentPenalty() {
        $db = Yii::$app->db_rjil;
        $sql = "select nip_vs_showrun,loopback0,syslog from built_environment_condition_penalty WHERE date(created_at)=date(now())";
        $command = $db->createCommand($sql);
        $nipvsshowrunData = $command->queryAll();
        $auditPenalty = 0;
        $data = array();
        if (!empty($nipvsshowrunData)) {
            foreach ($nipvsshowrunData as $nipvsshowrunDat) {
                if (!empty($nipvsshowrunDat)) {
                    $data[$nipvsshowrunDat['loopback0']] = ['nip_vs_showrun' => $nipvsshowrunDat['nip_vs_showrun'], 'syslog' => $nipvsshowrunDat['syslog']];
                }
            }
        }
        return $data;
    }

    public static function getDevicePerformance() {
        $db = Yii::$app->db_rjil;
        $sql = "SELECT `hostname`, `buffer_consumption`, `cpu_utilization`, `memory_utilization`, `core_dump` FROM `device_performance` WHERE `hostname`!='' AND date(created_date)=date(now()) group by `hostname`";
        $device_performance_points = $db->createCommand($sql)->queryAll();
        $data = array();
        if (!empty($device_performance_points)) {
            foreach ($device_performance_points as $value) {
                $data[$value['hostname']]['buffer_consumption'] = $value['buffer_consumption'];
                $data[$value['hostname']]['cpu_utilization'] = $value['cpu_utilization'];
                $data[$value['hostname']]['memory_utilization'] = $value['memory_utilization'];
                $data[$value['hostname']]['core_dump'] = $value['core_dump'];
}
        }
        return $data;
    }

    public static function CreateZip($fileName) {
        echo "fileName=" . $fileName;
        ini_set('max_execution_time', 86400);
        $output = array();
        @exec("cd /var/www/html/deepdive && zip -r {$fileName} uploads", $output);
        if (!empty($output)) {
            print_r($output);
        }
    }

}
