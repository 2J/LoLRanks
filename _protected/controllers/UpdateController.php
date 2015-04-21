<?php

namespace app\controllers;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Expression;

use app\models\Group;
use app\models\GroupAssignment;
use app\models\AddSummoner;
use app\models\Summoner;
use app\models\PastUsernames;

class UpdateController extends \yii\web\Controller
{
    public function actionCron()
    {
		
    }

    public function actionGroup($group_id)
    {	
		\Yii::$app->response->format = 'json';
		
        if (($model =Group::find()->where(['id'=>$group_id])->one()) == null) {
            throw new NotFoundHttpException('The requested page does not exist');
        }
		
		//get rid of those that have updated in the last two minutes
		$timestamp = \Yii::$app->db->createCommand('SELECT CURRENT_TIMESTAMP as timestamp')->queryOne()['timestamp'];
		$summoners = $model->summoners;
		
		foreach($summoners as $key=>$summoner){
			if((strtotime($timestamp) - strtotime($summoner->last_updated)) < 120){ //update can't happen more than once every 2 min
				unset($summoners[$key]);
			}
		}
		if(count($summoners) == 0) return ['msg'=>'This group has updated recently.', 'success'=>false];
		
		//sort by region
		$region_sort = [];
		foreach($summoners as $summoner){
			$region_sort[$summoner->region][$summoner->lolid] = $summoner;
		}
		
		//by region
		foreach($region_sort as $region=>$summoners){
			$region_code = Yii::$app->GenericFunctions->getRegioncode($region);
			//in batches of 40
			for($a=0; $a <= intval((count($summoners))/40); $a++){
				$summ_batch_40 = array_slice($summoners, $a*40, 40);
				
				//get name, level
				$summ_batch_40_lolids = [];
				foreach($summ_batch_40 as $temp){
					$summ_batch_40_lolids[] = $temp->lolid;
				}
				$datas = Yii::$app->GenericFunctions->lolapi("summonerByIds", $region_code, $summ_batch_40_lolids);
				if(!is_array($datas) || (count($datas)==0)) continue; //no data
				foreach($datas as $data){
					$region_sort[$region][$data['id']]->level;
					//check if name has been changed
					if($region_sort[$region][$data['id']]->styled_name != $data['name']){
						$past_username = new PastUsernames();
						$past_username->region = $region;
						$past_username->lolid = $data['id'];
						$past_username->past_username = $region_sort[$region][$data['id']]->styled_name;
						$past_username->current_username = $data['name'];
						$past_username->save(false);
						$region_sort[$region][$data['id']]->styled_name = $data['name'];
					};
					$region_sort[$region][$data['id']]->level = $data['summonerLevel'];
				}
				//TODO: CHECK IF USERS ARE GONE, delete from array and show on DB that it is deleted
				
				//in batches of 10
				for($b=0; $b <= intval((count($summ_batch_40))/10); $b++){
					$summ_batch_10 = array_slice($summ_batch_40, $b*10, 10);
					//get rank, division, lp, wins, losses
					$summ_batch_10_lolids = [];
					foreach($summ_batch_10 as $temp){
						$summ_batch_10_lolids[] = $temp->lolid;
					}
					$unranked_summoners = $summ_batch_10_lolids;
					$league_datas = Yii::$app->GenericFunctions->lolapi("leagueEntry", $region_code, $summ_batch_10_lolids);
					if(!is_array($league_datas) || (count($league_datas)==0)) continue; //no data
					foreach($league_datas as $lolid => $datas){
						foreach($datas as $data){
							if(!($data['queue'] == 'RANKED_SOLO_5x5')) continue;
							$region_sort[$region][$lolid]->rank = Yii::$app->params['tiers'][$data['tier']];
							$region_sort[$region][$lolid]->division = Yii::$app->params['divisions'][$data['entries'][0]['division']];
							$region_sort[$region][$lolid]->lp = $data['entries'][0]['leaguePoints'];
							$region_sort[$region][$lolid]->wins = $data['entries'][0]['wins'];
							$region_sort[$region][$lolid]->losses = $data['entries'][0]['losses'];
							//summoner is updated
							if (in_array($lolid, $unranked_summoners)) 
							{
								unset($unranked_summoners[array_search($lolid,$unranked_summoners)]);
							}
							break;
						}
					}
					//TODO: check if unranked summoners working
					foreach($unranked_summoners as $unranked){
						$region_sort[$region][$unranked]->rank = 0;
						$region_sort[$region][$unranked]->division = 5;
						$region_sort[$region][$unranked]->lp = 0;
						$region_sort[$region][$unranked]->wins = 0;
						$region_sort[$region][$unranked]->losses = 0;
					}
				}
			}
		}
		foreach($region_sort as $region=>$summoners){
			foreach($summoners as $summoner){
				$summoner->last_updated = new Expression('NOW()');
				$summoner->save();
			}
		}
		$model->last_visit = new Expression('NOW()');
		$model->save();
		return ['msg'=>'Group has successfully updated.', 'success'=>true];
    }

}

/*
		$region_id = Yii::$app->GenericFunctions->getRegionid($this->region);
		$summoners = Yii::$app->GenericFunctions->lolapi("summonerByNames", $this->region, $this->usernames, false);
		if(empty($summoners)) return false; //no summoners
		
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
		$num_10s = intval((count($update_summoners))/10);
		//get data for summoners
		for ($i = 0; $i <= $num_10s; $i++) {
			var_dump($i);
			$summoners_data = Yii::$app->GenericFunctions->lolapi("leagueEntry", $this->region, array_slice($update_summoners, 0, 10));
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
					if (in_array($lolid, $update_summoners)) 
					{
						unset($update_summoners[array_search($lolid,$update_summoners)]);
					}
					break;
				}
			}
		}
		var_dump($update_summoners);
		//unranked summoners
		foreach($update_summoners as $unranked_summoner){
			$summoner = Summoner::find()->where(['region'=>$region_id, 'lolid'=>$unranked_summoner])->one();
			$summoner->rank=0;
			$summoner->division=5;
			$summoner->lp=0;
			$summoner->wins=0;
			$summoner->losses=0;
			$summoner->save();
		}
		return true;
*/