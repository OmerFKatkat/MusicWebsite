<?php
/*

    Omer Faruk Katkat

*/
session_start();

$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "OmerFaruk_Katkat"; 

try {
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = trim($_POST['name']);
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $country_code = $_POST['country'];
        $password_input = $_POST['password'];
        
        $stmt = $conn->prepare("SELECT username FROM USERS WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            throw new Exception("Username already exists");
        }

        $stmt = $conn->prepare("SELECT email FROM USERS WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            throw new Exception("Email already exists");
        }

        $stmt = $conn->prepare("SELECT country_id FROM COUNTRY WHERE country_code = ?");
        $stmt->bind_param("s", $country_code);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 0) {
            throw new Exception("Invalid country code");
        }
        $country_id = $result->fetch_assoc()['country_id'];

        $hashed_password = $password_input;
        
        $currentTime = date("Y-m-d H:i:s");
        $dateJoined = date("Y-m-d");
        
        $stmt = $conn->prepare("INSERT INTO USERS (name, username, email, country_id, password, date_joined, last_login) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssisss", $name, $username, $email, $country_id, $hashed_password, $dateJoined, $currentTime);
        
        if ($stmt->execute()) {
            header("Location: login.html?success=1");
            exit();
        } else {
            throw new Exception("Error inserting user: " . $stmt->error);
        }
    }

} catch (Exception $e) {
    error_log("Registration error: " . $e->getMessage());
    header("Location: register.html?error=" . urlencode($e->getMessage()));
    exit();
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?>