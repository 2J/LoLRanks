<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "summoner".
 *
 * @property integer $id
 * @property integer $region
 * @property integer $lolid
 * @property string $name
 * @property string $styled_name
 * @property integer $level
 * @property integer $rank
 * @property integer $division
 * @property integer $lp
 * @property integer $wins
 * @property integer $losses
 * @property string $last_updated
 * @property integer $need_update
 *
 * @property RegionIndex $region0
 */
class Summoner extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'summoner';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['region', 'lolid', 'name', 'styled_name'], 'required'],
            [['region', 'lolid', 'level', 'rank', 'division', 'lp', 'wins', 'losses', 'need_update'], 'integer'],
            [['last_updated'], 'safe'],
            [['name', 'styled_name'], 'string', 'max' => 45],
            [['region', 'lolid'], 'unique', 'targetAttribute' => ['region', 'lolid'], 'message' => 'The combination of Region and Lolid has already been taken.']
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
            'name' => 'Name',
            'styled_name' => 'Name',
            'level' => 'Level',
            'rank' => 'Rank',
            'division' => 'Division',
            'lp' => 'Lp',
            'wins' => 'Wins',
            'losses' => 'Losses',
            'last_updated' => 'Last Updated',
            'need_update' => 'Need Update',
			'fullrank' => 'Rank',
			'regionDesc' => 'Region',
			'wlratio' => 'W / L (%)',
        ];
    }
	
	public function getFullrank($sort=false){
		$fullrank = '';
		
		if($sort) $fullrank .= 50 - $this->level;
		if(!$sort && ($this->rank == 0)) return "Unranked";
		
		//ranks
		if($sort) $fullrank .= 10 - $this->rank;
		else $fullrank .= Yii::$app->params['tiers_rev'][$this->rank];
		
		if($sort) $fullrank .= $this->division;
		else $fullrank .= ' '.array_search($this->division, Yii::$app->params['divisions']);
		
		if($sort) $fullrank .= $this->lp;
		else $fullrank .= ' - '.$this->lp . " LP";
		
		return $fullrank;
	}
	
	public function getRegionDesc(){
		return $this->regionIndex->description;
	}
	
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegionIndex()
    {
        return $this->hasOne(RegionIndex::className(), ['id' => 'region']);
    }
	
	public function getPastUsername(){
		//get usernames up to 1 month prior
		$past_username = PastUsernames::find()
			->select('region, lolid, past_username')
			->distinct(true)
			->where(['and', 'region=:region', 'lolid=:lolid', 'timestamp >= DATE_SUB(NOW(), INTERVAL 1 MONTH)'])
			->params([':region'=>$this->region, ':lolid'=>$this->lolid])
			->orderBy('timestamp DESC')
			->all();
		if(count($past_username) > 0){
			$pul = [];
			foreach($past_username as$pu) $pul[] = $pu->past_username;
			return ['changed'=>true, 'old_name'=>implode(', ', $pul)];
		}else return ['changed'=>false, 'old_name'=>''];
	}
	
	public function getTotal(){
		return $this->wins + $this->losses;
	}
	
	public function getWlratio($sort=true){
		if($this->total == 0) {
			if($sort) return -1;
			else return "N/A";
		}
		if($sort) return (100 * $this->wins / ($this->wins + $this->losses));
		$wlratio = (intval(1000 * $this->wins / ($this->wins + $this->losses)))/10;
		return $this->wins . " / " . $this->losses . " (". $wlratio ."%)";
	}
}
