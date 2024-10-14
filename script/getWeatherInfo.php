<?php

require_once 'config.php';

// Funktion zur Verbindung mit der Datenbank
try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Verbindung zur Datenbank fehlgeschlagen: " . $e->getMessage());
}

// Location aus GET-Parameter abrufen
$location = isset($_GET['location']) ? $_GET['location'] : '';

// Falls keine Location übergeben wurde, gib eine Fehlermeldung zurück
if (empty($location)) {
    $output = ['error' => 'Keine Location angegeben'];
    exit;
}

// Wetterinformationen für die Location aus der Datenbank abrufen
$stmt = $pdo->prepare("SELECT DISTINCT date, temperature, description FROM weather_data WHERE location = :location AND date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) ORDER BY date DESC;");
$stmt->bindParam(':location', $location);
$stmt->execute();

// Prüfe, ob Wetterdaten gefunden wurden
if ($stmt->rowCount() > 0) {
    $weatherData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $output = $weatherData;
} else {
    // Keine Wetterdaten gefunden
    $output = ['error' => 'Keine Wetterinformationen gefunden'];
}

header('Content-Type: application/json');
echo json_encode($output);