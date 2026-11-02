<?php

namespace app\controllers;

use Yii;
use app\models\SalesAudit;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

/**
 * مراجعة المدير لتعديلات فواتير المبيعات بعد حفظها (تحويل إلى آجل، تغيير المبلغ أو الزبون...).
 */
class SalesAuditController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['review' => ['POST'], 'review-all' => ['POST']],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'review', 'review-all'],
                        'roles' => ['companyInfo'],
                    ],
                ],
            ],
        ];
    }

    /** $all = 1 يعرض المراجَع أيضاً */
    public function actionIndex($all = 0)
    {
        $query = SalesAudit::find()->with(['user', 'sales'])->orderBy(['id' => SORT_DESC]);
        if (!(int) $all) {
            $query->where(['reviewed' => 0]);
        }

        return $this->render('index', [
            'dataProvider' => new ActiveDataProvider(['query' => $query, 'pagination' => ['pageSize' => 100], 'sort' => false]),
            'all' => (int) $all,
        ]);
    }

    /** تمت مراجعة كل تعديلات فاتورة واحدة */
    public function actionReview($salesId)
    {
        SalesAudit::updateAll(
            ['reviewed' => 1, 'reviewed_by' => Yii::$app->user->id, 'reviewed_at' => date('Y-m-d H:i:s')],
            ['salesId' => (int) $salesId, 'reviewed' => 0]
        );
        return $this->redirect(Yii::$app->request->referrer ?: ['index']);
    }

    public function actionReviewAll()
    {
        SalesAudit::updateAll(
            ['reviewed' => 1, 'reviewed_by' => Yii::$app->user->id, 'reviewed_at' => date('Y-m-d H:i:s')],
            ['reviewed' => 0]
        );
        return $this->redirect(['index']);
    }
}
