<?php

namespace api\modules\ticket\models;

use Yii;

/**
 * This is the model class for table "ticket_messages".
 *
 * @property int $id
 * @property string $message
 * @property int $ticketId
 * @property string $attachments
 * @property int $customerCareId
 * @property string $updateAt
 * @property string $createAt
 *
 * @property Tickets $ticket
 */
class TicketMessages extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ticket_messages';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['message'], 'string'],
            [['ticketId', 'customerCareId'], 'integer'],
            [['attachments'], 'required'],
            [['updateAt', 'createAt'], 'safe'],
            [['attachments'], 'string', 'max' => 191],
            [['ticketId'], 'exist', 'skipOnError' => true, 'targetClass' => Tickets::className(), 'targetAttribute' => ['ticketId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'message' => 'Message',
            'ticketId' => 'Ticket ID',
            'attachments' => 'Attachments',
            'customerCareId' => 'Customer Care ID',
            'updateAt' => 'Update At',
            'createAt' => 'Create At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTicket()
    {
        return $this->hasOne(Tickets::className(), ['id' => 'ticketId']);
    }

    /**
     * @return array
     */
    public function getDirtyAttributes()
    {
        return $this->dirtyAttributes;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * @return int
     */
    public function getCustomerCareUSer()
    {

        return  ;
    }


}
