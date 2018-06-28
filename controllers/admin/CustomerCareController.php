<?php

namespace aminkt\ticket\controllers\admin;

use aminkt\ticket\models\Department;
use aminkt\widgets\alert\Alert;
use yii\data\ActiveDataProvider;
use yii\web\Controller;

/**
 * Class CustomerCareController
 *
 * @package aminkt\ticket
 */
class CustomerCareController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}
