<?php

namespace frontend\models;


use yii\base\Model;
use yii\data\ArrayDataProvider;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "{{%tbl_nexus_interface_description}}".
 *
 * @property string $hostname
 * @property string $neid
 * @property string $state
 * @property string $site
 * @property string $service
 * @property string $mgmt_ip
 * @property string $rbu
 * @property string $vlan500
 * @property string $vlan801
 * @property integer $status
 * @property integer $ping_status
 * @property integer $ssh_status
 * @property integer $in_progress
 * @property string $physical_interface_desc
 * @property string $port_channel_desc
 * @property integer $is_error
 * @property string $comments
 * @property integer $is_checked
 * @property string $cdp_neighbor_cmd_output
 * @property string $port_channel_cmd_output
 * @property string $created_at
 * @property integer $created_by
 * @property string $modified_at
 * @property integer $modified_by
 * @property string $platform
 * @property integer $id
 */
class NexusInterfaceDescription extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tbl_nexus_interface_description}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['hostname', 'neid', 'state', 'site', 'mgmt_ip', 'comments'], 'required'],
            [['status', 'ping_status', 'ssh_status', 'in_progress', 'is_error', 'is_checked', 'created_by', 'modified_by'], 'integer'],
            [['physical_interface_desc', 'port_channel_desc', 'cdp_neighbor_cmd_output', 'port_channel_cmd_output'], 'string'],
            [['created_at', 'modified_at'], 'safe'],
            [['hostname'], 'string', 'max' => 20],
            [['neid'], 'string', 'max' => 35],
            [['state', 'site', 'service', 'vlan500', 'vlan801'], 'string', 'max' => 100],
            [['mgmt_ip'], 'string', 'max' => 16],
            [['rbu', 'comments'], 'string', 'max' => 255],
            [['platform'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'hostname' => 'Hostname',
            'neid' => 'Neid',
            'state' => 'State',
            'site' => 'Site',
            'service' => 'Service',
            'mgmt_ip' => 'Mgmt Ip',
            'rbu' => 'Rbu',
            'vlan500' => 'Vlan500',
            'vlan801' => 'Vlan801',
            'status' => 'Status',
            'ping_status' => 'Ping Status',
            'ssh_status' => 'Ssh Status',
            'in_progress' => 'In Progress',
            'physical_interface_desc' => 'Physical Interface Desc',
            'port_channel_desc' => 'Port Channel Desc',
            'is_error' => 'Is Error',
            'comments' => 'Comments',
            'is_checked' => 'Is Checked',
            'cdp_neighbor_cmd_output' => 'Cdp Neighbor Cmd Output',
            'port_channel_cmd_output' => 'Port Channel Cmd Output',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'modified_at' => 'Modified At',
            'modified_by' => 'Modified By',
            'platform' => 'Platform',
            'id' => 'ID',
        ];
    }
    
    public static function getSites() {
        $sql = "Select distinct site FROM tbl_nexus_interface_description ";

        $resultSet = Yii::app()->db->createCommand($sql)->queryAll();
        //CHelper::debug($resultSet);
        return $resultSet;
    }
    
    public function getSiteDataBySiteName($site){
        return Yii::$app()->db->createCommand("SELECT id, hostname, mgmt_ip, site, physical_interface_desc, port_channel_desc FROM tbl_nexus_interface_description WHERE site ='{$site}' group by mgmt_ip")->queryAll();
    }
}
