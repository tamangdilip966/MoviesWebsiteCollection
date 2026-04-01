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

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig   = new \Twig\Environment($loader, ['cache' => false]);
echo $twig->render('movie_details.twig', [
    'username'     => $_SESSION['username'],
    'movie'        => $movie,
    'site_name'    => SITE_NAME,
    'current_year' => date('Y'),
    'student_id'   => STUDENT_ID,
    'author'       => SITE_AUTHOR,
]);
?>
