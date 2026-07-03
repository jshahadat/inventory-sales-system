<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * Initializes RBAC roles & permissions.
 * Run once via console:  php yii rbac/init
 *
 * Roles created:
 *   - admin : full access (manageProduct, manageInvoice, manageUsers, viewReports ...)
 *   - staff : limited access (createInvoice, viewProduct, viewInvoice)
 */
class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll(); // clean slate

        // ---------------- Permissions ----------------
        $manageProduct   = $auth->createPermission('manageProduct');
        $manageProduct->description = 'Create / update / delete products, categories, suppliers';
        $auth->add($manageProduct);

        $manageInvoice   = $auth->createPermission('manageInvoice');
        $manageInvoice->description = 'Create / update / cancel sales invoices';
        $auth->add($manageInvoice);

        $viewInvoice     = $auth->createPermission('viewInvoice');
        $viewInvoice->description = 'View sales invoices';
        $auth->add($viewInvoice);

        $viewProduct     = $auth->createPermission('viewProduct');
        $viewProduct->description = 'View product / stock list';
        $auth->add($viewProduct);

        $manageUsers     = $auth->createPermission('manageUsers');
        $manageUsers->description = 'Create / update users and assign roles';
        $auth->add($manageUsers);

        $viewReports     = $auth->createPermission('viewReports');
        $viewReports->description = 'View dashboard & low-stock reports';
        $auth->add($viewReports);

        // ---------------- Roles ----------------
        $staff = $auth->createRole('staff');
        $auth->add($staff);
        $auth->addChild($staff, $viewProduct);
        $auth->addChild($staff, $viewInvoice);
        $auth->addChild($staff, $manageInvoice); // staff can create sales invoices

        $admin = $auth->createRole('admin');
        $auth->add($admin);
        $auth->addChild($admin, $manageProduct);
        $auth->addChild($admin, $manageUsers);
        $auth->addChild($admin, $viewReports);
        $auth->addChild($admin, $staff); // admin inherits everything staff can do

        $this->stdout("RBAC roles & permissions created successfully.\n");
        $this->stdout("Roles: admin, staff\n");
        $this->stdout("Next: assign a role to a user with  php yii rbac/assign <role> <userId>\n");

        return ExitCode::OK;
    }

    /**
     * Assign a role to a user.
     * Usage: php yii rbac/assign admin 1
     */
    public function actionAssign($role, $userId)
    {
        $auth = Yii::$app->authManager;
        $roleObj = $auth->getRole($role);
        if ($roleObj === null) {
            $this->stderr("Role '$role' not found. Run rbac/init first.\n");
            return ExitCode::DATAERR;
        }
        $auth->revokeAll($userId);
        $auth->assign($roleObj, $userId);
        $this->stdout("Assigned role '$role' to user #$userId\n");

        return ExitCode::OK;
    }
}
