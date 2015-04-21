<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Groups';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="group-index">

    <h1><?= Html::encode($this->title) ?> <?= Html::a('Create Group', ['create'], ['class' => 'btn btn-success']) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
		'summary'=>'',
        'columns' => [
            'name',
            'description',
			'slug',
			['attribute'=>'user_id', 
			 'format'=>'text',
			 'label'=>'USER',
			 'value'=>function ($model, $key, $index, $column){
				 return $model->user->username;
			 },
			 'visible'=>Yii::$app->user->can('theCreator'),
			],
            ['class' => 'yii\grid\ActionColumn',
			 'template'=>'{view}{update}{delete}',
			 'buttons'=>[
			 	'view'=>function($url, $model, $key){
					return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['view','slug'=>$model->slug]));
				}
			]],
        ],
    ]); ?>

</div>
