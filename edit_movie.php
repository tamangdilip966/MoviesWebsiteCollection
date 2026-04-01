<?php
require_once 'config.php';
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$movie_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$movie_id) { header("Location: index.php"); exit; }

$stmt = $mysqli->prepare("SELECT * FROM movies WHERE movie_id = ?");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$movie = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$movie) { header("Location: index.php"); exit; }

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title            = trim($_POST['title'] ?? '');
    $genre            = trim($_POST['genre'] ?? '');
    $director         = trim($_POST['director'] ?? '');
    $cast_members     = trim($_POST['cast_members'] ?? '');
    $release_year     = $_POST['release_year'] ?? null;
    $duration_minutes = $_POST['duration_minutes'] ?? null;
    $language         = trim($_POST['language'] ?? '');
    $country          = trim($_POST['country'] ?? '');
    $rating           = $_POST['rating'] ?? null;
    $watch_status     = $_POST['watch_status'] ?? 'Not Watched';
    $description      = trim($_POST['description'] ?? '');
    $price            = $_POST['price'] ?? null;
    $purchase_date    = $_POST['purchase_date'] ?? null;
    $is_favourite     = isset($_POST['is_favourite']) ? 1 : 0;
    $has_sequel       = isset($_POST['has_sequel']) ? 1 : 0;
    $is_4k            = isset($_POST['is_4k']) ? 1 : 0;

    if (empty($title)) $errors[] = 'Movie title is required.';

    if (empty($errors)) {
        $rating           = ($rating === '' || $rating === null) ? null : (float)$rating;
        $price            = ($price === '' || $price === null) ? null : (float)$price;
        $release_year     = ($release_year === '' || $release_year === null) ? null : (int)$release_year;
        $duration_minutes = ($duration_minutes === '' || $duration_minutes === null) ? null : (int)$duration_minutes;
        if ($purchase_date === '') $purchase_date = null;

        $stmt = $mysqli->prepare("
            UPDATE movies SET
            title=?, genre=?, director=?, cast_members=?, release_year=?, duration_minutes=?,
            language=?, country=?, rating=?, watch_status=?, description=?, price=?,
            purchase_date=?, is_favourite=?, has_sequel=?, is_4k=?
            WHERE movie_id=?
        ");
        $stmt->bind_param(
            "ssssiissdssdsiiii",
            $title, $genre, $director, $cast_members, $release_year, $duration_minutes,
            $language, $country, $rating, $watch_status, $description, $price,
            $purchase_date, $is_favourite, $has_sequel, $is_4k, $movie_id
        );
        if ($stmt->execute()) {
            header("Location: index.php");
            exit;
        } else {
            $errors[] = 'Failed to update movie: ' . $stmt->error;
        }
        $stmt->close();
    }
}

$genres_result   = $mysqli->query("SELECT DISTINCT genre FROM movies WHERE genre IS NOT NULL ORDER BY genre");
$existing_genres = $genres_result->fetch_all(MYSQLI_ASSOC);

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig   = new \Twig\Environment($loader, ['cache' => false]);
echo $twig->render('edit_movie.twig', [
    'username'        => $_SESSION['username'],
    'movie'           => $movie,
    'errors'          => $errors,
    'existing_genres' => $existing_genres,
    'site_name'       => SITE_NAME,
    'current_year'    => date('Y'),
    'student_id'      => STUDENT_ID,
    'author'          => SITE_AUTHOR,
]);
?>
