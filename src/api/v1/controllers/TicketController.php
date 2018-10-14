<?php

namespace aminkt\ticket\api\v1\controllers;

use aminkt\ticket\interfaces\TicketInterface;
use aminkt\ticket\Ticket;
use aminkt\ticket\traits\TicketTrait;
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

        $department = $departmentModelName::findOne(\Yii::$app->getRequest()->post('departmentId'));
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