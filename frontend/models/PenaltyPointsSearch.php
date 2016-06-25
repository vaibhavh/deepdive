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

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'ios_compliance_status', 'bgp_available', 'isis_available', 'resilent_status'], 'integer'],
            [['hostname', 'loopback0', 'device_type', 'ios_current_version', 'ios_built_version', 'created_date'], 'safe'],
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
        $collection = $database->week_penalty_master;
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

}
