<?php
namespace aminkt\ticket\controllers\front;

use aminkt\ticket\models\Ticket;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use aminkt\widgets\alert\Alert;

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
     * return user tickets List
     *
     * @return array|\yii\db\ActiveRecord[]
     * @throws \Exception
     * @throws \Throwable
     *
     * @author Mohammad Parvaneh <mohammad.pvn1375@gmail.com>
     */
    public function actionList()
    {
        $customer = \Yii::$app->getUser()->getIdentity();
        $ticketList = Ticket::find()->where('customerId' == $customer->getId())->all();
        return $ticketList;
    }

    /**
     * return ticket with it's messages by ticket trackingCode
     *
     * @return Ticket
     *
     * @author Mohammad Parvaneh <mohammad.pvn1375@gmail.com>
     */
    public function actionView()
    {
        $trackingCode = \Yii::$app->getRequest()->get('trackingCode');
        $ticket = Ticket::findOne(['trackingCode' => $trackingCode]);
        return $ticket;
    }
}