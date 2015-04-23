<?php

use yii\helpers\Html;
use yii\helpers\Url;


/* @var $this yii\web\View */



$this->title = Yii::t('app', 'About');

//$this->params['breadcrumbs'][] = $this->title;

?>

<div class="site-about">

<h1 id="about">About</h1>
<p>LoL Ranks allows you to make custom rankings like <a href="http://lolranks.j2.io/groups/demo">this</a> of your group, clan, or friends. It tracks your group list and displays a sortable table that you can browse through and see how others are doing. The primary goal for this application is to be used by groups, clans, and friends to check how everybody in your group is doing.</p>
<p>LoL Ranks is currently in Beta and have not officially launched yet. Bug and feature reports can be tweeted to <a href="http://twitter.com/LoLRanks">@LoLRanks</a> or sent through the <a href="http://lolranks.j2.io/contact">contact page</a>.</p>
<p>This application was made by Koggiri. Feel free to find me in social media: <a href="http://twitter.com/JeongHM"><i class="fa fa-twitter"></i> Twitter</a> | <a href="http://facebook.com/JHM.JJ"><i class="fa fa-facebook-official"></i> Facebook</a> | <a href="http://twitch.tv/koggiri"><i class="fa fa-twitch"></i> Twitch</a> | <a href="http://youtube.com/OsuLoL"><i class="fa fa-youtube-square"></i> Youtube</a></p>
<h1 id="features">Features</h1>
<ul>
<li>Create multiple groups</li>
<li>Secure sign up / login</li>
<li>Sort by name, rank, level, and win/loss ratio</li>
<li>Add multiple summoners at once</li>
<li>Add summoners from different regions into the same ranking</li>
<li>color coded rank and win/loss ratio</li>
<li>Track username changes once people are added to a group</li>
<li>Automatic group updates + manual update button</li>
</ul>
<h1 id="features-in-development">Features in development</h1>
<ul>
<li>Follow / favorite groups</li>
<li>Show usernames that were successfully added</li>
<li>Export ranking as markdown (for use in websites such as Reddit)</li>
<li>Option to track queues other than solo queue</li>
<li>Add editors that can also edit your ranking</li>
<li>Private rankings</li>
</ul>
<h1 id="instructions">Instructions</h1>
<ol>
<li>Once logged in, start by going to <a href="<?= Url::to('group/mygroups')  ?>">My Groups</a> and make a group.</li>
<li>Add users to your group and share your link to your friends to see where they are ranked!</li>
</ol>
<p><strong>Thank you for using my website!</strong></p>
<p><em>LoL Ranks</em> isn't endorsed by Riot Games and doesn't reflect the views or opinions of Riot Games or anyone officially involved in producing or managing League of Legends. League of Legends and Riot Games are trademarks or registered trademarks of Riot Games, Inc. League of Legends © Riot Games, Inc.</p>
</div>

