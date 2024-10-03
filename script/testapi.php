<?php
// Function to get weather data for a specific location
function getTemperature($location) {
    $weatherApiUrl = "https://wttr.in/" . urlencode($location) . "?format=j1";
    
    $weatherResponse = file_get_contents($weatherApiUrl);
    
    if ($weatherResponse === FALSE) {
        return null;
    }

    $weatherData = json_decode($weatherResponse, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        return null;
    }

    // Check if 'current_condition' exists and return temperature in Celsius
    if (isset($weatherData['current_condition'][0]['temp_C'])) {
        return $weatherData['current_condition'][0]['temp_C'] . "°C";
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

    // Get temperature for the location
    $temperature = getTemperature($location);
    if ($temperature === null) {
        continue;
    }
    
    // Display animal and temperature information
    echo "Animal: " . $animal['commonName'] . "\n";
    echo "Location: " . $location . "\n";
    echo "Current Temperature: " . $temperature . "\n";
    echo "----------------------------------------\n";
    $count++;
}

echo "Total animals with temperature data: " . $count . "\n";

?>
