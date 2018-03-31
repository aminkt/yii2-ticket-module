<?php

namespace aminkt\ticket\controllers\admin;
use aminkt\ticket\Ticket;

/**
 * Class CustomerCareController
 *
 * @package aminkt\ticket
 */
class CustomerCareController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }


    

}
