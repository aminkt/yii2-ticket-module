<?php
namespace aminkt\ticket\controllers\front;
use aminkt\ticket\models\CustomerTempModel;
use aminkt\ticket\models\TicketCategory;
use aminkt\ticket\models\Ticket;
use yii\data\ActiveDataProvider;
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
     * return ticket with it's messages by ticket trackingCode
     *
     * @return null|static
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
