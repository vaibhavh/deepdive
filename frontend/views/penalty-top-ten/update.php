<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PenaltyPoints */

$this->title = 'Update Penalty Points: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Penalty Points', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="penalty-points-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
