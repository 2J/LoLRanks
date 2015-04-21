<?php  
use yii\helpers\Url;
?>

<?php if(count($user->groups) > 0){ ?>
    <h1>My Groups</h1>
    <div class="row">
    <?php foreach($user->groups as $group){ ?>
        <div class="col-lg-4">
            <div class="list-group">
                <a href="<?= Url::to(['group/view','slug'=>$group->slug]) ?>" class="list-group-item">
                    <h4 class="list-group-item-heading"><?= substr(htmlspecialchars($group->name), 0, 20).((count(htmlspecialchars($group->name))>20)?"...":"") ?></h4>
                    <p class="list-group-item-text"><?= substr(htmlspecialchars($group->description), 0, 40).((count(htmlspecialchars($group->name))>40)?"...":"") ?></p>
                </a>
            </div>
        </div>
    <?php } ?>
    </div>
<?php }else{ ?>
	<h1>Getting Started</h1>
    <h3>1. Make a group</h3>
    <p>First, start by going to <a href="<?= Url::to('group/mygroups')  ?>">My Groups</a> and make a group!</p>
    
    <h3>2. Add users to your group</h3>
    <p>Go to your group and click on the "Add Summoners" button</p>
<?php } ?>