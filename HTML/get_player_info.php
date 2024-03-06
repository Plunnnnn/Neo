<?php

$servername = "moviedb.ch0eymmkwcev.eu-west-3.rds.amazonaws.com";
$username = "basmoussent";
$password = "zsedLOKI:123!";
$dbname = "MovieDB";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$movieID = isset($_GET['movieID']) ? $_GET['movieID'] : null;

if ($movieID) {
    
    $sql = "SELECT * FROM movies WHERE movieID = $movieID";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        
        $row = $result->fetch_assoc();

        
        $movieDetails = json_encode($row);

        
        header('Content-Type: application/json');
        echo $movieDetails;
    } else {
        
        header('HTTP/1.1 404 Not Found');
        echo json_encode(['erreur' => 'pas d efilm']);
    }
} else {
    
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['erreur' => 'pas de parametres']);
}


$conn->close();
?>
