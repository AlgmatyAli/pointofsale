<?php

namespace app\controllers;

use Yii;

use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\Totalinventory;
use app\models\CompanyInfo;
use app\models\Stocks;
use yii\db\Query;
use yii\helpers\Json;

class ExceptController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionItemlist($q = null, $id = null)
    {
        // if(Yii::$app->user->Identity->seeOtherBranchQ == 0){
        //     $whereBranch = 'branch='.Yii::$app->user->Identity->branch;
        // }else{
        //     $whereBranch = 'branch in (1,2,3,4,5,6,7,8,9)';
        // }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $q = str_replace(' ', '%', $q);
            $query = new Query;
            $secript = [
                'category.id', 'name AS text', 'company AS company', 'stocks.quantity as quantity',
                'maxPrice as maxPrice', 'costPrice as costPrice', 'serialNo AS serialNo', 'minPrice AS minPrice', 'place', 'commCode',
            ];
            $query->select(
                $secript
            )
                ->from('category')
                ->leftJoin('prices', 'prices.category = category.id')
                ->leftJoin('stocks', 'stocks.category = category.id')
                ->where('category.name like' . "'%" . $q . "%'")
                //->andWhere($whereBranch)
                ->orWhere(['like', 'category.serialNo', $q])
                ->orWhere(['like', 'category.commCode', $q])
                ->orWhere((['like', 'category.id', $q]))
                ->orWhere((['like', 'category.place', $q]))
                ->orWhere((['like', 'category.company', $q]))
                //->andWhere($whereBranch)
                ->andWhere(['=', 'category.status', 0])
                ->andWhere(['!=', 'stocks.type', 3])
                ->limit(60);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        return $out;
    }

    public function actionGetInv($category)
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

        $value = Stocks::find()
            ->leftJoin('prices', 'stocks.category = prices.category')
            ->select([
                'stocks.category AS id', 'stocks.quantity as quantity', 'costPrice',
                $maxPrice, $minPrice,
                'prices.minPrice2',
                'prices.minPrice3'
            ])
            // ->Where(['stocks.branch' => Yii::$app->user->identity->branch])
            ->andwhere(['stocks.category' => $category])
            ->andwhere(['in', 'type',  [1, 2]])
            ->asArray()->one();
        echo json::encode($value);
    }

    public function actionItemlistid($q = null, $id = null)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $q = str_replace(' ', '%', $q);
            $query = new Query;
            $secript = [
                'category.id', 'category.name AS text', 'company AS company', 'stocks.quantity as quantity',
                'maxPrice as maxPrice', 'costPrice as costPrice', 'serialNo AS serialNo', 'minPrice AS minPrice', 'place', 'commCode',
                'CASE 
            WHEN `type` =1 
            THEN "متوفر" 
            WHEN `type` =2 
            THEN "متوفر" 
            ELSE "قريبا" END as type'
            ];
            $query->select(
                $secript
            )
                ->from('category')
                ->leftJoin('prices', 'prices.category = category.id')
                ->leftJoin('stocks', 'stocks.category = category.id')
                ->Where((['=', 'category.id', $q]))
                ->andWhere(['=', 'category.status', 0])
                ->andWhere(['!=', 'stocks.type', 3])
                ->limit(60);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        } elseif ($id > 0) {
            $out['results'] = [
                'id' => $id, 'text' => TotalInventory::find($id)->name,
                'company' => TotalInventory::find($id)->company, 'quantity' => TotalInventory::find($id)->quantity,
                'maxPrice' => TotalInventory::find($id)->maxPrice, 'minPrice' => TotalInventory::find($id)->minPrice, 'serialNo' => TotalInventory::find($id)->serialNo, 'type' => TotalInventory::find($id)->type, 'commCode' => TotalInventory::find($id)->commCode
            ];
        }
        return $out;
    }
}
