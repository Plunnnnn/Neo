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

    // Check for an exact match first
    $exactMatchSQL = "SELECT * FROM movies WHERE titre = '$movieTitle'";
    $exactMatchResult = $conn->query($exactMatchSQL);

    if ($exactMatchResult->num_rows > 0) {
        while ($row = $exactMatchResult->fetch_assoc()) {
            echo "<p>Title: " . $row['titre'] . "</p>" . "<p>Description: " . $row['description'] . '</p>   <div class="reader-main"> <iframe id="reader" class="reader" width="1000" height="500" allowfullscreen="" src="' . $row['lecteur_link'] . '" style="display: block;"></iframe> </div>';

        }
    } else {
        // If no exact match, check using Levenshtein distance
        $threshold = 3; // You can adjust the threshold as needed

        $similarTitlesSQL = "SELECT * FROM movies";
        $similarTitlesResult = $conn->query($similarTitlesSQL);

        if ($similarTitlesResult) {  // Check if the query was successful
            while ($row = $similarTitlesResult->fetch_assoc()) {
                $distance = levenshtein($movieTitle, $row['titre']);

                if ($distance <= $threshold) {
                    echo "<p>Title: " . $row['titre'] . "</p>" . "<p>Description: " . $row['description'] . '</p>   <div class="reader-main"> <iframe id="reader" class="reader" width="1000" height="500" allowfullscreen="" src="' . $row['lecteur_link'] . '" style="display: block;"></iframe> </div>';

                }
            }
        } else {
            // Handle the case where the query was not successful
            die("Erreur dans la requête SQL : " . $conn->error);
        }

        
    }

    if (!$exactMatchResult) {
        die("Erreur dans la requête SQL : " . $conn->error);
    }
}

$conn->close();
?>
