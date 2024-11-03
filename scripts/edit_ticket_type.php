<?php
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $discount = $_POST['discount'];

    $query = "UPDATE ticket_types SET name = ?, discount = ? WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("sii", $name, $discount, $id);

    if ($stmt->execute()) {
        header("Location: ../tickets.php");
        exit();
    } else {
        echo "Ошибка при обновлении данных";
    }
    $stmt->close();
}
?>
