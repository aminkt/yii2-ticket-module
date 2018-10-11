<?php

namespace aminkt\ticket\traits;


use aminkt\normalizer\yii2\MoblieValidatoer;
use aminkt\ticket\interfaces\DepartmentInterface;
use aminkt\ticket\interfaces\MessageInterface;
use aminkt\ticket\interfaces\TicketInterface;
use aminkt\ticket\Ticket;

/**
 * Trait TicketTrait
 *
 * @property int $id
 * @property string $name
 * @property int $customerId
 * @property string $mobile
 * @property string $email
 * @property string $subject
 * @property int $departmentId
 * @property int $status
 * @property string $updateAt
 * @property string $createAt
 * @property string $trackingCode
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
    /**
     * @inheritdoc
     */
    public function rules($isMongo = false)
    {
        $departmentModel = Ticket::getInstance()->departmentModel;
        $customerModel = Ticket::getInstance()->userModel;
        $idName = $isMongo ? '_id' : 'id';
        return [
            [['subject', 'customerId', 'departmentId'], 'required'],
            [['name', 'mobile', 'email'], 'required', 'when' => function ($model) {
                return !$model->customerId;
            }],
            [['status'], 'in', 'range' => [
                TicketInterface::STATUS_BLOCKED,
                TicketInterface::STATUS_CLOSED,
                TicketInterface::STATUS_REPLIED,
                TicketInterface::STATUS_NOT_REPLIED
            ]],
            [['status'], 'default', 'value' => TicketInterface::STATUS_NOT_REPLIED],
            [['name', 'subject'], 'string', 'max' => 191],
            [['mobile'], MoblieValidatoer::class],
            [['email'], 'email'],
            [['customerId'], 'exist', 'skipOnError' => true, 'targetClass' => $customerModel, 'targetAttribute' => ['customerId' => $idName]],
            [['departmentId'], 'exist', 'skipOnError' => true, 'targetClass' => $departmentModel, 'targetAttribute' => ['departmentId' => $idName]],
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
            'customerId',
            'mobile',
            'email',
            'subject',
            'departmentId',
            'status',
            'updateAt',
            'createAt',
            'userName',
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
            'departmentId' => 'دپارتمان',
            'status' => 'موقعیت',
            'updateAt' => 'تاریخ ویرایش',
            'createAt' => 'تاریخ ایجاد',
            'userName' => 'نام کاربر',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTicketMessages()
    {

        return $this->hasMany(Ticket::getInstance()->ticketMessageModel, ['ticketId' => 'id'])->orderBy(['updateAt' => SORT_DESC]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartment()
    {
        return $this->hasOne(Ticket::getInstance()->departmentModel, ['id' => 'departmentId']);
    }

    /**
     * Change current ticket department.
     *
     * @param DepartmentInterface   $department
     *
     * @return void
     */
    public function setDepartment($department){
        $this->departmentId = $department->getId();
        $this->save();
    }

    /**
     * Get ticket subject.
     *
     * @return string
     */
    public function getSubject() : string
    {
        return $this->subject;
    }

    /**
     * Get name of user that created current ticket.
     *
     * @return string
     */
    function getUserName() : string
    {
        $user = $this->getCustomer();
        if($user){
            return $user->getName();
        }
    }

    /**
     * Return mobile number of user that created current ticket.
     *
     * @return string
     */
    function getUserMobile() : string
    {
        $user = $this->getCustomer();
        if($user){
            return $user->getMobile();
        }
    }

    /**
     * Return email number of user that created current ticket.
     *
     * @return string
     */
    function getUserEmail() : string
    {
        $user = $this->getCustomer();
        if($user){
            return $user->getEmail();
        }
    }

    /**
     * Return true if customer model not available and false if available.
     *
     * @return bool
     */
    function getIsGuestTicket() : bool {
        return $this->customerId ? false : true;
    }

    /**
     * Return model of user that created current ticket.
     *
     * @return CustomerInterface
     */
    function getCustomer() : CustomerInterface {
        if($this->getIsGuestTicket()){
            $customer = new class implements CustomerInterface {
                public $name;
                public $email;
                public $mobile;
                /**
                 * Return User Id.
                 *
                 * @return integer
                 */
                function getId(){
                    return null;
                }

                /**
                 * Return user full name.
                 *
                 * @return string
                 */
                function getName(){
                    return $this->name;
                }

                /**
                 * Return user email.
                 * @return string|null
                 */
                function getEmail(){
                    return $this->email;
                }

                /**
                 * Return user mobile.
                 *
                 * @return string|null
                 */
                function getMobile(){
                    return $this->mobile;
                }
            };
            $customer->name = $this->name;
            $customer->email = $this->email;
            $customer->mobile = $this->mobile;
            return $customer;
        }else{
            $modelClass = \aminkt\ticket\Ticket::getInstance()->userModel;
            $this->customerModel = $modelClass::findOne($this->customerId);
        }

        return $this->customerModel;
    }

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
    public static function createNewTicket(string $subject, CustomerInterface $customer, Department $department): self
    {
        $ticketModel = Ticket::getInstance()->ticketModel;
        $ticket = new $ticketModel();
        if($customer->getId()){
            $ticket->customerId = $customer->getId();
        }else{
            $ticket->name = $customer->getName();
            $ticket->mobile = $customer->getMobile();
            $ticket->email = $customer->getEmail();
        }
        $ticket->departmentId = $department->id;
        $ticket->subject = $subject;
        $ticket->trackingCode = $ticket->generateTrackingCode();
        $ticket->status = self::STATUS_NOT_REPLIED;
        return $ticket;
    }

    /**
     * create trackingCode for each ticket
     *
     * @return string
     *
     * @author Mohammad Parvaneh <mohammad.pvn1375@gmail.com>
     */
    protected function generateTrackingCode()
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
    public function closeTicket()
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
    public function openTicket()
    {
        $this->status = self::STATUS_NOT_REPLIED;
        if (!$this->save()) {
            \Yii::error($this->getErrors());
        }
        return $this;
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
    public static function getCustomerCareTickets($userId)
    {
        $ticketModel = Ticket::getInstance()->ticketModel;
        $query = $ticketModel::find();
        $query->leftJoin(
            '{{%ticket_user_departments}}',
            '{{%tickets}}.departmentId = {{%ticket_user_departments}}.departmentId')
            ->andWhere(['{{%ticket_user_departments}}.userId' => $userId]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        return $dataProvider;
    }

    /**
     * Returns status label
     *
     * @return string
     *
     * @author Saghar Mojdehi <saghar.mojdehi@gmail.com>
     */
    public function getStatusLabel()
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

        unset($fields['customerId'], $fields['name'], $fields['family'], $fields['mobile'], $fields['email']);

        return array_merge($fields, ['statusLabel', 'isGuestTicket', 'customer']);
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
    public function setStatus($status)
    {
        $this->status = $status;
        if (!$this->save()) {
            throw new RuntimeException('Status did not change');
        }
        return $this;
    }

}