<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "group_assignment".
 *
 * @property integer $group_id
 * @property integer $region
 * @property integer $summoner_id
 *
 * @property RegionIndex $region0
 * @property Group $group
 */
class GroupAssignment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'group_assignment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['group_id', 'region', 'summoner_id'], 'required'],
            [['group_id', 'region', 'summoner_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'group_id' => 'Group ID',
            'region' => 'Region',
            'summoner_id' => 'Summoner ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegion0()
    {
        return $this->hasOne(RegionIndex::className(), ['id' => 'region']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(Group::className(), ['id' => 'group_id']);
    }
}
