<?php
use yii\helpers\Url;
/* @var $this yii\web\View */
//$this->title = Yii::t('app', Yii::$app->name);
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>LoL Ranks</h1>

        <p class="lead">League of Legends ranking system for groups, clans, and friends</p>

        <p>
        	<a class="btn btn-lg btn-success" href="<?= Url::to(['group/view', 'slug'=>'demo']) ?>">View Demo</a>&nbsp;
            <a class="btn btn-lg btn-success" href="<?= Url::to('/signup') ?>">Sign Up</a>
        </p>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-3">
                <h3>Track Solo Queue Stats</h3>

                <p>Add summoners to your group and track who is the highest rank.</p>

            </div>
            <div class="col-lg-3">
                <h3>Multiple Groups</h3>

                <p>Create a group for each of your league groups.</p>
                
            </div>
            <div class="col-lg-3">
                <h3>Tracks Username Changes</h3>

                <p>Once a summoner is added to a group, their username will be tracked. If you see an unfamiliar username, just hover over their name and see their old username.</p>
            </div>
            <div class="col-lg-3">
                <h3>Multiple Regions</h3>

                <p>Have friends across different regions? No problem! Summoners from multiple regions can be added to the same group.</p>
            </div>
        </div>

    </div>
</div>

