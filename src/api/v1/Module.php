<?php

namespace aminkt\ticket\api\v1;

/**
 * Class Module
 * API version 1
 *
 * @package aminkt\ticket\api\v1
 *
 * @author  Amin Keshavarz <amin@keshavarz.pro>
 */
class Module extends \yii\base\Module
{
    public $controllerNamespace = 'aminkt\ticket\api\v1\controllers';

    public function init()
    {
        parent::init();
        \Yii::$app->user->enableSession = false;
    }

    /**
     * @return self
     *
     * @author Amin Keshavarz <amin@keshavarz.pro>
     */
    public static function getInstance()
    {
        if (parent::getInstance())
            return parent::getInstance();

        return \Yii::$app->getModule('ticket')->getModule('v1');
    }
}