<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\RegionIndex;

/* @var $this yii\web\View */
/* @var $model app\Models\AddSummoner */
/* @var $form ActiveForm */

$this->title = 'Add Members: ' . ' ' . $group->name;
$this->params['breadcrumbs'][] = ['label' => 'Groups', 'url' => ['mygroups']];
$this->params['breadcrumbs'][] = ['label' => $group->name, 'url' => ['view', 'slug' => $group->slug]];
$this->params['breadcrumbs'][] = 'Add Members';
?>

<div class="addmember">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($addSummoner, 'region')->dropDownList(ArrayHelper::map(RegionIndex::find()->orderBy('description')->all(), 'code', 'description')) ?>
        <?= $form->field($addSummoner, 'usernames')->textarea(['placeholder'=>'Add up to 40 names at once, separated by commas'])->label('Summoner Names') ?>
    
        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- addmember -->