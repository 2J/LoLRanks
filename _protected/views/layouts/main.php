<?php
use app\assets\AppAsset;
use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <link rel="shortcut icon" href="/favicon.ico"> 
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta property="og:site_name" content="LoL Ranks" />
    <meta property="og:url" content="http://lolranks.j2.io" />
    <meta property="og:title" content="LoL Ranks" />
    <meta property="og:description" content="League of Legends ranking system for groups, clans, and friends" />
    <meta property="og:image" content="http://lolranks.j2.io/images/lolranks.png" />
    <?= Html::csrfMetaTags() ?>
    <title>LoL Ranks<?= ((count($this->title)>0)? " - ":"").Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
    <?php $this->beginBody() ?>
    <div class="wrap">
        <?php
            NavBar::begin([
                'brandLabel' => Yii::t('app', Yii::$app->name),
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-default navbar-fixed-top',
                ],
            ]);

			$menuItems[] = ['label' => Yii::t('app', 'Home'), 'url' => ['/']];
			$menuItems[] = ['label' => Yii::t('app', 'About'), 'url' => ['/site/about']];
			$menuItems[] = ['label' => Yii::t('app', 'Contact'), 'url' => ['/site/contact']];

            if (Yii::$app->user->isGuest) {
//                $menuItems[] = ['label' => Yii::t('app', 'Home'), 'url' => ['/site/index']];
            }else{
                $menuItems[] = ['label' => Yii::t('app', 'My Groups'), 'url' => ['/group/mygroups']];
			}

            // display Users to admin+ roles
            if (Yii::$app->user->can('admin'))
            {
				$menuItems[] = ['label' => Yii::t('app', 'All Groups'), 'url' => ['/group/index']];
                $menuItems[] = ['label' => Yii::t('app', 'Users'), 'url' => ['/user/index']];
            }
            
            // display Signup and Login pages to guests of the site
            if (Yii::$app->user->isGuest) 
            {
                $menuItems[] = ['label' => Yii::t('app', 'Signup'), 'url' => ['/site/signup']];
                $menuItems[] = ['label' => Yii::t('app', 'Login'), 'url' => ['/site/login']];
            }
            // display Logout to all logged in users
            else 
            {
				$menuItems[] = ['label' => Yii::t('app', 'My Account'), 'url' => ['/site/account']];
                $menuItems[] = [
                    'label' => Yii::t('app', 'Logout'). ' (' . Yii::$app->user->identity->username . ')',
                    'url' => ['/site/logout'],
                    'linkOptions' => ['data-method' => 'post']
                ];
            }

            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => $menuItems,
            ]);

            NavBar::end();
        ?>

        <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
        <p class="pull-left">&copy; Koggiri <?= date('Y') ?></p>
        <p class="pull-right">
	        <a href="http://j2.io" title="Website"><i class="fa fa-globe"></i></a>&nbsp;
        	<a href="http://www.facebook.com/JHM.JJ" title="Facebook"><i class="fa fa-facebook"></i></a>&nbsp;
            <a href="http://twitter.com/JeongHM" title="Twitter"><i class="fa fa-twitter"></i></a>&nbsp;
        	<a href="http://imraising.tv/u/koggiri" title="Donate"><i class="fa fa-dollar"></i></a>
		</p>
        </div>
    </footer>

    <?php $this->endBody() ?>
</body>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-61917183-3', 'auto');
  ga('send', 'pageview');

</script>
</html>
<?php $this->endPage() ?>
