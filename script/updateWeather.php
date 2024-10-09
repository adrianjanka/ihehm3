<?php

require_once 'config.php';

// check for valid time to run the script (cronjob restriction)
$now = date('H:i:s');
if ($now < '06:00:00' || $now > '22:00:00') {
    exit;
}

// Establish a connection to the database
try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Function to get weather data for a specific location
function getWeatherData($location) {
    $weatherApiUrl = "https://wttr.in/" . urlencode($location) . "?format=j1";
    
    $weatherResponse = file_get_contents($weatherApiUrl);
    
    if ($weatherResponse === FALSE) {
        return null;
    }

    $weatherData = json_decode($weatherResponse, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        return null;
    }

    // Check if 'current_condition' exists
    if (isset($weatherData['current_condition'][0]) && isset($weatherData['weather'][0]['avgtempC'])) {
        $currentCondition = $weatherData['current_condition'][0];

        // Extract temperature and weather description
        $temperature = isset($weatherData['weather'][0]['avgtempC']) ? $weatherData['weather'][0]['avgtempC'] : null;
        $weatherDesc = isset($currentCondition['weatherDesc'][0]['value']) ? $currentCondition['weatherDesc'][0]['value'] : null;

        // Return both values as an array
        return [
            'temperature' => $temperature,
            'weatherDesc' => $weatherDesc
        ];
    }

    return null;
}

// Fetch extinct animal data from the API (replace with your actual API URL)
$apiUrl = "https://extinct-api.herokuapp.com/api/v1/animal/900?imageRequired=true";
$apiResponse = file_get_contents($apiUrl);

// Check if the API response is valid
if ($apiResponse === FALSE) {
    die("Error fetching data from the animal API");
}

// Decode the JSON response into a PHP array
$animalsData = json_decode($apiResponse, true);

// Check if decoding was successful
if (json_last_error() !== JSON_ERROR_NONE) {
    die("Error decoding JSON: " . json_last_error_msg());
}

// Check if 'data' exists in the API response
if (!isset($animalsData['data']) || !is_array($animalsData['data'])) {
    die("No valid 'data' found in the API response.");
}

// Loop through each animal in the 'data' array and fetch weather data for its location
$count =0;
foreach ($animalsData['data'] as $animal) {

    if (($animal['commonName'] === 'false') || ($animal['imageSrc'] === 'false')) {
        continue;
    }

    $location = $animal['location'];

    // Get weather for the location
    $weather = getWeatherData($location);
    if ($weather === null) {
        continue;
    }
    
    // // Display animal and temperature information
    // echo "Animal: " . $animal['commonName'] . "\n";
    // echo "Location: " . $location . "\n";
    // echo "Temperature: " . $weather['temperature'] . "°C\n";
    // echo "Weather Description: " . $weather['weatherDesc'] . "\n";
    // echo "----------------------------------------\n";


    // Insert weather data into the database
    $stmt = $pdo->prepare("INSERT INTO weather_data (location, temperature, description, date) VALUES (:location, :temperature, :description, :date)");
    
    // Aktuelles Datum
    $date = date('Y-m-d');

    // Daten binden und ausführen
    $stmt->bindParam(':location', $location);
    $stmt->bindParam(':temperature', $weather['temperature']);
    $stmt->bindParam(':description', $weather['weatherDesc']);
    $stmt->bindParam(':date', $date);

    try {
        $stmt->execute();
        echo "Wetterdaten erfolgreich gespeichert für $location\n";
    } catch (PDOException $e) {
        echo "Fehler beim Speichern der Wetterdaten für $location: " . $e->getMessage() . "\n";
    }

    $count++;
}

echo "Updated weather for: " . $count . " locations\n";

?>
