<?php

namespace aminkt\ticket\traits;


use aminkt\normalizer\yii2\MoblieValidatoer;
use aminkt\ticket\interfaces\CustomerCareInterface;
use aminkt\ticket\interfaces\CustomerInterface;
use aminkt\ticket\interfaces\DepartmentInterface;
use aminkt\ticket\interfaces\MessageInterface;
use aminkt\ticket\interfaces\TicketInterface;
use aminkt\ticket\Ticket;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQueryInterface;

/**
 * Trait TicketTrait
 *
 * @property int $id
 * @property string $name
 * @property int $customer_id
 * @property string $mobile
 * @property string $email
 * @property string $subject
 * @property int $department_id
 * @property int $status
 * @property string $update_at
 * @property string $create_at
 * @property string $tracking_code
 *
 * @property TicketMessage[] $ticketMessages
 * @property string $userName
 * @property string $userEmail
 * @property \aminkt\ticket\interfaces\CustomerInterface $customer
 * @property string $statusLabel
 * @property \yii\db\ActiveQuery $department
 * @property string $userMobile
 *
 * @package aminkt\ticket\traits
 *
 * @author Amin Keshavarz <ak_1596@yahoo.com>
 */
trait TicketTrait
{
    private $customerModel;
    private $customerCareModel;

    /**
     * Create new ticket.
     *
     * @param string $subject
     * @param CustomerInterface $customer
     * @param Department $department
     *
     * @return Ticket
     *
     * @author Mohammad Parvaneh <mohammad.pvn1375@gmail.com>
     */
    public static function createNewTicket(string $subject, CustomerInterface $customer, DepartmentInterface $department): TicketInterface
    {
        $ticketModel = Ticket::getInstance()->ticketModel;
        $ticket = new $ticketModel();
        if ($customer->getId()) {
            $ticket->customer_id = $customer->getId();
        } else {
            $ticket->name = $customer->getName();
            $ticket->mobile = $customer->getMobile();
            $ticket->email = $customer->getEmail();
        }
        $ticket->department_id = $department->id;
        $ticket->subject = $subject;
        $ticket->tracking_code = $ticket->generatetracking_code();
        $ticket->status = self::STATUS_NOT_REPLIED;
        return $ticket;
    }

    /**
     * Get customer care tickets by department
     *
     * @param $userId
     *
     * @return ActiveDataProvider
     *
     * @author Saghar Mojdehi <saghar.mojdehi@gmail.com>
     */
    public static function getCustomerCareTickets($userId): ActiveDataProvider
    {
        $ticketModel = Ticket::getInstance()->ticketModel;
        $query = $ticketModel::find();
        $query->leftJoin(
            '{{%ticket_user_departments}}',
            '{{%tickets}}.department_id = {{%ticket_user_departments}}.department_id')
            ->andWhere(['{{%ticket_user_departments}}.userId' => $userId]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        return $dataProvider;
    }

    /**
     * @inheritdoc
     */
    public function rules($isMongo = false)
    {
        $departmentModel = Ticket::getInstance()->departmentModel;
        $customerModel = Ticket::getInstance()->userModel;
        $idName = $isMongo ? '_id' : 'id';
        return [
            [['subject', 'customer_id', 'department_id'], 'required'],
            [['name', 'mobile', 'email'], 'required', 'when' => function ($model) {
                return !$model->customer_id;
            }],
            [['status'], 'in', 'range' => [
                static::STATUS_BLOCKED,
                static::STATUS_CLOSED,
                static::STATUS_REPLIED,
                static::STATUS_NOT_REPLIED
            ]],
            [['status'], 'default', 'value' => static::STATUS_NOT_REPLIED],
            [['name', 'subject'], 'string', 'max' => 191],
            [['mobile'], MoblieValidatoer::class],
            [['email'], 'email'],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => $customerModel, 'targetAttribute' => ['customer_id' => $idName]],
            [['department_id'], 'exist', 'skipOnError' => true, 'targetClass' => $departmentModel, 'targetAttribute' => ['department_id' => $idName]],
        ];
    }

