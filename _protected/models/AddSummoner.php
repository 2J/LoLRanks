<?php
namespace app\models;

use nenad\passwordStrength\StrengthValidator;
use app\rbac\helpers\RbacHelper;
use yii\base\Model;
use Yii;

use app\models\RegionIndex;
use app\models\GroupAssignment;
use app\models\Summoner;

/**
 * Model representing  Signup Form.
 */
class AddSummoner extends Model
{
	public $group_id;
	public $region;
    public $usernames;

    /**
     * Returns the validation rules for attributes.
     *
     * @return array
     */
    public function rules()
    {
        return [
			['region', 'required'],
			['group_id', 'required'],
			['region', 'checkRegion'],
            ['usernames', 'filter', 'filter' => 'trim'],
            ['usernames', 'required'],
        ];
    }

    /**
     * Returns the attribute labels.
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'usernames' => 'Summoner Names'
        ];
    }
	
	public function checkRegion(){ //TODO ADD AJAX VALIDATION
		if(!RegionIndex::find()->where(['code'=>$this->region])){
			$this->addError($attribute, 'Invalid Region');
		}
	}
	
	public function addUsers(){
		$region_id = Yii::$app->GenericFunctions->getRegionid($this->region);
		$batches_of_40 = [];
		foreach(explode(',',$this->usernames) as $key=>$username){
			$batches_of_40[intval($key / 40)][] = $username;
		}
		$summoners = [];
		foreach($batches_of_40 as $batch){
			$summoners = array_merge($summoners, Yii::$app->GenericFunctions->lolapi("summonerByNames", $this->region, $batch));
		}
		if(!is_array($summoners) || (count($summoners)==0)) return false; //no summoners
		
		$update_summoners = [];
		foreach($summoners as $name=>$summoner){
			if(!Summoner::find()->where(['region'=>$region_id, 'lolid'=>$summoner['id']])->one()){
				$update_summoners[] = $summoner['id'];
				//make new summoners for these in array
				$new_summoner = new Summoner();
				$new_summoner->region = $region_id;
				$new_summoner->lolid = $summoner['id'];
				$new_summoner->name = $name;
				$new_summoner->styled_name = $summoner['name'];
				$new_summoner->level = $summoner['summonerLevel'];
				$new_summoner->save();
			}
			
			//add to group assignment
			if(!GroupAssignment::find()->where(['group_id'=>$this->group_id, 'region'=>$region_id, 'summoner_id'=>$summoner['id']])->one()){
				$summ_assignment = new GroupAssignment();
				$summ_assignment->group_id = $this->group_id;
				$summ_assignment->region = $region_id;
				$summ_assignment->summoner_id = $summoner['id'];
				$summ_assignment->save();
			}
		}
		$unranked = $update_summoners;
		//get data for summoners
		for ($i = 0; $i <= intval((count($update_summoners))/10); $i++) {
			var_dump($i);
			$summoners_data = Yii::$app->GenericFunctions->lolapi("leagueEntry", $this->region, array_slice($update_summoners, $i*10, 10));
			if(!is_array($summoners_data) || (count($summoners_data)==0)) continue; //no data
			foreach($summoners_data as $lolid => $datas){
				$summoner = Summoner::find()->where(['region'=>$region_id, 'lolid'=>$lolid])->one();
				foreach($datas as $data){
					if(!($data['queue'] == 'RANKED_SOLO_5x5')) continue;
					$summoner->rank = Yii::$app->params['tiers'][$data['tier']];
					$summoner->division = Yii::$app->params['divisions'][$data['entries'][0]['division']];
					$summoner->lp = $data['entries'][0]['leaguePoints'];
					$summoner->wins = $data['entries'][0]['wins'];
					$summoner->losses = $data['entries'][0]['losses'];
					$summoner->save();
					//summoner is updated
					if (in_array($lolid, $unranked)) 
					{
						unset($unranked[array_search($lolid,$unranked)]);
					}
					break;
				}
			}
		}
		//unranked summoners
		foreach($unranked as $unranked_summoner){
			$summoner = Summoner::find()->where(['region'=>$region_id, 'lolid'=>$unranked_summoner])->one();
			$summoner->rank=0;
			$summoner->division=5;
			$summoner->lp=0;
			$summoner->wins=0;
			$summoner->losses=0;
			$summoner->save();
		}
		return true;
	}
}
