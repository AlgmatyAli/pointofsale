<?php

namespace app\controllers;

use Yii;
use app\models\Sales;
use app\models\SalesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;
use app\models\SalesDetails;
use yii\helpers\Json;
use app\models\Ftran;
use app\models\TempInvoice;
use app\models\CompanyInfo;
use app\models\Totalinventory;
use app\models\Dept;
use app\models\Client;
use app\models\Category;
use app\models\SalesDetailsSearchWQ;
use app\models\SalesDetailsSearchWQClient;
use app\models\Stocks;
use app\models\TempBackSales;
use Mpdf\Mpdf; #Php 7.0
use yii\data\ActiveDataProvider;
use kartik\mpdf\Pdf;


/**
 * SalesController implements the CRUD actions for Sales model.
 */
class SalesController extends Controller
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
                'class' => Yii\filters\AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create', 'view', 'print', 'noprice', 'itemlist', 'deserving', 'done', 'itemlistid', 'print-no-price', 'fast', 'save-pdf', 'delev'],
                        'roles' => ['createSales'],
                    ],

                    [
                        'allow' => true,
                        'actions' => ['update', 'view', 'print', 'noprice', 'remove', 'pdf', 'itemlist', 'deserving', 'done', 'transfer-to-temp-invoice', 'itemlistid', 'print-no-price', 'save-pdf', 'delev'],
                        'roles' => ['updateSales'],
                    ],

                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['deleteSales'],
                    ],

                    [
                        'allow' => true,
                        'actions' => ['save-as-new'],
                        'roles' => ['saveSalesAsNew'],
                    ],

                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['indexSales'],
                    ],

                    [
                        'allow' => true,
                        'actions' => ['profit', 'net-profit', 'wait-qnty', 'wait-qnty-client'],
                        'roles' => ['profit'],
                    ],

                    [
                        'allow' => true,
                        'actions' => ['ftran'],
                        'roles' => ['ftran'],
                    ],

                    [
                        'allow' => true,
                        'actions' => ['back-create'],
                        'roles' => ['createBackSales'],
                    ],

                    [
                        'allow' => true,
                        'actions' => ['histrans'],
                        'roles' => ['clientHistrans'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['category-histrans'],
                        'roles' => ['categoryHistrans'],
                    ],

                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new SalesSearch();
        $searchModel->type = 99;
        $searchModel->branch = Yii::$app->user->identity->branch;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        $providerSalesDetails = new Yii\data\ArrayDataProvider([
            'allModels' => $model->salesDetails,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]
            ],
            'pagination' => ['pageSize' => 200],

        ]);
        return $this->render('view', [
            'model' => $this->findModel($id),
            'providerSalesDetails' => $providerSalesDetails,
        ]);
    }

    public function actionUpdate($id)
    {
        $company = CompanyInfo::find()->one();
        $model = $this->findModel($id);
        if ($model->branch != Yii::$app->user->identity->branch) {
            Yii::$app->session->setFlash('error', Yii::t('app', "This invoice is not issued by the branch you work in, you cannot update it"));
            return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
        }
        // if ((!Yii::$app->user->can('update_earlier_date'))) {
        //     // if ($model->at != Date('Y-m-d')){
        //     //     Yii::$app->session->setFlash('error', Yii::t('app',"You can NOT update an invoice with an earlier date !"));
        //     //     return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);

        //     // }
        // }

        if (Yii::$app->user->can('saleOnHoldItems')) {
            $type = [1, 2, 3];
        } else {
            $type = [1, 2];
        }
        $dataProvider = new ActiveDataProvider([
            'query' => SalesDetails::find()->where(['=', 'salesId', $id]),
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
            ],
            'pagination' => false,
        ]);
        if (yii::$app->request->post('hasEditable')) {
            $id = Yii::$app->request->post('editableKey');
            $result = SalesDetails::findOne($id);
            $out = Json::encode(['output' => '', 'message' => '']);
            $post = [];
            $posted = current($_POST['SalesDetails']);

            $post['SalesDetails'] = $posted;
            if ($result->load($post)) {
                $sale = Sales::find()->where(['id' => $result->salesId])->one();

                if (isset($posted['quantity'])) {
                    $outMessage = $result->quantity;
                    $quantity = $posted['quantity'];
                    /** set value is zero  */
                    $delete = new SalesDetails();
                    $delete = SalesDetails::find()->where(['id' => $result->id])->one();
                    $delete->quantity = 0;
                    $delete->save();
                    /**get total inventory */
                    $stockQ = Stocks::find()
                        ->Where(['branch' => Yii::$app->user->identity->branch])
                        ->andwhere(['category' => $result->category])
                        ->andwhere(['in', 'type', $type])
                        ->one();
                    /** get if valeu larger than to requsetd val  */
                    if ($model->type != 2) {
                        if ($quantity >= abs($stockQ->quantity)) {
                            $result->quantity = abs($stockQ->quantity);
                        } else {
                            $result->quantity = $quantity;
                        }
                    }
                    $result->save(false);
                    $sale->total = SalesDetails::find()->where(['salesId' => $result->salesId])
                        ->sum('salePrice * quantity');
                    $sale->save(false);
                } elseif (isset($posted['salePrice'])) {
                    if (Yii::$app->user->can('userCanUpdateSalePriceAfterSave')) {
                        $quantity = $posted['salePrice'];
                        $sale = Sales::find()->where(['id' => $result->salesId])->one();

                        $result->save(false);
                        $sale->total = SalesDetails::find()->where(['salesId' => $result->salesId])
                            ->sum('salePrice * quantity');
                        $sale->save(false);
                        $outMessage = $result->salePrice;
                    } else {
                        $outMessage = 'لايوجد لديك صلاحية لتعديل سعر البيع';
                    }
                } elseif (isset($posted['packing'])) {
                    $packing = $posted['packing'];
                    $sale = Sales::find()->where(['id' => $result->salesId])->one();
                    $result->save(false);
                    $outMessage = $result->packing;
                }
                $output = $outMessage;
                $out = Json::encode(['output' => $output]);
                return $out;
            }
        }

        if ($model->load(Yii::$app->request->post())) {
            $post_paid = Client::find()->select('post_paid')->where(['=', 'id', $model->clinet])->one();
            if ($post_paid != 1) {
                if ($model->payWay != 0) {
                    Yii::$app->session->setFlash('error', Yii::t('app', "عفوا هذا الزبون لايمكن البيع له بالآجل"));
                    return $this->redirect(['temp-invoice/create', 'id' => 1]);
                }
            }
            $model->total = SalesDetails::find()->where(['salesId' => $id])->sum('salePrice * quantity');

            $debtBalance = Client::find()->select('debt')->where(['=', 'id', $model->clinet])->one();
            $balance = Dept::find()->where(['id' => $model->clinet])->sum('credt');
            $model->branch = Yii::$app->user->identity->branch;

            if ($model->disscount == null) {
                $model->disscount = 0;
            }

            $total = $balance + $model->total;
            if ($debtBalance["debt"] <> 0) {
                if (Yii::$app->user->can('userCansellOverDebt')) {
                    $model->update_at = date('Y-m-d H:i:s');
                    $model->user_update = Yii::$app->user->identity->id;
                    if ($model->save(false)) {
                        return $this->redirect(['print', 'id' => $id]);
                    }
                } else {
                    if ((float) $total >= (float) $debtBalance["debt"] && $model->payWay == 1) {
                        Yii::$app->session->setFlash('error', Yii::t('app', "Sorry The Client Cross The Line About Debt"));
                        return $this->redirect(['sales/update', 'id' => $id]);
                    }
                }
            } else {
                $model->update_at = date('Y-m-d H:i:s');
                $model->user_update = Yii::$app->user->identity->id;
                if ($model->save(false)) {
                    return $this->redirect(['print', 'id' => $id]);
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'company' => $company,

        ]);
    }

    public function actionDelete($id)
    {
        $sales = Sales::find()->select(['type'])->where(['id' => $id])->one();
        $salesDetails = SalesDetails::find()->where(['salesId' => $id])->all();

        Yii::$app->db->createCommand("
                INSERT INTO sales_deleted (
                                           id, billId, at, clinet, payWay, branch, total, paid, notes, path, type, 
                                            deleviryAt, carpenter, upholstered, paintId, deleviryId, user_insert, created_at, user_update,
                                            update_at) 
                    SELECT id, billId, at, clinet, payWay, branch, total, paid, notes, path, type, 
                    deleviryAt, carpenter, upholstered, paintId, deleviryId, user_insert, created_at, user_update,
                    update_at
                FROM sales 
                where id=" . $id . "
                ")
            ->execute();

        foreach ($salesDetails as $data) {
            if ($sales->type == 1) {
                Yii::$app->db->createCommand("UPDATE  stocks  SET  quantity =  quantity  + $data->quantity 
                 WHERE category=:category
                 and branch = :branch
                 and type = :type")
                    ->bindValue(':category', $data->category)
                    ->bindValue(':branch', Yii::$app->user->identity->branch)
                    ->bindValue(':type', 1)
                    ->execute();
            } elseif ($sales->type == 3) {
                Yii::$app->db->createCommand("UPDATE  stocks  SET  quantity =  quantity  + $data->quantity
                WHERE category=:category
                and branch = :branch
                and type = :type")
                    ->bindValue(':category', $data->category)
                    ->bindValue(':branch', Yii::$app->user->identity->branch)
                    ->bindValue(':type', 3)
                    ->execute();
            } elseif ($sales->type == 2) {
                Yii::$app->db->createCommand("UPDATE  stocks  SET  quantity =  quantity  + $data->quantity
                WHERE category=:category
                and branch = :branch
                and type = :type")
                    ->bindValue(':category', $data->category)
                    ->bindValue(':branch', Yii::$app->user->identity->branch)
                    ->bindValue(':type', 1)
                    ->execute();
            }
        }
        Yii::$app
            ->db
            ->createCommand()
            ->delete('sales', ['id' => $id])
            ->execute();
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Sales::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionGetPrices($id)
    {

        $info = Totalinventory::find()
            ->where(['name' => $id])
            ->one();
        echo json::encode($info);
    }

    public function actionFtran()
    {
        $model = new Ftran();

        if ($model->load(Yii::$app->request->post())) {

            if ($model->today != 0) {
                $model->min_date = date('Y-m-d');
                $model->max_date = date('Y-m-d');
            }

            if ($model->min_date == null) {
                echo
                '<script type="text/javascript"> alert(\'الرجاء تحديد تاريخ الحركة من\');
                        window.location.href="?r=sales%2Fftran";
                        </script>';
            }
            if ($model->max_date == null) {
                echo
                '<script type="text/javascript"> alert(\'الرجاء تحديد تاريخ الحركة إلى\');
                        window.location.href="?r=sales%2Fftran";
                        </script>';
            }



            if ($model->user_insert != null) {
                $sqlSum = " SELECT 
            sum(ftran.sader) as sader, sum(ftran.wared) as wared FROM ftran
            where ftran.date_ < '" . $model->min_date . "' and ftran.outBox =0 and ftran.branch =" . Yii::$app->user->identity->branch . " 
             and currancy = '" . $model->currancy . "' and ftran.user_insert ='" . $model->user_insert . "'";
                $connection = Yii::$app->db;
                $data = $connection->createCommand($sqlSum);
                $lastBalance = $data->queryAll();

                $sql = " SELECT * FROM ftran
            where  ftran.branch =" . Yii::$app->user->identity->branch . " 
            and ftran.date_  between '" . $model->min_date . "' and '" . $model->max_date . "' and ftran.outBox =0 and currancy = '" . $model->currancy . "' and ftran.user_insert = '" . $model->user_insert . "'";
            } else {

                $sqlSum = " SELECT 
            sum(ftran.sader) as sader, sum(ftran.wared) as wared FROM ftran
            where ftran.date_ < '" . $model->min_date . "' and ftran.outBox =0 and currancy = '" . $model->currancy . "' and ftran.branch =" . Yii::$app->user->identity->branch . " ";
                $connection = Yii::$app->db;
                $data = $connection->createCommand($sqlSum);
                $lastBalance = $data->queryAll();

                $sql = " SELECT * FROM ftran
            where  ftran.branch =" . Yii::$app->user->identity->branch . " 
            and ftran.date_  between '" . $model->min_date . "' and '" . $model->max_date . "' and currancy = '" . $model->currancy . "' and ftran.outBox = 0 order by ftran.date_  ";
            }

            $connection = Yii::$app->db;
            $data = $connection->createCommand($sql);
            $info = $data->queryAll();
            if ($info == null) {
                die("Sorry no thing to preview");
            }

            return $this->render('ftranRep', [
                'models' => $info,
                'min_date' => $model->min_date,
                'max_date' =>  $model->max_date,
                'sumwared' => 0,
                'sumsader' => 0,
                'disscount' => 0,
                'sum' => 0,
                'coun' => 1,
                'count' => 0,
                'lastBalance' => $lastBalance,
            ]);
        }

        return $this->render('ftran', [
            'model' => $model,
        ]);
    }

    public function actionCreate()
    {
        $model = new Sales();
        $company = CompanyInfo::find()->one();

        if ($company->payWayCash == 1) {
            $model->payWay = 0;
        } else {
            $model->payWay = 1;
        }
        if ($model->load(Yii::$app->request->post())) {
            $post_paid = Client::find()->select('post_paid')->where(['=', 'id', $model->clinet])->one();

            if ($post_paid != 1) {
                if ($model->payWay != 0) {
                    Yii::$app->session->setFlash('error', Yii::t('app', "عفوا هذا الزبون لايمكن البيع له بالآجل"));
                    return $this->redirect(['temp-invoice/create', 'id' => 1]);
                }
            }

            $debtBalance = Client::find()->select('debt')->where(['=', 'id', $model->clinet])->one();
            $balance = Dept::find()->where(['id' => $model->clinet])->sum('credt');

            $model->total = TempInvoice::find()->where(['created_by' => Yii::$app->user->identity->id, 'state' => 1])
                ->sum('salePrice * quantity');

            $model->branch = Yii::$app->user->identity->branch;
            $model->billId = Sales::find()->where(['=', 'type', $model->type])->max('billId') + 1;
            $model->created_at = date('Y-m-d H:i:s');
            $model->user_insert = Yii::$app->user->id;

            if ($model->disscount == null) {
                $model->disscount = 0;
            }

            if (Yii::$app->user->identity->client <> null) {
                $model->deleviried =  0;
                $model->wholesale = 0;
                $model->paid = 0;
            }

            $total = $balance + $model->total;
            if ($debtBalance["debt"] <> 0 && $model->type != 4) {
                if ((float) $total >= (float) $debtBalance["debt"] && $model->payWay == 1) {
                    if (Yii::$app->user->can('userCansellOverDebt')) {
                        $model->save(false);
                        $id = $model->id;

                        $temp_invoice = new TempInvoice();
                        $temp_invoice = TempInvoice::find()->where([
                            'created_by' => Yii::$app->user->identity->id,
                            'state' => 1
                        ])->all();
                        foreach ($temp_invoice as $data) {
                            $modelDetails = new salesDetails();
                            $modelDetails->salesId = $id;
                            $modelDetails->category =  $data->category;
                            $modelDetails->waitQnty = $data->waitQnty;
                            $modelDetails->costPrice = $data->costPrice;
                            $modelDetails->salePrice = $data->salePrice;
                            $modelDetails->box = $data->box;
                            $modelDetails->expire = $data->expire;
                            $modelDetails->type = $data->type;
                            $modelDetails->original_price = $data->salePrice;
                            $modelDetails->serial_number = $data->serial_number;
                            $modelDetails->mac_address = $data->mac_address;
                            if ($model->type != 4) {
                                if (Yii::$app->user->identity->seeOtherBranchQ == 0) {
                                    $branch = Yii::$app->user->identity->branch;
                                } else {
                                    $branch = [1, 2, 3];
                                }
                                $item = Stocks::find()
                                    ->select(['max(stocks.category) as id', 'sum(stocks.quantity) as quantity'])
                                    ->leftJoin('prices', 'stocks.category = prices.category')
                                    ->where(['stocks.category' => $data->category])
                                    ->andWhere(['<>', 'stocks.quantity', 0])
                                    ->andwhere(['in', 'stocks.type', $model->type])
                                    ->andWhere(['in', 'stocks.branch', $branch])
                                    ->one();

                                if ($item <> null) {
                                    if ($data->quantity > abs($item->quantity)) {
                                        $modelDetails->quantity = abs($item->quantity);
                                        $modelDetails->save(false);
                                    } else {
                                        $modelDetails->quantity = abs($data->quantity);
                                        $modelDetails->save(false);
                                    }
                                } else {
                                    $modelDetails->quantity = 0;
                                }

                                if ($model->type == 1) {
                                    Yii::$app->db->createCommand("UPDATE  stocks  SET  quantity =  quantity  - $modelDetails->quantity
                                 WHERE category=:category
                                 and branch = :branch
                                 and type = :type")
                                        ->bindValue(':category', $data->category)
                                        ->bindValue(':branch', Yii::$app->user->identity->branch)
                                        ->bindValue(':type', 1)
                                        ->execute();
                                } elseif ($model->type == 3) {
                                    Yii::$app->db->createCommand("UPDATE  stocks  SET  quantity =  quantity  - $modelDetails->quantity
                                WHERE category=:category
                                and branch = :branch
                                and type = :$model->type")
                                        ->bindValue(':category', $data->category)
                                        ->bindValue(':branch', Yii::$app->user->identity->branch)
                                        ->bindValue(':type', 3)
                                        ->execute();
                                }
                            } else {
                                $modelDetails->quantity = $data->quantity;
                                $modelDetails->save(false);
                            }
                            $zeroQuantity = SalesDetails::find()->where(['salesId' => $id, 'quantity' => 0])->count();
                            if ($zeroQuantity > 0) {
                                Yii::$app->session->setFlash('error', Yii::t('app', "عفوا الفاتورة تحتوي اصناف قيمتها 0"));
                            }
                            $SalesDetailsTotal = SalesDetails::find()->where(['salesId' => $id])->sum('salePrice * quantity');
                            if ($SalesDetailsTotal == 0) {
                                Yii::$app->session->setFlash('error', Yii::t('app', "عفوا الفاتورة لاتحتوي على اصناف الرجاء الغاءها"));
                            }
                            //امكانية البيع مع اظهار رسالة تنبيه
                            Yii::$app->session->setFlash('error', Yii::t('app', "عفوا هذا العميل تجاوز سقف الديون ! الرجاء المراجعة"));
                            $salesTotal = SalesDetails::find()->where(['=', 'salesId', $id])->sum('salePrice * quantity');
                            if ($salesTotal == 0) {
                                $salesTotal = 0;
                            }
                            Yii::$app->db->createCommand("UPDATE  sales  SET  total =  $salesTotal WHERE id=:id")
                                ->bindValue(':id', $id)->execute();
                            $temp_invoice = TempInvoice::deleteAll(['created_by' => Yii::$app->user->identity->id, 'state' => 1]);
                        }
                        return $this->redirect(['print', 'id' => $id]);
                    } else {
                        Yii::$app->session->setFlash('error', Yii::t('app', "Sorry The Client Cross The Line About Debt"));
                        return $this->redirect(['temp-invoice/create', 'id' => 1]);
                    }
                } else {
                    $model->save(false);
                    $id = $model->id;

                    $temp_invoice = TempInvoice::find()->where([
                        'created_by' => Yii::$app->user->identity->id,
                        'state' => 1
                    ])->all();

                    foreach ($temp_invoice as $data) {
                        $modelDetails = new salesDetails();
                        $modelDetails->salesId = $id;
                        $modelDetails->category =  $data->category;
                        $modelDetails->waitQnty = $data->waitQnty;
                        $modelDetails->costPrice = $data->costPrice;
                        $modelDetails->salePrice = $data->salePrice;
                        $modelDetails->box = $data->box;
                        $modelDetails->expire = $data->expire;
                        $modelDetails->type = $data->type;
                        $modelDetails->original_price = $data->salePrice;
                        $modelDetails->serial_number = $data->serial_number;
                        $modelDetails->mac_address = $data->mac_address;
                        if ($model->type != 4) {
                            if (Yii::$app->user->identity->seeOtherBranchQ == 0) {
                                $branch = Yii::$app->user->identity->branch;
                            } else {
                                $branch = [1, 2, 3];
                            }

                            $item = Stocks::find()
                                ->select(['max(stocks.category) as id', 'sum(stocks.quantity) as quantity'])
                                ->leftJoin('prices', 'stocks.category = prices.category')
                                ->where(['stocks.category' => $data->category])
                                ->andWhere(['<>', 'stocks.quantity', 0])
                                ->andwhere(['in', 'stocks.type', $model->type])
                                ->andWhere(['in', 'stocks.branch', $branch])
                                ->one();
                            if ($item <> null) {
                                if ($data->quantity > abs($item->quantity)) {
                                    $modelDetails->quantity = abs($item->quantity);
                                    $modelDetails->save(false);
                                } else {
                                    $modelDetails->quantity = abs($data->quantity);
                                    $modelDetails->save(false);
                                }
                            } else {
                                $modelDetails->quantity = 0;
                            }
                            if ($model->type == 1) {
                                Yii::$app->db->createCommand("UPDATE  stocks  SET  quantity =  quantity  - $modelDetails->quantity
                             WHERE category=:category
                             and branch = :branch
                             and type = :type")
                                    ->bindValue(':category', $data->category)
                                    ->bindValue(':branch', Yii::$app->user->identity->branch)
                                    ->bindValue(':type', 1)
                                    ->execute();
                            } elseif ($model->type == 3) {
                                Yii::$app->db->createCommand("UPDATE  stocks  SET  quantity =  quantity  - $modelDetails->quantity
                            WHERE category=:category
                            and branch = :branch
                            and type = :type")
                                    ->bindValue(':category', $data->category)
                                    ->bindValue(':branch', Yii::$app->user->identity->branch)
                                    ->bindValue(':type', 3)
                                    ->execute();
                            }
                        } else {
                            $modelDetails->quantity = $data->quantity;
                            $modelDetails->save(false);
                        }
                        $zeroQuantity = SalesDetails::find()->where(['salesId' => $id, 'quantity' => 0])->count();
                        if ($zeroQuantity > 0) {
                            Yii::$app->session->setFlash('error', Yii::t('app', "عفوا الفاتورة تحتوي اصناف قيمتها 0"));
                        }
                        $SalesDetailsTotal = SalesDetails::find()->where(['salesId' => $id])->sum('salePrice * quantity');
                        if ($SalesDetailsTotal == 0) {
                            Yii::$app->session->setFlash('error', Yii::t('app', "عفوا الفاتورة لاتحتوي على اصناف الرجاء الغاءها"));
                        }
                        $salesTotal = SalesDetails::find()->where(['=', 'salesId', $id])->sum('salePrice * quantity');
                        if ($salesTotal == 0) {
                            $salesTotal = 0;
                        }
                        Yii::$app->db->createCommand("UPDATE  sales  SET  total =  $salesTotal WHERE id=:id")
                            ->bindValue(':id', $id)->execute();
                        $temp_invoice = TempInvoice::deleteAll(['created_by' => Yii::$app->user->identity->id, 'state' => 1]);
                    }
                    return $this->redirect(['print', 'id' => $id]);
                }
            } else {
                $model->save(false);
                $id = $model->id;

                $temp_invoice = TempInvoice::find()->where([
                    'created_by' => Yii::$app->user->identity->id,
                    'state' => 1
                ])->all();
                foreach ($temp_invoice as $data) {
                    $modelDetails = new salesDetails();
                    $modelDetails->salesId = $id;
                    $modelDetails->category =  $data->category;
                    $modelDetails->waitQnty = $data->waitQnty;
                    $modelDetails->costPrice = $data->costPrice;
                    $modelDetails->salePrice = $data->salePrice;
                    $modelDetails->box = $data->box;
                    $modelDetails->expire = $data->expire;
                    $modelDetails->type = $data->type;
                    $modelDetails->original_price = $data->salePrice;
                    $modelDetails->serial_number = $data->serial_number;
                    $modelDetails->mac_address = $data->mac_address;
                    if ($model->type != 4) {
                        if (Yii::$app->user->identity->seeOtherBranchQ == 0) {
                            $branch = Yii::$app->user->identity->branch;
                        } else {
                            $branch = [1, 2, 3];
                        }
                        $item = Stocks::find()
                            ->select(['max(stocks.category) as id', 'sum(stocks.quantity) as quantity'])
                            ->leftJoin('prices', 'stocks.category = prices.category')
                            ->where(['stocks.category' => $data->category])
                            ->andWhere(['<>', 'stocks.quantity', 0])
                            ->andwhere(['in', 'stocks.type', $model->type])
                            ->andWhere(['in', 'stocks.branch', $branch])
                            ->one();
                        if ($item <> null) {
                            if ($data->quantity > abs($item->quantity)) {
                                $modelDetails->quantity = abs($item->quantity);
                                $modelDetails->save(false);
                            } elseif ($data->quantity <= abs($item->quantity)) {
                                $modelDetails->quantity = $data->quantity;
                                $modelDetails->save(false);
                            } else {
                                $modelDetails->quantity = $data->quantity;
                                $modelDetails->save(false);
                            }
                        } else {
                            $modelDetails->quantity = 0;
                            $modelDetails->save(false);
                        }
                    }
                    if ($model->type == 1) {
                        Yii::$app->db->createCommand("UPDATE  stocks  SET  quantity =  quantity  - $modelDetails->quantity
                         WHERE category=:category
                         and branch = :branch
                         and type = :type")
                            ->bindValue(':category', $data->category)
                            ->bindValue(':branch', Yii::$app->user->identity->branch)
                            ->bindValue(':type', 1)
                            ->execute();
                    } elseif ($model->type == 3) {
                        Yii::$app->db->createCommand("UPDATE  stocks  SET  quantity =  quantity  - $modelDetails->quantity
                        WHERE category=:category
                        and branch = :branch
                        and type = :type")
                            ->bindValue(':category', $data->category)
                            ->bindValue(':branch', Yii::$app->user->identity->branch)
                            ->bindValue(':type', 3)
                            ->execute();
                    }
                }
                $zeroQuantity = SalesDetails::find()->where(['salesId' => $id, 'quantity' => 0])->count();
                if ($zeroQuantity > 0) {
                    Yii::$app->session->setFlash('error', Yii::t('app', "عفوا الفاتورة تحتوي اصناف قيمتها 0"));
                }
                $SalesDetailsTotal = SalesDetails::find()->where(['salesId' => $id])->sum('salePrice * quantity');

                if ($SalesDetailsTotal == 0) {
                    Yii::$app->session->setFlash('error', Yii::t('app', "عفوا الفاتورة لاتحتوي على اصناف الرجاء الغاءها"));
                }
                $salesTotal = SalesDetails::find()->where(['=', 'salesId', $id])->sum('salePrice * quantity');
                if ($salesTotal == 0) {
                    $salesTotal = 0;
                }
                Yii::$app->db->createCommand("UPDATE sales SET total = $salesTotal WHERE id=:id")
                    ->bindValue(':id', $id)->execute();

                $temp_invoice = TempInvoice::deleteAll(['created_by' => Yii::$app->user->identity->id, 'state' => 1]);
                return $this->redirect(['print', 'id' => $id]);
            }
        } elseif (Yii::$app->request->isAjax) {
            $model->at = date('Y-m-d');
            $model->clinet = 1;
            $model->total = TempInvoice::find()->where(['created_by' => Yii::$app->user->identity->id, 'state' => 1])
                ->sum('salePrice * quantity');
            $model->paid = 0;
            $model->disscount = 0;

            return $this->renderAjax('_form', [
                'model' => $model,
            ]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionPrint($id)
    {
        $totalInvoice = SalesDetails::find()->where(['salesId' => $id])
            ->sum('salePrice * quantity');

        $company = CompanyInfo::find()->one();
        $model = $this->findModel($id);
        $providerSalesDetails = new ActiveDataProvider([
            'query' => SalesDetails::find()->where(['=', 'salesId', $id]),
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
            'pagination' => false,
        ]);

        $balance = Dept::find()->where(['id' => $model->clinet])->sum('credt');
        return $this->render('print', [
            'model' => $this->findModel($id),
            'providerSalesDetails' => $providerSalesDetails,
            'company' => $company,
            'balance' => $balance,
            'totalInvoice' => $totalInvoice,
        ]);
    }

    public function actionNoprice($id)
    {
        $sql = "select sales.id, sales.at, sales.payWay, sales.total, sales.disscount, sales.paid, sales.type, sales.billId, sales.notes, sales.deleviried,
                salesDetails.quantity, salesDetails.costPrice, salesDetails.salePrice,
                Totalinventory.quantity as Qtotalinventory,
                client.name as client, client.mobile,
                category.name as category, category.serialNo, category.company, category.commCode,
                category_reservation.quantity as reservation, category.place as place
                FROM sales
                JOIN salesDetails on sales.id = salesDetails.salesId
                JOIN Totalinventory on salesDetails.category = Totalinventory.id
                JOIN client on sales.clinet = client.id
                JOIN category on category.id = salesDetails.category
                left JOIN category_reservation on salesDetails.category = category_reservation.category
                where sales.id = " . $id . " and Totalinventory.branch = " . Yii::$app->user->identity->branch . " and Totalinventory.type <> 3 
                ";
        $connection = Yii::$app->db;
        $data = $connection->createCommand($sql);
        $info = $data->queryAll();
        return $this->render('printBill', [
            'infos' => $info,
            'models' => $info,
        ]);
    }

    public function actionPrintNoPrice($id)
    {
        $company = CompanyInfo::find()->one();
        $providerSalesDetails = new ActiveDataProvider([
            'query' => SalesDetails::find()->where(['=', 'salesId', $id]),
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
            ],
            'pagination' => false,
        ]);

        return $this->render('noPrice', [
            'model' => $this->findModel($id),
            'providerSalesDetails' => $providerSalesDetails,
            'company' => $company,
        ]);
    }

    public function actionProfit()
    {
        $model = new Sales();

        if ($model->load(Yii::$app->request->post())) {

            if ($model->min_date == null) {
                echo
                '<script type="text/javascript"> alert(\'الرجاء تحديد تاريخ الحركة من\');
                        window.location.href="?r=sales%2Fftran";
                        </script>';
            }
            if ($model->max_date == null) {
                echo
                '<script type="text/javascript"> alert(\'الرجاء تحديد تاريخ الحركة إلى\');
                        window.location.href="?r=sales%2Fftran";
                        </script>';
            }
            if (Yii::$app->user->can('profit')) {

                $sql = " SELECT salesDetails.category, category.name, salesDetails.costPrice, salesDetails.salePrice,  
            (salesDetails.salePrice - salesDetails.costPrice) profit, salesDetails.quantity
            FROM  sales, salesDetails, category 
            WHERE sales.id = salesDetails.salesId and salesDetails.category=category.id
            and sales.type = 1
            and sales.at  between '" . $model->min_date . "' and '" . $model->max_date . "'
            order by salesDetails.category ";
            } else {
                $sql = " SELECT salesDetails.category, category.name, salesDetails.costPrice, salesDetails.salePrice,  
                (salesDetails.salePrice - salesDetails.costPrice) profit, salesDetails.quantity
                FROM  sales, salesDetails, category 
                WHERE sales.id = salesDetails.salesId and salesDetails.category=category.id
                and sales.type = 1
                and sales.at  between '" . $model->min_date . "' and '" . $model->max_date . "'
                and sales.branch =" . Yii::$app->user->identity->branch . " 
                
                order by salesDetails.category ";
            }

            $connection = Yii::$app->db;
            $data = $connection->createCommand($sql);
            $info = $data->queryAll();
            if ($info == null) {
                die("Sorry no thing to preview");
            }

            return $this->render('profitRep', [
                'models' => $info,
                'min_date' => $model->min_date,
                'max_date' =>  $model->max_date,
                'coun' => 1,
                'count' => 0,
                'quantity' => 0,
                'costPrice' => 0,
                'salePrice' => 0,
                'total' => 0,
                'profit' => 0,
                'totalPrice' => 0,
            ]);
        }

        return $this->render('profit', [
            'model' => $model,
        ]);
    }

    public function actionNetProfit()
    {
        $model = new Sales();

        if ($model->load(Yii::$app->request->post())) {

            if ($model->min_date == null) {
                echo
                '<script type="text/javascript"> alert(\'الرجاء تحديد تاريخ الحركة من\');
                        window.location.href="?r=sales%2Fftran";
                        </script>';
            }
            if ($model->max_date == null) {
                echo
                '<script type="text/javascript"> alert(\'الرجاء تحديد تاريخ الحركة إلى\');
                        window.location.href="?r=sales%2Fftran";
                        </script>';
            }
            if (Yii::$app->user->can('profit')) {

                $sql = "SELECT     'ارباح المبيعات' AS DESCRIBTION, sum((salesDetails.salePrice - salesDetails.costPrice) * salesDetails.quantity) AS total
            , sum((salesDetails.salePrice - salesDetails.costPrice) * salesDetails.quantity) AS NET
            FROM  sales, salesDetails, category 
            WHERE sales.id = salesDetails.salesId and salesDetails.category=category.id
            and sales.type = 1
            and sales.at  between '" . $model->min_date . "' and '" . $model->max_date . "'
     
            UNION
            SELECT     'مسترجع المبيعات', sum((salesDetails.salePrice - salesDetails.costPrice) * salesDetails.quantity),
            sum((salesDetails.salePrice - salesDetails.costPrice) * salesDetails.quantity) *-1
            FROM  sales, salesDetails, category 
            WHERE sales.id = salesDetails.salesId and salesDetails.category=category.id
            and sales.type = 2
            and sales.at  between '" . $model->min_date . "' and '" . $model->max_date . "'
    
            UNION
            SELECT     'خصومات المبيعات', sum(sales.disscount), sum(sales.disscount) *-1
            FROM  sales
            WHERE sales.type = 1
            and sales.at  between '" . $model->min_date . "' and '" . $model->max_date . "'
         
            UNION
            SELECT     'اجمالي المصروفات', COALESCE(sum(expenses.value),'0'), COALESCE(sum(expenses.value),'0') *-1
            FROM  expenses
            where expenses.at  between '" . $model->min_date . "' and '" . $model->max_date . "'
 
            UNION
            SELECT     'اجمالي المرتبات', COALESCE(sum(emp_salary.value),'0'), COALESCE(sum(emp_salary.value),'0') *-1
            FROM  emp_salary
            where emp_salary.at  between '" . $model->min_date . "' and '" . $model->max_date . "'";
            } else {
                $sql = "SELECT     'ارباح المبيعات' AS DESCRIBTION, sum((salesDetails.salePrice - salesDetails.costPrice) * salesDetails.quantity) AS total
            , sum((salesDetails.salePrice - salesDetails.costPrice) * salesDetails.quantity) AS NET
            FROM  sales, salesDetails, category 
            WHERE sales.id = salesDetails.salesId and salesDetails.category=category.id
            and sales.type = 1
            and sales.at  between '" . $model->min_date . "' and '" . $model->max_date . "'
            and sales.branch = '" . Yii::$app->user->identity->branch . "' 
            UNION
            SELECT     'مسترجع المبيعات', sum((salesDetails.salePrice - salesDetails.costPrice) * salesDetails.quantity),
            sum((salesDetails.salePrice - salesDetails.costPrice) * salesDetails.quantity) *-1
            FROM  sales, salesDetails, category 
            WHERE sales.id = salesDetails.salesId and salesDetails.category=category.id
            and sales.type = 2
            and sales.at  between '" . $model->min_date . "' and '" . $model->max_date . "'
            and sales.branch = '" . Yii::$app->user->identity->branch . "'
            UNION
            SELECT     'خصومات المبيعات', sum(sales.disscount), sum(sales.disscount) *-1
            FROM  sales
            WHERE sales.type = 1
            and sales.at  between '" . $model->min_date . "' and '" . $model->max_date . "'
            and sales.branch = '" . Yii::$app->user->identity->branch . "'
            UNION
            SELECT     'اجمالي المصروفات', COALESCE(sum(expenses.value),'0'), COALESCE(sum(expenses.value),'0') *-1
            FROM  expenses
            where expenses.at  between '" . $model->min_date . "' and '" . $model->max_date . "'
            and expenses.branch = '" . Yii::$app->user->identity->branch . "'
            UNION
            SELECT     'اجمالي المرتبات', COALESCE(sum(emp_salary.value),'0'), COALESCE(sum(emp_salary.value),'0') *-1
            FROM  emp_salary
            where emp_salary.at  between '" . $model->min_date . "' and '" . $model->max_date . "'";
            }
            $connection = Yii::$app->db;
            $data = $connection->createCommand($sql);
            $info = $data->queryAll();
            if ($info == null) {
                die("Sorry no thing to preview");
            }

            return $this->render('netProfitRep', [
                'models' => $info,
                'min_date' => $model->min_date,
                'max_date' =>  $model->max_date,
                'coun' => 1,
                'count' => 0,
                'quantity' => 0,
                'costPrice' => 0,
                'salePrice' => 0,
                'total' => 0,
                'profit' => 0,
            ]);
        }

        return $this->render('netProfit', [
            'model' => $model,
        ]);
    }

    public function actionRemove($id)
    {
        $details =  salesDetails::find()->where(['id' => $id])->one();
        $salesId = $details["salesId"];
        $sale = Sales::find()->where(['id' => $salesId])->one();

        SalesDetails::deleteAll(['id' => $id]);
        $sale->total =  SalesDetails::find()->where(['salesId' => $salesId])->sum('salePrice * quantity');
        if ($sale->total != null) {
            $sale->save(false);
            return $this->redirect(['update', 'id' => $salesId]);
        } else {
            Sales::deleteAll(['id' => $salesId]);
            return $this->redirect(['sales/index']);
        }
    }

    public function actionItemlist($q = null, $id = null)
    {
        $company = CompanyInfo::find()->one();
        if ($company->criteriaـvalue != 0) {
            $criteriaـvalue = $company->criteriaـvalue;
        } else {
            $criteriaـvalue = 1;
        }

        if ($company->rate != 0) {
            $rate = ($company->rate / 100);
            $maxPrice = 'CASE
            WHEN maxPrice >= ' . $criteriaـvalue . ' THEN round(maxPrice * "' . $rate . '" + maxPrice)
            ELSE maxPrice
            END  as maxPrice';

            $minPrice = 'CASE
            WHEN maxPrice >= ' . $criteriaـvalue . ' THEN round(minPrice * "' . $rate . '" + minPrice)
            ELSE minPrice
            END  as minPrice';
        } else {
            $maxPrice = 'maxPrice';
            $minPrice = 'minPrice';
        }

        if (Yii::$app->user->Identity->seeOtherBranchQ == 0) {
            $whereBranch = 'branch=' . Yii::$app->user->Identity->branch;
        } else {
            $whereBranch = 'branch in (1,2,3,4,5,6,7,8,9)';
        }

        Yii::$app->response->format = Yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $q = str_replace(' ', '%', $q);
            // $q = preg_replace('/' '/', '', $q);
            $query = new Query;
            if (Yii::$app->user->identity->client != null) {
                $secript = [
                    'category.id',
                    'category.name AS text',
                    'company AS company',
                    'stocks.quantity as quantity',
                    $maxPrice,
                    'serialNo AS serialNo',
                    'place',
                    'commCode',
                    'CASE
                        WHEN `type` =1
                        THEN "متوفر"
                        WHEN `type` =2
                        THEN "متوفر"
                        ELSE "قريبا" END as type',
                    'branches.name AS BRNAME',
                ];
            } else {
                if (Yii::$app->user->identity->seeCostPrice == 1) {
                    //     $secript = ['category.id, name AS text, company AS company, stocks.quantity as quantity,
                    // maxPrice as maxPrice, costPrice as costPrice, serialNo AS serialNo, minPrice AS minPrice, place, commCode,
                    $secript = [
                        'category.id',
                        'category.name AS text',
                        'company AS company',
                        'stocks.quantity as quantity',
                        // 'CASE
                        //      WHEN maxPrice >= '. $criteriaـvalue .' THEN round(maxPrice * "' . $rate . '" + maxPrice)
                        //      ELSE maxPrice
                        //      END  as maxPrice',
                        $maxPrice,
                        'costPrice as costPrice',
                        'serialNo AS serialNo',
                        // 'CASE
                        //      WHEN minPrice >= '. $criteriaـvalue .' THEN round(minPrice * "' . $rate . '" + minPrice)
                        //      ELSE minPrice
                        //      END  as minPrice',
                        $minPrice,
                        'place',
                        'commCode',
                        'CASE
                            WHEN `type` =1
                            THEN "متوفر"
                            WHEN `type` =2
                            THEN "متوفر"
                            ELSE "قريبا" END as type',
                        'branches.name AS BRNAME',
                    ];
                } else {
                    $secript = [
                        'category.id',
                        'category.name AS text',
                        'company AS company',
                        'stocks.quantity as quantity',
                        // 'CASE
                        //     WHEN maxPrice >= '. $criteriaـvalue . ' THEN round(maxPrice * "' . $rate . '" + maxPrice)
                        //     ELSE maxPrice
                        //     END  as maxPrice',
                        $maxPrice,
                        'serialNo AS serialNo',
                        // 'CASE
                        //     WHEN minPrice >= '. $criteriaـvalue . ' THEN round(minPrice * "' . $rate . '" + minPrice)
                        //     ELSE minPrice
                        //     END  as minPrice',
                        $minPrice,
                        'place',
                        'commCode',
                        'CASE
                        WHEN `type` =1
                        THEN "متوفر"
                        WHEN `type` =2
                        THEN "متوفر"
                        ELSE "قريبا" END as type',
                        'branches.name AS BRNAME',
                    ];
                }
            }
            $query->select($secript)
                // ->from('Totalinventory')
                ->from('stocks')
                ->leftJoin('category', 'stocks.category = category.id')
                ->leftJoin('prices', 'prices.category = category.id')
                ->leftJoin('branches', 'branches.id = stocks.branch')
                ->where('category.name like' . "'%" . $q . "%'")
                //->andWhere('branch=' . Yii::$app->user->Identity->branch)
                ->andWhere($whereBranch)
                ->orWhere(['like', 'serialNo', $q])
                ->orWhere(['like', 'commCode', $q])
                ->orWhere((['like', 'category.id', $q]))
                ->orWhere((['like', 'place', $q]))
                ->orWhere((['like', 'company', $q]))
                //->andWhere((['=', 'stocks.branch', Yii::$app->user->identity->branch]))
                ->andWhere($whereBranch)
                ->andWhere(['<>', 'stocks.quantity', 0])
                ->limit(60)
                ->orderBy('category.id', 'asc');
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        } elseif ($id > 0) {
            $out['results'] = [
                'id' => $id,
                'text' => TotalInventory::find($id)->name,
                'company' => TotalInventory::find($id)->company,
                'quantity' => TotalInventory::find($id)->quantity,
                'maxPrice' => TotalInventory::find($id)->maxPrice,
                'minPrice' => TotalInventory::find($id)->minPrice,
                'serialNo' => TotalInventory::find($id)->serialNo,
                'type' => TotalInventory::find($id)->type,
                'commCode' => TotalInventory::find($id)->commCode
            ];
        }
        return $out;
    }

    public function actionBackCreate()
    {
        $model = new Sales();
        $model->payWay = 1;
        if ($model->load(Yii::$app->request->post())) {

            $model->total = TempBackSales::find()->where(['created_by' => Yii::$app->user->identity->id, 'state' => 1])
                ->sum('salePrice * quantity');
            $model->type = 2;
            $model->branch = Yii::$app->user->identity->branch;
            $model->billId = Sales::find()->where(['=', 'type', $model->type])->max('billId') + 1;
            $model->created_at = date('Y-m-d H:i:s');
            $model->user_insert = Yii::$app->user->id;
            $model->disscount = 0;
            $model->paid = 0;
            $created_by = Yii::$app->user->identity->id;
            $model->save();
            $id = $model->id;

            $temp_invoice = new TempBackSales();
            $temp_invoice = TempBackSales::find()->where([
                'created_by' => Yii::$app->user->identity->id,
                'state' => 1
            ])->all();
            foreach ($temp_invoice as $data) {
                $modelDetails = new salesDetails();

                $modelDetails->salesId = $id;
                $modelDetails->category =  $data->category;
                $modelDetails->quantity = $data->quantity;
                $modelDetails->costPrice = $data->costPrice;
                $modelDetails->salePrice = $data->salePrice;
                $modelDetails->box = $data->box;
                $modelDetails->expire = $data->expire;
                $modelDetails->original_price = $data->salePrice;
                $modelDetails->serial_number = $data->serial_number;
                $modelDetails->save(false);
                if ($model->type == 2) {
                    Yii::$app->db->createCommand("UPDATE  stocks  SET  quantity =  quantity  + $data->quantity 
                    WHERE category=:category
                    and branch = :branch
                    and type = :type")
                        ->bindValue(':category', $data->category)
                        ->bindValue(':branch', Yii::$app->user->identity->branch)
                        ->bindValue(':type', 1)
                        ->execute();
                }
            }
            $temp_invoice = TempBackSales::deleteAll([
                'created_by' => Yii::$app->user->identity->id,
                'state' => 1
            ]);

            return $this->redirect(['print', 'id' => $id]);
        } elseif (Yii::$app->request->isAjax) {
            $model->at = date('Y-m-d');
            $model->clinet = 1;
            $model->total = TempBackSales::find()->where(['created_by' => Yii::$app->user->identity->id, 'state' => 1])
                ->sum('salePrice * quantity');
            $model->paid = 0;
            $model->disscount = 0;

            return $this->renderAjax('back', [
                'model' => $model,
            ]);
        } else {

            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionSaveAsNew($oldId)
    {
        $model = new Sales();
        $model->payWay = 1;
        if ($model->load(Yii::$app->request->post())) {

            $model->total = SalesDetails::find()->where(['salesId' => $oldId])->sum('salePrice * quantity');
            $model->branch = Yii::$app->user->identity->branch;
            $model->billId = Sales::find()->where(['=', 'type', $model->type])->max('billId') + 1;
            $model->created_at = date('Y-m-d H:i:s');
            $model->user_insert = Yii::$app->user->id;
            $created_by = Yii::$app->user->identity->id;
            $model->save();

            $id = $model->id;

            $salesDetails = new SalesDetails();
            $salesDetails = SalesDetails::find()->where(['salesId' => $oldId])->all();
            foreach ($salesDetails as $data) {
                $modelDetails = new salesDetails();

                $modelDetails->salesId = $id;
                $modelDetails->category =  $data->category;
                $modelDetails->quantity = $data->quantity;
                $modelDetails->costPrice = $data->costPrice;
                $modelDetails->salePrice = $data->salePrice;
                $modelDetails->box = $data->box;
                $modelDetails->expire = $data->expire;
                $modelDetails->type = $data->type;
                $modelDetails->original_price = $data->salePrice;
                $modelDetails->serial_number = $data->serial_number;
                $modelDetails->mac_address = $data->mac_address;
                $modelDetails->save(false);
                //Yii::$app->db->createCommand("UPDATE `stocks` SET `quantity`= `quantity` - '$data->quantity' WHERE `category`='$data->category'")->execute();

            }

            return $this->redirect(['print', 'id' => $id]);
        } elseif (Yii::$app->request->isAjax) {
            $model->at = date('Y-m-d');
            $model->clinet = 1;
            $model->total = SalesDetails::find()->where(['salesId' => $oldId])
                ->sum('salePrice * quantity');
            $model->paid = 0;
            $model->disscount = 0;

            return $this->renderAjax('_form', [
                'model' => $model,
            ]);
        } else {

            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionPdf($id)
    {

        $totalInvoice = SalesDetails::find()->where(['salesId' => $id])
            ->sum('salePrice * quantity');

        $company = CompanyInfo::find()->one();
        $model = $this->findModel($id);
        $providerSalesDetails = new Yii\data\ArrayDataProvider([
            'allModels' => $model->salesDetails,
            'pagination' => false,
        ]);

        $balance = Dept::find()->where(['id' => $model->clinet])->sum('credt');

        if ($model->c->email  == '') {
            Yii::$app->session->setFlash('error', Yii::t('app', "Sorry This Client Has Not Email"));
            return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
        }

        $mpdf = new mPDF();
        $mpdf->WriteHTML($this->render('print', [
            'model' => $this->findModel($id),
            'providerSalesDetails' => $providerSalesDetails,
            'company' => $company,
            'balance' => $balance,
            'totalInvoice' => $totalInvoice,
        ]));

        $path = $mpdf->Output('img/upload/' . $model->id . '.pdf', 'F');
        $send = Yii::$app->mailer->compose()
            ->setFrom('algmatyali@gmail.com')
            ->setTo($model->c->email)
            ->setSubject('فاتورة مبيعات ')
            ->setTextBody('Plain text content. YII2 Application')
            ->setHtmlBody('<b>السلام عليكم ورحمة الله وبركاته.</b><br>
        السيـد: ' . ' ' . $model->c->name . '<br>' . ' أرفق لك مع رسالتي هذه نسخة من فاتورة رقم ' . ' ' . $model->billId)
            ->attach(Yii::getAlias('@webroot/img/upload/') . $model->id . '.pdf')
            ->send();
        if ($send) {
            Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Successfully Sent The Invoice To Customer'));
        }
        return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
    }

    public function actionDeserving()
    {
        if (Yii::$app->user->identity->client == NULL) {
            $sql = " SELECT max(sales.billId) as billId, max(sales.at) as at, max(sales.total) total
            , max(sales.disscount) as disscount, max(sales.paid) as paid, max(sales.deserving) as deserving
            , max(client.name) as clientName, sum(dept.credt) as credt, max(sales.id) as id
            , max(sales.clinet) as client FROM `sales` 
            LEFT JOIN `client` ON client.id = sales.clinet 
            LEFT JOIN `dept` ON client.id = dept.id 
            WHERE sales.deserving - CURRENT_DATE() <= 4 
            GROUP BY `sales`.`id`, `sales`.`deserving`";
        } else {
            $sql = " SELECT max(sales.billId) as billId, max(sales.at) as at, max(sales.total) total
            , max(sales.disscount) as disscount, max(sales.paid) as paid, max(sales.deserving) as deserving
            , max(client.name) as clientName, sum(dept.credt) as credt, max(sales.id) as id
            , max(sales.clinet) as client FROM `sales` 
            LEFT JOIN `client` ON client.id = sales.clinet 
            LEFT JOIN `dept` ON client.id = dept.id 
            WHERE sales.deserving - CURRENT_DATE() <= 4 and sales.clinet in(" . Yii::$app->user->identity->client . ")
            GROUP BY `sales`.`id`, `sales`.`deserving`";
        }


        $connection = Yii::$app->db;
        $data = $connection->createCommand($sql);
        $info = $data->queryAll();
        return $this->render('deserving', [
            'models' => $info,
            'count' => 0,
        ]);
    }

    public function actionDone($id)
    {
        Sales::updateAll(['deserving' => null,], ['=', 'id', $id]);

        return $this->redirect(['deserving']);
    }

    public function actionTransferToTempInvoice($id)
    {
        $Details = SalesDetails::find()->where(['id' => $id])->one();
        $salesId = $Details->salesId;
        $temp_invoice = new TempInvoice();
        $salesDetails = SalesDetails::find()->where(['id' => $id])->all();
        $sale = Sales::find()->where(['id' => $Details->salesId])->one();

        foreach ($salesDetails as $data) {
            $modelTempInvoice = new TempInvoice();
            $modelTempInvoice->category =  $data->category;
            $modelTempInvoice->quantity = $data->quantity;
            $modelTempInvoice->costPrice = $data->costPrice;
            $modelTempInvoice->salePrice = $data->salePrice;
            $modelTempInvoice->box = $data->box;
            $modelTempInvoice->expire = $data->expire;
            $modelTempInvoice->type = $data->type;
            $modelTempInvoice->state = 1;
            $modelTempInvoice->save(false);
            SalesDetails::deleteAll(['id' => $id]);
            if ($sale->type == 1) {
                Yii::$app->db->createCommand("UPDATE  stocks  SET  quantity =  quantity  + $data->quantity 
                WHERE category=:category
                and branch = :branch
                and type = :type")
                    ->bindValue(':category', $data->category)
                    ->bindValue(':branch', Yii::$app->user->identity->branch)
                    ->bindValue(':type', 1)
                    ->execute();
            } elseif ($sale->type == 3) {
                Yii::$app->db->createCommand("UPDATE  stocks  SET  quantity =  quantity  + $data->quantity 
                WHERE category=:category
                and branch = :branch
                and type = :type")
                    ->bindValue(':category', $data->category)
                    ->bindValue(':branch', Yii::$app->user->identity->branch)
                    ->bindValue(':type', 3)
                    ->execute();
            }
            $sale = Sales::find()->where(['id' => $Details->salesId])->one();
            $sale->total =  SalesDetails::find()->where(['salesId' => $Details->salesId])->sum('salePrice * quantity');

            if ($sale->total == null) {
                Sales::deleteAll(['id' => $salesId]);
                return $this->redirect(['temp-invoice/create']);
            } else {
                $sale->save(false);
                return $this->redirect(['update', 'id' => $salesId]);
            }
        }

        return $this->redirect(['update', 'id' => $salesId]);
    }

    public function actionItemlistid($q = null, $id = null)
    {
        $company = CompanyInfo::find()->one();
        if ($company->criteriaـvalue != 0) {
            $criteriaـvalue = $company->criteriaـvalue;
        } else {
            $criteriaـvalue = 1;
        }

        if (Yii::$app->user->Identity->seeOtherBranchQ == 0) {
            $whereBranch = 'branch=' . Yii::$app->user->Identity->branch;
        } else {
            $whereBranch = 'branch in (1,2,3,4,5,6,7,8,9)';
        }

        if ($company->rate != 0) {
            $rate = ($company->rate / 100);
            $maxPrice = 'CASE
            WHEN maxPrice >= ' . $criteriaـvalue . ' THEN round(maxPrice * "' . $rate . '" + maxPrice)
            ELSE maxPrice
            END  as maxPrice';

            $minPrice = 'CASE
            WHEN maxPrice >= ' . $criteriaـvalue . ' THEN round(minPrice * "' . $rate . '" + minPrice)
            ELSE minPrice
            END  as minPrice';
        } else {
            $maxPrice = 'maxPrice';
            $minPrice = 'minPrice';
        }

        Yii::$app->response->format = Yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $q = str_replace(' ', '%', $q);
            $query = new Query;
            if (Yii::$app->user->identity->client != null) {
                $secript = [
                    'category.id',
                    'category.name AS text',
                    'company AS company',
                    'stocks.quantity as quantity',
                    // 'CASE
                    //          WHEN maxPrice >= '. $criteriaـvalue .' THEN round(maxPrice * "' . $rate . '" + maxPrice)
                    //          ELSE maxPrice
                    //          END  as maxPrice',
                    $maxPrice,
                    'serialNo AS serialNo',
                    'place',
                    'commCode',
                    'CASE
                WHEN `type` =1
                THEN "متوفر"
                WHEN `type` =2
                THEN "متوفر"
                ELSE "قريبا" END as type',
                    'branches.name as BRNAME',
                ];
            } else {
                if (Yii::$app->user->identity->seeCostPrice == 1) {
                    $secript = [
                        'category.id',
                        'category.name AS text',
                        'company AS company',
                        'stocks.quantity as quantity',
                        // 'CASE
                        //      WHEN maxPrice >= '. $criteriaـvalue .' THEN round(maxPrice * "' . $rate . '" + maxPrice)
                        //      ELSE maxPrice
                        //      END  as maxPrice',
                        $maxPrice,
                        'costPrice as costPrice',
                        'serialNo AS serialNo',
                        // 'CASE
                        //     WHEN minPrice >= '. $criteriaـvalue . ' THEN round(minPrice * "' . $rate . '" + minPrice)
                        //     ELSE minPrice
                        //     END  as minPrice',
                        $minPrice,
                        'place',
                        'commCode',
                        'CASE
            WHEN `type` =1
            THEN "متوفر"
            WHEN `type` =2
            THEN "متوفر"
            ELSE "قريبا" END as type',
                        'branches.name as BRNAME'
                    ];
                } else {
                    $secript = [
                        'category.id',
                        'category.name AS text',
                        'company AS company',
                        'stocks.quantity as quantity',
                        // 'CASE
                        //      WHEN maxPrice >= '. $criteriaـvalue .' THEN round(maxPrice * "' . $rate . '" + maxPrice)
                        //      ELSE maxPrice
                        //      END  as maxPrice',
                        $maxPrice,
                        'serialNo AS serialNo',
                        // 'CASE
                        //     WHEN minPrice >= '. $criteriaـvalue . ' THEN round(minPrice * "' . $rate . '" + minPrice)
                        //     ELSE minPrice
                        //     END  as minPrice',
                        $minPrice,
                        'place',
                        'commCode',
                        'CASE
            WHEN `type` =1
            THEN "متوفر"
            WHEN `type` =2
            THEN "متوفر"
            ELSE "قريبا" END as type',
                        'branches.name as BRNAME'
                    ];
                }
            }
            $query->select($secript)
                ->from('category')
                ->from('stocks')
                ->leftJoin('category', 'stocks.category = category.id')
                ->leftJoin('prices', 'prices.category = category.id')
                ->leftJoin('branches', 'branches.id = stocks.category')
                //->andWhere('stocks.branch=' . Yii::$app->user->Identity->branch)
                ->Where((['=', 'category.id', $q]))
                //->andWhere((['=', 'stocks.branch', Yii::$app->user->identity->branch]))
                ->andWhere($whereBranch)
                ->andWhere(['=',  'category.status', 0])
                ->andWhere(['!=', 'stocks.quantity', 0])
                ->limit(60);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        } elseif ($id > 0) {
            $out['results'] = [
                'id' => $id,
                'text' => TotalInventory::find($id)->name,
                'company' => TotalInventory::find($id)->company,
                'quantity' => TotalInventory::find($id)->quantity,
                'maxPrice' => TotalInventory::find($id)->maxPrice,
                'minPrice' => TotalInventory::find($id)->minPrice,
                'serialNo' => TotalInventory::find($id)->serialNo,
                'type' => TotalInventory::find($id)->type,
                'commCode' => TotalInventory::find($id)->commCode
            ];
        }
        return $out;
    }

    public function actionFast($id)
    {
        $model = new Sales();
        $dataProvider = new ActiveDataProvider([
            'query' => SalesDetails::find()
                ->Where([
                    'salesId' => $id,
                ]),
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]
            ],
            'pagination' => [
                'pageSize' => 100
            ],
        ]);

        if ($model->load(Yii::$app->request->post())) {

            $selection = Yii::$app->request->post('selection');

            foreach ($selection as $select) {
                $this->actionTransferToTempInvoice($select);
                // $prices = Prices::find()->where(['category'=>$select])->one();
                // $category = Category::find()->where(['id'=>$id])->one();

                // $model = new TempInvoice();
                // $model->category= $id;

                // $model->quantity=1;
                // $model->costPrice = $prices->costPrice;
                // $model->salePrice = $prices->minPrice;
                // $model->box=$category->box;
                // $model->state= 1;

                // $model->save(false);

            }
            // return $this->redirect(['create']);
        }

        return $this->renderAjax('createFast', [
            // 'searchModel' => $searchModel,
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionHistrans($client, $allData, $type)
    {
        if ($allData != 0) {
            $min_date = '2020-01-30';
            $max_date = date('Y-m-d');
        }

        if ($type == 0 || $type == 2) {
            $sqlSum = " SELECT 
            sum(histrans_client.dept) as sader, sum(histrans_client.credt) as wared, max(histrans_client.type) as type FROM histrans_client
            where histrans_client.Type in(" . $type . ") and histrans_client.id = " . $client . " 
            and histrans_client.trandate < '" . $min_date . "'  ";
            $connection = Yii::$app->db;
            $data = $connection->createCommand($sqlSum);
            $lastBalance = $data->queryAll();
        }

        if ($type == 1) {
            $sqlSum = " SELECT 
            sum(histrans_client.dept) as sader, sum(histrans_client.credt) as wared, max(histrans_client.type) as type FROM histrans_client
            where histrans_client.Type in(" . $type . ") and histrans_client.id = " . $client . " 
            and histrans_client.trandate < '" . $min_date . "'  ";
            $connection = Yii::$app->db;
            $data = $connection->createCommand($sqlSum);
            $lastBalance = $data->queryAll();
        }

        if ($type == 0 || $type == 2) {
            $type = '0,2';
            $sql = " SELECT histrans_client.id, histrans_client.trandate, histrans_client.name as name,
            histrans_client.dept as sader, histrans_client.billId as billId, histrans_client.kind as kind,
            histrans_client.credt as wared, histrans_client.printId as printId, histrans_client.type as type, histrans_client.deleviried as deleviried
            FROM histrans_client
            where histrans_client.Type in(" . $type . ") and histrans_client.id = " . $client . " 
            and histrans_client.trandate between '" . $min_date . "' and '" . $max_date . "' 
            and histrans_client.sort <> 1
            order by  histrans_client.trandate, histrans_client.billId";
            $connection = Yii::$app->db;
            $data = $connection->createCommand($sql);
            $info = $data->queryAll();
            if ($info == null) {
                echo '<script type="text/javascript"> 
                    alert("عفوا لايوجد بيانات للعرض");
                    window.location.href="?r=client/histrans"
                    </script>';
            }
        } elseif ($type == 1) {
            $type = '1';
            $sql = " SELECT histrans_supplier.id, histrans_supplier.kind, histrans_supplier.trandate, histrans_supplier.name as name, 
            histrans_supplier.dept as wared, histrans_supplier.credt as sader, histrans_supplier.billId as billId,
            histrans_supplier.printId as printId, histrans_supplier.type as type
             FROM histrans_supplier
            where histrans_supplier.Type in(" . $type . ") and histrans_supplier.id = " . $client . " and histrans_supplier.trandate 
            between '" . $min_date . "' and '" . $max_date . "' 
            and histrans_supplier.sort <> 1
            order by histrans_supplier.trandate, histrans_supplier.billId";
            $connection = Yii::$app->db;
            $data = $connection->createCommand($sql);
            $info = $data->queryAll();
            if ($info == null) {
                echo '<script type="text/javascript"> 
                    alert("عفوا لايوجد بيانات للعرض");
                    window.location.href="?r=client/histrans"
                    </script>';
            }
        }

        return $this->render('/client/histransrep', [
            'models' => $info,
            'min_date' => $min_date,
            'max_date' => $max_date,
            'sumwared' => 0,
            'sumsader' => 0,
            'sum' => 0,
            'coun' => 1,
            'count' => 0,
            'lastBalance' => $lastBalance,
            'balance' => 0,
            'id' => null,
        ]);
    }

    public function actionSavePdf($id)
    {

        $totalInvoice = SalesDetails::find()->where(['salesId' => $id])
            ->sum('salePrice * quantity');

        $model = $this->findModel($id);

        $balance = Dept::find()->where(['id' => $model->clinet])->sum('credt');

        // get your HTML raw content without any layouts or scripts
        $sql = "select sales.id, sales.at, sales.payWay, sales.total, sales.disscount, sales.paid, sales.type, sales.billId, sales.notes, sales.deleviried,
                salesDetails.quantity, salesDetails.costPrice, salesDetails.salePrice,
                Totalinventory.quantity as Qtotalinventory,
                client.name as client, client.mobile,
                category.name as category, category.serialNo, category.company,
                category_reservation.quantity as reservation
                FROM sales
                JOIN salesDetails on sales.id = salesDetails.salesId
                JOIN Totalinventory on salesDetails.category = Totalinventory.id
                JOIN client on sales.clinet = client.id
                JOIN category on category.id = salesDetails.category
                left JOIN category_reservation on salesDetails.category = category_reservation.category
                where sales.id = " . $id . " and Totalinventory.branch = " . Yii::$app->user->identity->branch . " and Totalinventory.type <> 3 
                ";
        $connection = Yii::$app->db;
        $data = $connection->createCommand($sql);
        $info = $data->queryAll();


        // $balance = Dept::find()->where(['id'=>$info])->sum('credt');

        $content = $this->renderPartial('printPdf', [
            'model' => $this->findModel($id),
            'infos' => $info,
            'models' => $info,
            'balance' => $balance,
        ]);
        Yii::$app->response->format = Yii\web\Response::FORMAT_RAW;

        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_UTF8,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_DOWNLOAD, //DEST_BROWSER, 
            // your html content input
            'filename' => $model->c->name . '-' . $model->billId,

            'content' => $content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting 
            'cssFile' => ['@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css', '@vendor/bower-asset/bootstrap-rtl/dist/css/bootstrap-rtl.css'],

            'defaultFont' => 'Sans-serif',
            // any css to be embedded if required
            // 'cssInline' => '.kv-heading-1{font-size:10px}', 
            // set mPDF properties on the fly
            'options' => ['title' => 'فاتورة مبيعات'],
            // call mPDF methods on the fly
            'methods' => [
                'SetHeader' => [''],
                //  'SetFooter'=>['{PAGENO}'],
            ]
        ]);

        // return the pdf output as per the destination setting
        return $pdf->render();
    }

    public function actionCategoryHistrans($category, $allData)
    {
        $category = Category::find()->select('id')->where(['id' => $category])->one();

        if ($allData != 0) {
            $min_date = '2010-01-01';
            $max_date = date('Y-m-d');
        }

        $sqlSum = " SELECT 
            sum(category_histrans.quantity) as quantity 
            FROM category_histrans
            where category_histrans.id = " . $category->id . " 
            and category_histrans.tranDate < '" . $min_date . "'  ";
        $connection = Yii::$app->db;
        $data = $connection->createCommand($sqlSum);
        $lastBalance = $data->queryAll();

        $sql = " SELECT category_histrans.kind_id, category_histrans.id, category_histrans.printId, category_histrans.name as name, category_histrans.quantity as quantity, category_histrans.unit as unit, 
            category_histrans.box as box, category_histrans.class as class, category_histrans.client as client,
            branches.name as branch, category_histrans.kind as kind, category_histrans.trandate as trandate, category_histrans.billId as billId
            , category_histrans.deleviried as deleviried 
            FROM category_histrans, branches
            where category_histrans.branch = branches.id and category_histrans.id = " . $category->id . " 
            and category_histrans.trandate  between '" . $min_date . "' and '" . $max_date . "' 
            order by category_histrans.trandate
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

        return $this->render('/category/histransrep', [
            'models' => $info,
            'min_date' => $min_date,
            'max_date' => $max_date,
            'sumsader' => 0,
            'sumwared' => 0,
            'sum' => 0,
            'coun' => 1,
            'count' => 0,
            'name' => null,
            'id' => 0,
            'lastBalance' => $lastBalance,
            'sumPurchase' => 0,
            'sumBackPurchase' => 0,
            'sumSales' => 0,
            'sumBackSales' => 0,
        ]);
    }

    public function actionWaitQnty()
    {
        $searchModel = new SalesDetailsSearchWQ();
        $searchModel->commCode = '99';
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('waitByCat', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionWaitQntyClient()
    {
        $searchModel = new SalesDetailsSearchWQClient();
        $searchModel->commCode = '99';
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('waitByCatClient', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionDelev($id)
    {
        Sales::updateAll(['deleviried' => 1, 'deleviryAt' => date('Y-m-d')], ['=', 'id', $id]);

        Yii::$app->session->setFlash('success', Yii::t('app', "تمت عملية تغيير حالة الفاتورة بنجاح"));

        return $this->redirect(['print', 'id' => $id]);
    }
}
