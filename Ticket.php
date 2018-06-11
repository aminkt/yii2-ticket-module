<?php

namespace aminkt\ticket;

/**
 * ticket module definition class
 */
class Ticket extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'aminkt\ticket\controllers';

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
