<?php
class FavoriteModel {
    private $conn;
    private $table_name = "user_favorites";
    
    public function __construct($db) {
        $this->conn = $db;
        // Initialize session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        // Initialize favorites array in session if it doesn't exist
        if (!isset($_SESSION['favorites'])) {
            $_SESSION['favorites'] = [];
        }
    }
    
    // Get user favorites from session
    public function getUserFavorites($userId) {
        if (!isset($_SESSION['favorites'][$userId])) {
            return [];
        }
        
        // Get products for the favorite IDs
        $favorites = [];
        foreach ($_SESSION['favorites'][$userId] as $productId) {
            try {
                // Fetch product details from the database
                $query = "SELECT * FROM product WHERE id = :id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':id', $productId);
                $stmt->execute();
                $product = $stmt->fetch(PDO::FETCH_OBJ);
                
                if ($product) {
                    $favorites[] = $product;
                }
            } catch (Exception $e) {
                // Skip any products that cause errors
                continue;
            }
        }
        
        return $favorites;
    }
    
    // Add a product to favorites in session
    public function addFavorite($userId, $productId) {
        if (!isset($_SESSION['favorites'][$userId])) {
            $_SESSION['favorites'][$userId] = [];
        }
        
        if (!in_array($productId, $_SESSION['favorites'][$userId])) {
            $_SESSION['favorites'][$userId][] = $productId;
        }
        
        return true;
    }
    
    // Remove a product from favorites in session
    public function removeFavorite($userId, $productId) {
        if (isset($_SESSION['favorites'][$userId])) {
            $key = array_search($productId, $_SESSION['favorites'][$userId]);
            if ($key !== false) {
                unset($_SESSION['favorites'][$userId][$key]);
                // Re-index the array
                $_SESSION['favorites'][$userId] = array_values($_SESSION['favorites'][$userId]);
            }
        }
        
        return true;
    }
    
    // Check if a product is in the favorites
    public function isFavorite($userId, $productId) {
        if (!isset($_SESSION['favorites'][$userId])) {
            return false;
        }
        
        return in_array($productId, $_SESSION['favorites'][$userId]);
    }
    
    // Count the number of favorites
    public function countUserFavorites($userId) {
        if (!isset($_SESSION['favorites'][$userId])) {
            return 0;
        }
        
        return count($_SESSION['favorites'][$userId]);
    }
}
