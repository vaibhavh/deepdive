<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PenaltyPointsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="penalty-points-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'hostname') ?>

    <?= $form->field($model, 'loopback0') ?>

    <?= $form->field($model, 'device_type') ?>

    <?= $form->field($model, 'ios_compliance_status') ?>

    <?php // echo $form->field($model, 'ios_current_version') ?>

    <?php // echo $form->field($model, 'ios_built_version') ?>

    <?php // echo $form->field($model, 'bgp_available') ?>

    <?php // echo $form->field($model, 'isis_available') ?>

    <?php // echo $form->field($model, 'resilent_status') ?>

    <?php // echo $form->field($model, 'created_date') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
