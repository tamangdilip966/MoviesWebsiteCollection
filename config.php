<?php
session_save_path('/tmp');
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('SITE_NAME', 'Movie Collection System');
define('SITE_AUTHOR', 'Student 2439673');
define('STUDENT_ID', '2439673');

require_once __DIR__ . '/vendor/autoload.php';
?>
