<?php

namespace aminkt\ticket\api\v1\controllers;

use aminkt\ticket\Ticket;
use yii\rest\ActiveController;

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
}