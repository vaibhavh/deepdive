<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "penaltypoints".
 *
 * @property integer $id
 * @property string $hostname
 * @property string $loopback0
 * @property string $device_type
 * @property integer $ios_compliance_status
 * @property string $ios_current_version
 * @property string $ios_built_version
 * @property integer $bgp_available
 * @property integer $isis_available
 * @property integer $resilent_status
 * @property string $created_date
 */
class PenaltyPoints extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'penaltypoints';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['hostname', 'loopback0', 'device_type', 'ios_compliance_status', 'ios_current_version', 'ios_built_version', 'bgp_available', 'isis_available', 'resilent_status', 'created_date'], 'required'],
            [['ios_compliance_status', 'bgp_available', 'isis_available', 'resilent_status'], 'integer'],
            [['created_date'], 'safe'],
            [['hostname', 'loopback0'], 'string', 'max' => 20],
            [['device_type'], 'string', 'max' => 10],
            [['ios_current_version', 'ios_built_version'], 'string', 'max' => 50],
        ];
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
            'bgp_available' => 'Bgp Available',
            'isis_available' => 'Isis Available',
            'resilent_status' => 'Resilent Status',
            'created_date' => 'Created Date',
        ];
    }

}
