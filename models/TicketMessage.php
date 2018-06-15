<?php

namespace aminkt\ticket\models;

use aminkt\ticket\interfaces\CustomerCareInterface;
use aminkt\ticket\interfaces\CustomerInterface;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

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
 * @property \aminkt\ticket\interfaces\CustomerCareInterface|null $customerCareUser
 * @property \aminkt\ticket\interfaces\CustomerCareInterface|\aminkt\ticket\interfaces\CustomerInterface $user
 * @property Ticket $ticket
 */
class TicketMessage extends ActiveRecord
{
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
        return '{{%ticket_messages}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['message'], 'string'],
            [['ticketId', 'customerCareId'], 'integer'],
            [['updateAt', 'createAt'], 'safe'],
            [['attachments'], 'string', 'max' => 191],
            [['ticketId'], 'exist', 'skipOnError' => true, 'targetClass' => Ticket::class, 'targetAttribute' => ['ticketId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'message' => 'متن پیام',
            'ticketId' => 'شناسه تیکت',
            'attachments' => 'پیوست ها',
            'customerCareId' => 'Customer Care ID',
            'updateAt' => 'تاریخ ویرایش',
            'createAt' => 'تاریخ ایجاد',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTicket()
    {
        return $this->hasOne(Ticket::class, ['id' => 'ticketId']);
    }

    /**
     * Return ticket message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Return attachments models.
     *
     * @return string
     *
     * todo : Change return type to upload manager files.
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * Return customer care user model.
     *
     * @return CustomerCareInterface|CustomerInterface
     *
     * todo : should implement.
     */
    public function getUser()
    {
        if ($this->isCustomerCareReply()) {
            // todo : Should return customer care user model.
            return null;
        } else {
            return $this->ticket->customer;
        }
    }

    /**
     * Return true of message send bys customer care users.
     *
     * @return bool
     */
    public function isCustomerCareReply(): bool
    {
        return $this->customerCareId ? true : false;
    }

    /**
     * Send new message to current ticket.
     *
     * @param integer $id
     * @param string $message
     * @param string $attachments
     * @param CustomerCareInterface|null $customerCare
     *
     * @throws \RuntimeException    When cant create ticket.
     *
     * @return TicketMessage
     */
    public static function sendNewMessage(int $id, string $message, string $attachments, CustomerCareInterface $customerCare = null): self
    {
        $ticketMessage = new TicketMessage();
        $ticketMessage->ticketId = $id;
        $ticketMessage->message = $message;
        $ticketMessage->attachments = $attachments;
        if ($customerCare)
            $ticketMessage->customerCareId = $customerCare->getId();
        return $ticketMessage;
    }

}
