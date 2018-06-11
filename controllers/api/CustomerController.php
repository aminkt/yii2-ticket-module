<?php

namespace aminkt\ticket\controllers\front;

/**
 * Class CustomerController
 *
 * @package aminkt\ticket
 */
class CustomerController extends \yii\web\Controller
{
    public function actionIndex()
    {

        return $this->render('index');
    }

}
