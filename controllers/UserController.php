<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\PasswordForm;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use app\models\AuthItem;
use app\models\AuthAssignment;
use Exception;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    /**
     * @inheritdoc
     */
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
                        'actions' => ['index'],
                        'roles' => ['indexUsers'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'view'],
                        'roles' => ['createUsers'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'view'],
                        'roles' => ['updateUsers'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['deleteUsers'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['changepassword'],
                        'roles' => ['changePassword'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {

        if (Yii::$app->user->isGuest) {

            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        } else {

            $searchModel = new UserSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
    }
    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $user = User::find()->where(['id' => $id])->one();
        return $this->render('view', [

            'model' => $user, //$this->findModel($id),
        ]);
    }

    public function actionChangepassword()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }

        $model = new PasswordForm();

        $modeluser = User::find()
            ->where(['username' => Yii::$app->user->identity->username])
            ->one();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            try {

                $modeluser->setPassword($model->newpass);
                if ($modeluser->hasAttribute('authKey')) {
                    $modeluser->authKey = Yii::$app->security->generateRandomString(32); // يُبطل جلسات "تذكّرني" القديمة
                }

                if ($modeluser->save()) {

                    Yii::$app->session->setFlash(
                        'success',
                        'تم تعديل كلمة السر الخاصة بك بنجاح'
                    );

                    return $this->redirect([
                        'view',
                        'id' => $modeluser->id
                    ]);
                } else {

                    Yii::$app->session->setFlash(
                        'error',
                        'لم يتم تعديل كلمة السر الخاصة بك، الرجاء إعادة المحاولة'
                    );

                    return $this->redirect([
                        'view',
                        'id' => $modeluser->id
                    ]);
                }
            } catch (\Exception $e) {

                Yii::$app->session->setFlash(
                    'error',
                    $e->getMessage()
                );

                return $this->render('changepassword', [
                    'model' => $model
                ]);
            }
        }

        return $this->render('changepassword', [
            'model' => $model
        ]);
    }
    public function actionPrint()
    {
        $user = User::find()->all();

        return $this->render('print', ['users' =>  $user]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();

        if ($model->load(Yii::$app->request->post())) {

            $exist = User::find()->where(['username' => $model->username])->one();

            if ($exist != null && $exist->username == $model->username) {
                Yii::$app->session->setFlash('error', Yii::t('app', "User Name Alrady Exsist"));
                return $this->render('create', ['model' => $model]);
            }

            $model->createedDate = date('y-m-d');
            $plainPassword = $model->password;
            $model->setPassword($model->password);

            $path = "img/users";
            if (!is_dir($path)) {
                mkdir($path);
            }

            // التحقق من الصورة (الامتداد/نوع الملف/الحجم) قبل حفظها على القرص
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->file !== null && !$model->validate(['file'])) {
                Yii::$app->session->setFlash('error', implode(' ', $model->getErrors('file')));
                $model->password = $plainPassword;
                return $this->render('create', ['model' => $model]);
            }

            $clientIds = $model->client;
            if (is_array($model->client)) {
                $model->client = implode(",", $model->client);
            }

            $authItem = AuthItem::find()->where(['type' => $model->permission])->one();
            if ($authItem === null) {
                Yii::$app->session->setFlash('error', 'نوع الصلاحية المختار غير موجود');
                $model->password = $plainPassword;
                $model->client = $clientIds;
                return $this->render('create', ['model' => $model]);
            }

            if ($model->file !== null) {
                $fileName = $path . '/' . uniqid() . '.' . strtolower($model->file->extension);
                $model->file->saveAs($fileName, false);
                $model->path = $fileName;
            }

            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$model->save()) {
                    throw new Exception(implode(' ', $model->getErrorSummary(true)));
                }
                $AuthAssignment = new AuthAssignment();
                $AuthAssignment->item_name = $authItem->name;
                $AuthAssignment->user_id = $model->id;
                $AuthAssignment->save(false);
                $transaction->commit();
            } catch (Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage());
                $model->isNewRecord = true;
                $model->id = null;
                $model->password = $plainPassword;
                $model->client = $clientIds;
                return $this->render('create', ['model' => $model]);
            }

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $path = "img/users";
            if (!is_dir($path)) {
                mkdir($path);
            }

            // التحقق من الصورة قبل حفظها على القرص
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->file !== null && !$model->validate(['file'])) {
                Yii::$app->session->setFlash('error', implode(' ', $model->getErrors('file')));
                return $this->render('update', ['model' => $model]);
            }

            $clientIds = $model->client;
            if (is_array($model->client)) {
                $model->client = implode(",", $model->client);
            }

            $authItem = AuthItem::find()->where(['type' => $model->permission])->one();
            if ($authItem === null) {
                Yii::$app->session->setFlash('error', 'نوع الصلاحية المختار غير موجود');
                $model->client = $clientIds;
                return $this->render('update', ['model' => $model]);
            }

            if ($model->file !== null) {
                $fileName = $path . '/' . uniqid() . '.' . strtolower($model->file->extension);
                $model->file->saveAs($fileName, false);
                $model->path = $fileName;
            }

            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$model->save()) {
                    throw new Exception(implode(' ', $model->getErrorSummary(true)));
                }
                Yii::$app->db->createCommand()->delete('auth_assignment', ['user_id' => $id])->execute();

                $AuthAssignment = new AuthAssignment();
                $AuthAssignment->item_name = $authItem->name;
                $AuthAssignment->user_id = $id;
                $AuthAssignment->save(false);
                $transaction->commit();
            } catch (Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage());
                $model->client = $clientIds;
                return $this->render('update', ['model' => $model]);
            }

            return $this->redirect(['view', 'id' => $model->id]);
        }

        if ($model->client != null) {
            $model->client = explode(",", $model->client);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if ((int)$id === (int)Yii::$app->user->id) {
            Yii::$app->session->setFlash('error', 'لا يمكنك حذف حسابك الحالي');
            return $this->redirect(['index']);
        }
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
}
