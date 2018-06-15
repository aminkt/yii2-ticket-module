<?php
/** @var $this \yii\web\View */
/** @var $dataProvider \yii\data\ActiveDataProvider */

$this->title = 'مدیریت تیکت ها';
?>
<div class="ticket-default-index">
    <h1><?= $this->title ?></h1>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">لیست تیکت ها</h3>
            </div>
            <div class="panel-body">
                <?= \yii\grid\GridView::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'label' => 'نام مشتری',
                            'attribute' => 'customerId',
                            'value' => function ($model) {
                                return $model->getUserName();
                            }
                        ],
                        [
                            'label' => 'نام',
                            'attribute' => 'name'
                        ],
                        [
                            'label' => 'موضوع',
                            'attribute' => 'subject'
                        ],
                        [
                            'label' => 'دپارتمان',
                            'attribute' => 'departmentId',
                            'value' => function ($model) {
                                return $model->department->name;
                            },
                        ],
                        [
                            'label' => 'وضعیت',
                            'attribute' => 'status',
                            'value' => function ($model) {
                                return $model->getStatusLabel();
                            },
                        ],
                        [
                            'label' => 'تاریخ ایجاد',
                            'attribute' => 'createAt',
                            'format' => 'dateTime',
                        ],
                        [
                            'label' => 'تاریخ ویرایش',
                            'attribute' => 'updateAt',
                            'format' => 'dateTime',
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{view}',
                            'buttons' => [
                                'view' => function ($url, $model, $key) {
                                    return \yii\helpers\Html::a('<i class="icon-eye"></i>', $url, ['title' => 'نمایش']);
                                },
                            ],
                            'urlCreator' => function ($action, $model, $key, $index) {
                                if ($action === 'view') {
                                    return \yii\helpers\Url::to(['/ticket/customer-care/ticket', 'id' => $model->id]);
                                }
                            }
                        ],
                    ],
                ]);
                ?>
            </div>
        </div>
    </div>
</div>
