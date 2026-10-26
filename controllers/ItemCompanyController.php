<?php

namespace app\controllers;

use Yii;
use app\models\ItemCompany;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * إدارة الشركات المصنّعة للأصناف.
 */
class ItemCompanyController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'create', 'update'],
                        'roles' => ['createCategory', 'updateCategory'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['deleteCategory'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex($q = '')
    {
        $query = ItemCompany::find()
            ->select(['itemCompany.*', 'itemsCount' => '(SELECT COUNT(*) FROM category WHERE category.companyId = itemCompany.id)'])
            ->asArray();
        $q = trim(mb_substr((string) $q, 0, 100));
        if ($q !== '') {
            $query->andWhere(['like', 'name', $q]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['name' => SORT_ASC], 'attributes' => ['id', 'name']],
            'pagination' => ['pageSize' => 50],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'q' => $q,
        ]);
    }

    public function actionCreate()
    {
        $model = new ItemCompany();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'تم حفظ الشركة'));
            return $this->redirect(['index']);
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'تم حفظ الشركة'));
            return $this->redirect(['index']);
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->getCategories()->exists()) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'لا يمكن حذف شركة مرتبطة بأصناف'));
        } else {
            $model->delete();
        }

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = ItemCompany::findOne((int) $id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
