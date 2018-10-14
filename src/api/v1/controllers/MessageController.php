<?php

namespace aminkt\ticket\api\v1\controllers;

use aminkt\ticket\Ticket;
use yii\rest\ActiveController;

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
}