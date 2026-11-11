<?php

namespace app\controllers;

use Yii;
use app\components\CrmService;
use app\components\WhatsappHelper;
use app\models\Client;
use app\models\CrmActivity;
use app\models\CrmClient;
use app\models\CrmLevel;
use app\models\CrmLog;
use app\models\CrmPromise;
use app\models\CrmSetting;
use app\models\CrmTag;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * متابعة الزبائن (CRM): مهامي اليوم، بطاقة الزبون، لوحة التحصيل، وعود الدفع، المجموعات، الإعدادات.
 * كل أمر له صلاحيته الخاصة (انظر migration m261107_000001_create_crm).
 */
class CrmController extends Controller
{
    /** أوامر تعمل حتى لو كانت الوحدة غير مفعّلة (لتفعيلها) */
    const SETTINGS_ACTIONS = ['settings', 'level-save', 'level-delete', 'run-now'];

    public function behaviors()
    {
        $rule = fn(array $actions, string $perm) => ['allow' => true, 'actions' => $actions, 'roles' => [$perm]];
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'task-create' => ['POST'], 'task-done' => ['POST'], 'task-delete' => ['POST'], 'task-assign' => ['POST'],
                    'note' => ['POST'], 'assign' => ['POST'], 'state' => ['POST'],
                    'promise-create' => ['POST'], 'promise-cancel' => ['POST'], 'promise-review' => ['POST'],
                    'group-save' => ['POST'], 'group-delete' => ['POST'], 'group-add' => ['POST'], 'group-remove' => ['POST'], 'group-assign' => ['POST'],
                    'level-save' => ['POST'], 'level-delete' => ['POST'], 'run-now' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    $rule(['tasks'], 'crmMyTasks'),
                    $rule(['task-create'], 'crmTaskCreate'),
                    $rule(['task-done'], 'crmTaskDone'),
                    $rule(['task-delete'], 'crmTaskDelete'),
                    $rule(['task-assign', 'assign', 'group-assign'], 'crmAssign'),
                    $rule(['card'], 'crmCardView'),
                    $rule(['note'], 'crmNoteAdd'),
                    $rule(['board'], 'crmBoardView'),
                    $rule(['state'], 'crmStateChange'),
                    $rule(['promises'], 'crmPromiseView'),
                    $rule(['promise-create'], 'crmPromiseCreate'),
                    $rule(['promise-update'], 'crmPromiseEdit'),
                    $rule(['promise-cancel'], 'crmPromiseCancel'),
                    $rule(['promise-review'], 'crmAlerts'),
                    $rule(['whatsapp', 'promise-whatsapp'], 'crmWhatsapp'),
                    $rule(['groups', 'group'], 'crmGroupView'),
                    $rule(['group-save', 'group-delete'], 'crmGroupEdit'),
                    $rule(['group-add', 'group-remove'], 'crmGroupMembers'),
                    $rule(['settings', 'level-save', 'level-delete', 'run-now'], 'crmSettings'),
                    ['allow' => true, 'actions' => ['client-list'], 'roles' => ['crmGroupMembers', 'crmTaskCreate', 'crmCardView']],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }
        if (!in_array($action->id, self::SETTINGS_ACTIONS, true)) {
            if (!CrmSetting::isEnabled()) {
                Yii::$app->session->setFlash('error', 'متابعة الزبائن غير مفعّلة في هذا المحل.');
                $this->redirect(Yii::$app->user->can('crmSettings') ? ['settings'] : Yii::$app->homeUrl);
                return false;
            }
            CrmService::ensureDaily();
        }
        return true;
    }

    private function back($fallback)
    {
        return $this->redirect(Yii::$app->request->referrer ?: $fallback);
    }

    private function uid(): int
    {
        return (int) Yii::$app->user->id;
    }

    private function findClient($id): Client
    {
        $client = Client::find()->where(['id' => (int) $id, 'type' => [0, 2]])->one();
        if ($client === null) {
            throw new NotFoundHttpException('الزبون غير موجود.');
        }
        return $client;
    }

    /** يمنع من لا يملك «عرض مهام الجميع» من التصرف في مهام غيره */
    private function findTask($id): CrmActivity
    {
        $task = CrmActivity::findOne((int) $id);
        if ($task === null) {
            throw new NotFoundHttpException('المهمة غير موجودة.');
        }
        if ((int) $task->userId !== $this->uid() && !Yii::$app->user->can('crmAllTasks')) {
            throw new ForbiddenHttpException('هذه المهمة ليست لك.');
        }
        return $task;
    }

    // ---------------------------------------------------------------- مهامي اليوم

    public function actionTasks($user = null, $when = 'due', $id = null, $q = '')
    {
        $all = Yii::$app->user->can('crmAllTasks');
        $query = CrmActivity::find()->alias('a')->with(['client', 'user', 'crm.level'])->where(['a.doneAt' => null]);
        if (!$all) {
            $user = $this->uid();
        }
        if ($user === 'none') {
            $query->andWhere(['a.userId' => null]);
        } elseif ($user !== null && $user !== '') {
            $query->andWhere(['a.userId' => (int) $user]);
        }
        $q = trim((string) $q);
        if ($q !== '') {
            // بحث باسم الزبون أو هاتفه
            $query->innerJoin('client c', 'c.id = a.clientId')
                ->andWhere(['or', ['like', 'c.name', $q], ['like', 'c.mobile', $q], ['like', 'c.phone', $q]]);
        }
        $today = date('Y-m-d');
        if ($when === 'due') {
            $query->andWhere(['<=', 'a.dueDate', $today]);
        } elseif ($when === 'later') {
            $query->andWhere(['>', 'a.dueDate', $today]);
        }
        $tasks = $query->orderBy(['a.dueDate' => SORT_ASC, 'a.id' => SORT_ASC])->limit(300)->all();

        $selected = null;
        if ($id !== null) {
            foreach ($tasks as $t) {
                if ((int) $t->id === (int) $id) {
                    $selected = $t;
                }
            }
        }
        $selected = $selected ?: ($tasks[0] ?? null);

        return $this->render('tasks', [
            'tasks' => $tasks,
            'selected' => $selected,
            'balance' => $selected ? CrmService::balanceOf((int) $selected->clientId) : 0,
            'user' => $user,
            'when' => $when,
            'q' => $q,
            'all' => $all,
            'users' => CrmService::users(),
        ]);
    }

    public function actionTaskCreate()
    {
        $task = new CrmActivity();
        $task->load(Yii::$app->request->post());
        $task->auto = 0;
        $task->created_by = $this->uid();
        if (!Yii::$app->user->can('crmAssign') || !$task->userId) {
            $task->userId = $this->uid();
        }
        $task->dueDate = $task->dueDate ?: date('Y-m-d');
        if ($task->save()) {
            CrmLog::add($task->clientId, 'task', 'مهمة جديدة: ' . (CrmActivity::TYPES[$task->type] ?? $task->type) . ' بتاريخ ' . $task->dueDate
                . ($task->note ? ' — ' . $task->note : ''), $this->uid());
            Yii::$app->session->setFlash('success', 'أُضيفت المهمة.');
        } else {
            Yii::$app->session->setFlash('error', implode(' ', $task->getFirstErrors()));
        }
        return $this->back(['card', 'id' => $task->clientId]);
    }

    public function actionTaskDone($id)
    {
        $task = $this->findTask($id);
        $result = trim((string) Yii::$app->request->post('result', ''));
        $note = trim((string) Yii::$app->request->post('note', ''));
        if ($result === '') {
            Yii::$app->session->setFlash('error', 'اختر نتيجة المتابعة.');
            return $this->back(['tasks']);
        }
        $full = $result . ($note !== '' ? ' — ' . $note : '');
        $task->close($full, $this->uid());
        CrmLog::add($task->clientId, 'task', (CrmActivity::TYPES[$task->type] ?? $task->type) . ': ' . $full, $this->uid());

        $next = Yii::$app->request->post('nextDate');
        if ($next && preg_match('/^\d{4}-\d{2}-\d{2}$/', $next) && Yii::$app->user->can('crmTaskCreate')) {
            (new CrmActivity([
                'clientId' => $task->clientId,
                'type' => $task->type === 'manager' ? 'call' : $task->type,
                'dueDate' => $next,
                'userId' => $task->userId,
                'note' => 'متابعة بعد: ' . $full,
                'created_by' => $this->uid(),
            ]))->save(false);
        }
        Yii::$app->session->setFlash('success', 'أُغلقت المهمة.');
        return $this->redirect(['tasks', 'user' => Yii::$app->request->post('fUser'), 'when' => Yii::$app->request->post('fWhen', 'due'), 'q' => Yii::$app->request->post('fQ', '')]);
    }

    public function actionTaskDelete($id)
    {
        $task = $this->findTask($id);
        $task->delete();
        Yii::$app->session->setFlash('success', 'حُذفت المهمة.');
        return $this->back(['tasks']);
    }

    public function actionTaskAssign($id)
    {
        $task = $this->findTask($id);
        $userId = (int) Yii::$app->request->post('userId') ?: null;
        $task->userId = $userId;
        $task->save(false, ['userId']);
        CrmLog::add($task->clientId, 'assign', 'حُوّلت مهمة ' . (CrmActivity::TYPES[$task->type] ?? '') . ' إلى ' . (CrmService::users()[$userId] ?? 'بلا مكلّف'), $this->uid());
        return $this->back(['tasks']);
    }

    // ---------------------------------------------------------------- بطاقة الزبون

    public function actionCard($id)
    {
        $client = $this->findClient($id);
        CrmService::checkPromises((int) $client->id);
        CrmService::refresh((int) $client->id);
        $crm = CrmClient::forClient((int) $client->id);
        $balance = CrmService::balanceOf((int) $client->id);
        $lastReceipt = (new Query())->select(['at', 'value'])->from('receipt')
            ->where(['clinet' => $client->id, 'type' => 1])->orderBy(['at' => SORT_DESC, 'id' => SORT_DESC])->one();

        return $this->render('card', [
            'client' => $client,
            'crm' => $crm,
            'balance' => $balance,
            'usage' => CrmService::limitUsage($client, $balance),
            'lastReceipt' => $lastReceipt,
            'openTasks' => CrmActivity::find()->with('user')->where(['clientId' => $client->id, 'doneAt' => null])->orderBy('dueDate')->all(),
            'promise' => CrmPromise::openFor((int) $client->id),
            'tags' => CrmTag::forClient((int) $client->id),
            'allTags' => CrmTag::listAll(),
            'timeline' => CrmService::timeline((int) $client->id),
            'users' => CrmService::users(),
        ]);
    }

    public function actionNote($id)
    {
        $client = $this->findClient($id);
        $text = trim((string) Yii::$app->request->post('text', ''));
        if ($text !== '') {
            CrmLog::add((int) $client->id, 'note', mb_substr($text, 0, 2000), $this->uid());
        }
        return $this->back(['card', 'id' => $client->id]);
    }

    public function actionAssign($id)
    {
        $client = $this->findClient($id);
        $userId = (int) Yii::$app->request->post('userId') ?: null;
        $this->assignClients([(int) $client->id], $userId);
        Yii::$app->session->setFlash('success', 'تم تعيين المسؤول.');
        return $this->back(['card', 'id' => $client->id]);
    }

    /** يعيّن المسؤول وينقل إليه المهام المفتوحة */
    private function assignClients(array $ids, ?int $userId): void
    {
        $name = $userId ? (CrmService::users()[$userId] ?? '') : 'بلا مسؤول';
        foreach ($ids as $cid) {
            $crm = CrmClient::forClient((int) $cid);
            if ($crm->userId == $userId && !$crm->isNewRecord) {
                continue;
            }
            $crm->userId = $userId;
            $crm->save(false);
            CrmActivity::updateAll(['userId' => $userId], ['clientId' => $cid, 'doneAt' => null]);
            CrmLog::add((int) $cid, 'assign', 'المسؤول: ' . $name, $this->uid());
        }
    }

    public function actionState($id)
    {
        $client = $this->findClient($id);
        $state = (int) Yii::$app->request->post('state');
        if (!isset(CrmClient::STATES[$state])) {
            return $this->back(['board']);
        }
        $crm = CrmClient::forClient((int) $client->id);
        $crm->state = $state;
        $crm->save(false);
        CrmLog::add((int) $client->id, 'state', 'الحالة: ' . CrmClient::STATES[$state], $this->uid());
        return $this->back(['card', 'id' => $client->id]);
    }

    // ---------------------------------------------------------------- لوحة التحصيل

    public function actionBoard($user = null, $tag = null)
    {
        $all = Yii::$app->user->can('crmAllTasks');
        $query = CrmClient::find()->alias('c')->with(['client', 'user'])->where(['>', 'c.balance', CrmService::EPS]);
        if (!$all) {
            $user = $this->uid();
        }
        if ($user === 'none') {
            $query->andWhere(['c.userId' => null]);
        } elseif ($user !== null && $user !== '') {
            $query->andWhere(['c.userId' => (int) $user]);
        }
        if ($tag) {
            $query->innerJoin('crmClientTag t', 't.clientId = c.clientId')->andWhere(['t.tagId' => (int) $tag]);
        }
        $rows = $query->orderBy(['c.days' => SORT_DESC, 'c.balance' => SORT_DESC])->all(); // الأكثر تأخيراً أولاً، ثم الأكبر رصيداً
        $promised = (new Query())->select(['clientId', 'amount' => 'SUM(amount)', 'd' => 'MIN(promiseDate)'])->from('crmPromise')
            ->where(['status' => CrmPromise::OPEN])->groupBy('clientId')->indexBy('clientId')->all();

        $levels = CrmLevel::ordered();
        $columns = ['ok' => ['title' => 'منتظم', 'color' => 'success', 'items' => []]];
        foreach ($levels as $l) {
            $columns['l' . $l->id] = ['title' => $l->name . ' (+' . $l->days . ' يوم)', 'color' => $l->color, 'items' => []];
        }
        $columns['promise'] = ['title' => 'وعد بالدفع', 'color' => 'primary', 'items' => []];
        $columns['disputed'] = ['title' => 'متنازع عليه', 'color' => 'secondary', 'items' => []];
        $columns['escalated'] = ['title' => 'مصعّد للمدير', 'color' => 'dark', 'items' => []];

        foreach ($rows as $r) {
            if (isset($promised[$r->clientId])) {
                $key = 'promise';
            } elseif ((int) $r->state === CrmClient::STATE_DISPUTED) {
                $key = 'disputed';
            } elseif ((int) $r->state === CrmClient::STATE_ESCALATED) {
                $key = 'escalated';
            } else {
                $key = $r->levelId && isset($columns['l' . $r->levelId]) ? 'l' . $r->levelId : 'ok';
            }
            $columns[$key]['items'][] = $r;
        }

        return $this->render('board', [
            'columns' => $columns,
            'promised' => $promised,
            'user' => $user,
            'tag' => $tag,
            'all' => $all,
            'users' => CrmService::users(),
            'tags' => CrmTag::listAll(),
            'setting' => CrmSetting::current(),
        ]);
    }

    // ---------------------------------------------------------------- وعود الدفع

    public function actionPromises($status = '0')
    {
        $query = CrmPromise::find()->with(['client', 'createdBy']);
        if ($status === 'alert') {
            $query->where(['status' => CrmPromise::BROKEN, 'reviewed' => 0]);
        } elseif ($status !== '' && isset(CrmPromise::STATUSES[(int) $status])) {
            $query->where(['status' => (int) $status]);
        }
        return $this->render('promises', [
            'dataProvider' => new ActiveDataProvider([
                'query' => $query,
                'sort' => ['defaultOrder' => ['promiseDate' => SORT_ASC, 'id' => SORT_DESC]],
                'pagination' => ['pageSize' => 50],
            ]),
            'status' => $status,
        ]);
    }

    public function actionPromiseCreate()
    {
        $p = new CrmPromise();
        $p->load(Yii::$app->request->post());
        $p->status = CrmPromise::OPEN;
        $p->created_by = $this->uid();
        if ($p->save()) {
            CrmLog::add((int) $p->clientId, 'promise', 'وعد بدفع ' . WhatsappHelper::money($p->amount) . ' بتاريخ ' . $p->promiseDate
                . ($p->note ? ' — ' . $p->note : ''), $this->uid());
            // المتابعة الآن عند موعد الوعد: تُغلق المهام المفتوحة
            foreach (CrmActivity::find()->where(['clientId' => $p->clientId, 'doneAt' => null])->all() as $t) {
                $t->close('وعد بالدفع بتاريخ ' . $p->promiseDate, $this->uid());
            }
            Yii::$app->session->setFlash('success', 'سُجّل وعد الدفع.');
        } else {
            Yii::$app->session->setFlash('error', implode(' ', $p->getFirstErrors()));
        }
        return $this->back(['card', 'id' => $p->clientId]);
    }

    public function actionPromiseUpdate($id)
    {
        $p = CrmPromise::findOne((int) $id);
        if ($p === null || (int) $p->status !== CrmPromise::OPEN) {
            throw new NotFoundHttpException('لا يمكن تعديل هذا الوعد.');
        }
        $old = WhatsappHelper::money($p->amount) . ' بتاريخ ' . $p->promiseDate;
        if ($p->load(Yii::$app->request->post()) && $p->save()) {
            CrmLog::add((int) $p->clientId, 'promise', 'عُدّل وعد الدفع من ' . $old . ' إلى ' . WhatsappHelper::money($p->amount) . ' بتاريخ ' . $p->promiseDate, $this->uid());
            Yii::$app->session->setFlash('success', 'عُدّل الوعد.');
            return $this->redirect(['promises']);
        }
        return $this->render('promise-update', ['model' => $p]);
    }

    public function actionPromiseCancel($id)
    {
        $p = CrmPromise::findOne((int) $id);
        if ($p !== null && (int) $p->status === CrmPromise::OPEN) {
            $p->status = CrmPromise::CANCELLED;
            $p->closed_at = date('Y-m-d H:i:s');
            $p->save(false);
            CrmLog::add((int) $p->clientId, 'promise', 'أُلغي وعد الدفع بمبلغ ' . WhatsappHelper::money($p->amount), $this->uid());
        }
        return $this->back(['promises']);
    }

    public function actionPromiseReview($id)
    {
        CrmPromise::updateAll(['reviewed' => 1], ['id' => (int) $id, 'status' => CrmPromise::BROKEN]);
        return $this->back(['promises', 'status' => 'alert']);
    }

    // ---------------------------------------------------------------- واتساب

    public function actionWhatsapp($id, $task = null)
    {
        $client = $this->findClient($id);
        $phone = WhatsappHelper::normalizePhone($client->mobile ?: $client->phone);
        if ($phone === null) {
            Yii::$app->session->setFlash('error', 'رقم هاتف الزبون غير موجود أو غير صالح. أدخل رقماً ليبياً مثل 0912345678 في بيانات الزبون.');
            return $this->back(['card', 'id' => $client->id]);
        }
        $balance = CrmService::balanceOf((int) $client->id);
        CrmLog::add((int) $client->id, 'whatsapp', 'تذكير بالرصيد: ' . WhatsappHelper::balanceLine($balance), $this->uid());
        if ($task !== null && Yii::$app->user->can('crmTaskDone')) {
            $t = CrmActivity::findOne((int) $task);
            if ($t && !$t->doneAt && (int) $t->clientId === (int) $client->id && $t->type === 'whatsapp'
                && ((int) $t->userId === $this->uid() || Yii::$app->user->can('crmAllTasks'))) {
                $t->close('أُرسل تذكير واتساب', $this->uid());
            }
        }
        return $this->redirect(WhatsappHelper::waUrl($phone, CrmService::reminderText($client, $balance)));
    }

    // ---------------------------------------------------------------- المجموعات

    public function actionGroups()
    {
        $counts = (new Query())->select(['n' => 'COUNT(*)', 'tagId'])->from('crmClientTag')->groupBy('tagId')->indexBy('tagId')->column();
        return $this->render('groups', [
            'tags' => CrmTag::find()->orderBy('name')->all(),
            'counts' => $counts,
            'model' => new CrmTag(['color' => 'primary']),
        ]);
    }

    /** تذكير الزبون بوعد الدفع القائم عبر واتساب */
    public function actionPromiseWhatsapp($id)
    {
        $p = CrmPromise::findOne((int) $id);
        if ($p === null || !in_array((int) $p->status, [CrmPromise::OPEN, CrmPromise::BROKEN], true)) {
            throw new NotFoundHttpException('لا يوجد وعد قائم أو منكوث.');
        }
        $broken = (int) $p->status === CrmPromise::BROKEN;
        $client = $this->findClient($p->clientId);
        $phone = WhatsappHelper::normalizePhone($client->mobile ?: $client->phone);
        if ($phone === null) {
            Yii::$app->session->setFlash('error', 'رقم هاتف الزبون غير موجود أو غير صالح. أدخل رقماً ليبياً مثل 0912345678 في بيانات الزبون.');
            return $this->back(['promises']);
        }
        $balance = CrmService::balanceOf((int) $client->id);
        CrmLog::add((int) $client->id, 'whatsapp', ($broken ? 'تذكير بوعد لم يُنفَّذ: ' : 'تذكير بوعد الدفع: ') . WhatsappHelper::money($p->amount) . ' بتاريخ ' . $p->promiseDate, $this->uid());
        return $this->redirect(WhatsappHelper::waUrl($phone, CrmService::promiseReminderText($client, $p, $balance)));
    }

    public function actionGroup($id, $month = null)
    {
        $tag = CrmTag::findOne((int) $id);
        if ($tag === null) {
            throw new NotFoundHttpException('المجموعة غير موجودة.');
        }
        $members = Client::find()->alias('cl')
            ->innerJoin('crmClientTag t', 't.clientId = cl.id')
            ->where(['t.tagId' => $tag->id])->orderBy('cl.name')->all();
        $ids = array_map(fn($c) => (int) $c->id, $members);
        $crm = CrmClient::find()->with('user')->where(['clientId' => $ids])->indexBy('clientId')->all();

        // محصلة الشهر لكل موظف: مجموع إيصالات القبض من زبائن المجموعة، حسب المسؤول الحالي عن كل زبون
        $month = preg_match('/^\d{4}-\d{2}$/', (string) $month) ? $month : date('Y-m');
        $from = $month . '-01';
        $to = date('Y-m-t', strtotime($from));
        $paid = $ids ? (new Query())->select(['s' => 'SUM(value)', 'clinet'])->from('receipt')
            ->where(['type' => 1, 'clinet' => $ids])->andWhere(['between', 'at', $from, $to])
            ->groupBy('clinet')->indexBy('clinet')->column() : [];
        $defaultUser = CrmService::responsibleOf(null);
        $perUser = [];
        foreach ($ids as $cid) {
            $uid = isset($crm[$cid]) && $crm[$cid]->userId ? (int) $crm[$cid]->userId : ($defaultUser ?: 0);
            $perUser[$uid] = $perUser[$uid] ?? ['customers' => 0, 'collected' => 0.0, 'debt' => 0.0];
            $perUser[$uid]['customers']++;
            $perUser[$uid]['collected'] += (float) ($paid[$cid] ?? 0);
            $perUser[$uid]['debt'] += isset($crm[$cid]) ? max(0, (float) $crm[$cid]->balance) : 0;
        }
        uasort($perUser, fn($a, $b) => $b['collected'] <=> $a['collected']);

        return $this->render('group', [
            'tag' => $tag,
            'members' => $members,
            'crm' => $crm,
            'users' => CrmService::users(),
            'month' => $month,
            'perUser' => $perUser,
            'paid' => $paid,
        ]);
    }

    public function actionGroupSave($id = null)
    {
        $tag = $id ? CrmTag::findOne((int) $id) : new CrmTag();
        if ($tag === null) {
            throw new NotFoundHttpException();
        }
        if ($tag->load(Yii::$app->request->post()) && $tag->save()) {
            Yii::$app->session->setFlash('success', 'حُفظت المجموعة.');
        } else {
            Yii::$app->session->setFlash('error', implode(' ', $tag->getFirstErrors()));
        }
        return $this->back(['groups']);
    }

    public function actionGroupDelete($id)
    {
        $tag = CrmTag::findOne((int) $id);
        if ($tag !== null) {
            $tag->delete();
            Yii::$app->session->setFlash('success', 'حُذفت المجموعة (الزبائن لم يُحذفوا).');
        }
        return $this->redirect(['groups']);
    }

    public function actionGroupAdd($id)
    {
        $tag = CrmTag::findOne((int) $id);
        $ids = (array) Yii::$app->request->post('clients', []);
        if ($tag !== null) {
            $valid = Client::find()->select('id')->where(['id' => array_map('intval', $ids), 'type' => [0, 2]])->column();
            $existing = $tag->getMemberIds();
            foreach (array_diff($valid, $existing) as $cid) {
                Yii::$app->db->createCommand()->insert('crmClientTag', ['tagId' => $tag->id, 'clientId' => $cid])->execute();
                CrmLog::add((int) $cid, 'group', 'أُضيف إلى مجموعة «' . $tag->name . '»', $this->uid());
            }
        }
        return $this->back(['group', 'id' => $id]);
    }

    public function actionGroupRemove($id, $client)
    {
        $tag = CrmTag::findOne((int) $id);
        if ($tag !== null) {
            $n = Yii::$app->db->createCommand()->delete('crmClientTag', ['tagId' => $tag->id, 'clientId' => (int) $client])->execute();
            if ($n) {
                CrmLog::add((int) $client, 'group', 'أُزيل من مجموعة «' . $tag->name . '»', $this->uid());
            }
        }
        return $this->back(['group', 'id' => $id]);
    }

    public function actionGroupAssign($id)
    {
        $tag = CrmTag::findOne((int) $id);
        if ($tag === null) {
            throw new NotFoundHttpException();
        }
        $userId = (int) Yii::$app->request->post('userId') ?: null;
        $this->assignClients($tag->getMemberIds(), $userId);
        Yii::$app->session->setFlash('success', 'عُيّن المسؤول لكل أعضاء المجموعة.');
        return $this->back(['group', 'id' => $id]);
    }

    /** بحث الزبائن لـ Select2 */
    public function actionClientList($q = '')
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $q = trim((string) $q);
        if ($q === '') {
            return ['results' => []];
        }
        $rows = Client::find()->select(['id', 'text' => 'name'])->where(['type' => [0, 2]])
            ->andWhere(['or', ['like', 'name', $q], ['like', 'phone', $q], ['like', 'mobile', $q], ['id' => ctype_digit($q) ? (int) $q : -1]])
            ->orderBy('name')->limit(30)->asArray()->all();
        return ['results' => $rows];
    }

    // ---------------------------------------------------------------- الإعدادات

    public function actionSettings()
    {
        $setting = CrmSetting::current();
        if ($setting === null) {
            throw new NotFoundHttpException('نفّذ php yii migrate أولاً.');
        }
        if ($setting->load(Yii::$app->request->post())) {
            $setting->defaultUserId = $setting->defaultUserId ?: null;
            if ($setting->save()) {
                Yii::$app->session->setFlash('success', 'حُفظت الإعدادات.');
                return $this->redirect(['settings']);
            }
        }
        return $this->render('settings', [
            'setting' => $setting,
            'levels' => CrmLevel::ordered(),
            'newLevel' => new CrmLevel(['action' => 'call', 'color' => 'warning']),
            'users' => CrmService::users(),
        ]);
    }

    public function actionLevelSave($id = null)
    {
        $level = $id ? CrmLevel::findOne((int) $id) : new CrmLevel();
        if ($level === null) {
            throw new NotFoundHttpException();
        }
        if ($level->load(Yii::$app->request->post()) && $level->save()) {
            Yii::$app->session->setFlash('success', 'حُفظت المرحلة. تُطبَّق في التحديث القادم أو عند الضغط على «تحديث الآن».');
        } else {
            Yii::$app->session->setFlash('error', implode(' ', $level->getFirstErrors()));
        }
        return $this->redirect(['settings']);
    }

    public function actionLevelDelete($id)
    {
        $level = CrmLevel::findOne((int) $id);
        if ($level !== null) {
            CrmClient::updateAll(['levelId' => null], ['levelId' => $level->id]);
            $level->delete();
            Yii::$app->session->setFlash('success', 'حُذفت المرحلة.');
        }
        return $this->redirect(['settings']);
    }

    public function actionRunNow()
    {
        if (!CrmSetting::isEnabled()) {
            Yii::$app->session->setFlash('error', 'فعّل متابعة الزبائن أولاً.');
            return $this->redirect(['settings']);
        }
        Yii::$app->db->createCommand('UPDATE crmSetting SET lastRunDate = :d WHERE id = 1', [':d' => date('Y-m-d')])->execute();
        $s = CrmService::runDaily();
        Yii::$app->session->setFlash('success', 'تم التحديث: ' . $s['customers'] . ' زبون عليه دين، ' . $s['tasks'] . ' مهمة جديدة، '
            . $s['paidOff'] . ' سدّد، ' . $s['kept'] . ' وعد نُفّذ، ' . $s['broken'] . ' وعد منكوث.');
        return $this->back(['settings']);
    }
}
