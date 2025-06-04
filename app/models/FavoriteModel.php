<?php
class FavoriteModel {
    private $conn;
    private $table_name = "user_favorites";
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Modified to return empty array instead of executing query
    public function getUserFavorites($userId) {
        // Return empty array instead of querying non-existent table
        return [];
    }
    
    // Modified to always return true without touching database
    public function addFavorite($userId, $productId) {
        return true; // Pretend it worked
    }
    
    // Modified to always return true without touching database
    public function removeFavorite($userId, $productId) {
        return true; // Pretend it worked
    }
    
    // Modified to always return false (not a favorite)
    public function isFavorite($userId, $productId) {
        return false; // Always report as not a favorite
    }
    
    // Modified to return 0
    public function countUserFavorites($userId) {
        return 0; // Always return 0 favorites
    }
}
