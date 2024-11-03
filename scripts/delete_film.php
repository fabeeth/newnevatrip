<?php
include 'connect.php';

// Проверка, был ли передан идентификатор фильма для удаления
if (isset($_GET['id'])) {
    $event_id = (int)$_GET['id'];

    $delete_query = "DELETE FROM events WHERE event_id = ?";
    $stmt = $mysqli->prepare($delete_query);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: ../films.php");
exit();
?>