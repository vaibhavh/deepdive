<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use app\controllers\SiteController;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use frontend\models\CustomActionColumn;
use kartik\export\ExportMenu;

//use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PenaltyPointsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Penalty Points';
$this->params['breadcrumbs'][] = $this->title;
?>
<style type="text/css">
    @media screen and (min-width: 768px) {
        .modal-dialog {
            width: 800px; /* New width for default modal */
        }
        .modal-sm {
            width: 350px; /* New width for small modal */
        }
    }

    @media screen and (min-width: 992px) {
        .modal-lg {
            width: 950px; /* New width for large modal */
        }
    }

</style>
<?php
Modal::begin([
    'header' => '<h2>Penalty Points</h2>',
    'id' => 'model',
    'size' => 'model-lg',
]);
echo '<div id="modelContent" align="center"><img src="/deepdive/frontend/web/images/ajax-loader.gif"></div>';
Modal::end();
?>
<p>
    <?php
    $date = (empty($date)) ? date('d-m-Y') : $date;
    $fromDate = str_replace(':', "-", $date);
    $datetime = new DateTime($fromDate);
    $fromDate = $datetime->format('d-m-Y');
    $datetime->modify('+6 day');
    $toDate = $datetime->format('d-m-Y');
    ?>
</p>
<div class="penalty-points-index">
    <h1><?= Html::encode($this->title);
    ?><span style="color: #6666cc;"><h4><b><?= "Current Week [ From $fromDate till $toDate ]"; ?></b></h4></span> </h1>
        <?php // echo $this->render('_search', ['model' => $searchModel]);    ?>


    <?php // Pjax::begin(['id' => 'grid', 'timeout' => false, 'clientOptions' => ['method' => 'POST']]); ?> 
    <?php
    \yii\widgets\Pjax::begin(['id' => 'penlaty',
        'timeout' => false,
        'enablePushState' => false,
        'clientOptions' => ['method' => 'POST']]);
    $gridColumns = [
        ['class' => 'kartik\grid\SerialColumn'],
        'hostname',
        'loopback0',
        'sapid',
        [
            'filter' => true,
            'value' => "device_type",
            'attribute' => "device_type"
        ],
//            'device_type',
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
        [
            'label' => 'CRC',
            'filter' => false,
            'attribute' => 'crc',
            'value' => 'crc'
        ],
        'input_errors',
        'output_errors',
        'interface_resets',
        'power',
        'optical_power',
        'module_temperature',
        [
            'label' => 'Device Uptime',
            'filter' => false,
            'attribute' => 'device_stability',
            'value' => 'device_stability'
        ],
        [
            'label' => 'Packet Loss',
            'filter' => false,
            'attribute' => 'packetloss',
            'value' => 'packetloss'
        ],
        'latency',
        [
            'label' => 'Security Compliance',
            'filter' => false,
            'attribute' => 'audit_penalty',
            'value' => 'audit_penalty'
        ],
        [
            'label' => 'PvB Priority 1',
            'filter' => false,
            'attribute' => 'pvb_priority_1',
            'value' => 'pvb_priority_1'
        ],
        [
            'label' => 'PvB Priority 2',
            'filter' => false,
            'attribute' => 'pvb_priority_2',
            'value' => 'pvb_priority_2'
        ],
        [
            'label' => 'PvB Priority 3',
            'filter' => false,
            'attribute' => 'pvb_priority_3',
            'value' => 'pvb_priority_3'
        ],
        [
            'label' => 'ISIS Stability',
            'filter' => false,
            'attribute' => 'isis_stability_changed',
            'value' => 'isis_stability_changed'
        ],
        [
            'label' => 'BGP Stability',
            'filter' => false,
            'attribute' => 'bgp_stability_changed',
            'value' => 'bgp_stability_changed'
        ],
        [
            'label' => 'Bfd Stability',
            'filter' => false,
            'attribute' => 'bfd_stability_changed',
            'value' => 'bfd_stability_changed'
        ],
        [
            'label' => 'LDP Stability',
            'filter' => false,
            'attribute' => 'ldp_stability_changed',
            'value' => 'ldp_stability_changed'
        ],
        'total',
        ['class' => 'frontend\models\CustomActionColumn'],
    ];
    echo ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumns
    ]);
    ?>

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
//        'floatHeader' => true,
//        'floatHeaderOptions' => ['scrollingTop' => 1],
        'panel' => [
            'type' => GridView::TYPE_PRIMARY
        ],
        'beforeHeader' => [
            [
                'columns' => [
                    ['content' => '', 'options' => ['colspan' => 1, 'class' => 'text-center warning']],
                    ['content' => 'Device Details', 'options' => ['colspan' => 4, 'class' => 'text-center warning']],
                    ['content' => 'IOS & SMU Compliance', 'options' => ['colspan' => 1, 'class' => 'text-center warning']],
                    ['content' => 'Resiliency', 'options' => ['colspan' => 3, 'class' => 'text-center warning']],
                    ['content' => 'Interface Errors', 'options' => ['colspan' => 8, 'class' => 'text-center warning']],
                    ['content' => 'IPSLA', 'options' => ['colspan' => 2, 'class' => 'text-center warning']],
                    ['content' => 'Configuration Audit', 'options' => ['colspan' => 4, 'class' => 'text-center warning']],
                    ['content' => 'Protocol Stability', 'options' => ['colspan' => 4, 'class' => 'text-center warning']],
                    ['content' => '', 'options' => ['colspan' => 1, 'class' => 'text-center warning']],
                    ['content' => '', 'options' => ['colspan' => 1, 'class' => 'text-center warning']],
                ],
//                'options' => ['class' => 'skip-export'] // remove this row from export
            ]
        ],
        'columns' => $gridColumns,
    ]);
    ?>
    <?php Pjax::end(); ?></div>
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