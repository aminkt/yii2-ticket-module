<?php
/** @var $this \yii\web\View */
/** @var $model \aminkt\ticket\models\Department */

$this->title = 'ثبت دپارتمان'
?>

<div class="department-form">
    <?php $form = \yii\widgets\ActiveForm::begin(['method' => 'post']); ?>
    <?= $form->field($model, 'name'); ?>
    <?= $form->field($model, 'description')->textarea(); ?>
    <div class="form-group">
        <?= $form->field($model, 'status')->dropDownList([
            \aminkt\ticket\models\Department::STATUS_ACTIVE => 'فعال',
            \aminkt\ticket\models\Department::STATUS_DEACTIVE => 'غیر فعال',
        ])->label('وضعیت') ?>
    </div>
    <div class="form-group">
        <?= \yii\helpers\Html::submitButton($model->isNewRecord ? "ذخیره" : "ویرایش", [
            'class' => 'btn btn-primary']) ?>
        <?php \yii\widgets\ActiveForm::end(); ?>
    </div>
