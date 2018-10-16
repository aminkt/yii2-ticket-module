<?php

namespace aminkt\ticket\models;

use aminkt\ticket\interfaces\BaseTicketUserInterface;
use aminkt\ticket\interfaces\DepartmentInterface;
use aminkt\ticket\traits\DepartmentTrait;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "{{%ticket_departments}}".
 *
 * @author Saghar Mojdehi <saghar.mojdehi@gmail.com>
 */
class Department extends ActiveRecord implements DepartmentInterface
{
    use DepartmentTrait {
        rules as protected traitRules;
        attributeLabels as protected traitAttributeLabels;
        fields as protected traitFields;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%ticket_departments}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['create_at', 'update_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['update_at'],
                ],
                // if you're using datetime instead of UNIX timestamp:
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return $this->traitRules();
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return $this->traitAttributeLabels();
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        return $this->traitFields();
    }

    /**
     * @inheritdoc
     *
     * @param BaseTicketUserInterface $user User object.
     *
     * @return boolean
     */
    public function assign($user)
    {
        $model = new UserDepartment([
            'user_id' => $user->getId(),
            'department_id' => $this->id
        ]);

        return $model->save();
    }


    /**
     * @inheritdoc
     *
     * @param BaseTicketUserInterface $user User object.
     *
     * @return boolean
     */
    public function unAssign($user)
    {
        $model = UserDepartment::findOne([
            'user_id' => $user->getId(),
            'department_id' => $this->id
        ]);

        if ($model) {
            return $model->delete();
        }

        return true;
    }
}
