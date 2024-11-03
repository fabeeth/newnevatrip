<?php
require_once 'app-config.php';

$mysqli = new mysqli(DATABASE_HOST, USERNAME, PASSWORD, DATABASE_NAME);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
?>