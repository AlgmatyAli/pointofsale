<?php

namespace app\commands;

use app\components\CrmService;
use app\models\CrmSetting;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * التحديث اليومي لمتابعة الزبائن. اختياري: الشاشات تنفّذه تلقائياً مع أول فتح كل يوم.
 * لمن يريد تنفيذه ليلاً: php yii crm/daily
 */
class CrmController extends Controller
{
    public function actionDaily()
    {
        if (!CrmSetting::isEnabled()) {
            $this->stdout("متابعة الزبائن غير مفعّلة في هذا المحل.\n");
            return ExitCode::OK;
        }
        \Yii::$app->db->createCommand('UPDATE crmSetting SET lastRunDate = :d WHERE id = 1', [':d' => date('Y-m-d')])->execute();
        $stats = CrmService::runDaily();
        foreach ($stats as $k => $v) {
            $this->stdout("$k: $v\n");
        }
        return ExitCode::OK;
    }
}
