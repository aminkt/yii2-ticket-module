<?php

namespace aminkt\ticket\traits;

use aminkt\ticket\Ticket;

/**
 * Trait DepartmentTrait
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
 *
 * @author Amin Keshavarz <ak_1596@yahoo.com>
 */
trait TicketMessageTrait
{
    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * Each rule is an array with the following structure:
     *
     * ```php
     * [
     *     ['attribute1', 'attribute2'],
     *     'validator type',
     *     'on' => ['scenario1', 'scenario2'],
     *     //...other parameters...
     * ]
     * ```
     *
     * where
     *
     *  - attribute list: required, specifies the attributes array to be validated, for single attribute you can pass a string;
     *  - validator type: required, specifies the validator to be used. It can be a built-in validator name,
     *    a method name of the model class, an anonymous function, or a validator class name.
     *  - on: optional, specifies the [[scenario|scenarios]] array in which the validation
     *    rule can be applied. If this option is not set, the rule will apply to all scenarios.
     *  - additional name-value pairs can be specified to initialize the corresponding validator properties.
     *    Please refer to individual validator class API for possible properties.
     *
     * A validator can be either an object of a class extending [[Validator]], or a model class method
     * (called *inline validator*) that has the following signature:
     *
     * ```php
     * // $params refers to validation parameters given in the rule
     * function validatorName($attribute, $params)
     * ```
     *
     * In the above `$attribute` refers to the attribute currently being validated while `$params` contains an array of
     * validator configuration options such as `max` in case of `string` validator. The value of the attribute currently being validated
     * can be accessed as `$this->$attribute`. Note the `$` before `attribute`; this is taking the value of the variable
     * `$attribute` and using it as the name of the property to access.
     *
     * Yii also provides a set of [[Validator::builtInValidators|built-in validators]].
     * Each one has an alias name which can be used when specifying a validation rule.
     *
     * Below are some examples:
     *
     * ```php
     * [
     *     // built-in "required" validator
     *     [['username', 'password'], 'required'],
     *     // built-in "string" validator customized with "min" and "max" properties
     *     ['username', 'string', 'min' => 3, 'max' => 12],
     *     // built-in "compare" validator that is used in "register" scenario only
     *     ['password', 'compare', 'compareAttribute' => 'password2', 'on' => 'register'],
     *     // an inline validator defined via the "authenticate()" method in the model class
     *     ['password', 'authenticate', 'on' => 'login'],
     *     // a validator of class "DateRangeValidator"
     *     ['dateRange', 'DateRangeValidator'],
     * ];
     * ```
     *
     * Note, in order to inherit rules defined in the parent class, a child class needs to
     * merge the parent rules with child rules using functions such as `array_merge()`.
     *
     * @return array validation rules
     * @see scenarios()
     */
    public function rules()
    {
        $ticketModel = Ticket::getInstance()->ticketModel;
        return [
            [['message'], 'string'],
            [['ticketId', 'customerCareId'], 'integer'],
            [['updateAt', 'createAt'], 'safe'],
            [['attachments'], 'string', 'max' => 191],
            [['ticketId'], 'exist', 'skipOnError' => true, 'targetClass' => $ticketModel, 'targetAttribute' => ['ticketId' => 'id']],
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
            'message',
            'ticketId',
            'attachments',
            'customerCareId',
            'updateAt',
            'createAt',
        ];
    }

    /**
     * Returns the attribute labels.
     *
     * Attribute labels are mainly used for display purpose. For example, given an attribute
     * `firstName`, we can declare a label `First Name` which is more user-friendly and can
     * be displayed to end users.
     *
     * By default an attribute label is generated using [[generateAttributeLabel()]].
     * This method allows you to explicitly specify attribute labels.
     *
     * Note, in order to inherit labels defined in the parent class, a child class needs to
     * merge the parent labels with child labels using functions such as `array_merge()`.
     *
     * @return array attribute labels (name => label)
     * @see generateAttributeLabel()
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
        $ticketModel = Ticket::getInstance()->ticketModel;
        return $this->hasOne($ticketModel, ['id' => 'ticketId']);
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
     * @return array
     *
     * @author Saghar Mojdehi <saghar.mojdehi@gmail.com>
     */
    public function getAttachments()
    {
        $items = explode(',', $this->attachments);
        $attachments = [];
        foreach ($items as $item) {
            try {
                $attachments[] = UploadManager::getInstance()->getFile($item);
            } catch (NotFoundHttpException $e) {
                \Yii::error("File not found.");
            }
        }
        return $attachments;
    }

