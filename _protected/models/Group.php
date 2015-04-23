<?php

namespace app\models;

use Yii;
use yii\helpers\Url;
use himiklab\sitemap\behaviors\SitemapBehavior;

/**
 * This is the model class for table "group".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $name
 * @property string $description
 * @property string $slug
 * @property string $last_visit
 * @property integer $private
 * @property integer $has_low 
 *
 * @property User $user
 * @property GroupAssignment[] $groupAssignments
 */
class Group extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'group';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['user_id', 'private', 'has_low'], 'integer'],
            [['last_visit'], 'safe'],
            [['name'], 'string', 'max' => 100],
            [['description'], 'string', 'max' => 200],
            [['slug'], 'string', 'max' => 30],
			['slug', 'match', 'pattern' => '/^[A-Za-z0-9_-]+$/u', 'message'=>'Url can only contain alphanumeric characters, dashes, and hyphens'],
            [['slug'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'name' => 'Name',
            'description' => 'Description',
            'slug' => 'URL',
            'last_visit' => 'Last Visit',
            'private' => 'Private(NOTE: DOES NOT WORK RIGHT NOW)',
			'has_low' => 'Has Low',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupAssignments()
    {
        return $this->hasMany(GroupAssignment::className(), ['group_id' => 'id']);
    }
	
	public function getSummoners()
	{
        return $this->hasMany(Summoner::className(), ['lolid' => 'summoner_id'])
			->viaTable('group_assignment', ['group_id' => 'id']);
	}
	
	public function randomSlug($name=''){
		if(empty($name)) $name = (empty($this->name)? 'ranks' : $this->name);
		$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $name);
		$clean = strtolower(trim($clean, '-'));
		$clean = preg_replace("/[\/_|+ -]+/", '-', $clean);
		$slugs = self::find('`slug` LIKE ":slug%"')->params([':slug'=>$clean])->all();
		$taken_slugs = [];
		foreach ($slugs as $slug){
			$taken_slugs [] = $slug->slug;
		}
//		$query = new Query;
//		$query->select('name')->from('group')->where('`slug` LIKE ":slug%"')->params([':slug'=>$clean]);
//		$slugs = $query->all();
		$max=0;
		while(in_array( ($clean . '-' . ++$max ), $taken_slugs) );
		return $clean . '-' . $max;
	}
	
	public function isOwner(){
		if((Yii::$app->user->id == $this->user_id) || Yii::$app->user->can('theCreator')){
			return true;
		}else return false;
	}
 
	public function behaviors()
	{
		return [
			'sitemap' => [
				'class' => SitemapBehavior::className(),
				'scope' => function ($model) {
					/** @var \yii\db\ActiveQuery $model */
					$model->select(['slug', 'last_visit']);
					$model->andWhere(['private' => 0]);
				},
				'dataClosure' => function ($model) {
					/** @var self $model */
					return [
						'loc' => Url::to('groups/'.$model->slug, true),
						'lastmod' => strtotime($model->last_visit),
						'changefreq' => SitemapBehavior::CHANGEFREQ_DAILY,
						'priority' => 0.7
					];
				}
			],
		];
	}
}
