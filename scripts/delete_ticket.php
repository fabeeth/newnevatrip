<?php
include 'connect.php';

if (isset($_GET['id'])) {
    $ticket_id = (int)$_GET['id'];
    
    $query = "DELETE FROM tickets WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $ticket_id);
    $stmt->execute();
    $stmt->close();

    header("Location: ../tickets.php");
    exit();
} else {
    echo "ID билета не указан.";
}
?>
