<?php
/*

    Omer Faruk Katkat

*/
require_once 'helpers.php';
session_start();


$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "OmerFaruk_Katkat"; 

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$artist_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_name = htmlspecialchars($_SESSION['name'], ENT_QUOTES, 'UTF-8'); 

$artist = $conn->query("
    SELECT 
        ar.artist_id,
        ar.name,
        COALESCE(ar.image, '" . getImageUrl($artist_id, 'artist') . "') AS image,
        ar.listeners,
        ar.bio,
        ar.genre,
        ar.total_num_music,
        ar.total_albums,
        c.country_name
    FROM ARTISTS ar
    JOIN COUNTRY c ON ar.country_id = c.country_id
    WHERE ar.artist_id = $artist_id
")->fetch_assoc();

if (!$artist) {
    header("Location: homepage.php");
    exit();
}

$albums = $conn->query("
    SELECT 
        album_id,
        name,
        image,
        release_date,
        music_number
    FROM ALBUMS
    WHERE artist_id = $artist_id
    ORDER BY release_date DESC
    LIMIT 5
");


$top_songs = $conn->query("
    SELECT 
        s.song_id,
        s.title,
        s.duration,
        s.image,
        a.name as album_name,
        a.release_date,
        COUNT(ph.song_id) as play_count
    FROM SONGS s
    JOIN ALBUMS a ON s.album_id = a.album_id
    LEFT JOIN PLAY_HISTORY ph ON s.song_id = ph.song_id
    WHERE a.artist_id = $artist_id
    GROUP BY s.song_id, s.title, s.duration, s.image, a.name, a.release_date
    ORDER BY play_count DESC
    LIMIT 5
");

include 'artistpage.html';
$conn->close();
?>