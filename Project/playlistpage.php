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

$user_id = $_SESSION['user_id'];
$playlist_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$search_term = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$user_name = htmlspecialchars($_SESSION['name'], ENT_QUOTES, 'UTF-8');

$playlist = $conn->query("
    SELECT playlist_id, title, user_id, image
    FROM PLAYLISTS 
    WHERE playlist_id = $playlist_id
")->fetch_assoc();

if (!$playlist) {
    die("Playlist not found");
}

$is_owner = ($playlist['user_id'] == $user_id);

if ($is_owner && isset($_POST['add_song'])) {
    $song_id = (int)$_POST['song_id'];
    $stmt = $conn->prepare("
        INSERT IGNORE INTO PLAYLIST_SONGS (playlist_id, song_id, date_added)
        VALUES (?, ?, NOW())
    ");
    $stmt->bind_param("ii", $playlist_id, $song_id);
    $stmt->execute();
    header("Location: playlistpage.php?id=$playlist_id");
    exit();
}

$playlist_songs = $conn->query("
    SELECT 
        s.song_id,
        s.title AS title,
        s.duration,
        ar.name AS artist_name,
        al.name AS album_name,
        c.country_name,
        s.genre

    FROM PLAYLIST_SONGS ps
    JOIN SONGS s ON ps.song_id = s.song_id
    JOIN ALBUMS al ON s.album_id = al.album_id
    JOIN ARTISTS ar ON al.artist_id = ar.artist_id
    JOIN COUNTRY c ON ar.country_id = c.country_id
    WHERE ps.playlist_id = $playlist_id
    ORDER BY ps.date_added DESC
");

$search_results = null;
if ($search_term) {
    $search_results = $conn->query("
        SELECT 
            s.song_id,
            s.title AS title,
            s.duration,
            ar.name AS artist_name,
            al.name AS album_name,
            c.country_name,
            s.genre AS genre
        FROM SONGS s
        JOIN ALBUMS al ON s.album_id = al.album_id
        JOIN ARTISTS ar ON al.artist_id = ar.artist_id
        JOIN COUNTRY c ON ar.country_id = c.country_id
        WHERE s.title LIKE '%$search_term%' OR ar.name LIKE '%$search_term%'
        LIMIT 10
    ");
}

include 'playlistpage.html';

$conn->close();
?>