<?php


// Vos informations de base de données
$servername = "mediadb.czpusmrdxalt.eu-north-1.rds.amazonaws.com";
$username = "basmoussent";
$password = "zsedLOKI:123!";
$dbname = "MovieDB";

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Traitement du formulaire de connexion
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Requête SQL pour vérifier les informations d'identification
    $sql = "SELECT * FROM Users WHERE username = '$username' AND mdp = '$password'";
    $result = $conn->query($sql);

    if ($result !== false) {
        // La requête a réussi
        if ($result->num_rows == 1) {
            // L'utilisateur est authentifié avec succès
            $_SESSION['username'] = $username;
            header("Location: main.html"); // Rediriger vers la page d'accueil ou une autre page sécurisée
            exit();
        } else {
            // L'authentification a échoué
            http_response_code(401); // Définir le code de réponse HTTP 401 (Non autorisé)
            echo json_encode(["error" => "Identifiant ou mot de passe incorrect."]);
            exit();
        }
    } else {
        // La requête a échoué
        http_response_code(500); // Définir le code de réponse HTTP 500 (Erreur interne du serveur)
        echo json_encode(["error" => "Erreur dans la requête SQL : " . $conn->error]);
        exit();
    }

}