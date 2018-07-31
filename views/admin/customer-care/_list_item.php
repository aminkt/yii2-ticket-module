<?php
/** @var $model \aminkt\ticket\models\TicketMessage */
?>

<div class="form-group">
    <div class="row">
        <div class="col-md-2" style="padding-top: 15px;">
            <div><img src="https://pixelmator-pro.s3.amazonaws.com/community/avatar_empty@2x.png"
                      style="width: 60px;height: 60px;">
            </div>
        <div>
            <?php
            $ticket = \aminkt\ticket\models\Ticket::findOne($model->ticketId);
            $customerCare = $model->getIsCustomerCareReply();
            if ($customerCare)
                echo "<h5>پشتیبانی</h5>";
            else
                echo "<h5>" . $ticket->userName . "</h5>";

            ?>
        </div>
            <div style="font-size: 10px">
                <?= Yii::$app->getFormatter()->asDatetime($model->createAt)
                ?>
            </div>
        </div>
        <div class="col-md-8" style=" min-height: 20%;padding: 15px;justify-content: flex-end">
            <?php echo $model->message ?>
        </div>
        <div class="col-md-2 pull-right" style="direction: ltr;text-overflow: ellipsis;" id="attachment">
            <h5>پیوست ها</h5>
            <?php if ($model->attachments): ?>

                <?php foreach ($model->getAttachments() as $attachment): ?>
                    <nobr>
                        <?= \yii\helpers\Html::a(\yii\helpers\StringHelper::truncate($attachment->getFileName(), 15), $attachment->getUrl()) ?>
                    </nobr>
                <?php endforeach; ?>

            <?php endif; ?>

        </div>

    </div>


</div>

