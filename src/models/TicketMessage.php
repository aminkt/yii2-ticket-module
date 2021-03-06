<?php

namespace aminkt\ticket\models;

use aminkt\ticket\interfaces\CustomerCareInterface;
use aminkt\ticket\interfaces\CustomerInterface;
use aminkt\ticket\interfaces\MessageInterface;
use aminkt\ticket\traits\TicketMessageTrait;
use aminkt\uploadManager\UploadManager;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "ticket_messages".
 */
class TicketMessage extends ActiveRecord implements MessageInterface
{
    use TicketMessageTrait {
        rules as traitRules;
        attributeLabels as traitAttributeLabels;
        fields as traitFields;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
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
     * @inheritdoc
     */
    public static function tableName()
    {
        return "{{%ticket_messages}}";
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


    public function fields()
    {
        return $this->traitFields();
    }
}