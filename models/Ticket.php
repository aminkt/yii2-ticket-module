<?php

namespace aminkt\ticket\models;

use aminkt\ticket\interfaces\CustomerCareInterface;
use aminkt\ticket\interfaces\CustomerInterface;
use aminkt\ticket\interfaces\TicketInterface;
use aminkt\ticket\traits\TicketTrait;
use aminkt\widgets\alert\Alert;
use Imagine\Exception\RuntimeException;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "tickets".
 *
 * @package aminkt\ticket
 */
class Ticket extends ActiveRecord implements TicketInterface
{
    use TicketTrait {
        rules as protected traitRules;
        attributeLabels as protected traitAttributeLabels;
    }

    private $customerModel;
    private $customerCareModel;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
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
     * @inheritdoc
     */
    public static function tableName()
    {
        return "{{%tickets}}";
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return $this->traitRules();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return $this->traitAttributeLabels();
    }
}