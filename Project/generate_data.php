<?php
/*

    Omer Faruk Katkat

*/
$seedDir = __DIR__ . '/datas_txt/';
$outDir  = __DIR__ . '/datas_sql/';
date_default_timezone_set('Europe/Istanbul');

if (!is_dir($outDir) && !mkdir($outDir, 0755, true)) {
    die("Failed to create output directory: $outDir\n");
}

function readSeed(string $path): array {
    if (!file_exists($path)) return [];
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    return $lines ? array_map('trim', $lines) : [];
}
function randomDate(string $start, string $end): string {
    $ts = mt_rand(strtotime($start), strtotime($end));
    return date('Y-m-d', $ts);
}

function safeArrayRand(array $array, $defaultValue = 'Default') {
    if (empty($array)) {
        return $defaultValue;
    }
    return $array[array_rand($array)];
}

$countries     = readSeed($seedDir . 'countries.txt');         
$firstNames    = readSeed($seedDir . 'first_names.txt');
$lastNames     = readSeed($seedDir . 'last_names.txt');
$genres        = readSeed($seedDir . 'genres.txt');
$artistNames   = readSeed($seedDir . 'artist_names.txt');
$albumTitles   = readSeed($seedDir . 'album_titles.txt');
$songTitles    = readSeed($seedDir . 'song_titles.txt');
$playlistNames = readSeed($seedDir . 'playlist_names.txt');
$bios          = readSeed($seedDir . 'bios.txt');             
$minUsers       = 100;
$minArtists     = 100;
$minAlbums      = 200;
$minSongs       = 1000;
$minPlaylists   = 500;
$minPlaylistSongs = 500;

// COUNTRY
$lines = [];
foreach ($countries as $line) {
    if (is_string($line) && strpos($line, ',') !== false) {
        list($name, $code) = array_map('trim', explode(',', $line, 2));
    } else if (is_array($line)) {
        list($name, $code) = $line;
    } else {
        $name = $line ?? "Country";
        $code = substr($name, 0, 2);
    }
    $name = addslashes($name);
    $code = addslashes($code);
    $lines[] = "INSERT INTO COUNTRY (country_name, country_code) VALUES ('$name', '$code');";
}
file_put_contents("$outDir/country.sql", implode("\n", $lines)."\n");

// USERS
$lines = [];
$totalCountries = count($countries);
$totalCountries = max(1, $totalCountries); 
while (count($lines) < $minUsers) {
    $fn = addslashes(safeArrayRand($firstNames, "User"));
    $ln = addslashes(safeArrayRand($lastNames, "Doe"));
    $fullName = "$fn $ln";
    $username = strtolower($fn) . '.' . strtolower($ln) .' ' . mt_rand(1,99);
    $email    = $username . '@gmail.com';
    $password = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'),6,8); 
    $country_id = rand(1, $totalCountries);
    $age = rand(16, 70);
    $joinDate   = randomDate('2020-01-01', '2025-05-15');
    $lastLogin  = randomDate($joinDate, '2025-05-15');
    $follower_num = rand(0, 10000);
    $subscription_type = ['free','premium','family'][rand(0,2)];
    $top_genre  = empty($genres) ? "Pop" : addslashes($genres[array_rand($genres)]);
    $num_songs_liked = rand(0,500);
    $most_played_artist = empty($artistNames) ? "Unknown Artist" : addslashes($artistNames[array_rand($artistNames)]);
    $image = "user_" . (count($lines) + 1) . ".jpg";
    $lines[] = "INSERT INTO USERS (country_id, age, name, username, email, password, date_joined, last_login, follower_num, subscription_type, top_genre, num_songs_liked, most_played_artist, image) VALUES ($country_id, $age, '$fullName', '$username', '$email', '$password', '$joinDate', '$lastLogin', $follower_num, '$subscription_type', '$top_genre', $num_songs_liked, '$most_played_artist', '$image');";
}
file_put_contents("$outDir/users.sql", implode("\n", $lines)."\n");