    /**
     * Returns the list of all attribute names of the model.
     * This method must be overridden by child classes to define available attributes.
     * Note: primary key attribute "_id" should be always present in returned array.
     * For example:
     *
     * ```php
     * public function attributes()
     * {
     *     return ['_id', 'name', 'address', 'status'];
     * }
     * ```
     *
     * This method should use just in mongodb active record.
     */
    public function mongoAttributes()
    {
        return [
            '_id',
            'name',
            'customer_id',
            'mobile',
            'email',
            'subject',
            'department_id',
            'status',
            'update_at',
            'create_at',
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
            'customer_id' => 'کاربر',
            'mobile' => 'موبایل',
            'email' => 'ایمیل',
            'subject' => 'موضوع',
            'department_id' => 'دپارتمان',
            'status' => 'موقعیت',
            'update_at' => 'تاریخ ویرایش',
            'create_at' => 'تاریخ ایجاد',
            'userName' => 'نام کاربر',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTicketMessages(): ActiveQueryInterface
    {

        return $this->hasMany(Ticket::getInstance()->ticketMessageModel, ['ticketId' => 'id'])->orderBy(['update_at' => SORT_DESC]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartment(): ActiveQueryInterface
    {
        return $this->hasOne(Ticket::getInstance()->departmentModel, ['id' => 'department_id']);
    }

    /**
     * Change current ticket department.
     *
     * @param DepartmentInterface $department
     *
     * @return void
     */
    public function setDepartment($department)
    {
        $this->department_id = $department->getId();
        $this->save();
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
        $user = $this->getCustomer();
        if ($user) {
            return $user->getName();
        }
    }

    /**
     * Return model of user that created current ticket.
     *
     * @return CustomerInterface
     */
    function getCustomer(): CustomerInterface
    {
        if ($this->getIsGuestTicket()) {
            $customer = new class implements CustomerInterface
            {
                public $name;
                public $email;
                public $mobile;

                /**
                 * Return User Id.
                 *
                 * @return integer
                 */
                function getId()
                {
                    return null;
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
            };
            $customer->name = $this->name;
            $customer->email = $this->email;
            $customer->mobile = $this->mobile;
            return $customer;
        } else {
            $modelClass = \aminkt\ticket\Ticket::getInstance()->userModel;
            $this->customerModel = $modelClass::findOne($this->customer_id);
        }

        return $this->customerModel;
    }

    /**
     * Return true if customer model not available and false if available.
     *
     * @return bool
     */
    function getIsGuestTicket(): bool
    {
        return $this->customer_id ? false : true;
    }

    /**
     * Return mobile number of user that created current ticket.
     *
     * @return string
     */
    function getUserMobile(): string
    {
        $user = $this->getCustomer();
        if ($user) {
            return $user->getMobile();
        }
    }

    /**
     * Return email number of user that created current ticket.
     *
     * @return string
     */
    function getUserEmail(): string
    {
        $user = $this->getCustomer();
        if ($user) {
            return $user->getEmail();
        }
    }

    /**
     * Send new message to current ticket.
     *
     * @param string $message
     * @param array $attachments
     * @param CustomerCareInterface|null $customerCare
     *
     * @throws \RuntimeException    When cant create ticket.
     *
     * @return TicketMessage
     *
     * @author Mohammad Parvaneh <mohammad.pvn1375@gmail.com>
     */
    public function sendNewMessage(string $message, array $attachments, CustomerCareInterface $customerCare = null): MessageInterface
    {
        $ticketMessageModel = Ticket::getInstance()->ticketMessageModel;
        $message = $ticketMessageModel::sendNewMessage($this->id, $message, $attachments, $customerCare);
        return $message;
    }

    /**
     * Close current ticket.
     *
     * @return $this
     */
    public function closeTicket(): TicketInterface
    {
        $this->status = self::STATUS_CLOSED;
        if (!$this->save()) {
            \Yii::error($this->getErrors());
        }
        return $this;
    }

    /**
     * Open current ticket.
     *
     * @return $this
     */
    public function openTicket(): TicketInterface
    {
        $this->status = self::STATUS_NOT_REPLIED;
        if (!$this->save()) {
            \Yii::error($this->getErrors());
        }
        return $this;
    }

    /**
     * Returns status label
     *
     * @return string
     *
     * @author Saghar Mojdehi <saghar.mojdehi@gmail.com>
     */
    public function getStatusLabel(): string
    {
        switch ($this->status) {
            case self::STATUS_NOT_REPLIED:
                return 'بی پاسخ';
                break;
            case self::STATUS_REPLIED:
                return 'پاسخ داده شده';
                break;
            case self::STATUS_CLOSED:
                return 'بسته شده';
                break;
            case self::STATUS_BLOCKED:
                return 'بن شده';
                break;
            default:
                return "نامشخص";
                break;
        }
    }

    public function fields()
    {
        $fields = parent::fields();

        unset($fields['customer_id'], $fields['department_id'], $fields['name'], $fields['family'], $fields['mobile'], $fields['email']);

        return array_merge($fields, ['statusLabel', 'isGuestTicket', 'customer', 'department']);
    }

    /**
     * Set ticket status
     *
     * @param $status
     *
     * @return $this
     *
     * @author Saghar Mojdehi <saghar.mojdehi@gmail.com>
     */
    public function setStatus($status): TicketInterface
    {
        $this->status = $status;
        if (!$this->save()) {
            throw new RuntimeException('Status did not change');
        }
        return $this;
    }

    /**
     * create tracking_code for each ticket
     *
     * @return string
     *
     * @author Mohammad Parvaneh <mohammad.pvn1375@gmail.com>
     */
    protected function generatetracking_code()
    {
        $date = gmdate('yndhis', time());
        $finalCode = $this->generateRandomString(4) . $date . $this->generateRandomString(4);
        return $finalCode;
    }

    /**
     * create random characters for tracking code
     *
     * @param int $length
     *
     * @return string
     *
     * @author Mohammad Parvaneh <mohammad.pvn1375@gmail.com>
     */
    protected function generateRandomString($length = 10)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

}