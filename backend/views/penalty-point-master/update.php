<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PenaltyPointMaster */

$this->title = 'Update Penalty Point Master: ' . $model->section;
$this->params['breadcrumbs'][] = ['label' => 'Penalty Point Masters', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="penalty-point-master-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
