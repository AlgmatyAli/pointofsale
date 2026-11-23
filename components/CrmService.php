<?php

namespace app\components;

use Yii;
use app\models\Client;
use app\models\CrmActivity;
use app\models\CrmClient;
use app\models\CrmLevel;
use app\models\CrmLog;
use app\models\CrmPromise;
use app\models\CrmSetting;
use app\models\User;
use yii\db\Query;

/**
 * منطق متابعة الزبائن:
 *  - الرصيد من view dept (نفس مصدر تقرير إجمالي الديون)، للزبائن فقط (type 0 و2).
 *  - أيام بدون دفع = منذ آخر إيصال قبض، أو منذ أقدم فاتورة آجلة إن لم يدفع أبداً.
 *  - المرحلة تُحسب تلقائياً من الأيام، وعند دخول مرحلة جديدة تُنشأ مهمة واحدة للمسؤول.
 *  - الوعود: منفّذ إذا بلغ المقبوض منذ الوعد قيمته، منكوث إذا مرّ تاريخه.
 * التحديث اليومي يعمل تلقائياً مع أول فتح لشاشات المتابعة كل يوم، أو من cron: php yii crm/daily
 */
class CrmService
{
    const EPS = 0.0005;

    /** ينفّذ التحديث اليومي مرة واحدة في اليوم (أول من يفتح الشاشة) */
    public static function ensureDaily(): void
    {
        if (!CrmSetting::isEnabled()) {
            return;
        }
        $today = date('Y-m-d');
        // تحديث ذري: من يغيّر الصف أولاً هو من ينفّذ، فلا يتكرر التشغيل مع طلبين متزامنين
        $won = Yii::$app->db->createCommand(
            'UPDATE crmSetting SET lastRunDate = :d WHERE id = 1 AND (lastRunDate IS NULL OR lastRunDate < :d)',
            [':d' => $today]
        )->execute();
        if ($won) {
            try {
                static::runDaily();
            } catch (\Throwable $e) {
                Yii::error('CRM daily run failed: ' . $e->getMessage(), 'crm');
                Yii::$app->db->createCommand('UPDATE crmSetting SET lastRunDate = NULL WHERE id = 1')->execute();
            }
        }
    }

    /** التحديث الكامل: لقطات الأرصدة والمراحل والمهام التلقائية، ثم فحص الوعود */
    public static function runDaily(): array
    {
        $stats = static::refresh(null);
        $stats += static::checkPromises(null);
        return $stats;
    }

    /** رصيد زبون واحد مباشرة (لبطاقة الزبون) */
    public static function balanceOf(int $clientId): float
    {
        return (float) (new Query())->from('dept')->where(['id' => $clientId])->sum('credt');
    }

    /** المسؤول عن الزبون، أو المسؤول الافتراضي من الإعدادات */
    public static function responsibleOf(?CrmClient $crm): ?int
    {
        if ($crm !== null && $crm->userId) {
            return (int) $crm->userId;
        }
        $s = CrmSetting::current();
        return $s && $s->defaultUserId ? (int) $s->defaultUserId : null;
    }

    /** @return array id => اسم المستخدم للمستخدمين الفعّالين */
    public static function users(): array
    {
        return \yii\helpers\ArrayHelper::map(
            User::find()->select(['id', 'username'])->where(['<>', 'isActive', 'disabled'])->orderBy('username')->asArray()->all(),
            'id',
            'username'
        );
    }

