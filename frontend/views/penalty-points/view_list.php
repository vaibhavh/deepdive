<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use app\controllers\SiteController;
use kartik\grid\GridView;
use frontend\models\CustomActionColumn;
use kartik\export\ExportMenu;

$this->title = 'Penalty Points';
$this->params['breadcrumbs'][] = $this->title;
?>

<p>
    <?php
    $fromDate = str_replace(':', "-", $date);
    $datetime = new DateTime($fromDate);
    $fromDate = $datetime->format('d-m-Y');
    $datetime = new DateTime($toDate);
    $toDate = $datetime->format($toDate);
    ?>
</p>
<div class="penalty-points-index">

    <h1><?= Html::encode($this->title);
    ?><span style="color: #6666cc;"><h4><b><?= "Data from [ From $fromDate till $toDate ]"; ?></b></h4></span> </h1>
    <?php
    $gridColumns = [
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'filter' => false,
                'value' => "hostname",
                'attribute' => "hostname"
            ],
            [
                'filter' => false,
                'value' => "loopback0",
                'attribute' => "loopback0"
            ],
            [
                'filter' => false,
                'value' => "sapid",
                'attribute' => "sapid"
            ],
            [
                'filter' => false,
                'value' => "device_type",
                'attribute' => "device_type"
            ],
            [
                'filter' => false,
                'value' => "ios_compliance_status",
                'attribute' => 'ios_compliance_status',
            ],
            [
                'label' => '1 BGP Available',
                'filter' => false,
                'attribute' => 'bgp_available',
                'value' => 'bgp_available'
            ],
            [
                'label' => '1 ISIS Available',
                'filter' => false,
                'attribute' => 'isis_available',
                'value' => 'isis_available'
            ],
            [
                'label' => 'Repair Path Available',
                'filter' => false,
                'attribute' => 'resilent_status',
                'value' => 'resilent_status'
            ],
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
            //['class' => 'frontend\models\CustomActionColumn'],
        ];
    ?>
    <div style="padding-top: 50px;padding-left: 10px;position: absolute;"> <?=
        ExportMenu::widget([
        'dataProvider' => $exportAll,
        'filterModel' => $searchModel,
        'columns' => $gridColumns,
    ]);
    ?></div>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'containerOptions' => ['style' => 'overflow: auto'],
        'headerRowOptions' => ['class' => 'kartik-sheet-style'],
        'filterRowOptions' => ['class' => 'kartik-sheet-style'],
        'pjax' => true,
        'bordered' => true,
        'striped' => false,
        'condensed' => false,
        'responsive' => true,
        'panel' => [
            'type' => GridView::TYPE_PRIMARY
        ],
        'toolbar'=> [
            '{toggleData}',
        ],
        'beforeHeader' => [
            [
                'columns' => [
                    ['content' => '', 'options' => ['colspan' => 1, 'class' => 'text-center warning']],
                    ['content' => 'Device Details', 'options' => ['colspan' => 4, 'class' => 'text-center warning']],
                    ['content' => 'IOS & SMU Compliance', 'options' => ['colspan' => 1, 'class' => 'text-center warning']],
                    ['content' => 'Resiliency', 'options' => ['colspan' => 3, 'class' => 'text-center warning']],
                    ['content' => 'Interface Errors', 'options' => ['colspan' => 7, 'class' => 'text-center warning']],
                    ['content' => 'IPSLA', 'options' => ['colspan' => 2, 'class' => 'text-center warning']],
                    ['content' => 'Configuration Audit', 'options' => ['colspan' => 1, 'class' => 'text-center warning']],
                    ['content' => '', 'options' => ['colspan' => 1, 'class' => 'text-center warning']],
                    ['content' => '', 'options' => ['colspan' => 1, 'class' => 'text-center warning']],
                ],
//                'options' => ['class' => 'skip-export'] // remove this row from export
            ]
        ],
        'columns' => $gridColumns,
    ]);
    //$this->registerJsFile("js/bootstrap.min.js", ['position' => \yii\web\View::POS_END]);
    ?>
</div>

<style>
    .container{
        width: 100%;
    }
    tr th .text-center{
        background-color: #aabcfe;
    }
    tr td {
        text-align: center;
    }
</style>
