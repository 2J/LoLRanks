<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model app\models\Group */

$this->title = $model->name;
$this->og_title = 'LoL Ranks - '.$model->name;
$this->og_url = Url::to(['group/view','slug'=>$model->slug],true);
$this->og_description = $model->description;
/*if($model->isOwner()){
	$this->params['breadcrumbs'][] = ['label' => 'Groups', 'url' => ['mygroups']];
	$this->params['breadcrumbs'][] = $this->title;
}*/
global $group_id;
$group_id = $model->id;

if($update_group){
	$this->registerJs('
	$("#update-group-button").removeClass( "btn-default" ).addClass( "btn-loading" );
		$.ajax({url: "' .Url::to(['update/group', 'group_id'=>$group_id]). '", dataType: "json", success: function(result){
			if(result.success){
				$("#update-group-button").html(result.msg);
				$.pjax.reload({container:"#rankingtable"});
			}else{
				$("#update-group-button").html(result.msg);
				$.pjax.reload({container:"#rankingtable"});
			}
		}});
	', View::POS_READY, 'updateNoclick');
};

$this->registerJs('
$("#update-group-button").not(".btn-loading").on("click", function(){
	if($("#update-group-button").hasClass("btn-loading")) return false;
	$("#update-group-button").removeClass( "btn-default" ).addClass( "btn-loading" );
    $.ajax({url: "' .Url::to(['update/group', 'group_id'=>$group_id]). '", dataType: "json", success: function(result){
		if(result.success){
			$("#update-group-button").html(result.msg);
			$.pjax.reload({container:"#rankingtable"});
		}else{
			$("#update-group-button").html(result.msg);
			$.pjax.reload({container:"#rankingtable"});
		}
    }});
})
', View::POS_READY, 'update');

$this->registerJs('
$("#favorite-button").on("click", function(){
	if($("#favorite-button").hasClass("loading")) return false;
	unfavorite = $("#favorite-button").hasClass("favorited");
    $.ajax({url: "' .Url::to(['group/favorite', 'group_id'=>$group_id]). '&unfavorite="+unfavorite, dataType: "json", success: function(result){
		if(result){
			$("#favorite-button").removeClass("not_favorited").addClass("favorited");
		}else{
			$("#favorite-button").removeClass("favorited").addClass("not_favorited");
		}
    }});
})
', View::POS_READY, 'favorite');

$this->registerJs('
$(".share-popup").click(function(){
    var window_size = "";
    var url = this.href;
    var domain = url.split("/")[2];
    switch(domain) {
        case "www.facebook.com":
            window_size = "width=585,height=368";
            break;
        case "www.twitter.com":
            window_size = "width=585,height=261";
            break;
        default:
            window_size = "width=585,height=511";
    }
    window.open(url, "", "menubar=no,toolbar=no,resizable=yes,scrollbars=yes," + window_size);
    return false;
});
', View::POS_READY, 'share');
?>

<div class="group-view">
    <h1 class="text-center">
	    <a href="<?= Url::to(['group/view','slug'=>$model->slug], true) ?>">
			<?= $model->name ?>
    	</a>
    </h1>
    <h3 class="text-center"><?= $model->description ?></h3>
	<div class="text-center">
	    <div class="inline updated-ago">&nbsp;Updated <?= $updated_ago ?> ago&nbsp;</div><br />
        
    	<?php if(!Yii::$app->user->isGuest){ ?>
            <div id="favorite-button" class="btn btn-primary btn-sm favorite-button <?= ($model->favorited)? "favorited":"not_favorited" ?>">
                <div class="fav-on"><i class="fa fa-star"></i></div>
                <div class="fav-off"><i class="fa fa-star-o"></i></div>
            </div>
        <?php } ?>
        
		<div id="update-group-button" class="btn btn-default btn-sm" title="Updated <?= $updated_ago ?> ago">
        	<div class="hidden-loading">Update Group</div>
        	<div class="hidden-default">Updating &nbsp; <i class="fa fa-refresh fa-spin"></i></div>
        </div>
    </div>
	<?php if($model->isOwner()){ ?>
	    <br /><div class="text-center">
			<?= Html::a('<i class="fa fa-pencil"></i>', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Add Summoners', ['addmember', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
        </div>
        <br />
    <?php } ?>
    
    <?php if(!$model->isOwner()){ ?>
		<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <!-- lolranks_responsive -->
        <ins class="adsbygoogle"
             style="display:block"
             data-ad-client="ca-pub-4614603456200884"
             data-ad-slot="8005620256"
             data-ad-format="auto"></ins>
        <script>
        (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
    <?php } ?>
    
	<?php \yii\widgets\Pjax::begin(); ?>
	<?= GridView::widget([
        'dataProvider' => $dataProvider,
		'id'=>'rankingtable',
//        'filterModel' => $searchModel,
		'summary'=>'Showing {begin}-{end} of {totalCount} Summoners',
		'emptyText'=>$model->isOwner()? 'No Summoners found. '.Html::a('Add Summoners', ['addmember', 'id' => $model->id]):"No Summoners",
		'options'=>['class'=>'ranking'],
		'rowOptions'=>function($model, $key, $index, $grid){
			return ['class'=>Yii::$app->params['tiers_rev'][$model->rank]];
		},
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
    		['attribute'=>'styled_name',
			 'content'=>function ($model, $key, $index, $column){
				 $data = $model->styled_name;
				 if($model->pastUsername['changed']) $data.= ' &nbsp;&nbsp;<div class="inline recently-changed">Past: '.$model->pastUsername['old_name'].'</div>' ;
				 return $data;
			 }
			],
			['attribute'=>'regionDesc', 'visible'=>$show_region],
			'attribute'=>'fullrank',
			['attribute'=>'level', 'visible'=>$model->has_low],
			['attribute'=>'wlratio',
			 'content'=>function ($model, $key, $index, $column){
				 return $model->getWlratio(false);
			 },
			 'contentOptions'=>function($model, $key, $index, $column){
				 //get win/loss ratio
				 $wlratio=0;
				 if($model->total==0) $wlratio = '';
				 else $wlratio = intval(100 * $model->wins / ($model->wins + $model->losses));
				 
				 //get css accordingly
				 $wlcss = '';
				 if($wlratio=='') $wlcss="UR";
				 elseif($wlratio>=65) $wlcss="65";
				 elseif($wlratio>=60) $wlcss="60";
				 elseif($wlratio>=55) $wlcss="55";
				 elseif($wlratio>=50) $wlcss="50";
				 elseif($wlratio>=45) $wlcss="45";
				 elseif($wlratio>=40) $wlcss="40";
				 elseif($wlratio>=30) $wlcss="30";
				 elseif($wlratio>=0) $wlcss="0";
				 
				 return ['class'=>'wl-'.$wlcss];
			 },
			],
            ['class' => 'yii\grid\ActionColumn', 
			 'visible'=>$model->isOwner(),
		 	 'template'=>'{delete}',
			 'buttons'=>[
			 	'delete'=>function($url, $model, $key){
					global $group_id;
					return Html::a('<span class="glyphicon glyphicon-trash"></span>', 
						Url::to(['group/deletesummoner','group_id' => $group_id, 'region'=>$model->region, 'lolid'=>$model->lolid]),
						['data-method'=>'post', 'data-confirm'=>'Are you sure you want to delete this summoner?', 'data-pjax'=>'0']);
				}
			 ]
			],
        ],
    ]); ?>
	<?php \yii\widgets\Pjax::end(); ?>
</div>

<div class="row">
	<div class="col-md-12 text-right">
    	<div class="inline">Share: </div>&nbsp;
        <div class="inline">
        
        <a href="http://www.facebook.com/sharer/sharer.php?u=<?= Url::to(['group/view', 'slug'=>$model->slug],true) ?>&t=<?= $model->name ?>" target="_blank" class="share-popup"><div class="inline btn btn-default btn-xs text-center share-btn btn-facebook"><i class="fa fa-facebook"></i></div></a>
        
        <a href="http://www.twitter.com/intent/tweet?url=<?= Url::to(['group/view', 'slug'=>$model->slug],true) ?>&via=LoLRanks&text=<?= $model->name ?> - <?= $model->description ?> " target="_blank" class="share-popup"><div class="inline btn btn-default btn-xs text-center share-btn btn-twitter"><i class="fa fa-twitter"></i></div></a>
        
        </div>
        
		<div class="inline btn btn-default btn-xs" id="embed_btn" onclick="$(this).hide();$('#share_permalink').show();">Permalink</div>
        <div class="inline-block" id="share_permalink" style="display:none;">
        	Permalink: 
	        <input id="embed_code" type="text" readonly onClick="this.select();" value="<?= Url::to(['group/view','slug'=>$model->slug], true) ?>"></input>
        </div>
        
		<div class="inline btn btn-default btn-xs" id="embed_btn" onclick="$(this).hide();$('#embed_instructions').show();">Embed</div>
        <div class="inline-block" id="embed_instructions" style="display:none;">
        	Copy and paste this html code to embed: 
	        <input id="embed_code" type="text" readonly onClick="this.select();" value="<iframe src=&quot;<?= Url::to(['group/view','slug'=>$model->slug, 'embed'=>true], true) ?>&quot; style=&quot;width:500px;height:400px;border:0;&quot;>Loading Ranking...</iframe>"></input>
        </div>
        
    </div>
</div>


<?php if(!$model->isOwner()){ ?>
    <div class="row">
		<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <!-- lolranks_responsive -->
        <ins class="adsbygoogle"
             style="display:block"
             data-ad-client="ca-pub-4614603456200884"
             data-ad-slot="8005620256"
             data-ad-format="auto"></ins>
        <script>
        (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
    </div>
<?php } ?>