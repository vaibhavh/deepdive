<?php

use yii\helpers\Html;
use yii\bootstrap\Modal;
use frontend\models\CustomActionColumn;
use dosamigos\datepicker\DatePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PenaltyPointsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */



$this->title = 'Penalty Top Ten';
$this->params['breadcrumbs'][] = $this->title;

if(Yii::$app->session->hasFlash('error'))
{
    echo '<div class="alert alert-error">' . Yii::$app->session->getFlash('error') . '</div>';
}
if(Yii::$app->session->hasFlash('success'))
{
    echo '<div class="alert alert-success">' . Yii::$app->session->getFlash('success') . '</div>';
}
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
    'header' => '<h2>Top Ten Penalty Points</h2>',
    'id' => 'model',
    'size' => 'model-lg',
]);
echo '<div id="modelContent" align="center"><img src="/deepdive/frontend/web/images/ajax-loader.gif"></div>';
Modal::end();
?>
<div class="penalty-points-index">
    <?php $form = ActiveForm::begin(); ?>
    <input id="penaltytopten-circleval" class="form-control" name="PenaltyTopTen[circleval]" type="hidden" value="<?php if(!empty($_REQUEST['PenaltyTopTen']['circle']))  echo $_REQUEST['PenaltyTopTen']['circle']; else echo ''; ?>">
    <input id="penaltytopten-deviceval" class="form-control" name="PenaltyTopTen[deviceval]" type="hidden" value="<?php if(!empty($_REQUEST['PenaltyTopTen']['device']))  echo $_REQUEST['PenaltyTopTen']['device']; else echo ''; ?>">
    <div><h1><?= Html::encode($this->title) ?></h1>           
        <?php
        echo '<div style="width:200px;float:left;valign:center;">' .
        $form->field($model, 'fromDate')->widget(
                DatePicker::className(), [
            'name' => 'fromDate',
            //'value' => '02-16-2012',
            'template' => '{input}{addon}',
            'clientOptions' => [
                'autoclose' => true,
                'format' => 'dd-M-yyyy',
                'endDate' => '0d',
                'daysOfWeekDisabled' => '0,2,3,4,5,6'
            ]
        ]) . '</div><div style="width:200px;float:left;valign:center;padding-left:10px;">' .
        $form->field($model, 'toDate')->widget(
                DatePicker::className(), [
            'name' => 'toDate',
            //'value' => '02-16-2012',
            'template' => '{input}{addon}',
            'clientOptions' => [
                'autoclose' => true,
                'format' => 'dd-M-yyyy',
                'endDate' => '0d',
                'daysOfWeekDisabled' => '1,2,3,4,5,6'
            ]
        ]) . '</div><div style="width:200px;float:left;valign:center;padding-left:10px;">' . $form->field($model, 'scenario')->dropDownList(array('PAN-INDIA' => "PAN India", 'CIRCLE' => "Circle Wise", 'DEVICE' => "Device Wise")) . '</div><div id="div-circle" style="width:200px;float:left;valign:center;padding-left:10px;display:none;">' . $form->field($model, 'circle')->dropDownList($circleMasterData) . '</div><div id="div-device" style="width:200px;float:left;valign:center;padding-left:10px;display:none;">' . $form->field($model, 'device')->dropDownList(array('' => 'Device Type', 'ESR' => "CSS", 'PAR' => "AG1")) . '</div>';
        ?> 
        <div class="form-group" style="padding-top:25px;">
            &nbsp;&nbsp;
            <?= Html::submitButton('Search', ['class' => 'btn btn-success']) ?>
        </div>

    </div>    
    <?php ActiveForm::end(); ?>
    <?php
    
    if (!empty($dataProvider)) {
//        echo "<pre>",print_r($dataProvider);die;
        echo $this->renderAjax('view_list', [
            'dataProvider' => $dataProvider,
            'date' => $date,
            'searchModel' => $pointsModel,
            'toDate' => $toDate,
        ]);
    }
    ?>
    <?php
    $this->registerJsFile("js/topTenPenalty.js", ['position' => \yii\web\View::POS_END]);
//    $this->registerJsFile("js/jquery-1.11.3.min.js", ['position' => \yii\web\View::POS_END]);
    ?>    
</div>



