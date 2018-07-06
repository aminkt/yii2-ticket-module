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

    public $defaultRoute = 'customer-care/index';

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
    public static function getInstance() : self
    {
        if (parent::getInstance())
            return parent::getInstance();

        return \Yii::$app->getModule('ticket');
    }
}