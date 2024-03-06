<?php



$servername = "moviedb.ch0eymmkwcev.eu-west-3.rds.amazonaws.com";
$username = "basmoussent";
$password = "zsedLOKI:123!";
$dbname = "MovieDB";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    
    $sql = "SELECT * FROM Users WHERE username = '$username' AND mdp = '$password'";
    $result = $conn->query($sql);

    if ($result !== false) {
        
        if ($result->num_rows == 1) {
            
            $_SESSION['username'] = $username;
            header("Location: main.html"); 
            exit();
        } else {
            
            http_response_code(401); 
            echo json_encode(["error" => "Identifiant ou mot de passe incorrect."]);
            exit();
        }
    } else {
        
        http_response_code(500); 
        echo json_encode(["error" => "DB down ? : " . $conn->error]);
        exit();
    }

}