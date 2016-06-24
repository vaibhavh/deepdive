<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\PenaltyPoints */

$this->title = 'Create Penalty Points';
$this->params['breadcrumbs'][] = ['label' => 'Penalty Points', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="penalty-points-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
