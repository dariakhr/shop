<?php
class DB {
    private $pdo;

    // Verbindung aufbauen
    public function __construct($host, $db, $user, $pass, $charset = 'utf8mb4') {
        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,   // Exceptions bei Fehlern
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,         // Arrays statt Objekte
            PDO::ATTR_EMULATE_PREPARES   => false                     // native Prepared Statements
        ];
        $this->pdo = new PDO($dsn, $user, $pass, $options);
    }

    // SELECT: mehrere Zeilen
    public function select($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // SELECT: eine Zeile
    public function selectOne($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    // INSERT/UPDATE/DELETE
    public function execute($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    // letzte Auto-ID
    public function lastId() {
        return $this->pdo->lastInsertId();
    }
}

