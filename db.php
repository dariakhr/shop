<?php
class DB {
    private $pdo;

    // Konstruktor: Verbindung aufbauen
    public function __construct($host, $db, $user, $pass, $charset = 'utf8mb4') {
        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false
        ];
        $this->pdo = new PDO($dsn, $user, $pass, $options);
    }

    // Allgemeine SELECT-Abfrage (liefert Array)
    public function select($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(); // Array mit Datensätzen
    }

    // Ein einzelnes Ergebnis
    public function selectOne($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(); // einzelner Datensatz oder false
    }

    // INSERT, UPDATE, DELETE
    public function execute($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount(); // Anzahl betroffener Zeilen
    }

    // Letzte eingefügte ID
    public function lastId() {
        return $this->pdo->lastInsertId();
    }
}
?>
