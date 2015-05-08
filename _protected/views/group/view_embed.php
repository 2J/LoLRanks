<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\web\View;

?>

<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
	<link rel="shortcut icon" href="favicon.ico"> 
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta property="og:site_name" content="LoL Ranks" />
    <meta property="og:url" content="Url::to(['group/view','slug'=>$model->slug],true)" />
    <meta property="og:title" content="LoL Ranks - <?= $model->name ?>" />
    <meta property="og:description" content="<?= $model->description ?>" />
    <meta property="og:image" content="http://lolranks.j2.io/images/lolranks.png" />
    
    <link href="<?= Yii::$app->homeUrl ?>themes/jhm/css/site.css" rel="stylesheet">
	<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet"></head>

    <title>LoL Ranks<?= ((count($this->title)>0)? " - ":"").Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>

<?php

/* @var $this yii\web\View */
/* @var $model app\models\Group */

$this->title = $model->name;
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

/*$this->registerJs('
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
', View::POS_READY, 'update');*/

?>

<div class="group-view">
    <h1 class="text-center">
	    <a href="<?= Url::to(['group/view','slug'=>$model->slug]) ?>" target=_blank>
		<?= $model->name ?>
    	</a>
    </h1>
    <h3 class="text-center"><?= $model->description ?></h3>

	<?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
		'summary'=>'Showing {begin}-{end} of {totalCount} Summoners',
		'options'=>['class'=>'ranking'],
		'rowOptions'=>function($model, $key, $index, $grid){
			return ['class'=>Yii::$app->params['tiers_rev'][$model->rank]];
		},
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
    		['attribute'=>'styled_name',
			 'contentOptions'=>function($model, $key, $index, $column){
				 if($model->pastUsername['changed']) return ['title'=>'Past Username: '.$model->pastUsername['old_name']];
				 else return [];
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
        ],
    ]); ?>

</div>
</body>