// ARTISTS
$lines = [];
while (count($lines) < $minArtists) {
    $name = addslashes($artistNames[array_rand($artistNames)]) . ' ' . mt_rand(1,999);
    $genre = addslashes($genres[array_rand($genres)]);
    $date_joined = randomDate('2010-01-01', '2025-05-15');
    $total_num_music = rand(20,500);
    $total_albums    = rand(1,20);
    $listeners       = rand(1000,1000000);
    $bio = addslashes($bios[array_rand($bios)]);
    $country_id = rand(1, $totalCountries);
    $image = "artist_" . (count($lines) + 1) . ".jpg";
    $lines[] = "INSERT INTO ARTISTS (name, genre, date_joined, total_num_music, total_albums, listeners, bio, country_id, image) VALUES ('$name', '$genre', '$date_joined', $total_num_music, $total_albums, $listeners, '$bio', $country_id, '$image');";
}
file_put_contents("$outDir/artists.sql", implode("\n", $lines)."\n");

// ALBUMS
$lines = [];
while (count($lines) < $minAlbums) {
    $artist_id = rand(1, $minArtists);
    $name = addslashes($albumTitles[array_rand($albumTitles)]) . ' ' . mt_rand(1,999);
    $release_date = randomDate('2000-01-01', '2025-05-15');
    $genre = addslashes($genres[array_rand($genres)]);
    $music_number = rand(5,20);
    $image = "album_" . (count($lines) + 1) . ".jpg";
    $lines[] = "INSERT INTO ALBUMS (artist_id, name, release_date, genre, music_number, image) VALUES ($artist_id, '$name', '$release_date', '$genre', $music_number, '$image');";
}
file_put_contents("$outDir/albums.sql", implode("\n", $lines)."\n");

// SONGS
$lines = [];
while (count($lines) < $minSongs) {
    $album_id = rand(1, $minAlbums);
    $title = addslashes($songTitles[array_rand($songTitles)]) . ' ' . mt_rand(1,999);
    $duration = sprintf('%02d:%02d', rand(0,4), rand(0,59));
    $genre = addslashes($genres[array_rand($genres)]);
    $release_date = randomDate('2000-01-01', '2025-05-15');
    $rank = rand(1,1000);
    $image = "album_$album_id.jpg";
    $lines[] = "INSERT INTO SONGS (album_id, title, duration, genre, release_date, songs_rank, image) VALUES ($album_id, '$title', '$duration', '$genre', '$release_date', $rank, '$image');";
}
file_put_contents("$outDir/songs.sql", implode("\n", $lines)."\n");

// PLAYLISTS
$lines = [];
while (count($lines) < $minPlaylists) {
    $user_id = rand(1, $minUsers);
    $title = addslashes($playlistNames[array_rand($playlistNames)]);
    $desc_genre = strtolower($genres[array_rand($genres)]);
    $description = addslashes("A mix of " . $desc_genre);
    $date_created = randomDate('2020-01-01', '2025-05-15');
    $image = "playlist_" . (count($lines) + 1) . ".jpg";
    $lines[] = "INSERT INTO PLAYLISTS (user_id, title, description, date_created, image) VALUES ($user_id, '$title', '$description', '$date_created', '$image');";
}
file_put_contents("$outDir/playlists.sql", implode("\n", $lines)."\n");

// PLAYLIST_SONGS
$lines = [];
while (count($lines) < $minPlaylistSongs) {
    $playlist_id = rand(1, $minPlaylists);
    $song_id     = rand(1, $minSongs);
    $date_added  = randomDate('2020-01-01', '2025-05-15');
    $lines[] = "INSERT INTO PLAYLIST_SONGS (playlist_id, song_id, date_added) VALUES ($playlist_id, $song_id, '$date_added');";
}
file_put_contents("$outDir/playlist_songs.sql", implode("\n", $lines)."\n");

// PLAY_HISTORY
// $lines = [];
// while (count($lines) < $minHistory) {
//     $user_id   = rand(1, $minUsers);
//     $song_id   = rand(1, $minSongs);
//     $playtime  = date('Y-m-d H:i:s', mt_rand(strtotime('2020-01-01'), time()));
//     $lines[] = "INSERT INTO PLAY_HISTORY (user_id, song_id, playtime) VALUES ($user_id, $song_id, '$playtime');";
// }
// file_put_contents("$outDir/play_history.sql", implode("\n", $lines)."\n");

