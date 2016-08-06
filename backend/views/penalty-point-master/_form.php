<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PenaltyPointMaster */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="penalty-point-master-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'section')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_type')->dropDownList(array('CSS' => 'CSS', 'AG1' => 'AG1', 'AG2' => 'AG2', 'AG3' => 'AG3', 'SAR' => 'SAR')) ?>

    <?= $form->field($model, 'subsection')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'rule')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'frequency')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'points')->textInput() ?>

    <?php // $form->field($model, 'created_at')->textInput() ?>

    <?php // $form->field($model, 'modified_at')->textInput() ?>

    <?php // $form->field($model, 'created_by')->textInput() ?>

    <?php // $form->field($model, 'modified_by')->textInput() ?>

    <?php // $form->field($model, 'is_deleted')->textInput() ?>

    <?=  $form->field($model, 'is_active')->dropDownList(array(1 => "Active", 0 => "Disabled")) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
