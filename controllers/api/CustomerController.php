<?php
namespace aminkt\ticket\controllers\api;

use aminkt\ticket\interfaces\CustomerInterface;
use aminkt\ticket\models\Department;
use aminkt\ticket\models\Ticket;
use frontend\models\User;
use yii\filters\auth\HttpBearerAuth;
use aminkt\widgets\alert\Alert;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * Class CustomerController
 *
 * @package aminkt\ticket
 *
 * @author Mohammad Parvaneh <mohammad.pvn1375@gmail.com>
 */
class CustomerController extends \yii\rest\ActiveController
{
    public $modelClass = Ticket::class;

    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'data',
    ];


    public function behaviors()
    {
        $behaviors = parent::behaviors();

        unset($behaviors['authenticator']);

        // add CORS filter
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
            'cors' => [
                // restrict access to
                'Origin' => ['*'],
                'methods' => ['*']
            ],
            'actions' => [
                'login' => [
                    'Access-Control-Allow-Credentials' => true,
                ],
                'login-authed' => [
                    'Access-Control-Allow-Credentials' => true,
                ],
                'revoke-token' => [
                    'Access-Control-Allow-Credentials' => true,
                ]
            ]
        ];

        // re-add authentication filter
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => ['options'],
            'optional' => ['*']
        ];
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();

        // disable the default actions
        unset($actions['delete'], $actions['create'], $actions['update'], $actions['index'], $actions['view']);

        return $actions;
    }


    /**
     * Create new ticket
     *
     * @internal  string        $subject        Post request. Subject of ticket.
     * @internal  int           $department     Post request. Department id.
     * @internal  string|null    $name          Post request. Name of user
     * @internal  string|null    $email         Post request. Email of user
     * @internal  string|null    $mobile        Post request. Mobile of user
     *
     * @return array
     *
     * @throws ServerErrorHttpException
     *
     * @author Amin Keshavarz <ak_1596@yahoo.com>
     */
    public function actionCreate(){
        $subject = \Yii::$app->getRequest()->post('subject');
        $department = \Yii::$app->getRequest()->post('department');
        $department = Department::findOne($department);
        if(!$department){
            throw new NotFoundHttpException("Department ID is not valid");
        }
        if(\Yii::$app->getUser()->getId()){
            $user = \Yii::$app->getUser()->getIdentity();
        }else{
            $user = new class implements CustomerInterface{
                /**
                 * Return User Id.
                 *
                 * @return integer
                 */
                function getId(){
                    return null;
                }

                /**
                 * Return user full name.
                 *
                 * @return string
                 */
                function getName(){
                    return \Yii::$app->getRequest()->post('name');
                }

                /**
                 * Return user email.
                 * @return string|null
                 */
                function getEmail(){
                    return \Yii::$app->getRequest()->post('email');
                }

                /**
                 * Return user mobile.
                 *
                 * @return string|null
                 */
                function getMobile(){
                    return \Yii::$app->getRequest()->post('mobile');
                }
            };
        }

        $ticket = Ticket::createNewTicket($subject, $user, $department);
        if($ticket->save()){
            return $ticket;
        }else{
            \Yii::$app->getResponse()->setStatusCode(400);
            return [
                'message' => 'Validation error',
                'errors' => $ticket->getErrors()
            ];
        }
    }

    /**
     * Return user tickets List
     *
     * @param string|null $trackingCode
     *
     * @return array|\yii\db\ActiveRecord[]
     *
     * @throws \Exception
     * @throws \Throwable
     *
     * @author Mohammad Parvaneh <mohammad.pvn1375@gmail.com>
     * @author Amin Keshavarz <ak_1596@yahoo.com>
     */
    public function actionIndex($trackingCode=null)
    {
        $customer = \Yii::$app->getUser()->getIdentity();
        $ticketList = Ticket::find()->where(['customerId' => $customer->getId()])->all();
        return $ticketList;
    }

    /**
     * return ticket with it's messages by ticket trackingCode
     *
     * @return Ticket
     *
     * @author Mohammad Parvaneh <mohammad.pvn1375@gmail.com>
     */
    public function actionView($trackingCode)
    {
        $ticket = Ticket::findOne(['trackingCode' => $trackingCode]);
        return [
            'info' => $ticket,
            'messages' => $ticket->ticketMessages
        ];
    }

    /**
     * Add message to ticket.
     *
     * @param string    $message
     * @param string    $trackingCode
     */
    public function actionAddMessage($trackingCode){
        $ticket = Ticket::findOne(['trackingCode' => $trackingCode]);
        if(!$ticket){
            throw new NotFoundHttpException("Ticket not found.");
        }

        $message = \Yii::$app->getRequest()->post('message');
        $attachments = \Yii::$app->getRequest()->post('attachments', []);

        $message = $ticket->sendNewMessage($message, $attachments);

        if($message->save()){
            return $message;
        }else{
            return [
                'message' => "Validation error",
                'errors' => $message->getErrors()
            ];
        }
    }
}
