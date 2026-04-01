<?php
require_once 'config.php';
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$search            = trim($_GET['search'] ?? '');
$genre             = trim($_GET['genre'] ?? '');
$platform_filter   = trim($_GET['director_filter'] ?? '');
$completion_filter = trim($_GET['watch_filter'] ?? '');
$year_filter       = trim($_GET['year_filter'] ?? '');

$sql    = "SELECT * FROM movies WHERE 1=1";
$params = [];
$types  = "";

if (!empty($search)) {
    $sql     .= " AND (title LIKE ? OR description LIKE ? OR director LIKE ? OR cast_members LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types   .= "ssss";
}
if (!empty($genre)) {
    $sql     .= " AND genre = ?";
    $params[] = $genre;
    $types   .= "s";
}
if (!empty($platform_filter)) {
    $sql     .= " AND director LIKE ?";
    $params[] = "%$platform_filter%";
    $types   .= "s";
}
if (!empty($completion_filter)) {
    $sql     .= " AND watch_status = ?";
    $params[] = $completion_filter;
    $types   .= "s";
}
if (!empty($year_filter) && is_numeric($year_filter)) {
    $sql     .= " AND release_year = ?";
    $params[] = (int)$year_filter;
    $types   .= "i";
}

$sql .= " ORDER BY movie_id DESC";

if (!empty($params)) {
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $movies = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    $result = $mysqli->query($sql);
    $movies = $result->fetch_all(MYSQLI_ASSOC);
}

$genres_result = $mysqli->query("SELECT DISTINCT genre FROM movies WHERE genre IS NOT NULL ORDER BY genre");
$genres        = $genres_result->fetch_all(MYSQLI_ASSOC);

$stats_result = $mysqli->query("SELECT watch_status, COUNT(*) as count FROM movies GROUP BY watch_status");
$stats        = $stats_result->fetch_all(MYSQLI_ASSOC);

$total_movies   = (int)($mysqli->query("SELECT COUNT(*) as c FROM movies")->fetch_assoc()['c'] ?? 0);
$avg_rating     = $mysqli->query("SELECT AVG(rating) as avg FROM movies WHERE rating > 0")->fetch_assoc()['avg'] ?? 0;
$total_duration = $mysqli->query("SELECT SUM(duration_minutes) as total FROM movies")->fetch_assoc()['total'] ?? 0;

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig   = new \Twig\Environment($loader, ['cache' => false]);
echo $twig->render('index.twig', [
    'username'          => $_SESSION['username'],
    'movies'            => $movies,
    'genres'            => $genres,
    'stats'             => $stats,
    'search'            => $search,
    'genre_filter'      => $genre,
    'director_filter'   => $platform_filter,
    'watch_filter'      => $completion_filter,
    'year_filter'       => $year_filter,
    'total_movies'      => $total_movies,
    'avg_rating'        => round($avg_rating, 1),
    'total_duration'    => $total_duration,
    'site_name'         => SITE_NAME,
    'current_year'      => date('Y'),
    'student_id'        => STUDENT_ID,
    'author'            => SITE_AUTHOR,
]);
?>
