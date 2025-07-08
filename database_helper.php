
<?php
require_once 'pages/db.php';

class DatabaseHelper {
    private static $db;
    
    public static function init() {
        if (self::$db === null) {
            self::$db = Database::getInstance()->getConnection();
        }
        return self::$db;
    }
    
    public static function execute($query, $params = []) {
        $db = self::init();
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }
    
    public static function fetch($query, $params = []) {
        $stmt = self::execute($query, $params);
        return $stmt->fetch();
    }
    
    public static function fetchAll($query, $params = []) {
        $stmt = self::execute($query, $params);
        return $stmt->fetchAll();
    }
    
    public static function insert($table, $data) {
        $columns = implode(',', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $query = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        
        $stmt = self::execute($query, $data);
        return self::init()->lastInsertId();
    }
    
    public static function update($table, $data, $where, $whereParams = []) {
        $setParts = [];
        foreach (array_keys($data) as $key) {
            $setParts[] = "{$key} = :{$key}";
        }
        $setClause = implode(', ', $setParts);
        
        $query = "UPDATE {$table} SET {$setClause} WHERE {$where}";
        $params = array_merge($data, $whereParams);
        
        return self::execute($query, $params);
    }
}
?>
