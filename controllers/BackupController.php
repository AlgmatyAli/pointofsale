<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * تحميل النسخ الاحتياطية لقاعدة البيانات التي ينشئها deploy/deploy.sh على السيرفر
 * (backup_DB.sql.gz من deploy/backup.sh كل ساعة، ونسخ ما قبل migrate من deploy/deploy.sh).
 */
class BackupController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'actions' => ['index', 'download'], 'roles' => ['companyInfo']],
                ],
            ],
        ];
    }

    /** مجلد النسخ: يكتبه السكربت في runtime/backup.dir، والافتراضي ~/db-backups */
    private function backupDir(): ?string
    {
        $file = Yii::getAlias('@runtime/backup.dir');
        $dir = is_file($file) ? trim((string) file_get_contents($file)) : '';
        if ($dir === '') {
            $home = getenv('HOME');
            if (!$home && function_exists('posix_getpwuid') && function_exists('posix_geteuid')) {
                $home = posix_getpwuid(posix_geteuid())['dir'] ?? null;
            }
            $dir = $home ? rtrim($home, '/') . '/db-backups' : '';
        }
        return ($dir !== '' && is_dir($dir)) ? $dir : null;
    }

    /** الملفات المتاحة، الأحدث أولاً */
    private function files(?string $dir): array
    {
        if ($dir === null) {
            return [];
        }
        $list = [];
        foreach (glob($dir . '/*.sql.gz') ?: [] as $path) {
            $list[] = [
                'name' => basename($path),
                'size' => filesize($path),
                'time' => filemtime($path),
                'daily' => basename($path) === 'backup_DB.sql.gz',
            ];
        }
        usort($list, fn ($a, $b) => [$b['daily'], $b['time']] <=> [$a['daily'], $a['time']]);
        return $list;
    }

    public function actionIndex()
    {
        $dir = $this->backupDir();
        $runtime = Yii::getAlias('@runtime');
        $read = fn ($f) => is_file("$runtime/$f") ? trim((string) file_get_contents("$runtime/$f")) : null;

        return $this->render('index', [
            'dirFound' => $dir !== null,
            'files' => $this->files($dir),
            'lastDeploy' => $read('deploy.last'),
            'deployFailed' => $read('deploy.failed'),
            'backupFailed' => $read('backup.failed'),
        ]);
    }

    public function actionDownload($name)
    {
        // اسم الملف يجب أن يكون من القائمة نفسها (لا مسارات)
        $dir = $this->backupDir();
        $names = array_column($this->files($dir), 'name');
        if (!in_array($name, $names, true)) {
            throw new NotFoundHttpException('الملف غير موجود.');
        }
        $prefix = preg_replace('/[^A-Za-z0-9_-]+/', '', (string) Yii::$app->request->hostName);
        $download = $name === 'backup_DB.sql.gz'
            ? ($prefix ? $prefix . '-' : '') . 'backup_DB-' . date('Y-m-d_H-i', filemtime("$dir/$name")) . '.sql.gz'
            : $name;

        return Yii::$app->response->sendFile("$dir/$name", $download, ['mimeType' => 'application/gzip']);
    }
}
