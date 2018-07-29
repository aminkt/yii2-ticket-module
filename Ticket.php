<?php

namespace api\modules\ticket;

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
    public $controllerNamespace = 'aminkt\ticket\controllers';

    /** @var CustomerCareInterface Admin model */
    public $adminModel;

    /** @var CustomerInterface user model */
    public $userModel;

    /** event for send message */
    const EVENT_ON_REPLY = 'eventTicketReply';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     *
     * @author Amin Keshavarz <amin@keshavarz.pro>
     */
    public static function getInstance()
    {
        if (parent::getInstance())
            return parent::getInstance();

        return \Yii::$app->getModule('ticket');
    }
}