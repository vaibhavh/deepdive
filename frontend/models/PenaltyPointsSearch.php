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
    public function search($params) 
    { 
        $mySearchParams = [];
        if(!empty($params['PenaltyPointsSearch']))
        {
            $mySearchParams = $params['PenaltyPointsSearch'];
            $mySearchParams = array_filter($mySearchParams);
        }

        $connection = new \MongoClient(Yii::$app->mongodb->dsn);
        $database = $connection->deepdive;
        $collection = $database->weekdayPenalty;
        //$collection = Yii::$app->commonUtility->mongoDbConnection('deepdive', 'weekdayPenalty');
        $cursors = $collection->find($mySearchParams);
        $penaltyPoints = array();
        foreach ($cursors as $cursor) {
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
    public function points($params) 
    { 
        $mySearchParams = [];
        if(!empty($params['PenaltyPointsSearch']))
        {
            $mySearchParams = $params['PenaltyPointsSearch'];
            $mySearchParams = array_filter($mySearchParams);
        }
        
        $connection = new \MongoClient(Yii::$app->mongodb->dsn);
        $database = $connection->deepdive;
        $collection = $database->weekdayPenalty;
        $cursors = $collection->find($mySearchParams);
        $penaltyPoints = array();
        foreach ($cursors as $cursor) {
            $penaltyPoints[] = $cursor;
        }

        $recordWithSetPoints = self::setPoints($penaltyPoints);
        // add conditions that should always apply here
        $penaltyPointsProvider = new ArrayDataProvider([
            'allModels' => $recordWithSetPoints,
            'pagination' => ['pageSize' => 5]
        ]);

        $this->load($params);
        return $penaltyPointsProvider;
    }
    
    public function setPoints($penaltyPoints)
    {
        $connection = new \MongoClient(Yii::$app->mongodb->dsn);
        $database = $connection->deepdive;
        $collection = $database->penaltyPointMaster;
        $results = $collection->find(['is_deleted' => '0', 'is_active' => '1']);
        $penaltyPointMaster = array();
        $penaltyResultArr   = array();
        foreach ($results as $result) {
            $penaltyPointMaster[$result['device_type']][$result['rule']] = $result['points'];
        }  

        foreach($penaltyPoints as $key => $record)
        {
            if(isset($penaltyPointMaster[$record['device_type']]['ios_compliance_status']) && $record['ios_compliance_status'] > 0)
            {
                $record['ios_compliance_status'] = (int)$record['ios_compliance_status'] * (int)$penaltyPointMaster[$record['device_type']]['ios_compliance_status'];
            }
            
            if(isset($penaltyPointMaster[$record['device_type']]['bgp_available']) && $record['bgp_available'] > 0)
            {
                $record['bgp_available'] = (int)$record['bgp_available'] * (int)$penaltyPointMaster[$record['device_type']]['bgp_available'];
            }
            
            if(isset($penaltyPointMaster[$record['device_type']]['isis_available']) && $record['isis_available'] > 0)
            {
                $record['isis_available'] = (int)$record['isis_available'] * (int)$penaltyPointMaster[$record['device_type']]['isis_available'];
            }
            
            if(isset($penaltyPointMaster[$record['device_type']]['resilent_status']) && $record['resilent_status'] > 0)
            {
                $record['resilent_status'] = (int)$record['resilent_status'] * (int)$penaltyPointMaster[$record['device_type']]['resilent_status'];
            }
            $penaltyResultArr[$key] = $record;         
        }
        return $penaltyResultArr;
    }
}
