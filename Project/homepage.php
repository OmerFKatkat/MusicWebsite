<?php
/*

    Omer Faruk Katkat

*/
session_start();

require_once 'helpers.php';

$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "OmerFaruk_Katkat"; 

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id   = (int)$_SESSION['user_id'];
$user_name = htmlspecialchars($_SESSION['name'], ENT_QUOTES, 'UTF-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['new_playlist'])) {
    $title = $conn->real_escape_string(trim($_POST['new_playlist']));
    if ($title !== '') {
        $check_query = "SELECT playlist_id FROM PLAYLISTS WHERE user_id = $user_id AND title = '$title'";
        $existing = $conn->query($check_query);
        
        if ($existing && $existing->num_rows > 0) {
            $error_message = "Playlist \"$title\" already exists!";
        } else {
            if ($conn->query("
                INSERT INTO PLAYLISTS (user_id, title)
                VALUES ($user_id, '$title')
            ")) {
                $success_message = "Playlist \"$title\" created successfully!";

                header("Location: homepage.php?success=1");
                exit();
            }
        }
    } else {
        $error_message = "Playlist name cannot be empty.";
    }
}

if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success_message = "Playlist created successfully!";
}

$playlists = $conn->query("
    SELECT playlist_id, title, image
    FROM PLAYLISTS
    WHERE user_id = $user_id
    ORDER BY title
");

$history = $conn->query("
    SELECT s.song_id, s.title, s.genre, ar.name AS artist_name, a.name AS album_name, ph.playtime
    FROM PLAY_HISTORY ph
    JOIN SONGS s   ON ph.song_id = s.song_id
    JOIN ALBUMS a  ON s.album_id = a.album_id
    JOIN ARTISTS ar ON a.artist_id = ar.artist_id
    WHERE ph.user_id = $user_id
    ORDER BY ph.playtime DESC
    LIMIT 5
");

$country_id = 0;
if ($r = $conn->query("
    SELECT country_id
    FROM USERS
    WHERE user_id = $user_id
")->fetch_assoc()) {
    $country_id = (int)$r['country_id'];
}
$artists = $conn->query("
    SELECT artist_id, name, listeners, image
    FROM ARTISTS
    WHERE country_id = $country_id
    ORDER BY listeners DESC
    LIMIT 5
");

$conn->close();
include 'homepage.html';
?>