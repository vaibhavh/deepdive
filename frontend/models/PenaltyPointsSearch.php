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
        $mySearchParams = [];
        if (!empty($params['PenaltyPointsSearch'])) {
            $mySearchParams = $params['PenaltyPointsSearch'];
            $mySearchParams = array_filter($mySearchParams);
        }

        $connection = new \MongoClient(Yii::$app->mongodb->dsn);
        $database = $connection->deepdive;
        $collection = $database->week_penalty_master;
        //$collection = Yii::$app->commonUtility->mongoDbConnection('deepdive', 'weekdayPenalty');
        $cursors = $collection->find($mySearchParams);

        $penaltyPoints = array();
        foreach ($cursors as $cursor) {
            unset($cursor['_id']);

            $penaltyPoints[] = $cursor;
        }

        // add conditions that should always apply here
        $penaltyPointsProvider = new ArrayDataProvider([
            'allModels' => $penaltyPoints,
            'pagination' => ['pageSize' => 5]
        ]);

        $this->load($params);
        return $penaltyPointsProvider;
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
            if (isset($penaltyPointMaster[$record['device_type']]['ios_compliance_status']) && $record['ios_compliance_status'] > 0) {
                $record['total'] += $record['ios_compliance_status'] = (int) $record['ios_compliance_status'] * (int) $penaltyPointMaster[$record['device_type']]['ios_compliance_status'];
            }

            if (isset($penaltyPointMaster[$record['device_type']]['bgp_available']) && $record['bgp_available'] > 0) {
                $record['total'] +=$record['bgp_available'] = (int) $record['bgp_available'] * (int) $penaltyPointMaster[$record['device_type']]['bgp_available'];
            }

            if (isset($penaltyPointMaster[$record['device_type']]['isis_available']) && $record['isis_available'] > 0) {
                $record['total'] +=$record['isis_available'] = (int) $record['isis_available'] * (int) $penaltyPointMaster[$record['device_type']]['isis_available'];
            }

            if (isset($penaltyPointMaster[$record['device_type']]['resilent_status']) && $record['resilent_status'] > 0) {
                $record['total'] +=$record['resilent_status'] = (int) $record['resilent_status'] * (int) $penaltyPointMaster[$record['device_type']]['resilent_status'];
            }
            if (isset($penaltyPointMaster[$record['device_type']]['crc']) && $record['crc'] > 0) {
                $record['total'] +=$record['crc'] = (int) $record['crc'] * (int) $penaltyPointMaster[$record['device_type']]['crc'];
            }
            if (isset($penaltyPointMaster[$record['device_type']]['input_errors']) && $record['input_errors'] > 0) {
                $record['total'] += $record['input_errors'] = (int) $record['input_errors'] * (int) $penaltyPointMaster[$record['device_type']]['input_errors'];
            }
            if (isset($penaltyPointMaster[$record['device_type']]['output_errors']) && $record['output_errors'] > 0) {
                $record['output_errors'] = (int) $record['output_errors'] * (int) $penaltyPointMaster[$record['device_type']]['output_errors'];
            }
            if (isset($penaltyPointMaster[$record['device_type']]['interface_resets']) && $record['interface_resets'] > 0) {
                $record['total'] +=$record['interface_resets'] = (int) $record['interface_resets'] * (int) $penaltyPointMaster[$record['device_type']]['interface_resets'];
            }
            if (isset($penaltyPointMaster[$record['device_type']]['power']) && $record['power'] > 0) {
                $record['total'] +=$record['power'] = (int) $record['power'] * (int) $penaltyPointMaster[$record['device_type']]['power'];
            }
            if (isset($penaltyPointMaster[$record['device_type']]['optical_power']) && $record['optical_power'] > 0) {
                $record['total'] +=$record['optical_power'] = (int) $record['optical_power'] * (int) $penaltyPointMaster[$record['device_type']]['optical_power'];
            }
            if (isset($penaltyPointMaster[$record['device_type']]['module_temperature']) && $record['module_temperature'] > 0) {
                $record['total'] +=$record['module_temperature'] = (int) $record['module_temperature'] * (int) $penaltyPointMaster[$record['device_type']]['module_temperature'];
            }
            if (isset($penaltyPointMaster[$record['device_type']]['packetloss']) && $record['packetloss'] > 0) {
                $record['total'] +=$record['packetloss'] = (int) $record['packetloss'] * (int) $penaltyPointMaster[$record['device_type']]['packetloss'];
            }
            if (isset($penaltyPointMaster[$record['device_type']]['audit_penalty']) && $record['audit_penalty'] > 0) {
                $record['total'] +=$record['audit_penalty'] = (int) $record['audit_penalty'] * (int) $penaltyPointMaster[$record['device_type']]['audit_penalty'];
            }
            if (isset($penaltyPointMaster[$record['device_type']]['latency']) && $record['latency'] > 0) {
                $record['total'] +=$record['latency'] = (int) $record['latency'] * (int) $penaltyPointMaster[$record['device_type']]['latency'];
            }
            $penaltyResultArr[$key] = $record;
        }
        return $penaltyResultArr;
    }

    public function getData($params, $host_name = '') {

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
                    $details[$host_name] = $dataDtl;
                }
            }
            if (!empty($match)) {
                break;
            }

            $offsetValue = $limitValue;
            $limitValue = $limitValue + 5000;
        }

        $recordWithSetPoints = self::setPoints($details);
        $penaltyPointsProvider = new ArrayDataProvider([
            'allModels' => $recordWithSetPoints,
            'pagination' => ['pageSize' => 20],
            'sort' => ['attributes' => ['hostname', 'loopback0', 'ios_compliance_status', 'bgp_available', 'isis_available', 'isis_available', 'device_type', 'crc', 'input_errors', 'output_errors', 'interface_resets'
                    , 'power', 'optical_power', 'packetloss', 'audit_penalty', 'latency', 'module_temperature', 'total', 'resilent_status', 'sapid']],
        ]);
        
        $this->load($params);

        return array('data' => $penaltyPointsProvider, 'date' => $date, 'details' => $recordWithSetPoints);
    }

    public function getGraph($circle = '') {
        
    }

}
