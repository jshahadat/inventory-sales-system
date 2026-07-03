cat > create-admin.php << 'EOF'
<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
echo "Step 1: Script started...\n";

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

echo "Step 2: Loading autoload...\n";
require __DIR__ . '/vendor/autoload.php';

echo "Step 3: Loading Yii.php...\n";
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';

echo "Step 4: Loading console config...\n";
$config = require __DIR__ . '/config/console.php';

echo "Step 5: Creating console application...\n";
new yii\console\Application($config);

echo "Step 6: Building User model...\n";
$user = new app\models\User();
$user->username = 'admin';
$user->email = 'admin@example.com';
$user->setPassword('admin123');
$user->generateAuthKey();
$user->status = 10;
$user->created_at = time();
$user->updated_at = time();

echo "Step 7: Saving user...\n";
if ($user->save()) {
    echo "SUCCESS: Admin user created!\n";
    echo "Username: admin\n";
    echo "Password: admin123\n";
    echo "User ID: {$user->id}\n";
} else {
    echo "FAILED to save user. Errors:\n";
    print_r($user->errors);
}

echo "Step 8: Script finished.\n";