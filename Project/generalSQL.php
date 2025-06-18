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

$top_genres = [];
$query = "
    SELECT DISTINCT s.genre, SUM(a.listeners) as listeners
    FROM SONGS s
    JOIN ALBUMS al ON s.album_id = al.album_id
    JOIN ARTISTS a ON al.artist_id = a.artist_id
    WHERE s.genre IS NOT NULL AND s.genre != ''
    GROUP BY s.genre
    ORDER BY listeners DESC
    LIMIT 5
";
$result = $conn->query($query);
if ($result) {
    while($row = $result->fetch_assoc()) {
        $top_genres[] = $row;
    }
}

$top_countries = [];
$query = "
    SELECT c.country_name, COUNT(a.artist_id) as artist_count
    FROM COUNTRY c
    JOIN ARTISTS a ON c.country_id = a.country_id
    GROUP BY c.country_id, c.country_name
    ORDER BY artist_count DESC
    LIMIT 5
";
$result = $conn->query($query);
if ($result) {
    while($row = $result->fetch_assoc()) {
        $top_countries[] = $row;
    }
}

$most_played = [];
$query = "
    SELECT s.title, ar.name as artist_name, COUNT(ph.play_id) as play_count
    FROM PLAY_HISTORY ph
    JOIN SONGS s ON ph.song_id = s.song_id
    JOIN ALBUMS a ON s.album_id = a.album_id
    JOIN ARTISTS ar ON a.artist_id = ar.artist_id
    GROUP BY s.song_id, s.title, ar.name
    ORDER BY play_count DESC
    LIMIT 5
";
$result = $conn->query($query);
if ($result) {
    while($row = $result->fetch_assoc()) {
        $most_played[] = $row;
    }
}


$popular_artists = [];
$query = "
    SELECT name, listeners
    FROM ARTISTS
    ORDER BY listeners DESC
    LIMIT 5
";
$result = $conn->query($query);
if ($result) {
    while($row = $result->fetch_assoc()) {
        $popular_artists[] = $row;
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['custom_query'])) {
    $custom_query = trim($_POST['custom_query']);
    
    $result = $conn->query($custom_query);
    if ($result) {
        if ($result === true) {
            $success_message = "Query executed successfully.";
        } else {
            $custom_result = [];
            while($row = $result->fetch_assoc()) {
                $custom_result[] = $row;
            }
        }
    } else {
        $error_message = "Error executing query: " . $conn->error;
    }
}

$conn->close();
include 'generalSQL.html';
?>