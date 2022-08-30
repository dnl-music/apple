<?php

namespace backend\controllers;

use backend\models\Apple;
use backend\models\AppleException;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AppleController implements the CRUD actions for Apple model.
 */
class AppleController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Apple models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Apple::find(),
            /*
            'pagination' => [
                'pageSize' => 50
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
            */
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Apple model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionGenerate()
    {
        for($i = 0; $i < 10; $i++) {
            $model = new Apple();
            $model->color = ['green', 'yellow', 'red'][rand() % 3];
            $model->save();
        }

        return $this->redirect(['index']);
    }

    public function actionEat($id, $percent)
    {
        if(intval($percent) <= 0) {
            Yii::$app->session->setFlash('error', "Укажите процент");
            return $this->redirect(['index']);
        }
        $model = $this->findModel($id);
        try{
            $remain = $model->eat($percent);
        }
        catch(AppleException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['index']);
        }

        if($remain) {
            Yii::$app->session->setFlash('success', 'Яблоко успешно откушено');
        } else {
            Yii::$app->session->setFlash('success', 'Яблоко полностью съедено');
        }
        return $this->redirect(['index']);
    }

    public function actionFallToGround($id)
    {
        if($this->findModel($id)->fallToGround()) {
            Yii::$app->session->setFlash('success', 'Яблоко упало на землю');
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the Apple model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Apple the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Apple::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
