<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\NexusInterfaceDescription */

$this->title = 'Update Nexus Interface Description: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Nexus Interface Descriptions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="nexus-interface-description-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
