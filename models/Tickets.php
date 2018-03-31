<?php

namespace api\modules\ticket\models;

use common\models\User;
use Yii;

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
 * @property User user
 * @property string userName
 * @property TicketMessages[] $ticketMessages
 * @property TicketCategories $category
 * @property string categoryName
 */
class Tickets extends BaseActiveRecord
{
    const STATUS_NOT_REPLIED = 1;
    const STATUS_REPLIED = 2;
    const STATUS_CLOSED = 3;
    const STATUS_BLOCKED = 4;

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
            [['customerId', 'categoryId', 'status'], 'integer'],
            [['updateAt', 'createAt'], 'safe'],
            [['name', 'email', 'subject'], 'string', 'max' => 191],
            [['mobile'], 'string', 'max' => 15],
            [['categoryId'], 'exist', 'skipOnError' => true, 'targetClass' => TicketCategories::className(), 'targetAttribute' => ['categoryId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'نام کاربر',
            'customerId' => 'Customer ID',
            'mobile' => 'شماره موبایل',
            'email' => 'ایمیل',
            'subject' => 'موضوع',
            'categoryId' => 'Category ID',
            'status' => 'وضعیت',
            'updateAt' => 'تاریخ پاسخ',
            'createAt' => 'تاریخ ایجاد',
            'categoryName'=>'دسته بندی',
            'userName'=>'کاربر'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTicketMessages()
    {
        return $this->hasMany(TicketMessages::className(), ['ticketId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(TicketCategories::className(), ['id' => 'categoryId']);
    }

    public function getCustomer()
    {
        return $customer = User::findOne($this->customerId);
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    function getUserName()
    {
        return $this->name;
    }

    function getCategoryName(){
        if ($this->category)
            return $this->category->name;
        return null;
    }

    function getUserMobile()
    {
        return $this->mobile;

    }

    function getUserEmail()
    {
        return $this->email;

    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }


}
