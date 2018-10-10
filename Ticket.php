<?php

namespace aminkt\ticket;

use aminkt\ticket\interfaces\CustomerCareInterface;
use aminkt\ticket\interfaces\CustomerInterface;

/**
 * ticket module definition class
 */
class Ticket extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'aminkt\ticket\controllers\api';

    /** @var CustomerCareInterface Admin model */
    public $adminModel;

    /** @var CustomerInterface user model */
    public $userModel;

    /** @var string $deprtmentModel Department model active record class name */
    public $departmentModel = \aminkt\ticket\models\Department::class;

    /** @var string $ticketModel Ticket model active record class name */
    public $ticketModel = \aminkt\ticket\models\Ticket::class;

    /** @var string $ticketMessageModel Ticket model active record class name */
    public $ticketMessageModel = \aminkt\ticket\models\TicketMessage::class;

    /** Ticket events */
    const EVENT_BEFORE_TICKET_REPLfgmY = 'eventBeforeTicketReply';
    const EVENT_AFTER_TICKET_REPLY = 'eventAfterTicketReply';
    const EVENT_BEFORE_TICKET_CREATE = 'eventBeforeTicketCreate';
    const EVENT_AFTER_TICKET_CREATE = 'eventAfterTicketCreate';
    const EVENT_BEFORE_TICKET_ASSIGN = 'eventBeforeTicketAssign';
    const EVENT_AFTER_TICKET_ASSIGN = 'eventAfterTicketAssign';


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->modules = [
            'v1' => [
                'class' => 'aminkt\ticket\api\v1\Module',
            ],
        ];
    }

    /**
     * @inheritdoc
     *
     * @author Amin Keshavarz <amin@keshavarz.pro>
     */
    public static function getInstance() : self
    {
        if (parent::getInstance())
            return parent::getInstance();

        return \Yii::$app->getModule('ticket');
    }
}