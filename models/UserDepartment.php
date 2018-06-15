<?php

namespace aminkt\ticket\models;

use Yii;

/**
 * This is the model class for table "{{%ticket_user_departments}}".
 *
 * @property int $departmentId
 * @property int $userId
 * @property string $createAt
 *
 * @property Department $department
 */
class UserDepartment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%ticket_user_departments}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['departmentId', 'userId'], 'required'],
            [['departmentId', 'userId'], 'integer'],
            [['createAt'], 'safe'],
            [['departmentId', 'userId'], 'unique', 'targetAttribute' => ['departmentId', 'userId']],
            [['departmentId'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['departmentId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'departmentId' => 'Department ID',
            'userId' => 'User ID',
            'createAt' => 'Create At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartment()
    {
        return $this->hasOne(Department::className(), ['id' => 'departmentId']);
    }
}
