<?php
// Подключение к базе данных
require("connect.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_event'])) {
    $event_name = $_POST['event_name'];
    $description = $_POST['description'];
    $cost = $_POST['cost'];
    $datetimes = $_POST['event_datetimes'];

    foreach ($datetimes as $datetime) {
        // Извлекаем дату и время
        $date = date('Y-m-d', strtotime($datetime));
        $time = date('H:i:s', strtotime($datetime));

        // Вставляем запись с датой и временем
        $stmt = $mysqli->prepare("INSERT INTO events (event_name, event_date, event_time, description, cost) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssd", $event_name, $date, $time, $description, $cost);
        $stmt->execute();
        $stmt->close();
    }
    echo "<script>alert('Фмльм успешно добавлен');</script>";
}
?>

<div id="openModal2" class="modal">
<img src="nevatrip_logo.svg" alt="" align="right">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Добавить новый фильм</h3>
        <a href="#close" title="Close" class="close">×</a>
      </div>
      <div class="modal-body">  
<form method="POST" action="">
    <label>Название фильма:</label>
    <input type="text" name="event_name" required><br>

    <label>Описание:</label>
    <textarea name="description" required></textarea><br>

    <label>Стоимость:</label>
    <input type="number" name="cost" min="0" step="0.01" required><br>

    <label>Дата и время:</label>
    <div id="datetimesContainer">
        <input type="datetime-local" name="event_datetimes[]" required>
    </div>
    <button type="button" onclick="addDateTimeField()">Добавить еще одну дату и время</button><br>

    <button type="submit" name="add_event">Добавить событие</button>
</form>
</div>
    </div>
  </div>
</div>

<script>
    // Функция для добавления нового поля для даты и времени
    function addDateTimeField() {
        const container = document.getElementById("datetimesContainer");
        const newDateTimeField = document.createElement("input");
        newDateTimeField.type = "datetime-local";
        newDateTimeField.name = "event_datetimes[]";
        newDateTimeField.required = true;
        container.appendChild(newDateTimeField);
    }
</script>
