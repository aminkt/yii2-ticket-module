<?php
/** @var $model \aminkt\ticket\models\TicketMessage */
?>

<div class="form-group">
    <div class="col-md-3">
        <div> ax is here</div>
    </div>
    <div class="col-md-9">
        <?= \yii\helpers\Html::textarea('message', $model->message, ['rows' => 4, 'cols' => 68, 'readonly' => true]) ?>
    </div>
    <div>
        <?= \yii\helpers\Html::label($model->createAt) ?>
    </div>
</div>
