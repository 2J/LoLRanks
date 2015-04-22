<?php

namespace app\controllers;

use Yii;
use app\models\Group;
use app\models\AddSummoner;
use app\models\GroupAssignment;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Expression;

/**
 * GroupController implements the CRUD actions for Group model.
 */
class GroupController extends Controller
{
	public $defaultAction = 'dashboard';
	
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
				'rules' => [
                    [
                        'actions' => ['index', 'view', 'dashboard', 'mygroups', 'create', 'update', 'delete', 'addmember', 'deletesummoner'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                    [
                        'actions' => ['dashboard', 'create', 'update', 'mygroups', 'delete', 'view', 'addmember', 'deletesummoner'],
                        'allow' => true,
                        'roles' => ['member'],
                    ],
                    [
                        'actions' => ['view'],
                        'allow' => true
                    ],
				],
            ], // access
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
					'deletesummoner' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Group models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Group::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
			'user_id'=>$userId = Yii::$app->user->identity->id,
        ]);
    }
	
	public function actionMygroups(){
        $dataProvider = new ActiveDataProvider([
            'query' => Group::find()->where(['user_id'=>Yii::$app->user->id]),
        ]);
		
        return $this->render('index', [
            'dataProvider' => $dataProvider,
			'user_id'=> Yii::$app->user->id,
        ]);
	}
	
	public function actionAddmember($id){
		$group = $this->findModel($id);
		if(!$group->isOwner())throw new \yii\web\HttpException(403, 'You are not allowed to perform this action.', 405);
		
		$addSummoner = new AddSummoner();
		
		if (Yii::$app->request->isAjax && $addSummoner->load($_POST))
		{
			Yii::$app->response->format = 'json';
			return \yii\widgets\ActiveForm::validate($addSummoner);
		}
		
        if ($addSummoner->load(Yii::$app->request->post())) {
			$addSummoner->group_id = $group->id;
			
			if($addSummoner->validate()){
				$addSummoner->addUsers();
				//TODO: FLASH SUCCESSFUL USERNAMES
	            return $this->redirect(['view', 'slug' => $group->slug]);
			}else{
				return $this->render('addSummoner', [
					'addSummoner' => $addSummoner,
					'group'=>$group,
				]);
			}
        } else {
            return $this->render('addSummoner', [
                'addSummoner' => $addSummoner,
				'group'=>$group,
            ]);
        }
	}

    /**
     * Displays a single Group model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($slug, $sort='rank')
    {
        if (($model =Group::find()->where(['slug'=>$slug])->one()) == null) {
            throw new NotFoundHttpException('The requested page does not exist');
        }
		
		$timestamp = \Yii::$app->db->createCommand('SELECT CURRENT_TIMESTAMP as timestamp')->queryOne()['timestamp'];
		$update_group = false;
		if(($time_since = (strtotime($timestamp) - strtotime($model->last_visit))) > 86400){
			Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Group is updating.'));
			$update_group = true;
		}
		
        $dataProvider = new ArrayDataProvider([
			'allModels' => $model->summoners,
			'sort' => [
				'defaultOrder' => ['fullrank' => SORT_DESC],
				'attributes' => [
					'fullrank' => [
						'asc' => ['rank' => SORT_ASC, 'division' => SORT_DESC, 'lp' => SORT_ASC, 'level' => SORT_ASC],
						'desc' => ['rank' => SORT_DESC, 'division' => SORT_ASC, 'lp' => SORT_DESC, 'level' => SORT_DESC],
						'default' => SORT_DESC,
						'label' => 'Rank',
					], 
					'regionDesc',
					'styled_name', 
					'level', 
					'wins', 
					'losses',
					'total',
				],
			],
			'pagination' => [
				'pageSize' => 200,
			],
		]);
		
		$num_regions = (new \yii\db\Query())
			->select(['count(DISTINCT(region)) as regcount'])
			->from('group_assignment')
			->where(['group_id'=>$model->id])
			->one()['regcount'];
		$show_region = $num_regions > 1;
		
        return $this->render('view', [
            'model' => $model,
			'dataProvider'=>$dataProvider,
			'show_region'=>$show_region,
			'update_group'=>$update_group,
			'updated_ago'=>Yii::$app->GenericFunctions->TimeSince($time_since),
        ]);
    }
	
	public function actionDeletesummoner($group_id, $region, $lolid){
		$group = $this->findModel($group_id);
		if(!$group->isOwner())throw new \yii\web\HttpException(403, 'You are not allowed to perform this action.', 405);
		GroupAssignment::find()->where(['group_id'=>$group_id, 'region'=>$region, 'summoner_id'=>$lolid])->one()->delete();
        return $this->redirect(['view', 'slug'=>$group->slug]);
	}

    /**
     * Creates a new Group model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Group();
		
		if (Yii::$app->request->isAjax && $model->load($_POST))
		{
			Yii::$app->response->format = 'json';
			return \yii\widgets\ActiveForm::validate($model);
		}

        if ($model->load(Yii::$app->request->post())) {
			$model->user_id = Yii::$app->user->identity->id;
			if(empty($model->slug)){
				$model->slug = $model->randomSlug();
			}
			if($model->validate()){
				$model->save();
	            return $this->redirect(['view', 'slug' => $model->slug]);
			}else{
				return $this->render('create', [
					'model' => $model,
				]);
			}
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Group model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
		
		if(!$model->isOwner())throw new \yii\web\HttpException(403, 'You are not allowed to perform this action.', 405);
		
		if (Yii::$app->request->isAjax && $model->load($_POST))
		{
			Yii::$app->response->format = 'json';
			return \yii\widgets\ActiveForm::validate($model);
		}

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'slug' => $model->slug]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Group model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

		if($model->isOwner()) $model->delete();
		else throw new \yii\web\HttpException(403, 'You are not allowed to perform this action.', 405);

        return $this->redirect(['mygroups']);
    }

    /**
     * Finds the Group model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Group the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Group::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}

