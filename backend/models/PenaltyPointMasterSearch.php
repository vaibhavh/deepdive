<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\PenaltyPointMaster;
use yii\data\ArrayDataProvider;

/**
 * PenaltyPointMasterSearch represents the model behind the search form about `app\models\PenaltyPointMaster`.
 */
class PenaltyPointMasterSearch extends PenaltyPointMaster
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'points', 'created_by', 'modified_by', 'is_deleted', 'is_active'], 'integer'],
            [['section', 'device_type', 'subsection', 'rule', 'frequency', 'created_at', 'modified_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
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
        if(!empty($params['PenaltyPointMasterSearch']))
        {
            $mySearchParams = $params['PenaltyPointMasterSearch'];
            $mySearchParams = array_filter($mySearchParams);
        }
        $mySearchParams['is_deleted'] = '0';
        $mySearchParams['is_active'] = '1';
        
        $connection = new \MongoClient(Yii::$app->mongodb->dsn);
        $database = $connection->deepdive;
        $collection = $database->penaltyPointMaster;
        $results = $collection->find($mySearchParams);
        $penaltyPointMaster = array();
        foreach ($results as $key => $result) {
            $penaltyPointMaster[$key] = $result;
        }    

        // add conditions that should always apply here
        $dataProvider = new ArrayDataProvider([
            'allModels' => $penaltyPointMaster,
            'pagination' => ['pageSize' => 5]
        ]);

        $this->load($params);
        return $dataProvider;
    }
}
