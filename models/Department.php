<?php

namespace aminkt\ticket\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use aminkt\ticket\traits\DepartmentTrait;

/**
 * This is the model class for table "{{%ticket_departments}}".
 *
 * @author Saghar Mojdehi <saghar.mojdehi@gmail.com>
 */
class Department extends ActiveRecord
{
    use DepartmentTrait {
        rules as protected traitRules;
        attributeLabels as protected traitAttributeLabels;
        fields as protected traitFields;
    }

    const STATUS_ACTIVE = 1;
    const STATUS_DEACTIVE = 2;

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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['createAt', 'updateAt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updateAt'],
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
}
