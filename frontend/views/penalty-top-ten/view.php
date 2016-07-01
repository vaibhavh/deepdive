<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\PenaltyPoints */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Penalty Points', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="penalty-points-view">

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
            'id',
            'hostname',
            'loopback0',
            'device_type',
            'ios_compliance_status',
            'ios_current_version',
            'ios_built_version',
            'bgp_available',
            'isis_available',
            'resilent_status',
            'created_date',
        ],
    ]) ?>

</div>
