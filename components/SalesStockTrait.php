<?php

namespace app\components;

use Yii;
use yii\db\Query;

/**
 * منطق المخزون المشترك بين المبيعات الاعتيادية وشاشة التجزئة (قفل + معاملة + تغيير نسبي للرصيد + السعر الافتراضي).
 * القفل نفسه المستعمل في النقل والجرد: pos_stock_change.
 */
trait SalesStockTrait
{
    /**
     * ينفّذ العملية داخل قفل + معاملة. أي \DomainException تخرج منها رسالتها تُعرض للمستخدم،
     * وأي استثناء آخر يُلغي المعاملة كلها ثم يُرمى كما هو.
     */
    private function withStockLock(callable $fn)
    {
        $db = Yii::$app->db;
        $got = (int) $db->createCommand('SELECT GET_LOCK(:n, 10)', [':n' => 'pos_stock_change'])->queryScalar() === 1;
        if (!$got) {
            throw new \DomainException('النظام مشغول، أعد المحاولة بعد لحظات.');
        }
        $tx = $db->beginTransaction();
        try {
            $result = $fn();
            $tx->commit();
            return $result;
        } catch (\Throwable $e) {
            $tx->rollBack();
            throw $e;
        } finally {
            $db->createCommand('SELECT RELEASE_LOCK(:n)', [':n' => 'pos_stock_change'])->queryScalar();
        }
    }

    /** تغيير نسبي ذرّي لرصيد صنف في فرع ونوع مخزون؛ ينشئ الصف برصيد (delta) إن لم يوجد */
    private function changeStock($category, $branch, $stockType, $delta, $createIfMissing = true)
    {
        $delta = (float) $delta;
        if ($delta == 0.0) {
            return;
        }
        $db = Yii::$app->db;
        $params = [':c' => (int) $category, ':b' => (int) $branch, ':t' => (int) $stockType];
        $affected = $db->createCommand(
            'UPDATE stocks SET quantity = quantity + :d WHERE category = :c AND branch = :b AND type = :t',
            $params + [':d' => $delta]
        )->execute();
        if ($affected === 0 && $createIfMissing) {
            $exists = (new Query())->from('stocks')->where(['category' => (int) $category, 'branch' => (int) $branch, 'type' => (int) $stockType])->exists();
            if (!$exists) {
                $db->createCommand()->insert('stocks', [
                    'category' => (int) $category,
                    'branch' => (int) $branch,
                    'type' => (int) $stockType,
                    'quantity' => $delta,
                ])->execute();
            }
        }
    }

    /**
     * أثر نوع الفاتورة على المخزون: [نوع المخزون، الإشارة] أو null بلا أثر.
     * 1 نهائية: ينقص النوع 1 | 3 حجز: ينقص النوع 3 | 2 مسترجع: يزيد النوع 1 | 4 مبدئية: لا أثر.
     */
    private function stockEffect($salesType)
    {
        switch ((int) $salesType) {
            case 1:
                return [1, -1];
            case 3:
                return [3, -1];
            case 2:
                return [1, 1];
        }
        return null;
    }

    /** يطبّق (أو يعكس) أثر كمية سطر على مخزون فرع الفاتورة */
    private function applyDetail($salesType, $category, $branch, $qty, $reverse = false)
    {
        $effect = $this->stockEffect($salesType);
        if ($effect === null) {
            return;
        }
        $delta = $effect[1] * (float) $qty * ($reverse ? -1 : 1);
        $this->changeStock($category, $branch, $effect[0], $delta, true);
    }

    /** كل أرقام الفروع (بدل الأرقام الثابتة 1،2،3 في الكود القديم) */
    private function allBranchIds()
    {
        static $ids = null;
        if ($ids === null) {
            $ids = array_map('intval', (new Query())->select('id')->from('branches')->column());
            if (!$ids) {
                $ids = [(int) Yii::$app->user->identity->branch];
            }
        }
        return $ids;
    }

    /** الرصيد المتاح للبيع حسب أنواع المخزون المعطاة، بنفس منطق الكود القديم (الفروع التي يراها المستخدم + خيار الرصيد الصفري) */
    private function availableQty($category, array $stockTypes, $company)
    {
        $identity = Yii::$app->user->identity;
        $branches = ($identity->seeOtherBranchQ == 0) ? [(int) $identity->branch] : $this->allBranchIds();
        $q = (new Query())->from('stocks')
            ->where(['category' => (int) $category])
            ->andWhere(['in', 'type', array_map('intval', $stockTypes)])
            ->andWhere(['in', 'branch', $branches]);
        if ($company->zeroQnty == 0) {
            $q->andWhere(['<>', 'quantity', 0]);
        }
        return (float) $q->sum('quantity');
    }

    /**
     * الكمية التي تُحفظ فعلياً في سطر الفاتورة.
     * إن كان البيع بلا رصيد مفعلاً (zeroQnty != 0) تمر الكمية كما هي. وإلا تُقيَّد بالرصيد المتبقي (الرصيد السالب = صفر)،
     * فإن كان الرصيد صفراً عادت 0 ولا يُحفظ السطر.
     */
    private function allowedQty($category, $qty, array $stockTypes, $company)
    {
        if ($company->zeroQnty != 0) {
            return $qty;
        }
        $avail = max(0, $this->availableQty($category, $stockTypes, $company));
        return min((float) $qty, $avail);
    }

    /** السعر الافتراضي للصنف مع نسبة الزيادة (rate) والتقريب، بنفس منطق الفاتورة المؤقتة */
    private function defaultSalePrice($prices, $company)
    {
        $price = (float) $prices->maxPrice;
        $criteria = ($company->criteriaـvalue != 0) ? (float) $company->criteriaـvalue : 1.0;
        if ($company->rate != 0 && $price >= $criteria) {
            $price = round($price * ((float) $company->rate / 100) + $price);
        }
        if ($company->criteriaـvalue != 0) {
            $v = (int) round($price);
            if ($v >= 10) {
                $d = $v % 10;
                if ($d !== 0 && $d < 5) {
                    $v += 5 - $d;
                } elseif ($d > 5) {
                    $v += 10 - $d;
                }
            }
            $price = $v;
        }
        return (float) $price;
    }
}
