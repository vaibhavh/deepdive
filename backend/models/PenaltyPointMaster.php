<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "penaltypointmaster".
 *
 * @property integer $id
 * @property string $section
 * @property string $device_type
 * @property string $subsection
 * @property string $rule
 * @property string $frequency
 * @property integer $points
 * @property string $created_at
 * @property string $modified_at
 * @property integer $created_by
 * @property integer $modified_by
 * @property integer $is_deleted
 * @property integer $is_active
 */
class PenaltyPointMaster extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'penaltypointmaster';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['section', 'device_type', 'subsection', 'rule', 'frequency', 'points', 'created_at', 'modified_at', 'created_by', 'modified_by', 'is_deleted', 'is_active'], 'required'],
            [['points', 'created_by', 'modified_by', 'is_deleted', 'is_active'], 'integer'],
            [['created_at', 'modified_at'], 'safe'],
            [['section', 'device_type', 'subsection', 'rule'], 'string', 'max' => 50],
            [['frequency'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'section' => 'Section',
            'device_type' => 'Device',
            'subsection' => 'Subsection',
            'rule' => 'Rule',
            'frequency' => 'Frequency',
            'points' => 'Points',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
            'created_by' => 'Created By',
            'modified_by' => 'Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
        ];
    }

    public function saveToMongoDb($data) {
        if(!empty($data['PenaltyPointMaster']))
        {
            $myData = $data['PenaltyPointMaster'];
            $myData['created_at'] = date("Y/m/d h:i:s a");
            $myData['modified_at'] = date("Y/m/d h:i:s a");
            $myData['created_by'] = Yii::$app->user->identity->id;
            $myData['modified_by'] = Yii::$app->user->identity->id;
            $myData['is_deleted'] = '0'; 
            //$myData['is_active'] = '1';
            $connection = new \MongoClient(Yii::$app->mongodb->dsn);
            $database = $connection->deepdive;
            $collection = $database->penaltyPointMaster;
            $collection->insert($myData);
            Yii::$app->session->setFlash('success','Record Created Successfully!!!');
            return $myData;
        }
        //Yii::$app->session->setFlash('error','Failed to create the record');
        return false;
    }
    
    public function updateToMongoDb($id, $data) {
        if(!empty($data['PenaltyPointMaster']))
        {
            $model = self::mongoDbFindOne($id);
            $idObject = new \MongoId($id);
            $myData = $data['PenaltyPointMaster'];
            $myData['_id'] = $idObject;
            $myData['created_at'] = $model->created_at;
            $myData['modified_at'] = date("Y/m/d h:i:s a");
            $myData['created_by'] = Yii::$app->user->identity->id;
            $myData['modified_by'] = $model->modified_by;
            $myData['is_deleted'] = $model->is_deleted;
            $connection = new \MongoClient(Yii::$app->mongodb->dsn);
            $database = $connection->deepdive;
            $collection = $database->penaltyPointMaster;
            $collection->save($myData);
            Yii::$app->session->setFlash('success','Record Updated Successfully!!!');
            return $myData;
        }
        //Yii::$app->session->setFlash('error','Failed to update the record');
        return false;
    }
    
    public function deleteFromMongoDb($id)
    {
        $myDataObj = self::mongoDbFindOne($id);
        $myData = $myDataObj->attributes;
        $idObject = new \MongoId($id);
        $myData['_id'] = $idObject;
        $myData['modified_at'] = date("Y/m/d h:i:s a");
        $myData['modified_by'] = Yii::$app->user->identity->id;
        $myData['is_deleted'] = '1';
        $myData['is_active'] = '0';
        $connection = new \MongoClient(Yii::$app->mongodb->dsn);
        $database = $connection->deepdive;
        $collection = $database->penaltyPointMaster;
        $collection->save($myData);
        Yii::$app->session->setFlash('success','Record Deleted Successfully!!!');
        return true;
    }
    
    public function mongoDbFindOne($id) {
        $model = new PenaltyPointMaster();
        if(!empty($id))
        {
            $idObject = new \MongoId($id);
            $connection = new \MongoClient(Yii::$app->mongodb->dsn);
            $database = $connection->deepdive;
            $collection = $database->penaltyPointMaster;
            $results = $collection->findOne(["_id" => $idObject]);
            $model->id = $id;
            $model->attributes = $results;
            return $model;
        }
        return false;
    }
    
    
}
