<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\datepicker\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\models\PenaltyPoints */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="penalty-points-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'hostname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'loopback0')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ios_compliance_status')->textInput() ?>

    <?= $form->field($model, 'ios_current_version')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ios_built_version')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bgp_available')->textInput() ?>

    <?= $form->field($model, 'isis_available')->textInput() ?>

    <?= $form->field($model, 'resilent_status')->textInput() ?>

    <?php $form->field($model, 'created_date')->textInput() ?>
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
