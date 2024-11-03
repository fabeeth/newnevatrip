<?php
include 'connect.php';

// Проверка, был ли передан ID фильма
if (!isset($_GET['id'])) {
    echo "ID фильма не передан.";
    exit();
}

$event_id = (int)$_GET['id'];

// Проверка, была ли отправлена форма для изменения фильма
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $cost = (int)$_POST['cost'];

    $update_query = "UPDATE events SET event_name = ?, event_date = ?, event_time = ?, cost = ? WHERE event_id = ?";
    $stmt = $mysqli->prepare($update_query);
    $stmt->bind_param("ssssi", $event_name, $event_date, $event_time, $cost, $event_id);
    $stmt->execute();
    $stmt->close();

    header("Location: ../films.php");
    exit();
}

$query = "SELECT event_name, event_date, event_time, cost FROM events WHERE event_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Фильм не найден.";
    exit();
}

$event = $result->fetch_assoc();
?>
<?php
$mysqli->close();
?>