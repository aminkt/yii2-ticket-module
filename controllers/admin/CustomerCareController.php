<?php

namespace aminkt\ticket\controllers\admin;

use aminkt\ticket\models\Department;
use aminkt\widgets\alert\Alert;
use yii\data\ActiveDataProvider;
use yii\web\Controller;

/**
 * Class CustomerCareController
 *
 * @package aminkt\ticket
 */
class CustomerCareController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Create and update department
     *
     * @param null $id
     *
     * @return string|\yii\web\Response
     *
     * @author Saghar Mojdehi <saghar.mojdehi@gmail.ocm>
     */
    public function actionDepartment($id = null)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Department::find()
        ]);

        if ($id) {
            $model = Department::findOne($id);
            if (!$model) {
                Alert::error('خطا در انجام عملیات', "دپارتمان مورد نظر یافت نشد");
                return $this->redirect(['department']);
            }
        } else {
            $model = new Department();
        }

        if (\Yii::$app->getRequest()->isPost and $model->load(\Yii::$app->request->post())) {
            if (!$model->save()) {
                Alert::error('خطا در انجام عملیات', 'دپارتمان ذخیره نشد. دوباره تلاش کنید.');
            }
            Alert::success('عملیات با موفقیت انجام شد', '');
            return $this->redirect(['department']);
        }

        return $this->render('department', [
            'dataProvider' => $dataProvider,
            'model' => $model
        ]);
    }

}
