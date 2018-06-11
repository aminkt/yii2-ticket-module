<?php
/** @var $this \yii\web\View */
/** @var $dataProvider \yii\data\ActiveDataProvider */
/** @var $department */

$this->title = 'مدیریت دپارتمان';
?>
<div class="department-default-department">
    <h1><?= $this->title ?></h1>
    <div class="row">
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"> لیست دپارتمان ها</h3>
                </div>
                <div class="panel-body">
                    <?= \yii\grid\GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => "",
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            [
                                'attribute' => 'name'
                            ],
                            [
                                'attribute' => 'description'
                            ],
                            [
                                'label' => 'وضعیت',
                                'attribute' => 'status',
                                'value' => function ($model) {
                                    return $model->getStatusLabel();
                                },
                                'filter' => [
                                    \aminkt\ticket\models\Department::STATUS_ACTIVE => 'فعال',
                                    \aminkt\ticket\models\Department::STATUS_DEACTIVE => 'غیر فعال',
                                ],
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{update}',
                                'buttons' => [
                                    'update' => function ($url, $model, $key) {
                                        return \yii\helpers\Html::a('<i class="icon-pencil"></i>', $url, ['title' => 'ویرایش']);
                                    },
                                ],
                                'urlCreator' => function ($action, $model, $key, $index) {
                                    if ($action === 'update') {
                                        return \yii\helpers\Url::to(['/ticket/customer-care/department', 'id' => $model->id]);
                                    }
                                }
                            ],
                        ],
                    ]);
                    ?>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"> <?= $model->isNewRecord ? 'ایجاد ' : ' ویرایش '; ?> دپارتمان</h3>
                </div>
                <div class="panel-body">
                    <?= $this->render('_form', [
                        'model' => $model
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
