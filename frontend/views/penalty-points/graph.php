<?php

use yii\helpers\Html;
use yii\grid\GridView;
use \dosamigos\highcharts\HighCharts;
use \dosamigos\highcharts\HighChartsAsset;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PenaltyPointsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Penalty Points';
$this->params['breadcrumbs'][] = $this->title;

/* @var $this yii\web\View */
$this->title = 'My Yii Application';
$curretntDate = date("d-m-Y");

$fromDate = (!empty($deviceDetails['date']))?str_replace(':', "-", $deviceDetails['date']):'';
$datetime = new DateTime($fromDate);
$fromDate = $datetime->format('d-m-Y');

$deviceHeader = (!empty($deviceDetails['circle']))?$deviceDetails['circle'] . ' : ' .$deviceDetails['hostname'] .'/'. $deviceDetails['loopback0']:'';  
$dateHeader   = '<span style="font-size:12px;color:#000066;font-weight:bold;">' . $fromDate . ' to ' . $curretntDate .'</span>';

echo Highcharts::widget([
    'clientOptions' => [
        'chart' => [
            'type' => 'pie'
        ],
        'title' => [
            'text' => "{$deviceHeader} <br> {$dateHeader}"
        ],
        'subtitle' => [
            'text' => ''
        ],
        'plotOptions' => [
            'pie' => [
                'allowPointSelect' => true,
                'cursor' => 'pointer',
                'dataLabels' => [
                    'enabled' => true
                ],
                'showInLegend' => false
            ],
            'series' => [
                'dataLabels' => [
                    'enabled' => true,
                    'format' => '{point.name}: {point.y}, {percentage:.2f}%'
                ]
            ]
        ],
        'tooltip' => [
            'headerFormat' => '<span style="font-size:11px">{series.name}</span><br>',
            'pointFormat' => '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b> of total<br/>'
        ],
        'series' => [
            ['name' => 'Penalty Points', 'colorByPoint' => true,
                'data' => $sectionData
            ]
        ],
        'drilldown' => [
            'series' => $subSectionData
        ]
    ]
]);
echo "<hr>";
echo Highcharts::widget([
    'clientOptions' => [
        'chart' => [
            'type' => 'column'
        ],
        'title' => [
            'text' => ""
        ],
        'subtitle' => [
            'text' => ''
        ],
        'xAxis' => [
            'type' => 'category'
        ],
        'yAxis' => [
            'title' => [
                'text' => 'Total penalty points'
            ]
        ],
        'legend' => [
            'enabled' => false
        ],
        'plotOptions' => [
            'series' => [
                'borderWidth' => 0,
                'dataLabels' => [
                    'enabled' => true,
                    'format' => '{point.y}'
                ]
            ]
        ],
        'tooltip' => [
            'headerFormat' => '<span style="font-size:11px">{series.name}</span><br>',
            'pointFormat' => '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b> of total<br/>'
        ],
        'series' => [[
        'name' => 'Penalty Points',
        'colorByPoint' => true,
        'data' => $sectionData
            ]],
        'drilldown' => [
            'series' => $subSectionData
        ]
    ]
]);
?>