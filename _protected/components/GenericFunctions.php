<?php
namespace app\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use linslin\yii2\curl;

use app\models\RegionIndex;
use app\models\GroupViews;
 
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
	
  public function TimeSince($since){
    $chunks = array(
        array(60 * 60 * 24 * 365 , 'year'),
        array(60 * 60 * 24 * 30 , 'month'),
        array(60 * 60 * 24 * 7, 'week'),
        array(60 * 60 * 24 , 'day'),
        array(60 * 60 , 'hour'),
        array(60 , 'minute'),
        array(1 , 'second')
    );

    for ($i = 0, $j = count($chunks); $i < $j; $i++) {
        $seconds = $chunks[$i][0];
        $name = $chunks[$i][1];
        if (($count = floor($since / $seconds)) != 0) {
            break;
        }
    }

    $print = ($count == 1) ? '1 '.$name : "$count {$name}s";
    return $print;
  }
  
  public function ip(){
	  return getenv('HTTP_CLIENT_IP')?:
		getenv('HTTP_X_FORWARDED_FOR')?:
		getenv('HTTP_X_FORWARDED')?:
		getenv('HTTP_FORWARDED_FOR')?:
		getenv('HTTP_FORWARDED')?:
		getenv('REMOTE_ADDR')?:
		'UNKNOWN';
  }
  
  public function audit($type,$details){
	  if($type == 'group_view'){
		$ip = $this->ip();
		//only log once per hour
		$recent_audit = GroupViews::find()
			->distinct(true)
			->where(['and', 'group_id=:group_id', 'ip=:ip', 'timestamp >= DATE_SUB(NOW(), INTERVAL 1 HOUR)'])
			->params([':group_id'=>$details['group_id'], ':ip'=>$ip])
			->orderBy('timestamp DESC')
			->all();
		if(count($recent_audit) == 0){
			$audit = new GroupViews();
			$audit->group_id = $details['group_id'];
			$audit->user_id = $details['user_id'];
			
			$audit->ip = $ip;
				
			return $audit->save();
		}
	  }
  }
}