<?php
/** @var $model \aminkt\ticket\models\TicketMessage */
?>

<div class="form-group">
    <div class="col col-md-3">
        <div> photo here</div>
    </div>
    <div class="col col-md-9">
        <?= \yii\helpers\Html::textarea('message', $model->message, ['rows' => 4, 'cols' => 68, 'readonly' => true]) ?>
    </div>
    <div class="col col-md-12">
        <?php if ($model->attachments): ?>
            <?php foreach ($model->getAttachments() as $attachment): ?>
                <?= \yii\helpers\Html::a($attachment->name, $attachment->getUrl()) ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div class="col col-md-12">
        <?= \yii\helpers\Html::label($model->createAt) ?>
    </div>
</div>
