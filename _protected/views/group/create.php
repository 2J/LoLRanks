<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Group */

$this->title = 'Create Group';
$this->params['breadcrumbs'] = [];
$this->params['breadcrumbs'][] = ['label' => 'My Groups', 'url' => ['mygroups']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="group-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
