<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\web\View;

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
	$(this).removeClass( "btn-default" ).addClass( "btn-loading" );
		$.ajax({url: "' .Url::to(['update/group', 'group_id'=>$group_id]). '", dataType: "json", success: function(result){
			$(this).removeClass( "btn-loading" ).addClass( "btn-default" );
			if(result.success){
				alert(result.msg)
				location.reload();
			}
		}});
	', View::POS_READY, 'updateNoclick');
};

$this->registerJs('
$("#update-group-button").not(".btn-loading").on("click", function(){
	if($("#update-group-button").hasClass("btn-loading")) return false;
	$("#update-group-button").removeClass( "btn-default" ).addClass( "btn-loading" );
    $.ajax({url: "' .Url::to(['update/group', 'group_id'=>$group_id]). '", dataType: "json", success: function(result){
		$("#update-group-button").prop( "title" , "Updated Now" );
		$("#update-group-button").removeClass( "btn-loading" ).addClass( "btn-default" );
        alert(result.msg);
		if(result.success){
			location.reload();
		}
    }});
})
', View::POS_READY, 'update');
?>

<div class="group-view">
    <h1 class="text-center">
		<?= $model->name ?>
    </h1>
    <h3 class="text-center"><?= $model->description ?></h3>
	<div class="text-center">
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
			'level',
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
				 elseif($wlratio>=70) $wlcss="70";
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

</div>