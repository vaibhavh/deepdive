<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\PenaltyPoints;
use yii\data\ArrayDataProvider;

/**
 * PenaltyPointsSearch represents the model behind the search form about `app\models\PenaltyPoints`.
 */
class PenaltyPointsSearch extends PenaltyPoints {

    public $sapid = '';

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'ios_compliance_status', 'bgp_available', 'isis_available', 'resilent_status'], 'integer'],
            [['hostname', 'loopback0', 'device_type', 'ios_current_version', 'ios_built_version', 'created_date', 'sapid'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'hostname' => 'Hostname',
            'loopback0' => 'Loopback0',
            'device_type' => 'Device Type',
            'ios_compliance_status' => 'Ios Compliance Status',
            'ios_current_version' => 'Ios Current Version',
            'ios_built_version' => 'Ios Built Version',
            'bgp_available' => '1 Bgp Available',
            'isis_available' => '1 Isis Available',
            'resilent_status' => 'Repair Path Status',
            'created_date' => 'Created Date',
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params) {
        ini_set('max_execution_time', 86400);
        ini_set("memory_limit", "-1");
        error_reporting(E_ALL);
        ini_set("display_errors", 1);
        $connection = new \MongoClient(Yii::$app->mongodb->dsn);
        $database = $connection->deepdive;
        $collection = $database->week_master;
        $tables = $collection->find(['status' => 0], ['table_name', 'date']);
        $date = $table_name = '';
        foreach ($tables as $table) {
            $table_name = $table['table_name'];
            $date = $table['date'];
        }
        $match = array();
        if (!empty($_REQUEST['PenaltyPointsSearch'])) {
            $modelData = $_REQUEST['PenaltyPointsSearch'];
            if (!empty($modelData['hostname'])) {
                $match['hostname'] = $modelData['hostname'];
            }
            if (!empty($modelData['loopback0'])) {
                $match['loopback0'] = $modelData['loopback0'];
            }
            if (!empty($modelData['sapid'])) {
                $match['sapid'] = $modelData['sapid'];
            }
            if (!empty($modelData['device_type'])) {
                $match['device_type'] = $modelData['device_type'];
            }
        }

        if (!empty($host_name)) {
            $match['hostname'] = $host_name;
        }
        $limitValue = 5000;
        $offsetValue = 0;
        $details = array();
        for ($i = 0; $i < 1; $i++) {
            $pipeline = array();
            $data = array();
            $collection = $database->$table_name;
            $pipeline = array();
            if (!empty($match)) {
                $pipeline[]['$match'] = $match;
            }
            $pipeline[]['$sort'] = ['hostname' => 1];
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
            ];

            $pipeline[]['$limit'] = $limitValue;
            $pipeline[]['$skip'] = $offsetValue;

            $options = ['allowDiskUse' => true];
            $data = $collection->aggregate($pipeline);
            if (isset($data['result']) && !empty($data['result'])) {
                foreach ($data['result'] as $dataDtl) {
                    $host_name = $dataDtl['_id']['hostname'];
                    $loopback0 = $dataDtl['_id']['loopback0'];
                    unset($dataDtl['_id']);
                    $dataDtl['hostname'] = $host_name;
                    $dataDtl['loopback0'] = $loopback0;
                    $dataDtl['total'] = (int) $dataDtl['ios_compliance_status'] + (int) $dataDtl['bgp_available'] + (int) $dataDtl['isis_available'] + (int) $dataDtl['resilent_status'] + (int) $dataDtl['crc'] + (int) $dataDtl['input_errors'] + (int) $dataDtl['output_errors'] + (int) $dataDtl['interface_resets'] + (int) $dataDtl['power'] + (int) $dataDtl['optical_power'] + (int) $dataDtl['module_temperature'] + (int) $dataDtl['packetloss'] + (int) $dataDtl['audit_penalty'] + (int) $dataDtl['latency'];
                    $details[$host_name] = $dataDtl;
                }
            }
            if (!empty($match)) {
                break;
            }

            $offsetValue = $limitValue;
            $limitValue = $limitValue + 5000;
        }

//        $recordWithSetPoints = self::setPoints($details);
        $penaltyPointsProvider = new ArrayDataProvider([
            'allModels' => $details,
            'pagination' => ['pageSize' => 20],
            'sort' => ['attributes' => ['hostname', 'loopback0', 'ios_compliance_status', 'bgp_available', 'isis_available', 'isis_available', 'device_type', 'crc', 'input_errors', 'output_errors', 'interface_resets'
                    , 'power', 'optical_power', 'packetloss', 'audit_penalty', 'latency', 'module_temperature', 'total', 'resilent_status', 'sapid']],
        ]);

        $penaltyPointsExportProvider = new ArrayDataProvider([
            'allModels' => $details,
            'pagination' => ['pageSize' => 100000],
            'sort' => ['attributes' => ['hostname', 'loopback0', 'ios_compliance_status', 'bgp_available', 'isis_available', 'isis_available', 'device_type', 'crc', 'input_errors', 'output_errors', 'interface_resets'
                    , 'power', 'optical_power', 'packetloss', 'audit_penalty', 'latency', 'module_temperature', 'total', 'resilent_status', 'sapid']],
        ]);

        $this->load($params);

        return array('data' => $penaltyPointsProvider, 'date' => $date, 'details' => $details, 'export' => $penaltyPointsExportProvider);
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function points($params) {
        ini_set("memory_limit", "-1");
        error_reporting(E_ALL);
        ini_set("display_errors", 1);
        $mySearchParams = [];
        if (!empty($params['PenaltyPointsSearch'])) {
            $mySearchParams = $params['PenaltyPointsSearch'];
            $mySearchParams = array_filter($mySearchParams);
        }

        $connection = new \MongoClient(Yii::$app->mongodb->dsn);
        $database = $connection->deepdive;
        $collection = $database->week_master;
        $tables = $collection->find(['status' => 0], ['table_name']);
        foreach ($tables as $table) {
            $table_name = $table['table_name'];
        }
        $collection = $database->$table_name;
        $cursors = $collection->find($mySearchParams);
        $penaltyPoints = array();
        foreach ($cursors as $cursor) {
            $penaltyPoints[] = $cursor;
        }
        $recordWithSetPoints = self::setPoints($penaltyPoints);
        // add conditions that should always apply here
        $penaltyPointsProvider = new ArrayDataProvider([
            'allModels' => $recordWithSetPoints,
            'pagination' => ['pageSize' => 20]
        ]);

        $this->load($params);
        return $penaltyPointsProvider;
    }

    public function setPoints($penaltyPoints) {
        $connection = new \MongoClient(Yii::$app->mongodb->dsn);
        $database = $connection->deepdive;
        $collection = $database->penaltyPointMaster;
        $results = $collection->find(['is_deleted' => '0', 'is_active' => '1']);
        $penaltyPointMaster = array();
        $penaltyResultArr = array();
        foreach ($results as $result) {
            $penaltyPointMaster[$result['device_type']][$result['rule']] = $result['points'];
        }
        $total = 0;
        foreach ($penaltyPoints as $key => $record) {
            $record['total'] = 0;
            foreach ($record as $key1 => $recordValue) {
                if ($record['device_type'] == 'CSR')
                    $record['device_type'] = "SAR";
                if ($record['device_type'] == 'CCR')
                    $record['device_type'] = "AG3";
                if ($record['device_type'] == 'AAR')
                    $record['device_type'] = "AG2";
                if (isset($penaltyPointMaster[$record['device_type']][$key1]) && $recordValue > 0) {
                    $record['total'] += $record[$key1] = (int) $recordValue * (int) $penaltyPointMaster[$record['device_type']][$key1];
                }
            }
            $penaltyResultArr[$key] = $record;
        }
        return $penaltyResultArr;
    }

    public function getData($params, $host_name = '') {
        ini_set('max_execution_time', 86400);
        ini_set("memory_limit", "-1");
        //error_reporting(E_ALL);
        //ini_set("display_errors",1);
        $connection = new \MongoClient(Yii::$app->mongodb->dsn);
        $database = $connection->deepdive;
        $collection = $database->week_master;
        $tables = $collection->find(['status' => 0], ['table_name', 'date']);
        $date = $table_name = '';
        foreach ($tables as $table) {
            $table_name = $table['table_name'];
            $date = $table['date'];
        }
        $match = array();
        if (!empty($_REQUEST['PenaltyPointsSearch'])) {
            $modelData = $_REQUEST['PenaltyPointsSearch'];
            if (!empty($modelData['hostname'])) {
                $match['hostname'] = $modelData['hostname'];
            }
            if (!empty($modelData['loopback0'])) {
                $match['loopback0'] = $modelData['loopback0'];
            }
            if (!empty($modelData['sapid'])) {
                $match['sapid'] = $modelData['sapid'];
            }
            if (!empty($modelData['device_type'])) {
                $match['device_type'] = $modelData['device_type'];
            }
        }

        if (!empty($host_name)) {
            $match['hostname'] = $host_name;
        }
        $limitValue = 10000;
        $offsetValue = 0;
        $details = array();
        for ($i = 0; $i < 10; $i++) {
            $pipeline = array();
            $data = array();
            $collection = $database->$table_name;
            $pipeline = array();
            if (!empty($match)) {
//                $match[] = ['AG1'];
                $pipeline[]['$match'] = $match;
            }
            //$pipeline[]['$sort'] = ['hostname'=>-1];
            $pipeline[]['$group'] = [
                '_id' => ['hostname' => '$hostname', 'loopback0' => '$loopback0'],
                'hostname' => ['$first' => '$hostname'],
                'loopback0' => ['$first' => '$loopback0'],
                'sapid' => ['$first' => '$sapid'],
                'device_type' => ['$first' => '$device_type'],
                'ios_compliance_status' => ['$sum' => '$ios_compliance_status'],
                'bgp_available' => ['$sum' => '$bgp_available'],
                'isis_available' => ['$sum' => '$isis_available'],
                'resilent_status' => ['$sum' => '$resilent_status'],
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
            ];

            $pipeline[]['$limit'] = $limitValue;
            $pipeline[]['$skip'] = $offsetValue;

            $options = ['allowDiskUse' => true];
            $data = $collection->aggregate($pipeline);
            if (isset($data['result']) && !empty($data['result'])) {
                foreach ($data['result'] as $dataDtl) {
                    $host_name = $dataDtl['_id']['hostname'];
                    $loopback0 = $dataDtl['_id']['loopback0'];
                    unset($dataDtl['_id']);
//                    $dataDtl['hostname'] = $host_name;
//                    $dataDtl['loopback0'] = $loopback0;
                    $details[$host_name] = $dataDtl;
                }
            }
            if (!empty($match)) {
                break;
            }

            $offsetValue = $limitValue;
            $limitValue = $limitValue + 10000;
        }

        $recordWithSetPoints = self::setPoints($details);
        $penaltyPointsProvider = new ArrayDataProvider([
            'allModels' => $recordWithSetPoints,
            'pagination' => ['pageSize' => 20],
            'sort' => ['attributes' => ['hostname', 'loopback0', 'ios_compliance_status', 'bgp_available', 'isis_available', 'isis_available', 'device_type', 'crc', 'input_errors', 'output_errors', 'interface_resets'
                    , 'power', 'optical_power', 'packetloss', 'audit_penalty', 'latency', 'module_temperature', 'total', 'resilent_status', 'sapid',
                    'isis_stability_changed', 'bfd_stability_changed', 'bgp_stability_changed', 'ldp_stability_changed', 'device_stability', 'pvb_priority_1',
                    'pvb_priority_2', 'pvb_priority_3']],
        ]);
        $penaltyPointsExportProvider = new ArrayDataProvider([
            'allModels' => $recordWithSetPoints,
            'pagination' => ['pageSize' => 1000000],
            'sort' => ['attributes' => ['hostname', 'loopback0', 'ios_compliance_status', 'bgp_available', 'isis_available', 'isis_available', 'device_type', 'crc', 'input_errors', 'output_errors', 'interface_resets'
                    , 'power', 'optical_power', 'packetloss', 'audit_penalty', 'latency', 'module_temperature', 'total', 'resilent_status', 'sapid',
                    'isis_stability_changed', 'bfd_stability_changed', 'bgp_stability_changed', 'ldp_stability_changed', 'device_stability', 'pvb_priority_1',
                    'pvb_priority_2', 'pvb_priority_3']],
        ]);
        if (!empty($params))
            $this->load($params);

        return array('data' => $penaltyPointsProvider, 'date' => $date, 'details' => $recordWithSetPoints, 'export' => $penaltyPointsExportProvider);
    }

    public function getGraph($circle = '') {
        
    }

}
