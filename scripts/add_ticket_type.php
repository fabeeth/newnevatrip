<?php
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = isset($_POST['name']) ? $mysqli->real_escape_string(trim($_POST['name'])) : '';
    $discount = isset($_POST['discount']) ? (int)$_POST['discount'] : 0;

    if ($name !== '') {
        $query = "INSERT INTO ticket_types (name, discount) VALUES ('$name', $discount)";
        if ($mysqli->query($query)) {
            header("Location: ../tickets.php?status=success");
        } else {
            header("Location: ../tickets.php?status=error");
        }
    } else {
        header("Location: ../tickets.php?status=error");
    }
}
?>
