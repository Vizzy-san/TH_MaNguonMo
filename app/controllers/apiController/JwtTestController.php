<?php
require_once('app/utils/JWTHandler.php');

class JwtTestController
{
    private $jwtHandler;
    
    public function __construct()
    {
        $this->jwtHandler = new JWTHandler();
    }
    
    /**
     * Test if the JWT token is valid
     */
    public function testToken()
    {
        header('Content-Type: application/json');
        
        $headers = apache_request_headers();
        
        // Check for Authorization header
        if (!isset($headers['Authorization']) && !isset($headers['authorization'])) {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => 'No Authorization header found'
            ]);
            return;
        }
        
        // Get the token from Authorization header
        $authHeader = $headers['Authorization'] ?? $headers['authorization'];
        $arr = explode(" ", $authHeader);
        
        if (count($arr) != 2 || $arr[0] != 'Bearer') {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid Authorization header format. Expected: Bearer {token}'
            ]);
            return;
        }
        
        $jwt = $arr[1];
        $decoded = $this->jwtHandler->decode($jwt);
        
        if ($decoded) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Token is valid',
                'data' => $decoded
            ]);
        } else {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid token'
            ]);
        }
    }
}
?>
