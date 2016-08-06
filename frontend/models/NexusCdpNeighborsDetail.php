<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
/**
 * This is the model class for table "{{%tbl_nexus_cdp_neighbors_detail}}".
 *
 * @property integer $id
 * @property string $mgmt_ip
 * @property string $local_interface
 * @property string $remote_interface
 * @property string $ipv4
 * @property string $platform
 * @property string $capability
 * @property string $hold_time
 * @property string $created_at
 * @property string $port_id
 * @property string $local_port_channel
 * @property string $remote_port_channel
 * @property string $error
 */
class NexusCdpNeighborsDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tbl_nexus_cdp_neighbors_detail}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mgmt_ip', 'ipv4'], 'required'],
            [['created_at'], 'safe'],
            [['error'], 'string'],
            [['mgmt_ip'], 'string', 'max' => 30],
            [['local_interface', 'remote_interface', 'platform', 'capability', 'hold_time', 'local_port_channel', 'remote_port_channel'], 'string', 'max' => 50],
            [['ipv4'], 'string', 'max' => 255],
            [['port_id'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mgmt_ip' => 'Mgmt Ip',
            'local_interface' => 'Local Interface',
            'remote_interface' => 'Remote Interface',
            'ipv4' => 'Ipv4',
            'platform' => 'Platform',
            'capability' => 'Capability',
            'hold_time' => 'Hold Time',
            'created_at' => 'Created At',
            'port_id' => 'Port ID',
            'local_port_channel' => 'Local Port Channel',
            'remote_port_channel' => 'Remote Port Channel',
            'error' => 'Error',
        ];
    }
}
