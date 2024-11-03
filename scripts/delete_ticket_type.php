<?php
include 'connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $mysqli->begin_transaction();

    try {
        $deleteTicketsQuery = "DELETE FROM tickets WHERE ticket_type_id = ?";
        $deleteTicketsStmt = $mysqli->prepare($deleteTicketsQuery);
        $deleteTicketsStmt->bind_param("i", $id);
        $deleteTicketsStmt->execute();
        $deleteTicketsStmt->close();

        $deleteTypeQuery = "DELETE FROM ticket_types WHERE id = ?";
        $deleteTypeStmt = $mysqli->prepare($deleteTypeQuery);
        $deleteTypeStmt->bind_param("i", $id);
        $deleteTypeStmt->execute();
        $deleteTypeStmt->close();

        $mysqli->commit();

        header("Location: ../tickets.php");
        exit();
    } catch (Exception $e) {
        $mysqli->rollback();
        echo "Ошибка при удалении данных: " . $e->getMessage();
    }
}
?>
