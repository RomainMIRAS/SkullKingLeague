<?php
require_once '../config/database.php';

class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $pseudo;
    public $elo;
    public $parties_jouees;
    public $victoires;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY elo DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (pseudo) VALUES (?)";
        $stmt = $this->conn->prepare($query);
        
        $this->pseudo = htmlspecialchars(strip_tags($this->pseudo));
        
        $stmt->bindParam(1, $this->pseudo);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET elo = ?, parties_jouees = ?, victoires = ? 
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(1, $this->elo);
        $stmt->bindParam(2, $this->parties_jouees);
        $stmt->bindParam(3, $this->victoires);
        $stmt->bindParam(4, $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function updateElo($new_elo) {
        $query = "UPDATE " . $this->table_name . " SET elo = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $new_elo);
        $stmt->bindParam(2, $this->id);
        return $stmt->execute();
    }

    public function incrementStats($won = false) {
        $query = "UPDATE " . $this->table_name . " 
                  SET parties_jouees = parties_jouees + 1" . 
                  ($won ? ", victoires = victoires + 1" : "") . " 
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        return $stmt->execute();
    }
}
?>
