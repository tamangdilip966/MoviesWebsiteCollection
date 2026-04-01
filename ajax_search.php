<?php
require_once 'config.php';
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$json_input = file_get_contents('php://input');
if (empty($json_input)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No input data']);
    exit;
}

$input = json_decode($json_input, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

$title          = trim($input['title'] ?? '');
$genre          = $input['genre'] ?? '';
$director       = $input['director'] ?? '';
$release_year   = $input['release_year'] ?? '';
$min_rating     = $input['min_rating'] ?? '';
$max_rating     = $input['max_rating'] ?? '';
$watch_status   = $input['watch_status'] ?? '';
$language       = $input['language'] ?? '';
$is_favourite   = $input['is_favourite'] ?? '';
$has_sequel     = $input['has_sequel'] ?? '';
$is_4k          = $input['is_4k'] ?? '';
$sort_by        = $input['sort_by'] ?? 'title_asc';

$sql    = "SELECT * FROM movies WHERE 1=1";
$params = [];
$types  = "";

if (!empty($title)) {
    $sql     .= " AND title LIKE ?";
    $params[] = "%$title%";
    $types   .= "s";
}
if (!empty($genre)) {
    $sql     .= " AND genre = ?";
    $params[] = $genre;
    $types   .= "s";
}
if (!empty($director)) {
    $sql     .= " AND director LIKE ?";
    $params[] = "%$director%";
    $types   .= "s";
}
if (!empty($release_year) && is_numeric($release_year)) {
    $sql     .= " AND release_year = ?";
    $params[] = (int)$release_year;
    $types   .= "i";
}
if (!empty($min_rating) && is_numeric($min_rating)) {
    $sql     .= " AND rating >= ?";
    $params[] = (float)$min_rating;
    $types   .= "d";
}
if (!empty($max_rating) && is_numeric($max_rating)) {
    $sql     .= " AND rating <= ?";
    $params[] = (float)$max_rating;
    $types   .= "d";
}
if (!empty($watch_status)) {
    $sql     .= " AND watch_status = ?";
    $params[] = $watch_status;
    $types   .= "s";
}
if (!empty($language)) {
    $sql     .= " AND language LIKE ?";
    $params[] = "%$language%";
    $types   .= "s";
}
if ($is_favourite === '1') $sql .= " AND is_favourite = 1";
if ($has_sequel   === '1') $sql .= " AND has_sequel = 1";
if ($is_4k        === '1') $sql .= " AND is_4k = 1";

switch ($sort_by) {
    case 'title_desc':    $sql .= " ORDER BY title DESC"; break;
    case 'rating_asc':    $sql .= " ORDER BY rating ASC"; break;
    case 'rating_desc':   $sql .= " ORDER BY rating DESC"; break;
    case 'year_asc':      $sql .= " ORDER BY release_year ASC"; break;
    case 'year_desc':     $sql .= " ORDER BY release_year DESC"; break;
    case 'duration_desc': $sql .= " ORDER BY duration_minutes DESC"; break;
    default:              $sql .= " ORDER BY title ASC"; break;
}

$stmt = $mysqli->prepare($sql);
if ($stmt) {
    if (!empty($params)) $stmt->bind_param($types, ...$params);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $movies = $result->fetch_all(MYSQLI_ASSOC);
        $safe   = [];
        foreach ($movies as $m) {
            $safe[] = [
                'movie_id'         => (int)$m['movie_id'],
                'title'            => htmlspecialchars($m['title'] ?? ''),
                'genre'            => htmlspecialchars($m['genre'] ?? ''),
                'director'         => htmlspecialchars($m['director'] ?? ''),
                'cast_members'     => htmlspecialchars($m['cast_members'] ?? ''),
                'release_year'     => $m['release_year'] ?? null,
                'duration_minutes' => $m['duration_minutes'] ?? null,
                'language'         => htmlspecialchars($m['language'] ?? ''),
                'country'          => htmlspecialchars($m['country'] ?? ''),
                'rating'           => $m['rating'] ?? null,
                'watch_status'     => $m['watch_status'] ?? 'Not Watched',
                'price'            => $m['price'] ?? null,
                'is_favourite'     => (bool)($m['is_favourite'] ?? false),
                'has_sequel'       => (bool)($m['has_sequel'] ?? false),
                'is_4k'            => (bool)($m['is_4k'] ?? false),
            ];
        }
        header('Content-Type: application/json');
        echo json_encode($safe);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Query failed: ' . $stmt->error]);
    }
    $stmt->close();
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Preparation failed: ' . $mysqli->error]);
}
?>
