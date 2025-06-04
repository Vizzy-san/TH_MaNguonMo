<?php
class ApiRouterHelper {
    /**
     * Check if the current request is an API request
     * 
     * @param array $url URL segments
     * @return bool True if this is an API request
     */
    public static function isApiRequest($url) {
        return isset($url[0]) && $url[0] === 'api';
    }
    
    /**
     * Handle API request routing
     * 
     * @param array $url URL segments
     * @return bool True if request was handled, false otherwise
     */
    public static function handleRequest($url) {
        // Must have at least 'api/resource' format
        if (count($url) < 2) {
            self::sendJsonResponse(['message' => 'Invalid API request'], 400);
            return true;
        }
        
        // Get the API controller name from the second segment (api/products → ProductsApiController)
        $resourceName = ucfirst($url[1]);
        
        // Handle singular/plural resource names - convert product to Product, products to Product
        if (substr($resourceName, -1) === 's') {
            $resourceName = rtrim($resourceName, 's');
        }
        
        $apiControllerName = $resourceName . 'ApiController';
        $apiControllerFile = BASE_PATH . '/app/controllers/apiController/' . $apiControllerName . '.php';
        
        // Check if controller exists
        if (!file_exists($apiControllerFile)) {
            self::sendJsonResponse([
                'message' => 'API resource not found: ' . $resourceName,
                'debug' => [
                    'controller_name' => $apiControllerName,
                    'controller_file' => $apiControllerFile,
                    'url_segments' => $url
                ]
            ], 404);
            return true;
        }
        
        // Require the controller file if not already loaded
        // (The autoloader may have already loaded it)
        if (!class_exists($apiControllerName)) {
            require_once $apiControllerFile;
        }
        
        // Create controller instance
        $controller = new $apiControllerName();
        
        // Determine action based on HTTP method and URL structure
        $method = $_SERVER['REQUEST_METHOD'];
        $id = isset($url[2]) ? $url[2] : null;
        
        $action = self::determineAction($method, $id);
        
        // Check if the action method exists
        if (!method_exists($controller, $action)) {
            self::sendJsonResponse([
                'message' => 'API method not supported: ' . $method,
                'debug' => [
                    'controller' => $apiControllerName,
                    'action' => $action,
                    'method' => $method
                ]
            ], 405);
            return true;
        }
        
        // Call the appropriate method with or without ID parameter
        try {
            if ($id) {
                call_user_func_array([$controller, $action], [$id]);
            } else {
                call_user_func_array([$controller, $action], []);
            }
        } catch (Exception $e) {
            self::sendJsonResponse(['message' => 'API error: ' . $e->getMessage()], 500);
        }
        
        return true;
    }
    
    /**
     * Determine the controller action based on HTTP method and resource ID
     * 
     * @param string $method HTTP method
     * @param string|null $id Resource ID
     * @return string Action name
     */
    private static function determineAction($method, $id) {
        switch ($method) {
            case 'GET':
                return $id ? 'show' : 'index';
            case 'POST':
                return 'store';
            case 'PUT':
            case 'PATCH':
                return 'update';
            case 'DELETE':
                return 'destroy';
            default:
                return 'index';
        }
    }
    
    /**
     * Send JSON response with appropriate HTTP status code
     * 
     * @param mixed $data Response data
     * @param int $statusCode HTTP status code
     */
    public static function sendJsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_PRETTY_PRINT);
    }
}
