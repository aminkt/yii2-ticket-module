<?php

namespace aminkt\ticket\controllers\admin;

use aminkt\ticket\models\Department;
use aminkt\ticket\models\Ticket;
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
    /**
     * Tickets list
     *
     * @return string
     *
     * @author Saghar Mojdehi <saghar.mojdehi@gmail.com>
     */
    public function actionIndex()
    {
        $userId = \Yii::$app->getUser()->id;
        if (!$userId) {
            Alert::error('خطا', 'دسترسی به تیکت ها امکان پذیر نمی باشد.');
            $this->goBack();
        }

        $dataProvider = Ticket::getCustomerCareTickets($userId);

        return $this->render('index', [
            'dataProvider' => $dataProvider
        ]);

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

    /**
     * Change ticket department
     *
     * @param $id
     * @param $departmentId
     *
     * @author Saghar Mojdehi <saghar.mojdehi@gmail.com>
     */
    public function actionChangeDepartment($id, $departmentId)
    {
        $ticket = Ticket::findOne($id);
        if (!$ticket) {
            Alert::error('خطا', 'تیکت مورد نظر یافت نشد');
            $this->redirect(['ticket']);
        }
        $ticket->departmentId = $departmentId;
        if (!$ticket->save()) {
            Alert::error('خطا', 'تغییرات مورد نظر ذخیر نشد، دوباره تلاش کنید');
            $this->redirect(['ticket']);
        }
        Alert::success('عملیات با موفقیت انجام شد', '');
        $this->redirect(['ticket']);
    }

}
