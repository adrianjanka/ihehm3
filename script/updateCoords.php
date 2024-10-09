<?php

require_once 'config.php';


try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}


// Funktion, um die Koordinaten einer Stadt mittels Nominatim API abzurufen
function getCoordinates($location) {
    $nominatimUrl = "https://nominatim.openstreetmap.org/search?q=" . urlencode($location) . "&format=json&limit=1";
    
    // Create a stream context with the User-Agent header
    $opts = [
        'http' => [
            'method' => "GET",
            'header' => "User-Agent: YourAppName/1.0 (youremail@example.com)"
        ]
    ];
    $context = stream_context_create($opts);

    // Make the request with the User-Agent header
    $response = file_get_contents($nominatimUrl, false, $context);
    
    if ($response === FALSE) {
        return null;
    }

    $data = json_decode($response, true);

    if (isset($data[0]['lat']) && isset($data[0]['lon'])) {
        return [
            'latitude' => $data[0]['lat'],
            'longitude' => $data[0]['lon']
        ];
    }

    return null;
}


// Funktion zum Aktualisieren der Koordinaten in der Datenbank
function updateLocationCoordinates($pdo, $id, $latitude, $longitude) {
    $stmt = $pdo->prepare("UPDATE animals SET latitude = :latitude, longitude = :longitude WHERE id = :id");
    $stmt->bindParam(':latitude', $latitude);
    $stmt->bindParam(':longitude', $longitude);
    $stmt->bindParam(':id', $id);

    try {
        $stmt->execute();
        echo "Koordinaten für ID $id erfolgreich aktualisiert.\n";
    } catch (PDOException $e) {
        echo "Fehler beim Aktualisieren der Koordinaten für ID $id: " . $e->getMessage() . "\n";
    }
}


// Alle Orte ohne Koordinaten abrufen
$stmt = $pdo->query("SELECT id, location FROM animals WHERE latitude IS NULL OR longitude IS NULL");
$locations = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($locations as $location) {
    $id = $location['id'];
    $city = $location['location'];

    // Koordinaten für den Ort abrufen
    $coordinates = getCoordinates($city);

    echo print_r($coordinates,true);

    if ($coordinates !== null) {
        // Koordinaten in der Datenbank aktualisieren
        updateLocationCoordinates($pdo, $id, $coordinates['latitude'], $coordinates['longitude']);
    } else {
        echo "Keine Koordinaten für $city gefunden.\n";
    }
}