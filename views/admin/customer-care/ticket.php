<?php
/** @var $this \yii\web\View */
/** @var $ticket \aminkt\ticket\models\Ticket */
/** @var $dataProvider \yii\data\ActiveDataProvider */
/** @var $model \aminkt\ticket\models\TicketMessage */
$this->title = 'جزییات تیکت';
$departments = \aminkt\ticket\models\Department::findAll(['status' => \aminkt\ticket\models\Department::STATUS_ACTIVE]);
$departments = \yii\helpers\ArrayHelper::map($departments, 'id', 'name');
?>
<div class="ticket-details">
    <div class="container-fluid">
        <div class="col col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"> پاسخ</h3>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <?php $form = yii\widgets\ActiveForm::begin(['method' => 'post']); ?>
                        <?= $form->field($model, 'message')->textarea(['rows' => 4, 'maxlength' => true]); ?>
                        <div class="form-group attachment-input">
                            <?php echo \aminkt\uploadManager\components\UploadManager::widget([
                                'id' => 'ticket-attachment',
                                'model' => $model,
                                'attribute' => 'attachments',
                                'mediaType' => \aminkt\uploadManager\models\UploadmanagerFiles::FILE_TYPE_IMAGE,
                                'multiple' => true,
                                'titleTxt' => 'تصویر را وارد کنید.',
                                'helpBlockEnable' => false,
//                                'showImageContainer' => '#attachment',
//                                'showImagesTemplate' => "<img src='{url}' class='img-responsive'>",
                                'btnTxt' => 'جایگذاری پیوست'
                            ]);
                            ?>
                            <!--                            <div id="attachment"></div>-->
                        </div>
                        <div class="form-group ">
                            <?= yii\helpers\Html::submitButton('ارسال', ['class' => 'btn btn-success save-post-btn', 'style' => 'float: left;']); ?>
                            <?php \yii\widgets\ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"> جزییات</h3>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <?php $form = yii\widgets\ActiveForm::begin(['method' => 'post']); ?>
                        <?= $form->field($ticket, 'subject')->textInput(['readonly' => true]) ?>
                        <?= $form->field($ticket, 'name')->textInput(['readonly' => true]) ?>
                        <?= $form->field($ticket, 'userName')->textInput(['readonly' => true]) ?>
                        <?= $form->field($ticket, 'mobile')->textInput(['readonly' => true]) ?>
                        <?= $form->field($ticket, 'email')->textInput(['readonly' => true]) ?>
                        <?= $form->field($ticket, 'createAt')->textInput(['readonly' => true]) ?>
                        <?= $form->field($ticket, 'updateAt')->textInput(['readonly' => true]) ?>
                        <?= $form->field($ticket, 'departmentId')->widget(\kartik\select2\Select2::class, [
                            'data' => $departments,
                            'options' => ['placeholder' => 'دپارتمان را انتخاب کنید ...'],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ],
                        ]); ?>
                        <?= $form->field($ticket, 'status')->dropDownList([
                            \aminkt\ticket\models\Ticket::STATUS_NOT_REPLIED => 'بی پاسخ',
                            \aminkt\ticket\models\Ticket::STATUS_REPLIED => 'پاسخ داده شده',
                            \aminkt\ticket\models\Ticket::STATUS_CLOSED => 'بسته شده',
                            \aminkt\ticket\models\Ticket::STATUS_BLOCKED => 'بن شده',
                        ])->label('وضعیت') ?>
                        <?= yii\helpers\Html::submitButton('ذخیره', ['class' => 'btn btn-success save-post-btn', 'style' => 'float: left;']) ?>
                        <?php \yii\widgets\ActiveForm::end(); ?>
                    </div>
                </div>
            </div>
        </div>

        <!--    </div>-->
        <!--    <div class="container-fluid">-->
        <div class="col col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"> پیام ها</h3>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <div class="col-md-8"
                        <?= \yii\widgets\ListView::widget([
                            'dataProvider' => $dataProvider,
                            'itemOptions' => ['class' => 'item'],
                            'itemView' => function ($model, $key, $index, $widget) {
                                return $this->render('_list_item', ['model' => $model]);
                            }
                        ]);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


