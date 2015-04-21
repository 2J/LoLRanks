<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "system_setting".
 *
 * @property string $system_setting
 * @property string $setting_value
 * @property string $setting_description
 * @property string $stamp_date
 * @property integer $stamp_user
 */
class SystemSetting extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'system_setting';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['system_setting'], 'required'],
            [['setting_description'], 'string'],
            [['stamp_date'], 'safe'],
            [['stamp_user'], 'integer'],
            [['system_setting', 'setting_value'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'system_setting' => 'System Setting',
            'setting_value' => 'Setting Value',
            'setting_description' => 'Setting Description',
            'stamp_date' => 'Stamp Date',
            'stamp_user' => 'Stamp User',
        ];
    }
}
