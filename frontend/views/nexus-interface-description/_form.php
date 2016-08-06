<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\NexusInterfaceDescription */
/* @var $form yii\widgets\ActiveForm */

//$this->registerJsFile(Yii::$app->getUrlManager()->getBaseUrl()."/js/next.js");
//$this->registerCssFile(Yii::$app->getUrlManager()->getBaseUrl() ."/css/next.css");
//$this->registerJs( "var topologyData = {
//                        nodes: " . json_encode($nodes) . ",
//                        links: " . json_encode($links) . "
//                    };");
//, \yii\web\View::POS_END,"topologyData");

//            $this->registerJsFile(Yii::$app->getUrlManager()->getBaseUrl(). "/js/nexus_interface_topology.js");
//            , \yii\web\View::POS_END);
?>
<div class="nexus-interface-description-form">

    <?php $form = ActiveForm::begin(); ?>
<?php 
           
            echo $form->field($model, 'site')
        ->dropDownList(
            $items,           // Flat array ('id'=>'label')
            ['prompt'=>'']    // options
        );
            
        
            
    ?>
   
    <div class="form-group">
        <?= Html::submitButton('View Topology', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