    /**
     * يحدّث لقطة زبون واحد أو كل الزبائن.
     * @return array إحصائيات للعرض في سطر الأوامر
     */
    public static function refresh(?int $clientId): array
    {
        $db = Yii::$app->db;
        $now = date('Y-m-d H:i:s');
        $today = date('Y-m-d');
        $only = $clientId !== null ? ['id' => $clientId] : [];

        $balances = (new Query())->select(['b' => 'SUM(credt)', 'id'])->from('dept')
            ->where(['type' => [0, 2]])->andWhere($only)->groupBy('id')->indexBy('id')->column($db);

        $lastPay = (new Query())->select(['d' => 'MAX(DATE(`at`))', 'clinet'])->from('receipt')
            ->where(['type' => 1])->andFilterWhere(['clinet' => $clientId])->groupBy('clinet')->indexBy('clinet')->column($db);
        $firstCredit = (new Query())->select(['d' => 'MIN(DATE(`at`))', 'clinet'])->from('sales')
            ->where(['type' => 1, 'payWay' => [1, 2]])->andFilterWhere(['clinet' => $clientId])->groupBy('clinet')->indexBy('clinet')->column($db);

        $crmRows = CrmClient::find()->andFilterWhere(['clientId' => $clientId])->indexBy('clientId')->all();
        $levels = CrmLevel::ordered();
        $levelById = [];
        foreach ($levels as $l) {
            $levelById[$l->id] = $l;
        }
        $openPromise = (new Query())->select('clientId')->from('crmPromise')
            ->where(['status' => CrmPromise::OPEN])->andFilterWhere(['clientId' => $clientId])->indexBy('clientId')->column($db);

        // الزبائن الذين لهم مهمة مفتوحة الآن (لإعادة إنشاء مهمة من حُذفت مهمته)
        $hasOpenTask = (new Query())->select('clientId')->from('crmActivity')
            ->where(['doneAt' => null])->andFilterWhere(['clientId' => $clientId])->indexBy('clientId')->column($db);

        $stats = ['customers' => 0, 'tasks' => 0, 'paidOff' => 0];
        $ids = array_unique(array_merge(array_keys($balances), array_keys($crmRows)));

        foreach ($ids as $id) {
            $id = (int) $id;
            $balance = round((float) ($balances[$id] ?? 0), 3);
            $crm = $crmRows[$id] ?? null;
            if ($balance <= self::EPS && $crm === null) {
                continue; // لا دين ولا سجل متابعة: لا شيء نفعله
            }
            if ($crm === null) {
                $crm = new CrmClient(['clientId' => $id, 'state' => CrmClient::STATE_NORMAL]);
            }
            $oldLevel = $crm->levelId ? (int) $crm->levelId : null;
            $crm->balance = $balance;
            $crm->refreshed_at = $now;

            if ($balance <= self::EPS) {
                if ($oldLevel !== null || (float) $crm->getOldAttribute('balance') > self::EPS) {
                    $stats['paidOff']++;
                    static::closeOpenAuto($id, 'تم السداد');
                }
                $crm->levelId = null;
                $crm->days = null;
                $crm->sinceDate = null;
                $crm->save(false);
                continue;
            }

            $stats['customers']++;
            $since = $lastPay[$id] ?? ($firstCredit[$id] ?? null);
            $days = $since ? (int) floor((strtotime($today) - strtotime($since)) / 86400) : null;
            $level = CrmLevel::forDays($days, $levels);
            $crm->sinceDate = $since;
            $crm->days = $days;
            $crm->levelId = $level ? $level->id : null;
            $crm->save(false);

            $entered = $level !== null && (int) $level->id !== $oldLevel;
            // في التحديث اليومي الكامل: زبون في مرحلة تأخير وليست له مهمة مفتوحة (حُذفت مهمته)
            // تُنشأ له مهمة من جديد. المهمة المُغلقة (تمت) لا تتكرر لأن مفتاحها sourceKey باقٍ.
            $missing = $level !== null && $clientId === null && !isset($hasOpenTask[$id]);
            if ($entered || $missing) {
                if ($entered) {
                    CrmLog::add($id, 'level', 'دخل مرحلة «' . $level->name . '»: ' . $days . ' يوم بدون دفع، الرصيد ' . WhatsappHelper::money($balance));
                }
                $skip = (int) $crm->state === CrmClient::STATE_DISPUTED || isset($openPromise[$id]);
                if (!$skip && static::autoTask($crm, $level, (string) $since)) {
                    $stats['tasks']++;
                }
            }
        }
        return $stats;
    }

    /**
     * مهمة واحدة مفتوحة لكل زبون: إن وُجدت مهمة تلقائية مفتوحة تُرقّى للمرحلة الجديدة،
     * وإن وُجدت مهمة يدوية مفتوحة تُترك كما هي.
     */
    private static function autoTask(CrmClient $crm, CrmLevel $level, string $since): bool
    {
        $key = 'lvl-' . $crm->clientId . '-' . $level->id . '-' . $since;
        if (CrmActivity::find()->where(['sourceKey' => $key])->exists()) {
            return false;
        }
        $note = $level->name . ': ' . $crm->days . ' يوم بدون دفع، الرصيد ' . WhatsappHelper::money($crm->balance);
        $open = CrmActivity::find()->where(['clientId' => $crm->clientId, 'doneAt' => null])->orderBy(['id' => SORT_DESC])->one();
        if ($open !== null) {
            if (!$open->auto) {
                return false;
            }
            $open->type = $level->action;
            $open->note = $note;
            $open->dueDate = date('Y-m-d');
            $open->sourceKey = $key;
            return $open->save(false);
        }
        $task = new CrmActivity([
            'clientId' => $crm->clientId,
            'type' => $level->action,
            'dueDate' => date('Y-m-d'),
            'userId' => static::responsibleOf($crm),
            'note' => $note,
            'auto' => 1,
            'sourceKey' => $key,
        ]);
        return $task->save(false);
    }

