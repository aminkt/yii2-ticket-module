<?php

namespace aminkt\ticket\controllers\front;
use aminkt\ticket\models\TicketCategories;
use aminkt\ticket\models\TicketMessage;
use aminkt\ticket\models\Tickets;
use aminkt\ticket\Ticket;
use aminkt\widgets\alert\Alert;
use yii\web\NotFoundHttpException;

/**
 * Class CustomerController
 *
 * @package aminkt\ticket
 */
class CustomerController extends \yii\web\Controller
{
    public function actionIndex($id = null)
    {
        if ($id) {
            $model = Tickets::findOne($id);
            if (!$model) {
                throw new NotFoundHttpException("دسته مورر نظر یافت نشد");
            }
        } else {
            $category=TicketCategories::findOne(\Yii::$app->getRequest()->post(['categoryId']));
            $subject=\Yii::$app->getRequest()->post(['subject']);
            $model = Tickets::createNewTicket($subject,null,$category);
        }



        return $this->render('index', [
            'model' => $model
        ]);
    }


}
