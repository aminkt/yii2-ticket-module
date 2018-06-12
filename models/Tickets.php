<?php

namespace aminkt\ticket\models;

use aminkt\ticket\interfaces\CustomerCareInterface;
use aminkt\ticket\interfaces\CustomerInterface;
use aminkt\widgets\alert\Alert;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;


/**
 * This is the model class for table "tickets".
 *
 * @property int $id
 * @property string $name
 * @property int $customerId
 * @property string $mobile
 * @property string $email
 * @property string $subject
 * @property int $categoryId
 * @property int $status
 * @property string $updateAt
 * @property string $createAt
 *
 * @property TicketMessage[] $ticketMessages
 * @property TicketCategories $category
 * @property string $userName
 * @property string $userEmail
 * @property \aminkt\ticket\interfaces\CustomerInterface $customer
 * @property string $userMobile
 *
 * @package aminkt\ticket
 */
class Tickets extends ActiveRecord
{
    const STATUS_NOT_REPLIED = 1;
    const STATUS_REPLIED = 2;
    const STATUS_CLOSED = 3;
    const STATUS_BLOCKED = 4;


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
        return 'tickets';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'mobile', 'email', 'subject'], 'required'],
            [['customerId', 'categoryId', 'status', 'departmentId'], 'integer'],
            [['updateAt', 'createAt'], 'safe'],
            [['name', 'email', 'subject'], 'string', 'max' => 191],
            [['mobile'], 'string', 'max' => 15],
            [['categoryId'], 'exist', 'skipOnError' => true, 'targetClass' => TicketCategories::class, 'targetAttribute' => ['categoryId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'شماره شناسه',
            'name' => 'نام',
            'customerId' => 'کاربر',
            'mobile' => 'موبایل',
            'email' => 'ایمیل',
            'subject' => 'موضوع',
            'categoryId' => 'دسته بندی',
            'status' => 'موقعیت',
            'updateAt' => 'تاریخ ویرایش',
            'createAt' => 'تاریخ ایجاد',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTicketMessages()
    {
        return $this->hasMany(TicketMessage::className(), ['ticketId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(TicketCategories::className(), ['id' => 'categoryId']);
    }

    /**
     * Get ticket subject.
     *
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * Get name of user that created current ticket.
     *
     * @return string
     */
    function getUserName(): string
    {
        return $this->name;
    }

    /**
     * Return mobile number of user that created current ticket.
     *
     * @return string
     */
    function getUserMobile(): string
    {
        return $this->mobile;

    }

    /**
     * Return email number of user that created current ticket.
     *
     * @return string
     */
    function getUserEmail(): string
    {
        return $this->email;

    }

    /**
     * Return true if customer model not available and false if available.
     * @return bool
     */
    function isGuestTicket(): bool
    {
        return $this->customerId ? false : true;
    }

    /**
     * Return model of user that created current ticket.
     *
     * @return CustomerInterface
     */
    function getCustomer(): CustomerInterface
    {
        if ($this->isGuestTicket()) {
            return new CustomerTempModel(
                $this->getUserName(),
                $this->getUserEmail(),
                $this->getUserMobile()
            );
        } else {
            // todo should return user model.
            return null;
        }
    }

    /**
     * Create new ticket.
     *
     * @param string $subject
     * @param string $message
     * @param CustomerInterface $customer
     * @param TicketCategories $category
     *
     * @throws \RuntimeException    When cant create ticket.
     *
     * @return Tickets
     */
    public static function createNewTicket(string $subject, CustomerInterface $customer, TicketCategories $category): self
    {
        $ticket = new Tickets();
        $ticket->name = $customer->getName();
        $ticket->mobile = $customer->getMobile();
        $ticket->email = $customer->getEmail();
        $ticket->customerId = $customer->getId();
        $ticket->categoryId = $category->id;
        $ticket->subject = $subject;
        $ticket->status = 1;
        if ($ticket->save()) {
            Alert::success('تیکت با موفقیت ایجاد شد', 'اسم تیکت جدید : ' . $ticket->name);
            return $ticket;
        } else {
            \Yii::error($ticket->getErrors());
            throw new \RuntimeException('تیکت ذخیره نشد.');
        }
    }

    /**
     * Send new message to current ticket.
     *
     * @param string $message
     * @param string $attachments
     * @param CustomerCareInterface|null $customerCare
     *
     * @throws \RuntimeException    When cant create ticket.
     *
     * @return TicketMessage
     */
    public function sendNewMessage(string $message, string $attachments, CustomerCareInterface $customerCare = null): TicketMessage
    {

        $message = TicketMessage::sendNewMessage($this->id, $message, $attachments, $customerCare);
        // todo : Should implement.
        return $message;
    }

    /**
     * Close current ticket.
     *
     * @return $this
     *
     * @throws \RuntimeException    When cant close current ticket.
     */
    public function closeTicket()
    {
        $this->status = self::STATUS_CLOSED;
        if (!$this->save()) {
            \Yii::error($this->getErrors());
            throw new \RuntimeException("Cant close ticket.");
        }
        return $this;
    }

    /**
     * Open current ticket.
     *
     * @return $this
     *
     * @throws \RuntimeException    When cant open current ticket.
     */
    public function openTicket()
    {
        $this->status = self::STATUS_NOT_REPLIED;
        if (!$this->save()) {
            \Yii::error($this->getErrors());
            throw new \RuntimeException("Cant open ticket.");
        }
        return $this;
    }
}

class CustomerTempModel implements CustomerInterface
{
    public $id = null;
    public $name;
    public $email;
    public $mobile;

    function __construct(string $name, string $email = null, string $mobile = null)
    {
        $this->name = $name;
        $this->email = $email;
        $this->mobile = $mobile;
    }

    /**
     * Return User Id.
     *
     * @return integer
     */
    function getId()
    {
        return $this->getId();
    }

    /**
     * Return user full name.
     *
     * @return string
     */
    function getName()
    {
        return $this->name;
    }

    /**
     * Return user email.
     * @return string|null
     */
    function getEmail()
    {
        return $this->email;
    }

    /**
     * Return user mobile.
     *
     * @return string|null
     */
    function getMobile()
    {
        return $this->mobile;
    }
}