    private static function closeOpenAuto(int $clientId, string $result): void
    {
        foreach (CrmActivity::find()->where(['clientId' => $clientId, 'doneAt' => null, 'auto' => 1])->all() as $t) {
            $t->close($result, null);
        }
    }

    /** يفحص الوعود القائمة: منفّذ أو منكوث */
    public static function checkPromises(?int $clientId): array
    {
        $today = date('Y-m-d');
        $stats = ['kept' => 0, 'broken' => 0];
        $promises = CrmPromise::find()->where(['status' => CrmPromise::OPEN])->andFilterWhere(['clientId' => $clientId])->all();
        foreach ($promises as $p) {
            $paid = (float) (new Query())->from('receipt')
                ->where(['clinet' => $p->clientId, 'type' => 1])
                ->andWhere(['>', 'id', (int) $p->afterReceiptId])
                ->sum('value');
            $p->paidSince = round($paid, 3);
            $balance = static::balanceOf((int) $p->clientId);

            if ($paid + self::EPS >= (float) $p->amount || $balance <= self::EPS) {
                $p->status = CrmPromise::KEPT;
                $p->closed_at = date('Y-m-d H:i:s');
                $p->save(false);
                CrmLog::add($p->clientId, 'promise', 'نُفّذ وعد الدفع بمبلغ ' . WhatsappHelper::money($p->amount) . ' (المقبوض ' . WhatsappHelper::money($paid) . ')');
                $stats['kept']++;
            } elseif ($p->promiseDate < $today) {
                $p->status = CrmPromise::BROKEN;
                $p->closed_at = date('Y-m-d H:i:s');
                $p->save(false);
                CrmLog::add($p->clientId, 'promise', 'وعد منكوث: وعد بدفع ' . WhatsappHelper::money($p->amount) . ' بتاريخ ' . $p->promiseDate
                    . '، والمقبوض ' . WhatsappHelper::money($paid));
                $crm = CrmClient::findOne($p->clientId);
                $key = 'prm-' . $p->id;
                if (!CrmActivity::find()->where(['sourceKey' => $key])->exists()) {
                    // مهمة متابعة جديدة؛ تُغلق قبلها المهمة التلقائية المفتوحة حتى تبقى مهمة واحدة
                    static::closeOpenAuto((int) $p->clientId, 'استُبدلت بمتابعة الوعد المنكوث');
                    (new CrmActivity([
                        'clientId' => $p->clientId,
                        'type' => 'call',
                        'dueDate' => $today,
                        'userId' => static::responsibleOf($crm),
                        'note' => 'وعد منكوث: ' . WhatsappHelper::money($p->amount) . ' كان موعده ' . $p->promiseDate,
                        'auto' => 1,
                        'sourceKey' => $key,
                    ]))->save(false);
                }
                $stats['broken']++;
            } else {
                $p->save(false, ['paidSince']);
            }
        }
        return $stats;
    }

    /** نص تذكير واتساب بإجمالي الدين */
    public static function reminderText(Client $client, float $balance): string
    {
        $lines = ['السلام عليكم ' . $client->name . '،'];
        $lines[] = 'نود تذكيركم برصيد حسابكم لدينا.';
        $lines[] = WhatsappHelper::balanceLine($balance);
        $lines[] = 'كشف الحساب: ' . WhatsappHelper::statementUrl($client->id, (int) $client->type);
        $company = \app\models\CompanyInfo::current();
        if ($company !== null) {
            $lines = array_merge($lines, WhatsappHelper::footerLines($company));
        }
        return implode("\n", $lines);
    }

