<?php
include 'scripts/connect.php';
include 'scripts/add_event.php';

// Проверка, был ли отправлен запрос на удаление фильма
if (isset($_GET['delete'])) {
    $event_id = (int)$_GET['delete'];
    $delete_query = "DELETE FROM events WHERE event_id = ?";
    $stmt = $mysqli->prepare($delete_query);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $stmt->close();
    header("Location: films.php");
    exit();
}

$query = "SELECT event_id, event_name, event_date, event_time, cost FROM events";
$result = $mysqli->query($query);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <div class="description2">
<div class="description">
    <img src="nevatrip_logo.svg" alt="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список фильмов</title>
    <link rel="stylesheet" href="styles.css">

    
</head>
<body>

<div id="openModal3" class="modal">
<img src="nevatrip_logo.svg" alt="" align="right">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Изменить данные фильма</h3>
        <a href="#close" title="Close" class="close">×</a>
      </div>
      <div class="modal-body">  
<form action="scripts/edit_event.php?id=<?php echo $event_id; ?>" method="POST">
    <label for="event_name">Название фильма:</label>
    <input type="text" id="event_name" name="event_name" value="<?php echo htmlspecialchars($event['event_name']); ?>" required><br><br>
    
    <label for="event_date">Дата фильма:</label>
    <input type="date" id="event_date" name="event_date" value="<?php echo htmlspecialchars($event['event_date']); ?>" required><br><br>
    
    <label for="event_time">Время фильма:</label>
    <input type="time" id="event_time" name="event_time" value="<?php echo htmlspecialchars($event['event_time']); ?>"><br><br>
    
    <label for="cost">Стоимость:</label>
    <input type="number" id="cost" name="cost" value="<?php echo htmlspecialchars($event['cost']); ?>" required><br><br>
    
    <button type="submit">Сохранить изменения</button>
</form>
</div>
    </div>
  </div>
</div>

<h1>Список фильмов</h1>
</div>
</div>
<a href="index.php">  
        <button class="Back">Назад</button>  
    </a> 
<a href="#openModal2">  
        <button class="Add">Новый фильм</button>  
    </a> 
    <div class="description2">
<div class="description">
<table>
    <thead>
        <tr>
            <th>Название фильма</th>
            <th>Дата фильма</th>
            <th>Время фильма</th>
            <th>Стоимость</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['event_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['event_date']) . "</td>";
                echo "<td>" . htmlspecialchars($row['event_time']) . "</td>";
                echo "<td>" . htmlspecialchars($row['cost']) . "</td>";
                echo "<td>";?>
                <a href="#openModal3" class="btn btn-edit" onclick="openEditModal('<?php echo $row['event_id']; ?>', '<?php echo htmlspecialchars($row['event_name']); ?>', '<?php echo htmlspecialchars($row['event_date']); ?>', '<?php echo htmlspecialchars($row['event_time']); ?>', '<?php echo htmlspecialchars($row['cost']); ?>')">Изменить</a>
    <?echo "
                <a href='scripts/delete_film.php?id=" . $row['event_id'] . "' class='btn btn-delete' onclick=\"return confirm('Вы уверены, что хотите удалить этот фильм?');\">Удалить</a>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>Нет доступных данных</td></tr>";
        }
        ?>
    </tbody>
</table>
<script>
function openEditModal(eventId, eventName, eventDate, eventTime, cost) {
    document.getElementById('event_name').value = eventName;
    document.getElementById('event_date').value = eventDate;
    document.getElementById('event_time').value = eventTime;
    document.getElementById('cost').value = cost;
    
    document.querySelector('#openModal3 form').action = 'scripts/edit_event.php?id=' + eventId;
}
</script>

</body>
</html>

<?php
$mysqli->close();
?>