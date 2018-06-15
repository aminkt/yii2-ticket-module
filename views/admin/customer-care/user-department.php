<?php
/** @var $this \yii\web\View */
/** @var $dataProvider \yii\data\ActiveDataProvider */
/** @var $userDepartmentForm \aminkt\ticket\models\UserDepartmentForm */

$departments = \aminkt\ticket\models\Department::findAll(['status' => \aminkt\ticket\models\Department::STATUS_ACTIVE]);
$departments = \yii\helpers\ArrayHelper::map($departments, 'id', 'name');

$this->title = 'مدیریت دپارتمان ادمین ها';
?>
<div class="customer-care-default-user-department">
    <h1>دپارتمان ها</h1>
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"> لیست ادمین ها</h3>
                </div>
                <div class="panel-body">
                    <?= \yii\grid\GridView::widget([
                        'dataProvider' => $dataProvider,
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            [
                                'attribute' => 'name',
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
                                        return \yii\helpers\Url::to(['/ticket/admin/customer-care/user-department', 'userId' => $model->id]);
                                    }
                                }
                            ]
                        ]
                    ]);
                    ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"> ویرایش دپارتمان ادمین</h3>
                </div>
                <div class="panel-body">
                    <?php $form = \yii\widgets\ActiveForm::begin(['method' => 'post']); ?>
                    <?= $form->field($userDepartmentForm, 'userName')->textInput(['readOnly' => true]); ?>
                    <?= $form->field($userDepartmentForm, 'departmentIds')->widget(\kartik\select2\Select2::class, [
                        'data' => $departments,
                        'options' => ['placeholder' => 'دپارتمان را انتخاب کنید ...', 'multiple' => true],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'tag' => true,
                            'tokenSeparators' => [',', ' '],
                        ],
                    ]); ?>
                    <?= \yii\helpers\Html::submitButton("ذخیره", ['class' => 'btn btn-primary']) ?>
                    <?php \yii\widgets\ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>




