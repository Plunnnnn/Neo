<?php
$servername = "moviedb.ch0eymmkwcev.eu-west-3.rds.amazonaws.com";
$username = "basmoussent";
$password = "zsedLOKI:123!";
$dbname = "MovieDB";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['title'])) {
    $movieTitle = $_GET['title'];

    
    $exactMatchSQL = "SELECT * FROM movies WHERE titre = '$movieTitle'";
    $exactMatchResult = $conn->query($exactMatchSQL);

    if ($exactMatchResult->num_rows > 0) {
        while ($row = $exactMatchResult->fetch_assoc()) {
            echo "<div class='movie'>";
            echo "<div class='image-container'>";
            echo "<img src='{$row['poster']}' alt='{$row['titre']}'>"; //retourne tout pour les placer das movie infos
            echo "<a href='player.html?movieID={$row['movieid']}'>";
            echo "</div>";
            echo "<div class='details'>";
            echo "<h3>{$row['titre']}</h3>";
            echo "<p>{$row['description']}</p>";
            echo "<p>Note: {$row['ratings']}</p>"; 
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
                    echo "</div>";                                             //retourne tout pour les placer das movie infos
                    echo "<div class='details'>";
                    echo "<h3>{$row['titre']}</h3>";
                    echo "<p>{$row['description']}</p>";
                    echo "<p>Note: {$row['ratings']}</p>"; 
                    echo "</div>";
                    echo "</div>";
                }
            }
        } else {
            die("DB down ? : " . $conn->error);
        }
    }

    if (!$exactMatchResult) {
        die("DB down ? : " . $conn->error);
    }
}

$conn->close();
?>
