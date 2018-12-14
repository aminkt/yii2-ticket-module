<?php

namespace aminkt\ticket\models;

use yii\base\InvalidValueException;
use yii\base\Model;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

class UserDepartmentForm extends Model
{
    public $userId;
    public $userName;
    public $departmentIds = [];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userId'], 'integer'],
            [['userName'], 'string'],
            [['userId'], 'required'],
            ['departmentIds', 'each', 'rule' => ['integer']]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'userName' => 'نام',
            'departmentIds' => 'دپارتمان',
        ];
    }

    /**
     * Save user departments
     *
     * @return bool
     * @throws StaleObjectException
     * @throws \Exception
     * @throws \Throwable
     *
     * @author Saghar Mojdehi <saghar.mojdehi@gmail.com>
     */
    public function save()
    {
        $userDepartments = UserDepartment::find()->where(['userId' => $this->userId])->all();
        $userDepartmentIds = [];
        if ($userDepartments) {
            foreach ($userDepartments as $userDepartment)
                $userDepartmentIds[] = $userDepartment->departmentId;
        }
        if ($this->departmentIds == null) {
            $removes = $userDepartmentIds;
        } else {
            $newItems = array_diff($this->departmentIds, $userDepartmentIds);
            foreach ($newItems as $item) {
                $newRecord = new UserDepartment();
                $newRecord->userId = $this->userId;
                $newRecord->departmentId = $item;
                if (!$newRecord->save()) {
                    throw new InvalidValueException('User department did not save');
                }
            }
            $removes = array_diff($userDepartmentIds, $this->departmentIds);
        }
        foreach ($removes as $remove) {
            $item = UserDepartment::findOne(['departmentId' => $remove, 'userId' => $this->userId]);
            if ($item) {
                if (!$item->delete()) {
                    throw new StaleObjectException('User department did not update');
                }
            }
        }

        return true;
    }

    /**
     * Find user departments by user id
     *
     * @param $userId
     *
     * @return UserDepartmentForm
     *
     * @throws NotFoundHttpException
     * @throws \Exception
     * @throws \Throwable
     *
     * @author Saghar Mojdehi <saghar.mojdehi@gmail.com>
     */
    public function find($userId)
    {
        $userDepartments = UserDepartment::find()->where(['userId' => $userId])->all();
        if (!$userDepartments) {
            throw new NotFoundHttpException('Model not found');
        }

        $form = new self();
        $form->userId = $userId;
        $form->userName = \Yii::$app->getUser()->getIdentity()->getName();
        foreach ($userDepartments as $userDepartment) {
            $this->departmentIds[] = $userDepartment->departmentId;
        }

        return $form;
    }

}