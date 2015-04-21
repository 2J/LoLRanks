<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "past_usernames".
 *
 * @property integer $id
 * @property integer $region
 * @property integer $lolid
 * @property string $past_username
 * @property string $current_username
 * @property string $timestamp
 *
 * @property RegionIndex $region0
 */
class PastUsernames extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'past_usernames';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['region', 'lolid', 'past_username', 'current_username'], 'required'],
            [['region', 'lolid'], 'integer'],
            [['timestamp'], 'safe'],
            [['past_username', 'current_username'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'region' => 'Region',
            'lolid' => 'Lolid',
            'past_username' => 'Past Username',
            'current_username' => 'Current Username',
            'timestamp' => 'Timestamp',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegion0()
    {
        return $this->hasOne(RegionIndex::className(), ['id' => 'region']);
    }
}
