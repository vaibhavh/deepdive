<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use app\controllers\SiteController;
use kartik\grid\GridView;
//use yii\bootstrap\Modal;
use frontend\models\CustomActionColumn;

$this->title = 'Penalty Points';
$this->params['breadcrumbs'][] = $this->title;
?>
<!--<style type="text/css">
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

</style>-->
<?php
//Modal::begin([
//    'header' => '<h2>Penalty Points</h2>',
//    'id' => 'model',
//    'size' => 'model-lg',
//]);
//echo '<div id="modelContent" align="center"></div>';
//Modal::end();
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
        <?php // echo $this->render('_search', ['model' => $searchModel]);    ?>


    <?php // Pjax::begin(['id' => 'grid', 'timeout' => false, 'clientOptions' => ['method' => 'POST']]);  ?> 
    <?php
    \yii\widgets\Pjax::begin(['id' => 'penlaty',
        'timeout' => false,
        'enablePushState' => false,
        'clientOptions' => ['method' => 'POST']]);
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
                    ['content' => 'Interface Errors', 'options' => ['colspan' => 7, 'class' => 'text-center warning']],
                    ['content' => 'IPSLA', 'options' => ['colspan' => 2, 'class' => 'text-center warning']],
                    ['content' => 'Configuration Audit', 'options' => ['colspan' => 1, 'class' => 'text-center warning']],
                    ['content' => '', 'options' => ['colspan' => 1, 'class' => 'text-center warning']],
                    ['content' => '', 'options' => ['colspan' => 1, 'class' => 'text-center warning']],
                ],
//                'options' => ['class' => 'skip-export'] // remove this row from export
            ]
        ],
        'columns' => [
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
            ['class' => 'frontend\models\CustomActionColumn'],
        ],
    ]);
    $this->registerJsFile("js/bootstrap.min.js", ['position' => \yii\web\View::POS_END]);
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
