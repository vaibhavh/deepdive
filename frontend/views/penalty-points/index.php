<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\controllers\SiteController;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PenaltyPointsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Penalty Points';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="penalty-points-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]);  ?>

    <p>
        <?php //Html::a('Create Penalty Points', ['create'], ['class' => 'btn btn-success'])  ?>
    </p>
    <?php Pjax::begin(); ?>    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            //'id',
            'hostname',
            'loopback0',
            'device_type',
            'ios_compliance_status',
//            'ios_current_version',
//            'ios_built_version',
            'bgp_available',
            'isis_available',
            'resilent_status',
            'crc',
            'input_errors',
            'output_errors',
            'interface_resets',
            'power',
            'optical_power',
            'module_temperature',
            'packetloss',
            'audit_penalty',
            'latency',
            'total',
            'created_at',
                //['class' => 'yii\grid\ActionColumn'],
                ],
            ]);
            ?>
            <?php Pjax::end(); ?></div>
