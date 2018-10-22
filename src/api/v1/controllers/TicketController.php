<?php

namespace aminkt\ticket\api\v1\controllers;

use aminkt\ticket\interfaces\BaseTicketUserInterface;
use aminkt\ticket\interfaces\DepartmentInterface;
use aminkt\ticket\interfaces\TicketInterface;
use aminkt\ticket\Ticket;
use aminkt\ticket\traits\TicketTrait;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;

/**
 * Class TicketController
 * Handle tickets actions.
 *
 * @package aminkt\ticket\api\v1\controllers
 *
 * @author Amin Keshavarz <ak_1596@yahoo.com>
 */
class TicketController extends ActiveController
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->modelClass = Ticket::getInstance()->ticketModel;
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create'], $actions['update']);
        return $actions;
    }


    /**
     * Create a new ticket.
     *
     * @var String     $message        Ticket message.
     * @var String     $subject        Ticket subject.
     * @var String     $departmentId   Ticket department id.
     * @var String     $customerId     Ticket customer id.
     * @var String     $status         Ticket status.
     */
    public function actionCreate(){
        $params = \Yii::$app->request->post();

        $message = ArrayHelper::getValue($params, 'message');
        $subject = ArrayHelper::getValue($params, 'subject');
        $departmentId = ArrayHelper::getValue($params, 'departmentId');
        $customerId = ArrayHelper::getValue($params, 'customerId');
        $status = ArrayHelper::getValue($params, 'status');


        $modelName = Ticket::getInstance()->ticketModel;
        $customerModelName = Ticket::getInstance()->userModel;
        $departmentModelName = Ticket::getInstance()->departmentModel;

        /** @var BaseTicketUserInterface $user */
        $user = $customerModelName::findOne($customerId);
        if(!$user) {
            throw new NotFoundHttpException("Coustomer id is not valid!");
        }

        /** @var DepartmentInterface $department */
        $department = $departmentModelName::findOne($departmentId);
        if(!$department) {
            throw new NotFoundHttpException("Department id is not valid!");
        }

        /** @var TicketInterface $model */
        $model = $modelName::createNewTicket($subject, $user, $department);
        if(!$model){
            throw new NotFoundHttpException("Ticket not found!");
        }

        if(!$model->save()){
            return $model->getErrors();
        }

        $message = $model->sendNewMessage($message, []);

        if(!$message->save()){
            return $message->getErrors();
        }

        return $model;
    }

    /**
     * Update user ticket and send new message if filled message param.
     *
     * @param string    $id     Ticket id.
     *
     * @return TicketInterface
     *
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id){
        $params = \Yii::$app->request->post();

        $message = ArrayHelper::getValue($params, 'message');
        $subject = ArrayHelper::getValue($params, 'subject');


        $modelName = Ticket::getInstance()->ticketModel;
        $customerModelName = Ticket::getInstance()->userModel;
        $departmentModelName = Ticket::getInstance()->departmentModel;

        /** @var BaseTicketUserInterface $user */
        $user = $customerModelName::findOne($customerId);
        if(!$user) {
            throw new NotFoundHttpException("Coustomer id is not valid!");
        }

        /** @var DepartmentInterface $department */
        $department = $departmentModelName::findOne($departmentId);
        if(!$department) {
            throw new NotFoundHttpException("Department id is not valid!");
        }

        /** @var TicketInterface $model */
        $model = $modelName::findOne($id);
        if(!$model){
            throw new NotFoundHttpException("Ticket not found!");
        }

        if($message){
            $messageModel = $model->sendNewMessage($message, []);

            if(!$messageModel->save()){
                return $messageModel->getErrors();
            }
        }


        return $model;
    }

    /**
     * Open defined ticket.
     *
     * @param mixed $id Ticket id.
     *
     * @return array
     *
     * @throws NotFoundHttpException
     */
    public function actionOpenTicket($id){
        $modelName = Ticket::getInstance()->ticketModel;
        /** @var TicketInterface $model */
        $model = $modelName::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException("Ticket not found!");
        }

        $model->openTicket();

        if($model->hasErrors()){
            return $model->getErrors();
        }

        return [
            'message' => 'Ticket opened.'
        ];
    }

    /**
     * Close defined ticket.
     *
     * @param mixed $id Ticket id.
     *
     * @return array
     *
     * @throws NotFoundHttpException
     */
    public function actionCloseTicket($id){
        $modelName = Ticket::getInstance()->ticketModel;
        /** @var TicketInterface $model */
        $model = $modelName::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException("Ticket not found!");
        }

        $model->closeTicket();

        if($model->hasErrors()){
            return $model->getErrors();
        }

        return [
            'message' => 'Ticket closed.'
        ];
    }

    /**
     * Close defined ticket.
     *
     * @param mixed $id Ticket id.
     *
     * @return array
     *
     * @throws NotFoundHttpException
     */
    public function actionBlockTicket($id){
        $modelName = Ticket::getInstance()->ticketModel;
        /** @var TicketInterface $model */
        $model = $modelName::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException("Ticket not found!");
        }

        $model->setStatus($model::STATUS_BLOCKED);

        if($model->hasErrors()){
            return $model->getErrors();
        }

        return [
            'message' => 'Ticket blocked.'
        ];
    }

    /**
     * Change ticket department.
     *
     * @param mixed $id Ticket id.
     *
     * @internal mixed  $departmentId Department id.
     */
    public function actionChangeTicketDepartment($id)
    {
        $ticketModelName = Ticket::getInstance()->ticketModel;
        $departmentModelName = Ticket::getInstance()->departmentModel;
        /** @var TicketInterface $ticket */
        $ticket = $ticketModelName::findOne($id);
        if (!$ticket) {
            throw new NotFoundHttpException("Ticket dose not exist.");
        }

        $department = $departmentModelName::findOne(\Yii::$app->getRequest()->post('department_id'));
        if(!$department){
            throw new NotFoundHttpException("Department dose not exist.");
        }

        $ticket->department = $department;
        $oldDepId = $ticket->department->id;

        if($ticket->hasErrors()){
            return $ticket->getErrors();
        }

        return [
            'message' => 'Department changed.',
            'department' => $department
        ];
    }
}