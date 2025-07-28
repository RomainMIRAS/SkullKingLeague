<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'skull_king_league';
    private $username = 'skullking_user';
    private $password = 'SkullKing_2025!';
    private $conn;

    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Erreur de connexion: " . $exception->getMessage();
        }
        
        return $this->conn;
    }
}
?>
