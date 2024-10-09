<?php

require_once 'config.php';

// Funktion zur Verbindung mit der Datenbank
try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Verbindung zur Datenbank fehlgeschlagen: " . $e->getMessage());
}

// Daten aus der Tabelle abfragen
$stmt = $pdo->query("SELECT DISTINCT animals.name, animals.location, last_record, image, latitude, longitude, animals.description FROM animals, weather_data WHERE animals.location = weather_data.location AND animals.latitude IS NOT NULL AND animals.longitude IS NOT NULL;");
$locations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// JSON-Ausgabe
header('Content-Type: application/json');
echo json_encode($locations);
?>