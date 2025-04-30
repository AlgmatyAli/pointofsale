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

            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        } else {

            $model = new PasswordForm;
            $modeluser = User::find()->where([
                'username' => Yii::$app->user->identity->username
            ])->one();

            if ($model->load(Yii::$app->request->post())) {
                if ($model->validate()) {
                    try {
                        $modeluser->password = md5($_POST['PasswordForm']['newpass']);
                        if ($modeluser->save()) {
                            echo '<script type="text/javascript"> alert(\'تم تعديل كلمة السر الخاصة بك بنجاح\');
                        window.location.href="index";
                        </script>';
                        } else {
                            echo '<script type="text/javascript"> alert(\'لم يتم تعديل كلمة السر الخاصة بك الرجاء اعادة المحاولة\');
                        window.location.href="index.php";
                        </script>';
                        }
                    } catch (Exception $e) {
                        Yii::$app->getSession()->setFlash(
                            'error',
                            "{$e->getMessage()}"
                        );
                        return $this->render('changepassword', [
                            'model' => $model
                        ]);
                    }
                } else {
                    return $this->render('changepassword', [
                        'model' => $model
                    ]);
                }
            } else {
                return $this->render('changepassword', [
                    'model' => $model
                ]);
            }
        }
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
            } else {
                $model->createedDate = date('y-m-d');
                // $orignPass = $model->password;
                $pass = md5($model->password);
                $model->password = $pass;
                if (!is_dir("img/users")) {
                    mkdir("img/users");
                    $path = "img/users";
                } else {
                    $path = "img/users";
                }
                $model->file = UploadedFile::getInstance($model, 'file');
                $ext = substr(strrchr($model->file, '.'), 1);
                if ($ext != null) {
                    $uniqid = uniqid();
                    $model->file->saveAs($path . '/' . $uniqid . '.' . $model->file->extension);
                    $model->path = $path . '/' . $uniqid . '.' . $model->file->extension;
                }
                if ($model->client != null) {
                    $model->client = implode(",", $model->client);
                }
                if ($model->save()) {
                    //=========================
                    $user_type = $model->permission;
                    $authItem = AuthItem::find()->where(['type' => $user_type])->One();
                    $AuthAssignment = new AuthAssignment();
                    $AuthAssignment->item_name = $authItem->name;
                    $AuthAssignment->user_id = $model->id;
                    $AuthAssignment->save(false);
                    //=========================
                    // Yii::$app->mailer->compose()
                    // ->setTo($model->email)
                    // ->setFrom('algmatyali@gmail.com')
                    // ->setSubject('كلمة السر الخاصة بك')
                    // ->setTextBody(' السيد الفاضل :'.$model->username.' كلمة السر الخاصة بك هي '.'  '.$orignPass)
                    // ->send();   
                }
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = User::find()->where(['id' => $id])->one();

        if ($model->load(Yii::$app->request->post())) {
            if (!is_dir("img/users")) {
                mkdir("img/users");
                $path = "img/users";
            } else {
                $path = "img/users";
            }
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->file != null) {
                $ext = substr(strrchr($model->file, '.'), 1);
                if ($ext != null) {
                    $uniqid = uniqid();
                    $model->file->saveAs($path . '/' . $uniqid . '.' . $model->file->extension);
                    $model->path = $path . '/' . $uniqid . '.' . $model->file->extension;
                }
            }
            if ($model->client != null) {
                $model->client = implode(",", $model->client);
            }

            $model->save();
            //=========================
            $user_type = $model->permission;
            $authItem = AuthItem::find()->where(['type' => $user_type])->One();
            Yii::$app->db->createCommand()->delete('auth_assignment', ['user_id' => $id])->execute();

            $AuthAssignment = new AuthAssignment();
            $AuthAssignment->item_name = $authItem->name;
            $AuthAssignment->user_id = $id;
            $AuthAssignment->save(false);
            //=========================

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            if ($model->client != null) {
                $model->client = explode(",", $model->client);
            }
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
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
