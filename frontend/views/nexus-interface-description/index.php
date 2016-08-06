<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Nexus Interface Descriptions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="nexus-interface-description-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Nexus Interface Description', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'hostname',
            'neid',
            'state',
            'site',
            'service',
             'mgmt_ip',
            // 'rbu',
            // 'vlan500',
            // 'vlan801',
            // 'status',
            // 'ping_status',
            // 'ssh_status',
            // 'in_progress',
            // 'physical_interface_desc:ntext',
            // 'port_channel_desc:ntext',
            // 'is_error',
            // 'comments',
            // 'is_checked',
            // 'cdp_neighbor_cmd_output:ntext',
            // 'port_channel_cmd_output:ntext',
            // 'created_at',
            // 'created_by',
            // 'modified_at',
            // 'modified_by',
            // 'platform',
            // 'id',

//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
