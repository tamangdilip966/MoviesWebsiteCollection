<?php
$host     = 'localhost';
$username = '2439673';
$password = 'p15b4e';
$database = 'db2439673';

$mysqli = new mysqli($host, $username, $password, $database);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$mysqli->set_charset("utf8mb4");

// Create users table
$check_table = $mysqli->query("SHOW TABLES LIKE 'users'");
if ($check_table->num_rows == 0) {
    $create_users = "
    CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    if (!$mysqli->query($create_users)) {
        die("Failed to create users table: " . $mysqli->error);
    }
    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    $mysqli->query("INSERT INTO users (username, email, password_hash) VALUES ('admin', 'admin@movies.com', '$hash')");
}

// Create movies table
$check_movies = $mysqli->query("SHOW TABLES LIKE 'movies'");
if ($check_movies->num_rows == 0) {
    $create_movies = "
    CREATE TABLE movies (
        movie_id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        genre VARCHAR(100),
        director VARCHAR(255),
        cast_members TEXT,
        release_year INT,
        duration_minutes INT,
        language VARCHAR(100),
        country VARCHAR(100),
        rating DECIMAL(3,1),
        watch_status ENUM('Not Watched','Watching','Watched','Dropped') DEFAULT 'Not Watched',
        description TEXT,
        price DECIMAL(10,2),
        purchase_date DATE,
        is_favourite BOOLEAN DEFAULT 0,
        has_sequel BOOLEAN DEFAULT 0,
        is_4k BOOLEAN DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    if (!$mysqli->query($create_movies)) {
        die("Failed to create movies table: " . $mysqli->error);
    }

    $sample = "
    INSERT INTO movies (title, genre, director, cast_members, release_year, duration_minutes, language, country, rating, watch_status, description, price, is_favourite, has_sequel, is_4k) VALUES
    ('The Shawshank Redemption', 'Drama', 'Frank Darabont', 'Tim Robbins, Morgan Freeman', 1994, 142, 'English', 'USA', 9.3, 'Watched', 'Two imprisoned men bond over years, finding solace and eventual redemption through acts of common decency.', 9.99, 1, 0, 1),
    ('The Godfather', 'Crime', 'Francis Ford Coppola', 'Marlon Brando, Al Pacino', 1972, 175, 'English', 'USA', 9.2, 'Watched', 'The aging patriarch of an organized crime dynasty transfers control to his reluctant son.', 12.99, 1, 1, 1),
    ('The Dark Knight', 'Action', 'Christopher Nolan', 'Christian Bale, Heath Ledger', 2008, 152, 'English', 'USA', 9.0, 'Watched', 'Batman faces the Joker, a criminal mastermind who wants to plunge Gotham into anarchy.', 14.99, 1, 1, 1),
    ('Inception', 'Sci-Fi', 'Christopher Nolan', 'Leonardo DiCaprio, Joseph Gordon-Levitt', 2010, 148, 'English', 'USA', 8.8, 'Watched', 'A thief who enters the dreams of others to steal their secrets is given a reverse task.', 11.99, 1, 0, 1),
    ('Interstellar', 'Sci-Fi', 'Christopher Nolan', 'Matthew McConaughey, Anne Hathaway', 2014, 169, 'English', 'USA', 8.6, 'Watched', 'A team of explorers travel through a wormhole in space in an attempt to save humanity.', 13.99, 1, 0, 1),
    ('Parasite', 'Thriller', 'Bong Joon-ho', 'Song Kang-ho, Lee Sun-kyun', 2019, 132, 'Korean', 'South Korea', 8.5, 'Watched', 'Greed and class discrimination threaten the symbiotic relationship between a wealthy family and a destitute one.', 10.99, 1, 0, 1),
    ('Avengers: Endgame', 'Action', 'Anthony Russo', 'Robert Downey Jr., Chris Evans', 2019, 181, 'English', 'USA', 8.4, 'Watched', 'The Avengers assemble once more to reverse Thanos\\'s actions and restore balance to the universe.', 15.99, 0, 1, 1),
    ('The Lion King', 'Animation', 'Roger Allers', 'Matthew Broderick, James Earl Jones', 1994, 88, 'English', 'USA', 8.5, 'Watched', 'Lion prince flees his kingdom only to learn the true meaning of responsibility and bravery.', 8.99, 0, 1, 0),
    ('Forrest Gump', 'Drama', 'Robert Zemeckis', 'Tom Hanks, Robin Wright', 1994, 142, 'English', 'USA', 8.8, 'Watched', 'The presidencies of Kennedy and Johnson through the eyes of an Alabama man with a low IQ.', 9.99, 0, 0, 1),
    ('Pulp Fiction', 'Crime', 'Quentin Tarantino', 'John Travolta, Samuel L. Jackson', 1994, 154, 'English', 'USA', 8.9, 'Watched', 'The lives of two mob hitmen, a boxer, a gangster and his wife intertwine in four tales of violence.', 10.99, 1, 0, 0),
    ('Spirited Away', 'Animation', 'Hayao Miyazaki', 'Daveigh Chase, Suzanne Pleshette', 2001, 125, 'Japanese', 'Japan', 8.6, 'Watched', 'During her family\\'s move, a sullen girl wanders into a world ruled by spirits.', 11.99, 1, 0, 0),
    ('Joker', 'Drama', 'Todd Phillips', 'Joaquin Phoenix, Robert De Niro', 2019, 122, 'English', 'USA', 8.4, 'Watched', 'A failed stand-up comedian is driven insane and becomes a psychotic murderer.', 12.99, 0, 1, 1),
    ('1917', 'War', 'Sam Mendes', 'George MacKay, Dean-Charles Chapman', 2019, 119, 'English', 'UK', 8.3, 'Watched', 'Two British soldiers carry a message across enemy territory during World War I.', 9.99, 0, 0, 1),
    ('Dune', 'Sci-Fi', 'Denis Villeneuve', 'Timothée Chalamet, Rebecca Ferguson', 2021, 155, 'English', 'USA', 8.0, 'Watching', 'A noble family becomes embroiled in a war for control over the galaxy\\'s most valuable asset.', 14.99, 0, 1, 1),
    ('Everything Everywhere All at Once', 'Sci-Fi', 'Daniel Kwan', 'Michelle Yeoh, Ke Huy Quan', 2022, 139, 'English', 'USA', 8.0, 'Not Watched', 'A woman is caught up in an adventure in which she alone can save the multiverse.', 13.99, 0, 0, 1)
    ";

    $mysqli->multi_query($sample);
    while ($mysqli->more_results()) {
        $mysqli->next_result();
    }
}

// Create indexes if they don't exist
foreach (['idx_genre' => 'genre', 'idx_director' => 'director', 'idx_rating' => 'rating'] as $idx_name => $col) {
    $r = $mysqli->query("SHOW INDEX FROM movies WHERE Key_name = '$idx_name'");
    if ($r->num_rows == 0) {
        $mysqli->query("CREATE INDEX $idx_name ON movies($col)");
    }
}
?>
