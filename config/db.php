<?php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=' . env('DB_HOST') . ';dbname=' . env('DB_NAME'),
    'username' => env('DB_USER'),
    'password' => env('DB_PASS'),
    'charset' => 'utf8',

    // كاش هيكل الجداول: بدونه يسأل Yii قاعدة البيانات عن أعمدة كل جدول في كل طلب.
    // بعد أي ترحيل (migrate) أو تعديل على الجداول نفّذ: php yii cache/flush-schema db
    'enableSchemaCache' => true,
    'schemaCacheDuration' => 3600,
    'schemaCache' => 'cache',
];
