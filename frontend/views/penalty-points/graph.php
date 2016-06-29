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
     
echo Highcharts::widget([
'clientOptions' => [
    'chart' => [
        'type' => 'pie'
    ],
    'title' => [
        'text' => 'Penalty Points {Device Name} 01-06-2016 to 30-06-2016'
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
        'pointFormat' => '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b> of total<br/>'
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
            'text' => 'Penalty Points {Device Name} 01-06-2016 to 30-06-2016'
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
                    'format' => '{point.y:.1f}%'
                ]
            ]
        ],

        'tooltip' => [
            'headerFormat' => '<span style="font-size:11px">{series.name}</span><br>',
            'pointFormat' => '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b> of total<br/>'
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