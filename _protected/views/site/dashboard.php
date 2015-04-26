<?php  

use yii\helpers\Url;

?>

<h1>Favorites</h1>

<?php if(count($user->favorites) > 0){ ?>

    <div class="row">

    <?php foreach($user->favorites as $group){ ?>

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

    <p>You do not have any favorites yet. <br />Go to a group and press <i class="fa fa-star-o"></i> to add to favorites.</p>

<?php } ?>



<h1>My Groups</h1>

<?php if(count($user->groups) > 0){ ?>

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

    <p>You have not made any groups yet.</p>

    <h4>1. Make a group</h4>

    <p>First, start by going to <a href="<?= Url::to('group/mygroups')  ?>">My Groups</a> and make a group!</p>

    <h4>2. Add users to your group</h4>

    <p>Go to your group and click on the "Add Summoners" button</p>

<?php } ?>