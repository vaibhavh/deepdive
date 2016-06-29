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
            'bgp_available' => '1 Bgp Available',
            'isis_available' => '1 Isis Available',
            'resilent_status' => 'Repair Path Status',
            'created_date' => 'Created Date',
        ];
    }

    public function getSectionData($sectionPoints = [])
    {
        $sections = [
            ['name' => 'IPSLA', 'y' => 0, 'drilldown' => 'IPSLA'],
            ['name' => 'Interface Errors', 'y' => 0, 'drilldown' => 'Interface Errors'], 
            ['name' => 'Device Performance Management', 'y' => 0, 'drilldown' => 'Device Performance Management'],
            ['name' => 'TAC Case Raised', 'y' => 0, 'drilldown' => 'TAC Case Raised'],
            ['name' => 'Syslog', 'y' => 0, 'drilldown' => 'Syslog'],
            ['name' => 'Environment Condition', 'y' => 0, 'drilldown' => 'Environment Condition'],
            ['name' => 'Configuration Audit', 'y' => 0, 'drilldown' => 'Configuration Audit'],
            ['name' => 'Resiliency', 'y' => 0, 'drilldown' => 'Resiliency'],
            ['name' => 'IOS & SMU Compliance', 'y' => 0, 'drilldown' => 'IOS & SMU Compliance'],
            ['name' => 'Protocol Stability', 'y' => 0, 'drilldown' => 'Protocol Stability'],
            ['name' => 'System Internals', 'y' => 0, 'drilldown' => 'System Internals'],
            ['name' => 'Network Resiliency Audit', 'y' => 0, 'drilldown' => 'Network Resiliency Audit'],
            ['name' => 'Quality of Service', 'y' => 0, 'drilldown' => 'Quality of Service']];

        if(!empty($sectionPoints))
        {
            foreach($sections as $key => $section)
            {
                if(isset($sectionPoints[$section['name']]))
                {
                    $sections[$key]['y'] = $sectionPoints[$section['name']];
                }
            }
        }
        return $sections;
    }
    
    public function getSubSectionDrilldownData($subSectionPoints = [])
    {
        $drilldown = [[
                'name' => 'IPSLA',
                'id' => 'IPSLA',
                'data' => [
                    ['Packet Loss', 0],
                    ['Jitter', 0],
                    ['Latency', 0]
                ]
            ], [
                'name' => 'Interface Errors',
                'id' => 'Interface Errors',
                'data' => [
                    ['Crc', 0],
                    ['Input Errors', 0],
                    ['Output Errors', 0],
                    ['Interface Reset', 0],
                    ['SFP Module Temperature', 0],
                    ['Optical Power', 0],
                    ['Buffer Consumption', 0],
                    ['Power Error', 0]
                ]
            ], [
                'name' => 'Device Performance Management',
                'id' => 'Device Performance Management',
                'data' => [
                    ['CPU Utilization', 0],
                    ['Memory Utilization', 0],
                    ['Device Uptime', 0],
                    ['Core Dump', 0],
                    ['Packet drops observed in CoPP', 0]
                ]
            ], [
                'name' => 'TAC Case Raised',
                'id' => 'TAC Case Raised',
                'data' => [
                    ['TBD', 0]
                ]
            ], [
                'name' => 'Syslog',
                'id' => 'Syslog',
                'data' => [
                    ['Syslog', 0]
                ]
            ], [
                'name' => 'Environment Condition',
                'id' => 'Environment Condition',
                'data' => [
                    ['EnvCond', 0]
                ]
            ], [
                'name' => 'Configuration Audit',
                'id' => 'Configuration Audit',
                'data' => [
                    ['Priority Css Ag1', 0],
                    ['Priority Ag2', 0],
                    ['Priority Ag3', 0],
                    ['Compare NIP & Show run', 0],
                    ['Priority 1', 0]
                ]
            ], [
                'name' => 'Resiliency',
                'id' => 'Resiliency',
                'data' => [
                    ['1 bgp available', 0],
                    ['1 isis available', 0],
                    ['Repair path not available', 0]
                ]
            ], [
                'name' => 'IOS & SMU Compliance',
                'id' => 'IOS & SMU Compliance',
                'data' => [
                    ['IOS', 0],
                    ['IOS & SMU', 0]
                ]
            ], [
                'name' => 'Protocol Stability',
                'id' => 'Protocol Stability',
                'data' => [
                    ['ISIS stability', 0],
                    ['LDP', 0],
                    ['BGP', 0],
                    ['BFD', 0]
                ]
            ], [
                'name' => 'System Internals',
                'id' => 'System Internals',
                'data' => [
                    ['RSPs in hot stand by', 0],
                    ['All Line cards in good condition', 0],
                    ['RSP RIB/FIB is transferred to line cards', 0],
                    ['MPLS label database condition', 0]
                ]
            ], [
                'name' => 'Network Resiliency Audit',
                'id' => 'Network Resiliency Audit',
                'data' => [
                    ['v8.0', 0],
                    ['v7.1', 0],
                    ['v5.1', 0],
                    ['v5.0', 0],
                    ['v6.1', 0],
                    ['v7.0', 0],
                    ['v6.2', 0]
                ]
            ], [
                'name' => 'Quality of Service',
                'id' => 'Quality of Service',
                'data' => [
                    ['Packet drops in priorty queue', 0],
                    ['Packet drops in control queue', 0],
                    ['Packet drops in second queue', 0],
                    ['Packet drops in third queue', 0]
                ]
            ]];
        
        if(!empty($subSectionPoints))
        {
            foreach($drilldown as $key => $section)
            {
                $mydata = $section['data'];
                if(isset($subSectionPoints[$section['name']]))
                {
                    $sectionArr = $subSectionPoints[$section['name']];
                    foreach($section['data'] as $mykey => $rule)
                    {
                        if(isset($sectionArr[$rule[0]]))
                        {    
                            $drilldown[$key]['data'][$mykey][1] = $sectionArr[$rule[0]];   
                        }
                    }
                }
            }
        }
        return $drilldown;
    }
}
