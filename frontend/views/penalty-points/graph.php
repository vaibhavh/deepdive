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

$fromDate = str_replace(':', "-", $deviceDetails['date']);
$datetime = new DateTime($fromDate);
$fromDate = $datetime->format('d-m-Y');


echo Highcharts::widget([
    'clientOptions' => [
        'chart' => [
            'type' => 'pie'
        ],
        'title' => [
            'text' => "Penalty Graph <b>{$deviceDetails['hostname']} {$deviceDetails['loopback0']}<b><br> {$fromDate} to {$curretntDate}"
        ],
        'subtitle' => [
            'text' => 'Click the slices to view subsections.'
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
                    'format' => '{point.name}: {point.y} {percentage:.2f}%'
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

echo Highcharts::widget([
    'clientOptions' => [
        'chart' => [
            'type' => 'column'
        ],
        'title' => [
            'text' => "Penalty Graph {$deviceDetails['hostname']} {$deviceDetails['loopback0']} {$fromDate} to {$curretntDate}"
        ],
        'subtitle' => [
            'text' => 'Click the columns to view subsections.'
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
        'name' => 'Brands',
        'colorByPoint' => true,
        'data' => $sectionData
            ]],
        'drilldown' => [
            'series' => $subSectionData
        ]
    ]
]);
?>