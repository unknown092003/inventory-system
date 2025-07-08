<?php
class Database {
    private static $instance = null;
    private $connection;
    
    private $host = 'localhost';
    private $dbname = 'inventory_system';
    private $username = 'root';
    private $password = '';

    private function __construct() {
        try {
            $this->connection = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname}", 
                $this->username, 
                $this->password
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    // Prevent cloning and unserialization
    private function __clone() {}
    public function __wakeup() {}
}

// Legacy function for backward compatibility
function getConnection() {
    return Database::getInstance()->getConnection();
}
?>