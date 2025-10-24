CREATE DATABASE IF NOT EXISTS testdb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE testdb;

-- Produkte (mit Bild-URL)
CREATE TABLE IF NOT EXISTS produkte (
  ProduktID INT AUTO_INCREMENT PRIMARY KEY,
  ProduktName VARCHAR(255) NOT NULL,
  ProduktBild VARCHAR(255) DEFAULT NULL,
  ProduktPreis DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  ProduktMenge INT NOT NULL DEFAULT 0,
  ProduktKommentar TEXT
);

-- Kunden (Admin-Verwaltung)
CREATE TABLE IF NOT EXISTS kunden (
  KundenID INT AUTO_INCREMENT PRIMARY KEY,
  Vorname VARCHAR(255),
  Nachname VARCHAR(255),
  Email VARCHAR(255),
  Telefon VARCHAR(255),
  Kommentar TEXT
);

-- Bestellungen 
CREATE TABLE IF NOT EXISTS bestellungen (
  BestellungID INT AUTO_INCREMENT PRIMARY KEY,
  KundeVorname VARCHAR(255) NOT NULL,
  KundeNachname VARCHAR(255) NOT NULL,
  KundeEmail VARCHAR(255) NOT NULL,
  Bestelldatum DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Bestellpositionen (Positionen je Bestellung)
CREATE TABLE IF NOT EXISTS bestellpositionen (
  PositionID INT AUTO_INCREMENT PRIMARY KEY,
  BestellungID INT NOT NULL,
  ProduktID INT NOT NULL,
  ProduktName VARCHAR(255) NOT NULL,
  ProduktPreis DECIMAL(10,2) NOT NULL,
  Menge INT NOT NULL,
  INDEX (BestellungID),
  INDEX (ProduktID)
);

-- Benutzer (Login + Rollen)
CREATE TABLE IF NOT EXISTS users (
  UserID INT AUTO_INCREMENT PRIMARY KEY,
  Email VARCHAR(255) NOT NULL UNIQUE,
  Passwort VARCHAR(255) NOT NULL,
  Rolle ENUM('admin','user') NOT NULL DEFAULT 'user'
);

-- Admin-User: admin@shop.de / admin123 
INSERT IGNORE INTO users (Email, Passwort, Rolle)
VALUES ('admin@shop.de', MD5('admin123'), 'admin');

-- Produkte (Weihnachtsmarkt)
INSERT INTO produkte (ProduktName, ProduktBild, ProduktPreis, ProduktMenge, ProduktKommentar) VALUES
('Lebkuchen', 'images/lebkuchen.jpg', 4.99, 15, 'Sehr leckere Süßigkeit fürs Weihnachten'),
('Gebrannte Mandeln', 'images/mandel.jpg', 6.50, 12, 'Frisch karamellisierte Mandeln, süß und warm'),
('Churros', 'images/churros.jpg', 5.90, 18, 'Knusprige Churros mit Zucker und Zimt'),
('Glühwein', 'images/glühwein.jpg', 7.50, 10, 'Aromatischer heißer Glühwein mit Gewürzen'),
('Bratwurst', 'images/bratwurst.jpg', 8.90, 14, 'Traditionelle Bratwurst frisch vom Grill'),
('Brezel', 'images/pretzel.jpg', 3.90, 20, 'Frisch gebackene Brezel, außen knusprig, innen weich');
