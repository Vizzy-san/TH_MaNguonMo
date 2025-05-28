<?php
// Start session after autoloader is registered
// Define the base path
define('BASE_PATH', __DIR__);

// Load ProductModel class first to prevent unserialization issues
require_once BASE_PATH . '/app/models/ProductModel.php';

// Now start the session after model is loaded
session_start();

// Autoload classes
spl_autoload_register(function($className) {
    // Check in main app directory for namespaced classes
    $file = BASE_PATH . '/app/' . str_replace('\\', '/', $className) . '.php';
    if (file_exists($file)) {
        require_once $file;
        return true;
    }
    
    // Check in models directory
    $modelFile = BASE_PATH . '/app/models/' . $className . '.php';
    if (file_exists($modelFile)) {
        require_once $modelFile;
        return true;
    }
    
    // Check in controllers directory
    $controllerFile = BASE_PATH . '/app/controllers/' . $className . '.php';
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        return true;
    }
    
    return false;
});

$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);
// Kiểm tra phần đầu tiên của URL để xác định controller
$controllerName = isset($url[0]) && $url[0] != '' ? ucfirst($url[0]) . 'Controller' :
'ProductController';
// Kiểm tra phần thứ hai của URL để xác định action
$action = isset($url[1]) && $url[1] != '' ? $url[1] : 'list';

// Set default action to 'index' for AdminController
if ($controllerName === 'AdminController' && $action === 'list') {
    $action = 'index';
}

// die ("controller=$controllerName - action=$action");

// Kiểm tra xem controller và action có tồn tại không
if (!file_exists(BASE_PATH . '/app/controllers/' . $controllerName . '.php')) {
// Xử lý không tìm thấy controller
die('Controller not found: ' . $controllerName);
}
require_once BASE_PATH . '/app/controllers/' . $controllerName . '.php';
$controller = new $controllerName();
if (!method_exists($controller, $action)) {
// Xử lý không tìm thấy action
die('Action not found: ' . $action);
}
// Gọi action với các tham số còn lại (nếu có)
call_user_func_array([$controller, $action], array_slice($url, 2));