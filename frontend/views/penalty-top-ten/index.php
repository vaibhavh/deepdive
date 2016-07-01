<?php

use yii\helpers\Html;
use yii\bootstrap\Modal;
use frontend\models\CustomActionColumn;
use dosamigos\datepicker\DatePicker;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $searchModel app\models\PenaltyPointsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

//echo "<pre>";
//print_r($model);
//die;

$this->title = 'Penalty Top Ten';
$this->params['breadcrumbs'][] = $this->title;

$db = Yii::$app->db;
$sql = "SELECT `circle_code`,`circle_name` FROM `tbl_circle_master`";
$command = $db->createCommand($sql);
$circleData = $command->queryAll();
$circleMasterData = [];
$circleMasterData[''] = 'Circle';
foreach($circleData as $myCircle)
{
    $circleMasterData[$myCircle['circle_code']] = $myCircle['circle_name'];
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
<div class="penalty-points-index">
    <?php $form = ActiveForm::begin(); ?>
    <div><h1><?= Html::encode($this->title) ?></h1>           
    <?php echo '<div style="width:200px;float:left;valign:center;">' .
    $form->field($model, 'fromDate')->widget(
    DatePicker::className(), [
    'name' => 'fromDate',
    //'value' => '02-16-2012',
    'template' => '{input}{addon}',
        'clientOptions' => [
            'autoclose' => true,
            'format' => 'dd-M-yyyy',
            'endDate' => '0d',
            'daysOfWeekDisabled'=>'0,2,3,4,5,6'
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
            'daysOfWeekDisabled'=>'1,2,3,4,5,6'
        ]
]) . '</div><div style="width:200px;float:left;valign:center;padding-left:10px;">'. $form->field($model, 'scenario')->dropDownList(array('PAN-INDIA' => "PAN India", 'CIRCLE' => "Circle Wise", 'DEVICE' => "Device Wise")) . '</div><div id="div-circle" style="width:200px;float:left;valign:center;padding-left:10px;display:none;">'. $form->field($model, 'circle')->dropDownList($circleMasterData) . '</div><div id="div-device" style="width:200px;float:left;valign:center;padding-left:10px;display:none;">'. $form->field($model, 'device')->dropDownList(array('' => 'Device Type', 'CSS' => "CSS", 'AG1' => "AG1")) . '</div>'; ?> 
    <div class="form-group" style="padding-left:90px;padding-top:25px;">
        <?= Html::submitButton('Search', ['class' => 'btn btn-success']) ?>
    </div>
        
    </div>    
    <?php ActiveForm::end(); ?>
<?php
    Modal::begin([
        'header' => '<h2>Penalty Points</h2>',
            'id' => 'model',
            'size' => 'model-lg',
    ]);
    echo '<div id="modelContent" align="center"></div>';
    Modal::end();
?>    
</div>
<?php

$this->registerJs("
$('#div-circle').hide();
$('#div-device').hide();
$('#penaltytopten-scenario').change(function() {
  if($('#penaltytopten-scenario').val() == 'PAN-INDIA')
  {
        $('#div-circle').hide('slow');
        $('#div-device').hide('slow');
  }
  else if($('#penaltytopten-scenario').val() == 'CIRCLE')
  {
        $('#div-circle').show('slow');
        $('#div-device').hide('slow');
  }
  else if($('#penaltytopten-scenario').val() == 'DEVICE')
  {
        $('#div-circle').hide('slow');
        $('#div-device').show('slow');
  }

    });", yii\web\View::POS_END);
?>
