<?php

namespace aminkt\ticket\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "ticket_categories".
 *
 * @property int $id
 * @property string $name
 * @property int $status
 * @property string $updateAt
 * @property string $createAt
 *
 * @property Ticket[] $tickets
 */
class TicketCategory extends ActiveRecord
{
    const STATUS_ACTIVE=1;
    const STATUS_DE_ACTIVE=2;


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
        return '{{%ticket_categories}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['status'], 'integer'],
            [['updateAt', 'createAt'], 'safe'],
            [['name'], 'string', 'max' => 191],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'status' => 'Status',
            'updateAt' => 'Update At',
            'createAt' => 'Create At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTickets()
    {
        return $this->hasMany(Ticket::className(), ['categoryId' => 'id']);
    }

    /**
     * Create new Category.
     *
     * @param string $name
     *
     * @return TicketCategory
     *
     * @throws \RuntimeException    If cant create new Category.
     */
    public static function createNewCategory(string $name) : self {
        // todo Implement this function.
    }
}
