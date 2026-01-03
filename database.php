<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost:3306');
define('DB_USER', 'if0_40817474');
define('DB_PASS', 'misu7890');
define('DB_NAME', 'if0_40817474_pasteeleria09');

class Database {
    private $conn;
    
    public function __construct() {
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($this->conn->connect_error) {
            die("Error de conexión: " . $this->conn->connect_error);
        }
        
        $this->conn->set_charset("utf8");
    }
    
    public function getConnection() {
        return $this->conn;
    }
    
    public function closeConnection() {
        $this->conn->close();
    }
}

// Crear instancia global de la base de datos
$db = new Database();
$conn = $db->getConnection();
?>