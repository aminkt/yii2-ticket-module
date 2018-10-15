<?php

namespace aminkt\ticket\api\v1\controllers;

use aminkt\ticket\interfaces\TicketInterface;
use aminkt\ticket\Ticket;
use common\models\TicketMessage;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;

/**
 * Class MessageController
 * Handle message actions.
 *
 * @package aminkt\ticket\api\v1\controllers
 *
 * @author Amin Keshavarz <ak_1596@yahoo.com>
 */
class MessageController extends ActiveController
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->modelClass = Ticket::getInstance()->ticketMessageModel;
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['delete'], $actions['update'], $actions['create'], $actions['index']);
        return $actions;
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if(\Yii::$app->request->isOptions){
            \Yii::$app->response->setStatusCode(200);
            die();
        }
        return parent::beforeAction($action);
    }

    /**
     * Prepare a data provider for listing messages.
     *
     * @param string    $id     Ticket id.
     *
     * @return ActiveDataProvider   Return founded messages.
     *
     * @throws NotFoundHttpException    When ticket id is not valid.
     */
    public function actionIndex($ticketId){
        $ticketModel = Ticket::getInstance()->ticketModel;
        $ticket = $ticketModel::findOne($ticketId);
        if(!$ticket){
            throw new NotFoundHttpException("Ticket not found!");
        }

        /** @var TicketMessage $messagesModel */
        $messagesModel = Ticket::getInstance()->ticketMessageModel;

        $messages = $messagesModel::find()->where([
            'ticketId' => $ticketId,
        ])->orderBy(['id' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $messages
        ]);

        return $dataProvider;
    }


    /**
     * Send new message to ticket.
     *
     * @param   string      $id     Ticket id.
     *
     * @return \aminkt\ticket\interfaces\MessageInterface|array     If validation error exist return errors as array or return message object.
     *
     * @throws NotFoundHttpException    Return not found exception if ticket not found.
     */
    public function actionCreate($ticketId)
    {
        $ticketModel = Ticket::getInstance()->ticketModel;
        /** @var TicketInterface $ticket */
        $ticket = $ticketModel::findOne($ticketId);
        if (!$ticket) {
            throw new NotFoundHttpException("Ticket not found.");
        }

        $values = \Yii::$app->getRequest()->post();
        $message = ArrayHelper::getValue($values, 'message');
        $attachments = ArrayHelper::getValue($values, 'atachments', []);
        $customerCareId = ArrayHelper::getValue($post, 'customerCareId', null);

        $message = $ticket->sendNewMessage($message, $attachments, $customerCareId);


        if(!$message->save()){
            return $message->getErrors();
        }

        return $message;
    }
}