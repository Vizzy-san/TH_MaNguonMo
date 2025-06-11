<?php
require_once 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTHandler
{
    private $secret_key;
    private $token_expiration;

    public function __construct()
    {
        // For enhanced security, consider moving this to an environment variable or config file
        $this->secret_key = "BFYL"; 
        $this->token_expiration = 3600; // Token valid for 1 hour
    }

    /**
     * Create a JWT token
     * 
     * @param array $data Data to encode in the token
     * @return string The encoded JWT token
     */
    public function encode($data)
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + $this->token_expiration;
        
        $payload = [
            'iat' => $issuedAt,          // Issued at: time when the token was generated
            'exp' => $expirationTime,     // Expiration time
            'nbf' => $issuedAt,           // Not before
            'iss' => 'BFYL_API',          // Issuer
            'data' => $data               // Data to be encoded in the JWT
        ];
        
        return JWT::encode($payload, $this->secret_key, 'HS256');
    }

    /**
     * Decode a JWT token
     * 
     * @param string $jwt The JWT token to decode
     * @return array|null The decoded data or null if invalid
     */
    public function decode($jwt)
    {
        try {
            $decoded = JWT::decode($jwt, new Key($this->secret_key, 'HS256'));
            return (array) $decoded->data;
        } catch (\Exception $e) {
            error_log('JWT Decode Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Validate a JWT token
     * 
     * @param string $jwt The JWT token to validate
     * @return bool True if valid, false otherwise
     */
    public function validate($jwt)
    {
        return $this->decode($jwt) !== null;
    }

    /**
     * Get remaining time for a token in seconds
     * 
     * @param string $jwt The JWT token
     * @return int|null Seconds until expiration or null if invalid
     */
    public function getTokenRemainingTime($jwt)
    {
        try {
            $decoded = JWT::decode($jwt, new Key($this->secret_key, 'HS256'));
            if (isset($decoded->exp)) {
                $remainingTime = $decoded->exp - time();
                return $remainingTime > 0 ? $remainingTime : 0;
            }
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
?>
