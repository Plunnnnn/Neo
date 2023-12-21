<?php

$servername = "mediadb.czpusmrdxalt.eu-north-1.rds.amazonaws.com";
$username = "basmoussent";
$password = "zsedLOKI:123!";
$dbname = "MovieDB";

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the movieID from the URL parameter
$movieID = isset($_GET['movieID']) ? $_GET['movieID'] : null;

if ($movieID) {
    // Fetch movie details from the database based on the movieID
    $sql = "SELECT * FROM movies WHERE movieID = $movieID";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Fetch the first row (assuming movieID is unique)
        $row = $result->fetch_assoc();

        // Convert the movie details to JSON format
        $movieDetails = json_encode($row);

        // Return the JSON response
        header('Content-Type: application/json');
        echo $movieDetails;
    } else {
        // Movie not found
        header('HTTP/1.1 404 Not Found');
        echo json_encode(['error' => 'Movie not found']);
    }
} else {
    // Invalid or missing movieID parameter
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Invalid or missing movieID parameter']);
}

// Close the database connection
$conn->close();
?>
