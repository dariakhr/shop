<?php
require 'DB.php';

// Verbindung herstellen
$db = new DB('localhost', 'meine_datenbank', 'root', '');

// Datensatz einfügen
$db->execute("INSERT INTO personen (name, alter) VALUES (?, ?)", ['Ali', 28]);
echo "Neue ID: " . $db->lastId() . "<br>";

// Alle Personen abfragen
$personen = $db->select("SELECT * FROM personen");
foreach ($personen as $p) {
    echo $p['id'] . " - " . $p['name'] . " (" . $p['alter'] . " Jahre)<br>";
}

// Einzelnen Datensatz holen
$person = $db->selectOne("SELECT * FROM personen WHERE id = ?", [1]);
if ($person) {
    echo "<hr>Person Nr. 1: " . $person['name'];
}
?>
