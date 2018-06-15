<?php

namespace aminkt\ticket\controllers\admin;

use aminkt\ticket\models\Department;
use aminkt\ticket\models\Ticket;
use aminkt\ticket\models\TicketMessage;
use aminkt\ticket\models\UserDepartment;
use aminkt\ticket\models\UserDepartmentForm;
use aminkt\widgets\alert\Alert;
use Imagine\Exception\RuntimeException;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Class CustomerCareController
 *
 * @package aminkt\ticket
 */
class CustomerCareController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'department', 'change-department', 'user-department', 'ticket'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

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
     * @author Saghar Mojdehi <saghar.mojdehi@gmail.com>
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
     * View ticket details and reply
     *
     * @param $id
     *
     * @return string
     * @author Saghar Mojdehi <saghar.mojdehi@gmail.com>
     */
    public function actionTicket($id)
    {
        $ticket = Ticket::findOne($id);
        if (!$ticket) {
            Alert::error('خطا', 'تیکت مورد نظر یافت نشد');
            $this->redirect(['index']);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => TicketMessage::find()->where(['ticketId' => $ticket->id])
        ]);

        $model = new TicketMessage();

        if (\Yii::$app->request->isPost) {

            if ($model->load(\Yii::$app->getRequest()->post())) {
                try {
                    $ticket->sendNewMessage($model->getMessage(), $model->attachments, \Yii::$app->getUser()->getIdentity());
                    try {
                        $ticket->setStatus($ticket::STATUS_REPLIED);
                    } catch (RuntimeException $e) {
                        Alert::error('خطا', 'وضعیت تیکت ویرایش نشد');
                        return $this->redirect(['ticket', 'id' => $id]);
                    }
                } catch (\Exception $e) {
                    Alert::error('خطا', 'پاسخ تیکت ارسال نشد، دوباره تلاش کنید');
                    return $this->redirect(['ticket', 'id' => $id]);
                } catch (\Throwable $e) {
                    Alert::error('خطا', 'پاسخ تیکت ارسال نشد، دوباره تلاش کنید');
                    return $this->redirect(['ticket', 'id' => $id]);
                }
            }
            if ($ticket->load(\Yii::$app->getRequest()->post())) {
                if (!$ticket->save()) {
                    Alert::error('خطا', 'تغییرات ذخیره نشد');
                    return $this->redirect(['ticket', 'id' => $id]);
                }
            }

            Alert::success('عملیات با موفقیت انجام شد', '');
            return $this->redirect(['ticket', 'id' => $id]);
        }


        return $this->render('ticket', [
            'ticket' => $ticket,
            'dataProvider' => $dataProvider,
            'model' => $model
        ]);

    }

    /**
     * Closing ticket
     *
     * @param $id
     *
     * @author Saghar Mojdehi <saghar.mojdehi@gmail.com>
     */
    public function actionCloseTicket($id)
    {
        $ticket = Ticket::findOne($id);
        if (!$ticket) {
            Alert::error('خطا', 'تیکت مورد نظر یافت نشد');
            $this->redirect(['ticket']);
        }
        if (!$ticket->closeTicket()) {
            Alert::error('خطا', 'تغییرات مورد نظر ذخیر نشد، دوباره تلاش کنید');
            $this->redirect(['ticket']);
        }
        Alert::success('عملیات با موفقیت انجام شد', '');
        $this->redirect(['ticket']);
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

    /**
     * Assign departments to users
     *
     * @param null $userId
     *
     * @return string
     * @throws StaleObjectException
     * @throws \Exception
     * @throws \Throwable
     *
     * @author Saghar Mojdehi <saghar.mojdehi@gmail.com>
     */
    public function actionUserDepartment($userId = null)
    {
        $adminModel = \aminkt\ticket\Ticket::getInstance()->adminModel;
        $admins = $adminModel::find();
        if (!$admins) {
            Alert::error('خطا', 'ادمین یافت نشد');
            $this->goBack();
        }

        $userDepartmentFrom = new UserDepartmentForm();

        if ($userDepartmentFrom->load(\Yii::$app->getRequest()->post())) {
            $userDepartmentFrom->userId = $userId;
            if (!$userDepartmentFrom->save()) {
                Alert::error('خطا', 'عملیات مورد نظر انجام نشد');
            } else {
                Alert::success('عملیات با موفقیت انجام شد', '');
            }

        } elseif ($userId) {
            $userDepartments = UserDepartment::find()->where(['userId' => $userId])->all();
            if ($userDepartments) {
                foreach ($userDepartments as $userDepartment) {
                    $userDepartmentFrom->departmentIds[] = $userDepartment->departmentId;
                }
            }
            $userDepartmentFrom->userName = $adminModel::findOne($userId)->getName();
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $admins
        ]);

        return $this->render('user-department', [
            'dataProvider' => $dataProvider,
            'userDepartmentForm' => $userDepartmentFrom
        ]);
    }

}
