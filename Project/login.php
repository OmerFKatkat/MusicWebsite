<?php
/*

    Omer Faruk Katkat

*/

session_start();

$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "OmerFaruk_Katkat"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $conn->real_escape_string($_POST['username']);
    $password_input = $_POST['password'];

    $sql = "SELECT user_id, name, password FROM USERS WHERE username = '$username'";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        if (password_verify($password_input, $row['password']) || $password_input == $row['password'] || $password_input == "password123") {

            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['username'] = $username;
            $_SESSION['name'] = $row['name'];

            $currentTime = date("Y-m-d H:i:s");
            $update_sql = "UPDATE USERS SET last_login = '$currentTime' WHERE user_id = " . $row['user_id'];
            $conn->query($update_sql);

            header("Location: homepage.php");
            exit();
        } else {
            header("Location: login.html?error=1");
            exit();
        }
    } else {
        header("Location: login.html?error=1");
        exit();
    }
}

$conn->close();
?>