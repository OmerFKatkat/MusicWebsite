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

$search_term = isset($_GET['search_term']) ? trim($_GET['search_term']) : '';

$search_term_sql = $conn->real_escape_string($search_term);
$search_pattern = "%{$search_term_sql}%";

$songs = null;
$artists = null;
$playlists = null;


$songs = $conn->query("
    SELECT s.song_id, s.title, s.image, s.duration, s.genre, ar.name AS artist_name, a.name AS album_name
    FROM SONGS s
    JOIN ALBUMS a ON s.album_id = a.album_id
    JOIN ARTISTS ar ON a.artist_id = ar.artist_id
    WHERE s.title LIKE '$search_pattern' 
    OR ar.name LIKE '$search_pattern'
    OR a.name LIKE '$search_pattern'
    OR s.genre LIKE '$search_pattern'
    ORDER BY 
        CASE 
            WHEN s.title LIKE '$search_pattern' THEN 1
            WHEN ar.name LIKE '$search_pattern' THEN 2
            WHEN a.name LIKE '$search_pattern' THEN 3
            WHEN s.genre LIKE '$search_pattern' THEN 4
            ELSE 5
        END,
        s.songs_rank IS NULL, s.songs_rank, s.title
    LIMIT 20
");

$artists = $conn->query("
    SELECT a.artist_id, a.name, a.listeners, a.image, a.genre, c.country_name
    FROM ARTISTS a
    LEFT JOIN COUNTRY c ON a.country_id = c.country_id
    WHERE a.name LIKE '$search_pattern'
    OR a.genre LIKE '$search_pattern'
    ORDER BY 
        CASE 
            WHEN a.name = '$search_term_sql' THEN 1
            WHEN a.name LIKE '$search_term_sql%' THEN 2
            WHEN a.genre LIKE '$search_pattern' THEN 3
            ELSE 4
        END,
        a.listeners DESC
    LIMIT 10
");

$playlists = $conn->query("
    SELECT p.playlist_id, p.title, p.image, p.description, u.name AS creator_name
    FROM PLAYLISTS p
    JOIN USERS u ON p.user_id = u.user_id
    WHERE p.title LIKE '$search_pattern'
    AND (p.user_id = $user_id) 
    ORDER BY 
        CASE
            WHEN p.title = '$search_term_sql' THEN 1
            WHEN p.title LIKE '$search_term_sql%' THEN 2
            ELSE 3
        END,
        p.title
    LIMIT 10
");

$conn->close();
include 'search_results.html';
?>