<?php

use yii\helpers\Html;
$this->registerJsFile(Yii::$app->getUrlManager()->getBaseUrl()."/js/next.js");
$this->registerCssFile(Yii::$app->getUrlManager()->getBaseUrl() ."/css/next.css");

$this->registerJs("var topologyData = {
                        nodes: " . json_encode($nodes) . ",
                        links: " . json_encode($links) . "
                    };
                     var nodes=" . json_encode($nodes) . "; "
        . " var links=" . json_encode($links) . ";", \yii\web\View::POS_BEGIN);

$this->registerJsFile(Yii::$app->getUrlManager()->getBaseUrl(). "/js/setData.js");
//$this->registerJs(Yii::$app->getUrlManager()->getBaseUrl(). "/js/nexus_interface_topology.js", \yii\web\View::POS_LOAD);



$this->title = 'Nexus Topology';
//$this->params['breadcrumbs'][] = ['label' => 'Nexus Interface Descriptions', 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="nexus-interface-description-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
         'items'=>$items,
        'nodes'=>$nodes,
         'links'=>$links
    ]) ?>
</div>
<div id="clocking_delta_topology">
</div>
