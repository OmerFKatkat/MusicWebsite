Project Overview

This is a simple PHP-&-MySQL–powered web music player. It lets users register, log in, browse artists/albums/songs, build playlists, and play tracks—all in a dynamic, database-driven interface.
Architecture & Components

    Frontend templates (*.html + paired *.php files)

        homepage.html/php – Main landing page, shows featured artists/albums.

        artistpage.html/php – Displays details and albums for a selected artist.

        currentmusic.html/php – “Now Playing” view with controls/status.

        search_results.html/php – Shows results when you search by song/artist/album.

        playlistpage.html/php – Interface for viewing and editing your playlists.

        login.html/php & register.html – Authentication screens.

    Backend logic (.php scripts)

        Each page’s PHP counterpart handles DB queries, session management, and rendering dynamic content.

        install.php (if present) sets up DB connection parameters and can run initial setup.

    Database schema scripts (datas_sql/)

        users.sql – user accounts table

        artists.sql – artist metadata

        albums.sql – album listings with artist foreign keys

        songs.sql – track listings with album/artist links

        playlists.sql + playlist_songs.sql – user playlists and their song relations

        play_history.sql – logs of recently played tracks

        country.sql – optional lookup for artist origins

    Seed data (datas_txt/)

        Text files with lists of artist names, album titles, bios, countries—used to bulk-import sample data.

    Assets (assets/)

        CSS, images and (optional) JavaScript needed for styling and UI enhancements.

Data Flow & Usage

    User Authentication

        New visitors register; returning users log in. PHP sessions track “who’s online.”

    Browsing & Searching

        Queries against artists, albums and songs tables power the browse/search pages.

    Playing Music

        Clicking a track updates currentmusic.php, logs it in play_history, and displays controls.

    Playlist Management

        Users can create playlists; songs are linked in playlist_songs. Pages let you add/remove tracks.
