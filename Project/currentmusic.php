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
$user_name = htmlspecialchars($_SESSION['name'], ENT_QUOTES, 'UTF-8');

if (isset($_GET['song_id'])) {
    $song_id = (int)$_GET['song_id'];
    
    $song_exists = $conn->query("SELECT COUNT(*) as count FROM SONGS WHERE song_id = $song_id")->fetch_assoc();
    
    if ($song_exists['count'] > 0) {
        $currentTime = date("Y-m-d H:i:s");
        $conn->query("
            INSERT INTO PLAY_HISTORY (user_id, song_id, playtime)
            VALUES ($user_id, $song_id, '$currentTime')
        ");
    } else {
        header("Location: homepage.php");
        exit();
    }
} else {
    $recent_play = $conn->query("
        SELECT song_id 
        FROM PLAY_HISTORY 
        WHERE user_id = $user_id 
        ORDER BY playtime DESC 
        LIMIT 1
    ");
    
    if ($recent_play->num_rows > 0) {
        $song_id = $recent_play->fetch_assoc()['song_id'];
    } else {
        header("Location: homepage.php");
        exit();
    }
}

$song_query = $conn->query("
    SELECT 
        s.song_id,
        s.title,
        COALESCE(s.image, 'https://picsum.photos/seed/" . $song_id . "/300/300') AS image,
        s.duration,
        s.genre,
        s.release_date,
        s.songs_rank,
        ar.name AS artist_name,
        al.name AS album_name,
        c.country_name
    FROM SONGS s
    JOIN ALBUMS al ON s.album_id = al.album_id
    JOIN ARTISTS ar ON al.artist_id = ar.artist_id
    JOIN COUNTRY c ON ar.country_id = c.country_id
    WHERE s.song_id = $song_id
");

if ($song_query->num_rows == 0) {

    header("Location: homepage.php");
    exit();
}

$song = $song_query->fetch_assoc();

$play_count = $conn->query("
    SELECT COUNT(*) as plays
    FROM PLAY_HISTORY
    WHERE song_id = $song_id
")->fetch_assoc()['plays'];

$song_stats = [
    'plays' => $play_count,
    'rank' => $song['songs_rank'] ?: 'N/A',
    'release_year' => $song['release_date'] ? date('Y', strtotime($song['release_date'])) : 'Unknown'
];

$from_playlist = isset($_GET['playlist_id']) ? (int)$_GET['playlist_id'] : 0;

$conn->close();

include 'currentmusic.html';
?>