<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "region_index".
 *
 * @property integer $id
 * @property string $code
 * @property string $description
 *
 * @property GroupAssignment[] $groupAssignments
 * @property PastUsernames[] $pastUsernames
 * @property Summoner[] $summoners
 */
class RegionIndex extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'region_index';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code', 'description'], 'required'],
            [['code'], 'string', 'max' => 10],
            [['description'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'description' => 'Description',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupAssignments()
    {
        return $this->hasMany(GroupAssignment::className(), ['region' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPastUsernames()
    {
        return $this->hasMany(PastUsernames::className(), ['region' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSummoners()
    {
        return $this->hasMany(Summoner::className(), ['region' => 'id']);
    }
}
