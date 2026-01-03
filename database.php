<?php
// Configuración de la base de datos
define('DB_HOST', 'sql311.infinityfree.com');
define('DB_USER', 'if0_40727550');
define('DB_PASS', 'R384n6XMKCK4');
define('DB_NAME', 'if0_40727550_pastel1212');

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