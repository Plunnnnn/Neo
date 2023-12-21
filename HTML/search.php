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
            echo "<div class='movie'>";
            echo "<div class='image-container'>";
            echo "<img src='{$row['poster']}' alt='{$row['titre']}'>";
            echo "<a href='player.html?movieID={$row['movieid']}'>";
            echo "</div>";
            echo "<div class='details'>";
            echo "<h3>{$row['titre']}</h3>";
            echo "<p>{$row['description']}</p>";
            echo "<p>Note: {$row['ratings']}</p>"; // Ajout de la section de la note
            echo "</div>";
            echo "</div>";
        }
    } else {
        $threshold = 3; // MARGE D'ERREUR

        $similarTitlesSQL = "SELECT * FROM movies";
        $similarTitlesResult = $conn->query($similarTitlesSQL);

        if ($similarTitlesResult) {
            while ($row = $similarTitlesResult->fetch_assoc()) {
                $distance = levenshtein($movieTitle, $row['titre']);

                if ($distance <= $threshold) {
                    echo "<div class='movie'>";
                    echo "<div class='image-container'>";
                    echo "<img src='{$row['poster']}' alt='{$row['titre']}'>";
                    echo "<a href='player.html?movieID={$row['movieid']}'>";
                    echo "</div>";
                    echo "<div class='details'>";
                    echo "<h3>{$row['titre']}</h3>";
                    echo "<p>{$row['description']}</p>";
                    echo "<p>Note: {$row['ratings']}</p>"; // Ajout de la section de la note
                    echo "</div>";
                    echo "</div>";
                }
            }
        } else {
            die("Erreur dans la requête SQL : " . $conn->error);
        }
    }

    if (!$exactMatchResult) {
        die("Erreur dans la requête SQL : " . $conn->error);
    }
}

$conn->close();
?>
