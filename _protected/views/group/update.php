<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Group */

$this->title = 'Update Group: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Groups', 'url' => ['mygroups']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'slug' => $model->slug]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="group-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
