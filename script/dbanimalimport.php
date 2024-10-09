<?php

require_once 'config.php';

// Establish a connection to the database
try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Fetch data from the API
$apiUrl = "https://extinct-api.herokuapp.com/api/v1/animal/900?imageRequired=true";
$apiResponse = file_get_contents($apiUrl);

// Check if the API response is valid
if ($apiResponse === FALSE) {
    die("Error fetching data from the API");
}

// Decode the JSON response into a PHP array
$animalsData = json_decode($apiResponse, true);

// Check if decoding was successful
if (json_last_error() !== JSON_ERROR_NONE) {
    die("Error decoding JSON: " . json_last_error_msg());
}

// Ensure the response is an array (to handle the case where it's just one record)
// Check if 'data' exists in the API response
if (!isset($animalsData['data']) || !is_array($animalsData['data'])) {
    die("No valid 'data' found in the API response.");
}

// SQL query to insert the data
$sql = "INSERT INTO animals (name, location, last_record, image, description)
        VALUES (:name, :location, :lastRecord, :image, :description)";

// Prepare the statement for execution
$stmt = $pdo->prepare($sql);


// Loop through each animal in the 'data' array and insert into the database
$count=0;
foreach ($animalsData['data'] as $animal) {
    // Handle missing or "false" values by setting defaults
    if (($animal['commonName'] === 'false') || ($animal['imageSrc'] === 'false')) {
        continue;
    }
    
    // Execute the SQL statement for each animal
    try {
        $stmt->execute([
            ':name' => $animal['commonName'],
            ':location' => $animal['location'],
            ':lastRecord' => $animal['lastRecord'],
            ':image' => $animal['imageSrc'],
            ':description' => $animal['shortDesc']
        ]);
        echo "Data for {$animal['commonName']} inserted successfully.\n";
    } catch (PDOException $e) {
        echo "Error inserting data for {$animal['commonName']}: " . $e->getMessage() . "\n";
    }

    $count++;
}

echo "Total animals inserted: " . $count . "\n";

// Close the database connection
$pdo = null;
?>