<?php

namespace aminkt\ticket\controllers\front;

use aminkt\ticket\models\CustomerTempModel;
use aminkt\ticket\models\Department;
use aminkt\ticket\models\Ticket;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use aminkt\widgets\alert\Alert;
use aminkt\uploadManager\components\Upload;

/**
 * Class CustomerController
 *
 * @package aminkt\ticket
 *
 * @author Mohammad Parvaneh <mohammad.pvn1375@gmail.com>
 */
class CustomerController extends \yii\rest\Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'optional' => ['close', 'create', 'list', 'index', 'view']
        ];
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'only' => ['index', 'create'],
            'rules' => [
                [
                    'actions' => ['index', 'close', 'view'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
                [
                    'actions' => ['create', 'close', 'view'],
                    'allow' => true,
                ]
            ],
        ];
        return $behaviors;
    }

    /**
     * create new ticket
     *
     * @return \aminkt\ticket\models\TicketMessage
     * @throws \Exception
     * @throws \Throwable
     *
     * @author  Mohammad Parvaneh <mohammad.pvn1375@gmail.com>
     */
    public function actionCreate()
    {
        $ticketId = \Yii::$app->getRequest()->post('ticketId');
        if ($ticketId) {
            $model = Ticket::findOne($ticketId);
            if (!$model) {
                Alert::error('تیکت پیدا نشد', '');
            }
        } else {
            $customer = \Yii::$app->getUser()->getIdentity();
            if (!$customer) {
                $name = \Yii::$app->getRequest()->post('name');
                $mobile = \Yii::$app->getRequest()->post('mobile');
                $email = \Yii::$app->getRequest()->post('email');
                $customer = new CustomerTempModel($name, $email, $mobile);
            }
            $department = Department::findOne(\Yii::$app->getRequest()->post('departmentId'));
            $subject = \Yii::$app->getRequest()->post('subject');
            $model = Ticket::createNewTicket($subject, $customer, $department);
        }
        $message = \Yii::$app->getRequest()->post('message');
        $attachment = \Yii::$app->getRequest()->post('attachment');
        if (is_numeric($attachment)) {
            $attachment = (string)$attachment;
        } else {
            $file = Upload::directUpload('attachment');
            $attachment = (string)$file->id;
        }
        $ticketMessage = $model->sendNewMessage($message, $attachment);
        return $ticketMessage;
    }

    /**
     * close ticket
     *
     * @return bool
     *
     * @author Mohammad Parvaneh <mohammad.pvn1375@gmail.com>
     */
    public function actionClose()
    {
        $model = Ticket::findOne(\Yii::$app->getRequest()->get('ticketId'));
        if (!$model) {
            Alert::error('تیکت پیدا نشد', '');
            return false;
        } else {
            if ($model->closeTicket()) {
                Alert::success('تیکت با موفقیت بسته شد', 'اسم تیکت جدید : ' . $model->subject);
                return true;
            } else {
                Alert::success('تیکت بسته نشد', $model->subject);
                return false;
            }

        }

    }

}
