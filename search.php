<?php
require_once 'config.php';
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$genres_result = $mysqli->query("SELECT DISTINCT genre FROM movies WHERE genre IS NOT NULL ORDER BY genre");
$genres        = $genres_result->fetch_all(MYSQLI_ASSOC);

$years_result = $mysqli->query("SELECT DISTINCT release_year FROM movies WHERE release_year IS NOT NULL ORDER BY release_year DESC");
$years        = $years_result->fetch_all(MYSQLI_ASSOC);

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig   = new \Twig\Environment($loader, ['cache' => false]);
echo $twig->render('search.twig', [
    'username'     => $_SESSION['username'],
    'genres'       => $genres,
    'years'        => $years,
    'site_name'    => SITE_NAME,
    'current_year' => date('Y'),
    'student_id'   => STUDENT_ID,
    'author'       => SITE_AUTHOR,
]);
?>
