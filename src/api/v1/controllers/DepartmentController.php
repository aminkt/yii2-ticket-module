<?php

namespace aminkt\ticket\api\v1\controllers;

use aminkt\ticket\interfaces\BaseTicketUserInterface;
use aminkt\ticket\Ticket;
use yii\rest\ActiveController;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * Class DepartmentController
 * Handle departments actions.
 *
 * @package aminkt\ticket\api\v1\controllers
 *
 * @author Amin Keshavarz <ak_1596@yahoo.com>
 */
class DepartmentController extends ActiveController
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->modelClass = Ticket::getInstance()->departmentModel;
        parent::init();
    }

    public function beforeAction($action)
    {
        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }

    /**
     * Assign user to defined department.
     *
     * @param $id
     *
     * @throws NotFoundHttpException
     */
    public function actionAssignment($id)
    {
        $department = ($this->modelClass)::findOne($id);
        $this->checkAccess('assignment', $department);

        if (!$department) {
            throw new NotFoundHttpException("Department dose not exist.");
        }

        $user = \Yii::$app->getRequest()->post('user');

        if (!$user) {
            $userId = \Yii::$app->getRequest()->post('user_id');
        } else {
            if (!isset($user['id'])) {
                throw new BadRequestHttpException("User object is not valid.");
            }

            $userId = $user['id'];
        }

        if (!$userId) {
            throw new BadRequestHttpException("User object or user id should send as post request.");
        }

        $customerCareModel = Ticket::getInstance()->adminModel;
        $user = $customerCareModel::findOne($userId);

        if (\Yii::$app->request->isDelete) {
            if (!$department->unAssign($user)) {
                throw new ServerErrorHttpException("User un assignment become failed.");
            }
        } else {
            if (!$department->assign($user)) {
                throw new ServerErrorHttpException("User assignment become failed.");
            };
        }

        return $department;
    }

    /**
     * @inheritdoc
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        parent::checkAccess($action, $model, $params);

        if ($action == 'index' or $action == 'view') {
            return;
        }

        if (!\Yii::$app->getUser() or \Yii::$app->getUser()->getIdentity()->getType() !== BaseTicketUserInterface::TYPE_CUSTOMER_CARE) {
            if ($action == 'assignment') {
                throw new ForbiddenHttpException("Just customer cares can assign users to departments.");
            }

            throw new ForbiddenHttpException("Just customer cares can create or update departments");
        }
    }
}