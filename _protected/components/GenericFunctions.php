<?php
namespace app\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use linslin\yii2\curl;

use app\models\RegionIndex;
 
//The name is a lie. These functions are not so generic
class GenericFunctions extends Component
{
	//Yii::$app->GenericFunctions->lolapi();
	
	public function cleanString($str){
		$str = preg_replace('/\s+/', '', $str);
		return $str;
	}
	
	public function lolapi($action, $region, $params, $is_array = true){
		$url = "https://{region}.api.pvp.net/api/lol/{region}";
		if($is_array){
			$params = implode(",", $params);
		}
		if($action == "summonerByNames"){
			$url.= "/v1.4/summoner/by-name/{params}";
		}else if ($action == "summonerByIds"){
			$url.= "/v1.4/summoner/{params}";
		}else if ($action == "leagueEntry"){
			$url.= "/v2.5/league/by-summoner/{params}/entry";
		}
		$url = str_replace("{region}", $region, $url);
		$url = str_replace("{params}", rawurlencode($this->cleanString($params)), $url);
//		echo $url."?api_key=".Yii::$app->params['API_KEY'];
		
        $curl = new curl\Curl();
		return json_decode($curl->get($url."?api_key=".Yii::$app->params['API_KEY']),true);
		//TODO: better decode
	}
	
	public function getRegionid($code){
		return RegionIndex::find()->where(['code'=>$code])->one()->id;
	}
	
	public function getRegioncode($id){
		return RegionIndex::find()->where(['id'=>$id])->one()->code;
	}
}