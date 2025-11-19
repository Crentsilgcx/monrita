<?php

define('APP_START', microtime(true));

use App\Controllers\AuthController;
use App\Controllers\DeliveriesController;
use App\Controllers\DashboardController;
use App\Controllers\MasterDataController;
use App\Controllers\PlanPeriodsController;
use App\Controllers\SupplyPlanImportsController;
use App\Controllers\SupplyPlansController;
use App\Controllers\UsersController;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;
use App\Utils\Router;

require_once __DIR__ . '/../vendor_autoload.php';

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

date_default_timezone_set(getenv('APP_TIMEZONE') ?: 'UTC');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$router = new Router();
$authController = new AuthController();
$usersController = new UsersController();
$planPeriodsController = new PlanPeriodsController();
$deliveriesController = new DeliveriesController();
$dashboardController = new DashboardController();
$supplyPlansController = new SupplyPlansController();
$supplyPlanImportsController = new SupplyPlanImportsController();

$authMiddleware = (new AuthMiddleware())->handle();
$superAdminOnly = (new RoleMiddleware())->handle(['SUPER_ADMIN']);
$fieldOrAdmin = (new RoleMiddleware())->handle(['SUPER_ADMIN', 'FIELD_STAFF']);

// Auth routes
$router->add('POST', '/api/v1/auth/login', function () use ($authController) {
    $authController->login();
});
$router->add('GET', '/api/v1/auth/me', function ($request) use ($authController) {
    $authController->me($request);
}, [$authMiddleware]);
$router->add('POST', '/api/v1/auth/logout', function () use ($authController) {
    $authController->logout();
}, [$authMiddleware]);

// Users
$router->add('GET', '/api/v1/users', function ($request) use ($usersController) {
    $usersController->index($request);
}, [$authMiddleware, $superAdminOnly]);
$router->add('POST', '/api/v1/users', function ($request) use ($usersController) {
    $usersController->store($request);
}, [$authMiddleware, $superAdminOnly]);

// Plan Periods
$router->add('GET', '/api/v1/plan-periods', function ($request) use ($planPeriodsController) {
    $planPeriodsController->index($request);
}, [$authMiddleware, $superAdminOnly]);
$router->add('POST', '/api/v1/plan-periods', function ($request) use ($planPeriodsController) {
    $planPeriodsController->store($request);
}, [$authMiddleware, $superAdminOnly]);
$router->add('GET', '/api/v1/plan-periods/([0-9]+)/metrics', function ($request) use ($planPeriodsController) {
    $planPeriodsController->metrics($request);
}, [$authMiddleware, $superAdminOnly]);

// Master data
$router->add('GET', '/api/v1/schools', function ($request) {
    (new MasterDataController('schools', ['code', 'name', 'region', 'district']))->index($request);
}, [$authMiddleware, $superAdminOnly]);
$router->add('POST', '/api/v1/schools', function ($request) {
    (new MasterDataController('schools', ['code', 'name', 'region', 'district']))->store($request);
}, [$authMiddleware, $superAdminOnly]);

$router->add('GET', '/api/v1/commodities', function ($request) {
    (new MasterDataController('commodities', ['code', 'name', 'unit']))->index($request);
}, [$authMiddleware, $superAdminOnly]);
$router->add('POST', '/api/v1/commodities', function ($request) {
    (new MasterDataController('commodities', ['code', 'name', 'unit']))->store($request);
}, [$authMiddleware, $superAdminOnly]);

$router->add('GET', '/api/v1/suppliers', function ($request) {
    (new MasterDataController('suppliers', ['name']))->index($request);
}, [$authMiddleware, $superAdminOnly]);
$router->add('POST', '/api/v1/suppliers', function ($request) {
    (new MasterDataController('suppliers', ['name']))->store($request);
}, [$authMiddleware, $superAdminOnly]);

// Supply plans
$router->add('GET', '/api/v1/supply-plans', function ($request) use ($supplyPlansController) {
    $supplyPlansController->index($request);
}, [$authMiddleware, $superAdminOnly]);
$router->add('POST', '/api/v1/supply-plans', function ($request) use ($supplyPlansController) {
    $supplyPlansController->store($request);
}, [$authMiddleware, $superAdminOnly]);
$router->add('GET', '/api/v1/supply-plan-imports', function ($request) use ($supplyPlanImportsController) {
    $supplyPlanImportsController->index($request);
}, [$authMiddleware, $superAdminOnly]);
$router->add('POST', '/api/v1/supply-plan-imports', function ($request) use ($supplyPlanImportsController) {
    $supplyPlanImportsController->store($request);
}, [$authMiddleware, $superAdminOnly]);
$router->add('GET', '/api/v1/supply-plans/balance', function ($request) use ($deliveriesController) {
    $deliveriesController->balance($request);
}, [$authMiddleware, $fieldOrAdmin]);

// Deliveries
$router->add('GET', '/api/v1/deliveries', function ($request) use ($deliveriesController) {
    $deliveriesController->index($request);
}, [$authMiddleware, $fieldOrAdmin]);
$router->add('POST', '/api/v1/deliveries', function ($request) use ($deliveriesController) {
    $deliveriesController->store($request);
}, [$authMiddleware, $fieldOrAdmin]);

// Dashboard
$router->add('GET', '/api/v1/dashboard/summary', function ($request) use ($dashboardController) {
    $dashboardController->summary($request);
}, [$authMiddleware, $superAdminOnly]);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
