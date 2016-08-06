<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\PenaltyPointMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Penalty Point Masters';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="penalty-point-master-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Penalty Point Master', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'section',
            'device_type',
            'subsection',
            'rule',
            'frequency',
            'points',
            'created_at',
            'modified_at',
            // 'created_by',
            // 'modified_by',
            // 'is_deleted',
            // 'is_active',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
