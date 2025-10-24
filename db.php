<?php
class DB {
    private $pdo;

    // Konstruktor: Verbindung aufbauen (MAMP nutzt oft Port 8889)
    public function __construct($host, $db, $user, $pass, $port = 8889, $charset = 'utf8mb4') {
        $dsn = "mysql:host={$host};port={$port};dbname={$db};charset={$charset}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false
        ];
        $this->pdo = new PDO($dsn, $user, $pass, $options);
    }

    public function select($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function selectOne($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public function execute($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public function lastId() {
        return $this->pdo->lastInsertId();
    }
}


