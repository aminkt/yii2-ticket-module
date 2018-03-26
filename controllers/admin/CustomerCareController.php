<?php

namespace aminkt\ticket\controllers\admin;

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
