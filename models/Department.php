<?php

namespace aminkt\ticket\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "{{%ticket_departments}}".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $status
 * @property string $createAt
 * @property string $updateAt
 *
 * @property string $statusLabel
 * @property Ticket[] $tickets
 *
 * @author Saghar Mojdehi <saghar.mojdehi@gmail.com>
 */
class Department extends ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_DEACTIVE = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%departments}}';
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
        return [
            [['name'], 'required'],
            [['status'], 'integer'],
            [['createAt', 'updateAt'], 'safe'],
            [['name', 'description'], 'string', 'max' => 191],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'نام',
            'description' => 'توضیحات',
            'status' => 'وضعیت',
            'createAt' => 'تاریخ ایجاد',
            'updateAt' => 'تاریخ ویرایش',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTickets()
    {
        return $this->hasMany(Ticket::class, ['departmentId' => 'id']);
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
            case self::STATUS_ACTIVE:
                return "فعال";
                break;
            case self::STATUS_DEACTIVE:
                return "غیر فعال";
                break;
            default:
                return "نامشخص";
        }
    }
}