    /**
     * Return attachments url
     *
     * @return array
     *
     * @author Saghar Mojdehi <saghar.mojdehi@gmail.com>
     */
    public function getAttachmentUrl()
    {
        $urls = [];
        foreach ($this->getAttachments() as $attachment) {
            $urls[] = $attachment->getUrl();
        }

        return $urls;
    }

    /**
     * Return customer care user model.
     *
     * @return CustomerCareInterface|CustomerInterface
     *
     * @author Saghar Mojdehi <saghar.mojdehi@gmail.com>
     */
    public function getUser()
    {
        if ($this->getIsCustomerCareReply()) {
            $adminModel = \aminkt\ticket\Ticket::getInstance()->adminModel;
            $customerCare = $adminModel::findOne($this->customerCareId);
            $customerCareReturn = new class implements CustomerInterface {
                public $id;
                public $name;

                /**
                 * Return User Id.
                 *
                 * @return integer
                 */
                function getId(){
                    return $this->id;
                }

                /**
                 * Return user full name.
                 *
                 * @return string
                 */
                function getName(){
                    return $this->name;
                }

                function getMobile()
                {
                    return null;
                }

                public function getEmail()
                {
                    return null;
                }
            };
            $customerCareReturn->name = $customerCare->fullName;
            $customerCareReturn->id = $customerCare->id;
            return $customerCareReturn;
        } else {
            return $this->ticket->customer;
        }
    }

    /**
     * Return true of message send bys customer care users.
     *
     * @return bool
     */
    public function getIsCustomerCareReply(): bool
    {
        return $this->customerCareId ? true : false;
    }

    /**
     * Send new message to current ticket.
     *
     * @param integer $id
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
    public static function sendNewMessage(int $id, string $message, array $attachments, CustomerCareInterface $customerCare = null): self
    {
        $messageModel = Ticket::getInstance()->ticketMessageModel;
        $ticketMessage = new $messageModel();
        $ticketMessage->ticketId = $id;
        $ticketMessage->message = Html::encode($message);
        $ticketMessage->attachments = implode(',', $attachments);
        if ($customerCare)
            $ticketMessage->customerCareId = $customerCare->getId();
        return $ticketMessage;
    }


    /**
     * Returns the list of fields that should be returned by default by [[toArray()]] when no specific fields are specified.
     *
     * A field is a named element in the returned array by [[toArray()]].
     *
     * This method should return an array of field names or field definitions.
     * If the former, the field name will be treated as an object property name whose value will be used
     * as the field value. If the latter, the array key should be the field name while the array value should be
     * the corresponding field definition which can be either an object property name or a PHP callable
     * returning the corresponding field value. The signature of the callable should be:
     *
     * ```php
     * function ($model, $field) {
     *     // return field value
     * }
     * ```
     *
     * For example, the following code declares four fields:
     *
     * - `email`: the field name is the same as the property name `email`;
     * - `firstName` and `lastName`: the field names are `firstName` and `lastName`, and their
     *   values are obtained from the `first_name` and `last_name` properties;
     * - `fullName`: the field name is `fullName`. Its value is obtained by concatenating `first_name`
     *   and `last_name`.
     *
     * ```php
     * return [
     *     'email',
     *     'firstName' => 'first_name',
     *     'lastName' => 'last_name',
     *     'fullName' => function ($model) {
     *         return $model->first_name . ' ' . $model->last_name;
     *     },
     * ];
     * ```
     *
     * In this method, you may also want to return different lists of fields based on some context
     * information. For example, depending on [[scenario]] or the privilege of the current application user,
     * you may return different sets of visible fields or filter out some fields.
     *
     * The default implementation of this method returns [[attributes()]] indexed by the same attribute names.
     *
     * @return array the list of field names or field definitions.
     * @see toArray()
     */
    public function fields(){
        $fields =  parent::fields();

        unset($fields['customerCareId'], $fields['ticketId']);

        return array_merge($fields, ['isCustomerCareReply', 'user']);
    }
}