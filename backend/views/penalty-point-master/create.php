<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\PenaltyPointMaster */

$this->title = 'Create Penalty Point Master';
$this->params['breadcrumbs'][] = ['label' => 'Penalty Point Masters', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="penalty-point-master-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