    /** نص تذكير واتساب بوعد الدفع القائم */
    public static function promiseReminderText(Client $client, CrmPromise $promise, float $balance): string
    {
        $lines = ['السلام عليكم ' . $client->name . '،'];
        if ((int) $promise->status === CrmPromise::BROKEN) {
            // وعد مرّ موعده دون سداد
            $lines[] = 'نذكّركم بأن موعد سداد المبلغ المتفق عليه ' . WhatsappHelper::money($promise->amount) . ' كان بتاريخ ' . $promise->promiseDate . ' ولم يصلنا السداد بعد.';
            $lines[] = 'نرجو منكم السداد في أقرب وقت.';
        } else {
            $lines[] = 'نذكّركم بموعد سداد المبلغ المتفق عليه ' . WhatsappHelper::money($promise->amount) . ' بتاريخ ' . $promise->promiseDate . '.';
        }
        $lines[] = 'رصيد حسابكم الحالي: ' . preg_replace('/^الرصيد: /u', '', WhatsappHelper::balanceLine($balance));
        $company = \app\models\CompanyInfo::current();
        if ($company !== null) {
            $lines = array_merge($lines, WhatsappHelper::footerLines($company));
        }
        return implode("\n", $lines);
    }

    /** نسبة استهلاك سقف الدين؛ null للسقف المفتوح (debt = 0) */
    public static function limitUsage(?Client $client, float $balance): ?float
    {
        $limit = $client ? (float) $client->debt : 0;
        if ($limit <= 0) {
            return null;
        }
        return round($balance / $limit * 100, 1);
    }

    /**
     * الخط الزمني: سجل المتابعة + إيصالات القبض + فواتير الآجل والمرتجعات، الأحدث أولاً.
     * @return array[] كل عنصر: time, date, icon, color, title, text, user
     */
    public static function timeline(int $clientId, int $limit = 60): array
    {
        $items = [];
        foreach (CrmLog::find()->with('user')->where(['clientId' => $clientId])->orderBy(['id' => SORT_DESC])->limit($limit)->all() as $log) {
            $items[] = [
                'time' => $log->created_at,
                'date' => $log->created_at,
                'icon' => CrmLog::ICONS[$log->kind] ?? 'bi-dot',
                'color' => $log->kind === 'promise' ? 'primary' : ($log->kind === 'whatsapp' ? 'success' : 'secondary'),
                'title' => ['note' => 'ملاحظة', 'whatsapp' => 'واتساب', 'task' => 'مهمة', 'promise' => 'وعد دفع', 'state' => 'الحالة', 'assign' => 'المسؤول', 'level' => 'المرحلة', 'group' => 'المجموعات'][$log->kind] ?? $log->kind,
                'text' => $log->text,
                'user' => $log->user->username ?? '',
            ];
        }
        $receipts = (new Query())->select(['rId', 'at', 'value', 'why'])->from('receipt')
            ->where(['clinet' => $clientId, 'type' => 1])->orderBy(['at' => SORT_DESC, 'id' => SORT_DESC])->limit($limit)->all();
        foreach ($receipts as $r) {
            $items[] = [
                'time' => $r['at'] . ' 23:59:58',
                'date' => $r['at'],
                'icon' => 'bi-cash-coin',
                'color' => 'success',
                'title' => 'إيصال قبض رقم ' . $r['rId'],
                'text' => WhatsappHelper::money($r['value']) . ($r['why'] ? ' — ' . $r['why'] : ''),
                'user' => '',
            ];
        }
        $sales = (new Query())->select(['id', 'billId', 'at', 'type', 'total', 'paid', 'disscount', 'created_at'])->from('sales')
            ->where(['clinet' => $clientId])
            ->andWhere(['or', ['type' => 1, 'payWay' => [1, 2]], ['type' => 2, 'payWay' => 2]])
            ->orderBy(['created_at' => SORT_DESC])->limit($limit)->all();
        foreach ($sales as $s) {
            $isReturn = (int) $s['type'] === 2;
            $remain = $isReturn ? (float) $s['total'] : (float) $s['total'] - (float) $s['paid'] - (float) $s['disscount'];
            $items[] = [
                'time' => $s['created_at'] ?: $s['at'],
                'date' => $s['at'],
                'icon' => $isReturn ? 'bi-arrow-return-left' : 'bi-receipt',
                'color' => $isReturn ? 'info' : 'warning',
                'title' => ($isReturn ? 'مرتجع على الحساب رقم ' : 'فاتورة آجلة رقم ') . $s['billId'],
                'text' => ($isReturn ? 'قيمة المرتجع ' : 'المتبقي ') . WhatsappHelper::money($remain),
                'user' => '',
                'salesId' => $s['id'],
            ];
        }
        usort($items, fn($a, $b) => strcmp((string) $b['time'], (string) $a['time']));
        return array_slice($items, 0, $limit);
    }
}
