<?php
require_once 'config.php';
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$movie_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($movie_id) {
    $stmt = $mysqli->prepare("DELETE FROM movies WHERE movie_id = ?");
    $stmt->bind_param("i", $movie_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: index.php");
exit;
?>
