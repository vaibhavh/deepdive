<?php

use yii\base\Model;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use frontend\models\NexusInterfaceDescription;

/* @var $this sitetopology */
/* @var $model sitetopology */
$this->registerJsFile(Yii::$app->getUrlManager()->getBaseUrl()."/js/next.js");
$this->registerCssFile(Yii::$app->getUrlManager()->getBaseUrl() ."/css/next.css");


//Yii::app()->clientScript->registerScript('search', "
//$('.search-button').click(function(){
//	$('.search-form').toggle();
//	return false;
//});
//$('.search-form form').submit(function(){
//	$('#nexus-interface-description-grid').yiiGridView('update', {
//		data: $(this).serialize()
//	});
//	return false;
//});
//
////$('.get_site_list').change(function(){
////    $('div.custom-loader').show();
////    $('#sitetopology_sites').submit();
////});
//
//$('.refresh_page').click(function(e){
//    location.reload();
//});
//");
?>
<?php // if (CHelper::hasFlash('rechability_success') && CHelper::getFlash('rechability_success') != ''): ?>
    <div class="alert alert-success">
    <?php
//    echo CHelper::getFlash('rechability_success');
//    CHelper::setFlash('rechability_success', '');
    ?>
    </div>
<?php // endif ?>
<?php // if (CHelper::hasFlash('rechability_error') && CHelper::getFlash('rechability_error') != ''): ?>
    <div class="alert alert-notice">
    <?php
//    echo CHelper::getFlash('rechability_error');
//    CHelper::setFlash('rechability_error', '');
    ?>
    </div>
<?php // endif ?>

<style>
    
    .refresh_page{
        cursor:pointer; 
        float: left;
        margin-left: 5px;
            
    }
</style>
<h1>Site Topology</h1>

<div class="form-group clearfix test-panal" style="margin-top: 20px;">
    <h4>Select Site</h4>    
    <!--<form id="sitetopology_sites" method="get" action="/deepdive/frontend/web/nexus-interface-description/nexus-topology" enctype="multipart/form-data">-->
<?php 
$form = ActiveForm::begin([
'method' => 'post',
'action' => ['nexus-interface-description/nexus-topology'],
]);
$site_id = "";
//echo Html::dropDownList("site_id", $site_name, Html::listData(NexusInterfaceDescription::model()->getSites(), 'site', 'site'), array('class' => 'get_site_list', 'prompt' => 'Please select'));
//echo ArrayHelper::map(NexusInterfaceDescription::model()->getSites(), 'site','site');
//echo Html::activeDropDownList($model, 'site',
//      ArrayHelper::map(NexusInterfaceDescription::find()->all(), 'site', 'site')); 
echo Html::activeDropDownList($model, 'site',$items);
//echo CHtml::link('Refresh', array('/NexusInterfaceDescription/sitetopology?site_id='.$site_name), array('class' => 'refresh_page'));

echo Html::submitButton('ViewTopology',array('name'=>'process', 'confirm'=>'Are you sure you want to View Topology?'));
//$this->endWidget();
ActiveForm::end(); 

?>
        <!--</form>-->
</div>
