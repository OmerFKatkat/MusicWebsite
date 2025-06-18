<?php
/*

    Omer Faruk Katkat

*/
$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "OmerFaruk_Katkat"; 


$conn = new mysqli($servername, $username, $password);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($conn->query("DROP DATABASE IF EXISTS `$dbname`") === TRUE) {
    echo "<p>Dropped database `$dbname` </p>";
} else {
    echo "<p>Error dropping database: " . $conn->error . "</p>";
}

if ($conn->query("CREATE DATABASE `$dbname`") === TRUE) {
    echo "<p>Created database `$dbname`.</p>";
} else {
    die("<p>Error creating database: " . $conn->error . "</p>");
}


$conn->select_db($dbname);

$sql = "CREATE TABLE IF NOT EXISTS COUNTRY (
    country_id INT PRIMARY KEY AUTO_INCREMENT,
    country_name VARCHAR(100) NOT NULL,
    country_code VARCHAR(2) NOT NULL
)";

if ($conn->query($sql) === TRUE) {
    echo "<p>Table COUNTRY created successfully</p>";
} else {
    echo "<p>Error creating table COUNTRY: " . $conn->error . "</p>";
}

$sql = "CREATE TABLE IF NOT EXISTS USERS (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    country_id INT,
    age INT,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    date_joined DATE,
    last_login DATETIME,
    follower_num INT DEFAULT 0,
    subscription_type VARCHAR(20),
    top_genre VARCHAR(50),
    num_songs_liked INT DEFAULT 0,
    most_played_artist varchar(150),
    image VARCHAR(255),
    FOREIGN KEY (country_id) REFERENCES COUNTRY(country_id)
)";

if ($conn->query($sql) === TRUE) {
    echo "<p>Table USERS created successfully</p>";
} else {
    echo "<p>Error creating table USERS: " . $conn->error . "</p>";
}

$sql = "CREATE TABLE IF NOT EXISTS ARTISTS (
    artist_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    genre VARCHAR(50),
    date_joined DATE,
    total_num_music INT DEFAULT 0,
    total_albums INT DEFAULT 0,
    listeners INT DEFAULT 0,
    bio TEXT,
    country_id INT,
    image VARCHAR(255),
    FOREIGN KEY (country_id) REFERENCES COUNTRY(country_id)
)";

if ($conn->query($sql) === TRUE) {
    echo "<p>Table ARTISTS created successfully</p>";
} else {
    echo "<p>Error creating table ARTISTS: " . $conn->error . "</p>";
}

$sql = "CREATE TABLE IF NOT EXISTS ALBUMS (
    album_id INT PRIMARY KEY AUTO_INCREMENT,
    artist_id INT,
    name VARCHAR(100) NOT NULL,
    release_date DATE,
    genre VARCHAR(50),
    music_number INT DEFAULT 0,
    image VARCHAR(255),
    FOREIGN KEY (artist_id) REFERENCES ARTISTS(artist_id)
)";

if ($conn->query($sql) === TRUE) {
    echo "<p>Table ALBUMS created successfully</p>";
} else {
    echo "<p>Error creating table ALBUMS: " . $conn->error . "</p>";
}

$sql = "CREATE TABLE IF NOT EXISTS SONGS (
    song_id INT PRIMARY KEY AUTO_INCREMENT,
    album_id INT,
    title VARCHAR(100) NOT NULL,
    duration TIME,
    genre VARCHAR(50),
    release_date DATE,
    songs_rank INT,
    image VARCHAR(255),
    FOREIGN KEY (album_id) REFERENCES ALBUMS(album_id)
)";

if ($conn->query($sql) === TRUE) {
    echo "<p>Table SONGS created successfully</p>";
} else {
    echo "<p>Error creating table SONGS: " . $conn->error . "</p>";
}

$sql = "CREATE TABLE IF NOT EXISTS PLAYLISTS (
    playlist_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    date_created DATE,
    image VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES USERS(user_id)
)";

if ($conn->query($sql) === TRUE) {
    echo "<p>Table PLAYLISTS created successfully</p>";
} else {
    echo "<p>Error creating table PLAYLISTS: " . $conn->error . "</p>";
}

$sql = "CREATE TABLE IF NOT EXISTS PLAYLIST_SONGS (
    playlistsong_id INT PRIMARY KEY AUTO_INCREMENT,
    playlist_id INT,
    song_id INT,
    date_added DATE,
    FOREIGN KEY (playlist_id) REFERENCES PLAYLISTS(playlist_id),
    FOREIGN KEY (song_id) REFERENCES SONGS(song_id)
)";

if ($conn->query($sql) === TRUE) {
    echo "<p>Table PLAYLIST_SONGS created successfully</p>";
} else {
    echo "<p>Error creating table PLAYLIST_SONGS: " . $conn->error . "</p>";
}

$sql = "CREATE TABLE IF NOT EXISTS PLAY_HISTORY (
    play_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    song_id INT,
    playtime DATETIME,
    FOREIGN KEY (user_id) REFERENCES USERS(user_id),
    FOREIGN KEY (song_id) REFERENCES SONGS(song_id)
)";

if ($conn->query($sql) === TRUE) {
    echo "<p>Table PLAY_HISTORY created successfully</p>";
} else {
    echo "<p>Error creating table PLAY_HISTORY: " . $conn->error . "</p>";
}

include 'generate_data.php';

$sqlDir = __DIR__ . '/datas_sql/';
$sqlFiles = [
    'country.sql',
    'users.sql',
    'artists.sql',
    'albums.sql',
    'songs.sql',
    'playlists.sql',
    'playlist_songs.sql',
    'play_history.sql'
];

foreach ($sqlFiles as $file) {
    $sqlContent = file_get_contents($sqlDir . $file);
    $queries = explode(';', $sqlContent);
    
    foreach ($queries as $query) {
        $query = trim($query);
        if (!empty($query)) {
            if ($conn->query($query) === false) {
                echo "<p>Error executing query from $file: " . $conn->error . "</p>";
                die("Failed to execute query: " . htmlspecialchars($query));
            }
        }
    }
}

$updateUser55 = "
UPDATE users 
SET 
    country_id = 1,
    age = 25,
    name = 'Default User',
    username = 'a',
    email = 'a@gmail.com',
    password = 'a',
    date_joined = CURDATE(),
    last_login = NOW(),
    follower_num = 0,
    subscription_type = 'free',
    top_genre = 'pop',
    num_songs_liked = 0,
    most_played_artist = 'Unknown Artist',
    image = 'default_user.jpg'
WHERE user_id = 55
";

if ($conn->query($updateUser55) === TRUE) {
    echo "<p>User ID 55 updated successfully as default user.</p>";
} else {
    echo "<p>Error updating user 55: " . $conn->error . "</p>";
}

$conn->close();

echo "<meta http-equiv='refresh' content='2;url=login.html' />";
?>