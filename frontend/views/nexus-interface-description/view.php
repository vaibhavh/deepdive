<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\NexusInterfaceDescription */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Nexus Interface Descriptions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="nexus-interface-description-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'hostname',
            'neid',
            'state',
            'site',
            'service',
            'mgmt_ip',
            'rbu',
            'vlan500',
            'vlan801',
            'status',
            'ping_status',
            'ssh_status',
            'in_progress',
            'physical_interface_desc:ntext',
            'port_channel_desc:ntext',
            'is_error',
            'comments',
            'is_checked',
            'cdp_neighbor_cmd_output:ntext',
            'port_channel_cmd_output:ntext',
            'created_at',
            'created_by',
            'modified_at',
            'modified_by',
            'platform',
            'id',
        ],
    ]) ?>

</div>
