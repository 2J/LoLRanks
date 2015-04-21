<?php
use app\rbac\models\AuthItem;
use nenad\passwordStrength\PasswordInput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $user app\models\User */
/* @var $form yii\widgets\ActiveForm */
/* @var $role app\rbac\models\Role; */
?>
<div class="user-form">
	<h1>My Account</h1>
    <?php $form = ActiveForm::begin(['id' => 'form-user']); $form->enableAjaxValidation = true; ?>

        <?= $form->field($user, 'username')->textInput(['readonly' => 'true']) ?>
        
        <?= $form->field($user, 'email') ?>

        <?php if ($user->scenario === 'account'): ?>
            <?= $form->field($user, 'password_old')->passwordInput() ?>
        <?php endif ?>

        <?php if ($user->scenario === 'create'): ?>
            <?= $form->field($user, 'password')->passwordInput() ?>
        <?php else: ?>
            <?= $form->field($user, 'password')->passwordInput(['placeholder' => Yii::t('app', 'New password (optional)')]) ?>       
        <?php endif ?>
        
        <?php if ($user->scenario === 'create'): ?>
            <?= $form->field($user, 'password_confirm')->passwordInput() ?>
        <?php else: ?>
            <?= $form->field($user, 'password_confirm')->passwordInput(['placeholder' => Yii::t('app', 'Confirm password (optional)')]) ?>       
        <?php endif ?>

    <div class="form-group">     
        <?= Html::submitButton($user->isNewRecord ? Yii::t('app', 'Create') 
            : Yii::t('app', 'Update'), ['class' => $user->isNewRecord 
            ? 'btn btn-success' : 'btn btn-primary']) ?>

        <?= Html::a(Yii::t('app', 'Cancel'), ['user/index'], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>
 
</div>
