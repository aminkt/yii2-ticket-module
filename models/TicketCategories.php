<?php

namespace api\modules\ticket\models;

use Yii;

/**
 * This is the model class for table "ticket_categories".
 *
 * @property int $id
 * @property string $name
 * @property int $status
 * @property string $updateAt
 * @property string $createAt
 *
 * @property Tickets[] $tickets
 */
class TicketCategories extends \yii\db\ActiveRecord
{
    const STARUS_ACTIVE=1;
    const STARUS_DEACTIVE=2;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ticket_categories';
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
        return $this->hasMany(Tickets::className(), ['categoryId' => 'id']);
    }
}
