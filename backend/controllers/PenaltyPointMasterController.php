<?php

namespace backend\controllers;

use Yii;
use backend\models\PenaltyPointMaster;
use backend\models\PenaltyPointMasterSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PenaltyPointMasterController implements the CRUD actions for PenaltyPointMaster model.
 */
class PenaltyPointMasterController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all PenaltyPointMaster models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new PenaltyPointMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PenaltyPointMaster model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findMongoDbModel($id),
        ]);
    }

    /**
     * Creates a new PenaltyPointMaster model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new PenaltyPointMaster();
        $model->isNewRecord = true;
        if ($model->saveToMongoDb(Yii::$app->request->post())) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing PenaltyPointMaster model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findMongoDbModel($id);
        $model->isNewRecord = false;
        if ($model->updateToMongoDb($id, Yii::$app->request->post())) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing PenaltyPointMaster model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        if (PenaltyPointMaster::deleteFromMongoDb($id))
            return $this->redirect(['index']);
    }

    /**
     * Finds the PenaltyPointMaster model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PenaltyPointMaster the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = PenaltyPointMaster::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the PenaltyPointMaster model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PenaltyPointMaster the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findMongoDbModel($id) {
        if (($model = PenaltyPointMaster::mongoDbFindOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
