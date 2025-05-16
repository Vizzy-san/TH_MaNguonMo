<?php
class CategoryModel
{
private $conn;
private $table_name = "category";
public function __construct($db)
{
$this->conn = $db;
}
public function getCategories()
{
$query = "SELECT id, name, description FROM " . $this->table_name;
$stmt = $this->conn->prepare($query);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_OBJ);
return $result;
}

public function getCategoryById($id)
{
    $query = "SELECT id, name, description FROM " . $this->table_name . " WHERE id = :id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    
    return $stmt->fetch(PDO::FETCH_OBJ);
}

public function addCategory($name, $description)
{
    $query = "INSERT INTO " . $this->table_name . " (name, description) VALUES (:name, :description)";
    $stmt = $this->conn->prepare($query);
    
    // Sanitize data
    $name = htmlspecialchars(strip_tags($name));
    $description = htmlspecialchars(strip_tags($description));
    
    // Bind parameters
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    
    // Execute the query
    if ($stmt->execute()) {
        return true;
    }
    
    return false;
}

public function updateCategory($id, $name, $description)
{
    $query = "UPDATE " . $this->table_name . " SET name = :name, description = :description WHERE id = :id";
    $stmt = $this->conn->prepare($query);
    
    // Sanitize data
    $id = htmlspecialchars(strip_tags($id));
    $name = htmlspecialchars(strip_tags($name));
    $description = htmlspecialchars(strip_tags($description));
    
    // Bind parameters
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    
    // Execute the query
    if ($stmt->execute()) {
        return true;
    }
    
    return false;
}

public function deleteCategory($id)
{
    $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
    $stmt = $this->conn->prepare($query);
    
    // Sanitize data
    $id = htmlspecialchars(strip_tags($id));
    
    // Bind parameter
    $stmt->bindParam(':id', $id);
    
    // Execute the query
    if ($stmt->execute()) {
        return true;
    }
    
    return false;
}

// Get the total count of categories
public function getCategoryCount() {
    $query = "SELECT COUNT(*) as count FROM " . $this->table_name;
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_OBJ);
    return $result->count;
}
}
?> 