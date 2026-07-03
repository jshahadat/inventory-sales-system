<?php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=inventory_sales',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',

    // Uncomment for production - big performance win on repeated schema reads
    // 'enableSchemaCache' => true,
    // 'schemaCacheDuration' => 60,
    // 'schemaCache' => 'cache',
];
