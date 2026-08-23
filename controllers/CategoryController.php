<?php

namespace app\controllers;

use Yii;
use app\models\Category;
use app\models\CategorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use app\models\Inventory;
use app\models\Purchases;
use app\models\Sales;
use app\models\Stocks;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use app\models\Totalinventory;
use Exception;
use yii\helpers\Json;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use yii\helpers\FileHelper;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends Controller
{
    /**
     * {@inheritdoc}
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
                        'actions' => [
                            'create',
                            'view',
                            'create-category',
                            'upload',
                            'state',
                            'data-table',
                            'change-place',
                            'export'
                        ],
                        'roles' => ['createCategory'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'view', 'state', 'upload', 'change-place'],
                        'roles' => ['updateCategory'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['deleteCategory'],
                    ],

                    [
                        'allow' => true,
                        'actions' => ['index', 'stagnant'],
                        'roles' => ['indexCategory'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['reorder', 'hangouts'],
                        'roles' => ['reOrder'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['more-request'],
                        'roles' => ['moreRequest'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['barcode-print'],
                        'roles' => ['barcodePrint'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['info', 'image'],
                        'roles' => ['info'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['histrans', 'histrans-by-client'],
                        'roles' => ['categoryHistrans'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Category models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CategorySearch();
        $searchModel->class = 'xyz';
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Category model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Category model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Category();

        if ($model->load(Yii::$app->request->post())) {
            $model->status = 0;

            $model->created_at     = date('Y-m-d H:i:s');
            $model->user_insert = Yii::$app->user->identity->id;
            //=========
            if (!is_dir("img/category")) {
                // mkdir("img/category");
                FileHelper::createDirectory(Yii::getAlias('@webroot/img/category'));
                $path = "img/category";
            } else {
                $path = "img/category";
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
            //=========
            if ($model->quantity == null) {
                $model->quantity = 0;
            }
            if ($model->cost     == null) {
                $model->cost = 0;
            }
            if ($model->price == null) {
                $model->price = 0;
            }
            if ($model->qShow == null) {
                $model->qShow = 0;
            } else {
                $model->qShow = 1;
            }


            $name = trim($model->name, " \t\n\r");
            $model->name = $name;
            $model->save(false);
            // if (!$model->save()) {
            //     Yii::$app->session->setFlash('error', 'تعذر حفظ الصنف');
            //     return $this->render('create', ['model' => $model]);
            // }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Category model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->update_at     = date('Y-m-d H:i:s');
            $model->user_update = Yii::$app->user->id;
            //=========
            if (!is_dir("img/category")) {
                // mkdir("img/category");
                FileHelper::createDirectory(Yii::getAlias('@webroot/img/category'));

                $path = "img/category";
            } else {
                $path = "img/category";
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
            $model->save();

            return $this->redirect(['view', 'id' => $model->id]);
        } elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('_form', [
                'model' => $model,
            ]);
        } else {

            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Category model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Category the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Category::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionState($id)
    {
        $state = $this->findModel($id);
        if ($state->status == 0) {
            Category::updateAll(['status' => 1], ['=', 'id', $id]);
        } else {
            Category::updateAll(['status' => 0], ['=', 'id', $id]);
        }

        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionCreateCategory()
    {
        $model = new Category();

        if ($model->load(Yii::$app->request->post())) {
            $model->status = 0;
            $model->qShow = 0;
            if ($model->quantity == null) {
                $model->quantity = 0;
            }
            if ($model->cost     == null) {
                $model->cost = 0;
            }
            if ($model->price == null) {
                $model->price = 0;
            }
            $model->created_at     = date('Y-m-d H:i:s');
            $model->user_insert = Yii::$app->user->id;
            $model->save();

            return $this->redirect(Yii::$app->request->referrer);
        } elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('_form', [
                'model' => $model,
            ]);
        } else {

            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Lists all Inventory models.
     * @return mixed
     */
    public function actionMoreRequest()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Inventory::find()
                ->select('inventory.id, max(inventory.name) name, max(inventory.serialNo) serialNo, max(category.minimum) as minimum, sum(inventory.quantity) quantity')
                ->leftJoin('category', 'category.id = inventory.id')
                ->where('category.moreRequest = 1')
                ->groupBy('inventory.id, category.minimum ')
                ->having('sum(inventory.quantity) <= category.minimum')
                ->orderBy('inventory.id'),

            'pagination' => [
                'pageSize' => 50
            ],
        ]);

        return $this->render('moreRequest', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionHistrans()
    {
        $model = new Inventory();
        $data = Category::find()->where(['status' => 0])->all();

        if ($model->load(Yii::$app->request->post())) {
            $category = Category::find()->select('id')->where(['id' => $model->id])->one();

            if ($model->allData != 0) {
                $model->min_date = '2010-01-01';
                $model->max_date = date('Y-m-d');
            }
            if (Yii::$app->user->identity->client != null) {
                $sqlSum = " SELECT
                sum(category_histrans.quantity) as quantity
                FROM category_histrans
                where category_histrans.clientId in ( " . Yii::$app->user->identity->client . " )
                and category_histrans.id = " . $category->id . "
                and category_histrans.tranDate < '" . $model->min_date . "'  ";
                $connection = Yii::$app->db;
                $data = $connection->createCommand($sqlSum);
                $lastBalance = $data->queryAll();

                $sql = " SELECT category_histrans.kind_id, category_histrans.id, category_histrans.printId,
                category_histrans.name as name, category_histrans.quantity as quantity, category_histrans.unit as unit,
                category_histrans.box as box, category_histrans.class as class, category_histrans.client as client,
                branches.name as branch, category_histrans.kind as kind,
                 category_histrans.trandate as trandate, category_histrans.billId as billId
                , category_histrans.deleviried as deleviried
                FROM category_histrans, branches
                where category_histrans.branch = branches.id and category_histrans.id = " . $category->id . "
                and category_histrans.trandate  between '" . $model->min_date . "' and '" . $model->max_date . "'
                and category_histrans.clientId in ( " . Yii::$app->user->identity->client . ")
                order by category_histrans.trandate, category_histrans.kind_id
                ";
                $connection = Yii::$app->db;
                $data = $connection->createCommand($sql);
                $info = $data->queryAll();
                if ($info == null) {
                    echo '<script type="text/javascript">
                    alert("عفوا لايوجد بيانات للعرض");
                    window.location.href="?r=category"
                    </script>';
                }
            } else {
                $sqlSum = " SELECT
            sum(category_histrans.quantity) as quantity
            FROM category_histrans
            where category_histrans.id = " . $category->id . "
            and category_histrans.tranDate < '" . $model->min_date . "'  ";
                $connection = \Yii::$app->db;
                $data = $connection->createCommand($sqlSum);
                $lastBalance = $data->queryAll();

                $sql = " SELECT category_histrans.kind_id, category_histrans.id, category_histrans.printId,
             category_histrans.name as name, category_histrans.quantity as quantity, category_histrans.unit as unit,
            category_histrans.box as box, category_histrans.class as class, category_histrans.client as client,
            branches.name as branch, category_histrans.kind as kind, category_histrans.trandate as trandate,
             category_histrans.billId as billId
            , category_histrans.deleviried as deleviried
            FROM category_histrans, branches
            where category_histrans.branch = branches.id and category_histrans.id = " . $category->id . "
            and category_histrans.trandate  between '" . $model->min_date . "' and '" . $model->max_date . "'
            order by category_histrans.trandate, category_histrans.kind_id
            ";
                $connection = Yii::$app->db;
                $data = $connection->createCommand($sql);
                $info = $data->queryAll();
                if ($info == null) {
                    echo '<script type="text/javascript">
                alert("عفوا لايوجد بيانات للعرض");
                window.location.href="?r=category"
                </script>';
                }
            }
            return $this->render('histransrep', [
                'models' => $info,
                'min_date' => $model->min_date,
                'max_date' => $model->max_date,
                'sumsader' => 0,
                'sumwared' => 0,
                'sumPurchase' => 0,
                'sumBackPurchase' => 0,
                'sumSales' => 0,
                'sumBackSales' => 0,
                'sum' => 0,
                'coun' => 1,
                'count' => 0,
                'name' => null,
                'id' => 0,
                'lastBalance' => $lastBalance,
                'sumQuantity' => 0,
            ]);
        }

        return $this->render('histrans', [
            'model' => $model,
            'data' => $data
        ]);
    }

    public function actionUpload()
    {
        $model = new Category();

        if ($model->load(Yii::$app->request->post())) {
            //=========
            if (!is_dir("img/upload")) {
                mkdir("img/upload");
                $path = "img/upload";
            } else {
                $path = "img/upload";
            }
            $model->file = UploadedFile::getInstance($model, 'file');
            $ext = substr(strrchr($model->file, '.'), 1);
            if ($ext != null) {
                $uniqid = uniqid();
                $model->file->saveAs($path . '/' . $uniqid . '.' . $model->file->extension);
                $model->path = $path . '/' . $uniqid . '.' . $model->file->extension;
            }
            //=========
            $inputFile = $path . '/' . $uniqid . '.' . $model->file->extension;

            try {
                $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($inputFile);
                $objReader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFile);
            } catch (Exception $e) {
                die('Erorr');
            }
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

            for ($row = 1; $row <= $highestRow; $row++) {

                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
                if ($row == 1) {
                    continue;
                }

                $workSheet =   new  Category();
                $workSheet->name = $rowData[0][0];
                $workSheet->class = $rowData[0][1];
                $workSheet->unit = $rowData[0][2];
                $workSheet->box = $rowData[0][3];
                $workSheet->cost = $rowData[0][4];
                $workSheet->price = $rowData[0][5];
                $workSheet->quantity = $rowData[0][6];
                $workSheet->minimum = $rowData[0][7];
                $workSheet->ending = $rowData[0][8];
                $workSheet->qShow = $rowData[0][9];
                $workSheet->status = $rowData[0][10];
                $workSheet->place = $rowData[0][11];
                $workSheet->serialNo = $rowData[0][12];
                $workSheet->country = $rowData[0][13];
                $workSheet->company = $rowData[0][14];
                $workSheet->path = $rowData[0][15];
                $workSheet->commCode = $rowData[0][16];
                $workSheet->moreRequest = $rowData[0][17];
                $workSheet->weight = $rowData[0][18];

                $workSheet->user_insert = Yii::$app->user->identity->id;
                $workSheet->created_at = date('Y-m-d H:i:s');
                $workSheet->user_update = Yii::$app->user->identity->id;
                $workSheet->update_at = date('Y-m-d H:i:s');
                if ($workSheet->name <> Null) {
                    $workSheet->save(false);
                } else {
                    break;
                }
            }

            return $this->redirect(['index', 'id' => $model->id]);
        }

        return $this->render('upload', [
            'model' => $model,
        ]);
    }

    public function actionHangouts()
    {
        $dataProvider = new ActiveDataProvider([
            'query'  => Totalinventory::find()
                ->select('max(name)name, max(serialNo)serialNo, sum(quantity)quantity, max(costPrice)totalCost')
                ->where(['branch' => Yii::$app->user->identity->branch])
                ->andWhere(['type' => 1])
                ->groupBy('id'),

            'pagination' => [
                'pageSize' => false
            ],
        ]);

        return $this->render('hangouts', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionBarcodePrint()
    {
        $model = new Category();


        if ($model->load(Yii::$app->request->post())) {
            return $this->render('printBarcode', [
                'model' => $model,
            ]);
        }
        return $this->render('barcodeGenerate', [
            'model' => $model,
        ]);
    }

    public function actionGetData($category)
    {
        $value = Category::find()
            ->where(['id' => $category])
            ->one();

        echo json::encode($value);
    }

    public function actionInfo($id)
    {
        $model = new Category();
        $modelInfo = Stocks::find()
            ->select(['category', 'sum(quantity) as quantity'])
            ->where(['category' => $id])
            ->groupBy(['category'])
            ->one();

        $dateOfArrival = Purchases::find()
            ->leftJoin('purchasesDetails', 'purchases.id = purchasesDetails.PurchasesId')
            ->where(['type' => 3])
            ->andWhere('dateOfArrival is not null')
            ->andWhere(['=', 'purchasesDetails.category', $id])
            ->one();

        $salesInfo = Sales::find()
            ->select(['sales.at', 'salesDetails.salePrice'])
            ->leftJoin('salesDetails', 'sales.id = salesDetails.salesId')
            ->where(['sales.type' => 1])
            ->andWhere(['=', 'salesDetails.category', $id])
            ->limit(3)
            ->orderBy(['sales.at' => SORT_DESC])
            ->all();



        if ($model->load(Yii::$app->request->post())) {
            return $this->redirect(Yii::$app->request->referrer);
        } elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('info', [
                'model' => $model,
                'modelInfo' => $modelInfo,
                'dateOfArrival' => $dateOfArrival,
                'salesInfo' => $salesInfo,
            ]);
        }
    }

    public function actionStagnant()
    {
        $dataProvider = new ActiveDataProvider([
            'query'  => Totalinventory::find()
                ->select(['*'])
                ->leftJoin('stagnant', 'Totalinventory.id = stagnant.category')
                ->where("stagnant.category is null")
                ->andWhere(['<>', 'quantity', 0]),

            'pagination' => [
                'pageSize' => 100
            ],
        ]);

        return $this->render('stagnant', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionDataTable()
    {
        $searchModel = new CategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->renderAjax('data-table', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionChangePlace($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->save(false);
            return $this->redirect(['temp-arrangement/create', 'id' => $model->id]);
        } elseif (Yii::$app->request->isAjax) {

            return $this->renderAjax('changePlace', [
                'model' => $model,
            ]);
        } else {
            return $this->render('changePlace', [
                'model' => $this->findModel($id),
            ]);
        }
    }

    public function actionExport($created_at)
    {
        $export = Yii::createObject([
            'class' => 'codemix\excelexport\ExcelFile',
            'sheets' => [
                'الاصناف' => [
                    'class' => 'codemix\excelexport\ActiveExcelSheet',

                    'query' => Category::find()
                        ->where(['=', 'created_at', $created_at])
                        ->orderBy(['id' => SORT_ASC]),


                    'attributes' => [
                        'name',
                        'class',
                        'unit',
                        'box',
                        'cost',
                        'price',
                        'quantity',
                        'minimum',
                        'ending',
                        'qShow',
                        'status',
                        'place',
                        'serialNo',
                        'country',
                        'company',
                        'path',
                        'commCode',
                        'moreRequest',
                        'weight'
                    ],
                    'styles' => [
                        'A1:Z1000' => [
                            'font' => [
                                'bold' => true,
                                'color' => ['rgb' => '000000'],
                                'size' => 14,
                                'name' => 'Times New Roman'
                            ],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_RIGHT,
                            ],
                        ],
                    ],
                ]
            ]
        ]);
        $export->send('category.xlsx');
    }

    public function actionHistransByClient()
    {
        $model = new Inventory();
        $data = Category::find()->where(['status' => 0])->all();

        if ($model->load(Yii::$app->request->post())) {
            $category = Category::find()->select('id')->where(['id' => $model->id])->one();

            if ($model->allData != 0) {
                $model->min_date = '2010-01-01';
                $model->max_date = date('Y-m-d');
            }
            if (Yii::$app->user->identity->client != null) {
                $sqlSum = " SELECT
                sum(category_histrans.quantity) as quantity
                FROM category_histrans
                where category_histrans.clientId in ( " . Yii::$app->user->identity->client . " )
                and category_histrans.id = " . $category->id . "
                and category_histrans.tranDate < '" . $model->min_date . "'  
                and category_histrans.ClientId = " . $model->client;
                $connection = Yii::$app->db;
                $data = $connection->createCommand($sqlSum);
                $lastBalance = $data->queryAll();

                $sql = " SELECT category_histrans.kind_id, category_histrans.id, category_histrans.printId,
                category_histrans.name as name, category_histrans.quantity as quantity, category_histrans.unit as unit,
                category_histrans.box as box, category_histrans.class as class, category_histrans.client as client,
                branches.name as branch, category_histrans.kind as kind,
                 category_histrans.trandate as trandate, category_histrans.billId as billId
                , category_histrans.deleviried as deleviried
                FROM category_histrans, branches
                where category_histrans.branch = branches.id and category_histrans.id = " . $category->id . "
                and category_histrans.trandate  between '" . $model->min_date . "' and '" . $model->max_date . "'
                and category_histrans.tranDate.ClientId = " . $model->client . "
                and category_histrans.clientId in ( " . Yii::$app->user->identity->client . ")
                order by category_histrans, category_histrans.kind_id
                ";
                $connection = Yii::$app->db;
                $data = $connection->createCommand($sql);
                $info = $data->queryAll();
                if ($info == null) {
                    echo '<script type="text/javascript">
                    alert("عفوا لايوجد بيانات للعرض");
                    window.location.href="?r=category"
                    </script>';
                }
            } else {
                $sqlSum = " SELECT
            sum(category_histrans.quantity) as quantity
            FROM category_histrans
            where category_histrans.id = " . $category->id . "
            and category_histrans.tranDate < '" . $model->min_date . "'  
            and category_histrans.ClientId = " . $model->client;
                $connection = Yii::$app->db;
                $data = $connection->createCommand($sqlSum);
                $lastBalance = $data->queryAll();

                $sql = " SELECT category_histrans.kind_id, category_histrans.id, category_histrans.printId,
             category_histrans.name as name, category_histrans.quantity as quantity, category_histrans.unit as unit,
            category_histrans.box as box, category_histrans.class as class, category_histrans.client as client,
            branches.name as branch, category_histrans.kind as kind, category_histrans.trandate as trandate,
             category_histrans.billId as billId
            , category_histrans.deleviried as deleviried
            FROM category_histrans, branches
            where category_histrans.branch = branches.id and category_histrans.id = " . $category->id . "
            and category_histrans.trandate  between '" . $model->min_date . "' and '" . $model->max_date . "'
            and category_histrans.ClientId = " . $model->client . "
            order by category_histrans.trandate, category_histrans.kind_id";
                $connection = Yii::$app->db;
                $data = $connection->createCommand($sql);
                $info = $data->queryAll();
                if ($info == null) {
                    echo '<script type="text/javascript">
                alert("عفوا لايوجد بيانات للعرض");
                window.location.href="?r=category"
                </script>';
                }
            }
            return $this->render('histransrep', [
                'models' => $info,
                'min_date' => $model->min_date,
                'max_date' => $model->max_date,
                'sumsader' => 0,
                'sumwared' => 0,
                'sumPurchase' => 0,
                'sumBackPurchase' => 0,
                'sumSales' => 0,
                'sumBackSales' => 0,
                'sum' => 0,
                'coun' => 1,
                'count' => 0,
                'name' => null,
                'id' => 0,
                'lastBalance' => $lastBalance,
                'sumQuantity' => 0,
            ]);
        }

        return $this->render('histransClient', [
            'model' => $model,
            'data' => $data
        ]);
    }

    public function actionImage($id)
    {
        $model = Category::find()->where(['id' => $id])->one();

        if ($model->load(Yii::$app->request->post())) {
            return $this->redirect(Yii::$app->request->referrer);
        } elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('previewImage', [
                'model' => $model,
            ]);
        }
    }
}
