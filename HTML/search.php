<?php
$servername = "mediadb.czpusmrdxalt.eu-north-1.rds.amazonaws.com";
$username = "basmoussent";
$password = "zsedLOKI:123!";
$dbname = "MovieDB";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['title'])) {
    $movieTitle = $_GET['title'];
    $sql = "SELECT * FROM movies WHERE titre = '$movieTitle'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<p>Title: " . $row['titre'] . "</p>" . "<p>Description: " . $row['description'] . "</p>";
            
            
        }
    } else {
        echo "pas d efilm trouvé";
    }
    if (!$result) {
        die("Erreur dans la requête SQL : " . $conn->error);
    }
}

$conn->close();
?>