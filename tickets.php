<?php
include 'scripts/connect.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <div class="description2">
<div class="description">
    <img src="nevatrip_logo.svg" alt="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список билетов</title>
    <link rel="stylesheet" href="styles.css">

</head>
<body>

<h1>Список билетов</h1>
</div>
</div>
<a href="index.php"><button>Назад</button></a>
<a href="#openModalAddType"><button>Добавить тип билета</button></a>
<div class="description2" style="max-width: 550px; margin: auto;" >
<div class="description"  align="center">
<h2>Типы билетов</h2>
    <table class="ticket-types-table">
        <thead>
            <tr>
                <th>Название типа</th>
                <th>Скидка</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody >
            <?php
            $query = "SELECT id, name, discount FROM ticket_types";
            $result = $mysqli->query($query);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['discount']) . "%</td>";
                    echo "<td>
                    <a href='scripts/delete_ticket_type.php?id=" . $row['id'] . "' onclick='return confirm(\"Удалить тип билета?\");'>
                    <button class='btn btn-delete'>Удалить</button>
                            <a href='#openModalEditType' onclick='openEditModal(" . $row['id'] . ", \"" . htmlspecialchars($row['name']) . "\", " . $row['discount'] . ");'>
                                <button class='btn btn-edit'>Изменить</button>
                            </a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>Нет доступных данных</td></tr>";
            }
            ?>
        </tbody>
    </table>
        </div>
        </div>
        <br>
        <div class="description2">
<div class="description">
    <div id="openModalEditType" class="modal">
    <img src="nevatrip_logo.svg" alt="" align="right">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Изменить тип билета</h3>
                <a href="#close" title="Закрыть" class="close">×</a>
            </div>
            <div class="modal-body">
                <form action="scripts/edit_ticket_type.php" method="POST">
                    <input type="hidden" id="edit-id" name="id">
                    
                    <label for="edit-name">Название типа билета:</label>
                    <input type="text" id="edit-name" name="name" required><br><br>
                    
                    <label for="edit-discount">Скидка:</label>
                    <input type="number" id="edit-discount" name="discount" min="0" value="0"><br><br>
                    
                    <button type="submit">Сохранить изменения</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
function openEditModal(id, name, discount) {
    document.getElementById('edit-id').value = id;
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-discount').value = discount;
}
</script>
<table>
    <thead>
        <tr>
            <th>ID Заказа</th>
            <th>Название фильма</th>
            <th>Дата</th>
            <th>Тип билета</th>
            <th>Цена билета</th>
            <th>Штрихкод</th>
            <th>Удалить</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $query = "
            SELECT 
                t.id AS ticket_id, t.order_id, e.event_name, e.event_date, tt.name AS ticket_type, t.price, t.barcode
            FROM 
                tickets t
            JOIN 
                orders o ON t.order_id = o.id
            JOIN 
                events e ON o.event_id = e.event_id
            JOIN 
                ticket_types tt ON t.ticket_type_id = tt.id
            ORDER BY 
                t.id ASC
        ";
        $result = $mysqli->query($query);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['order_id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['event_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['event_date']) . "</td>";
                echo "<td>" . htmlspecialchars($row['ticket_type']) . "</td>";
                echo "<td>" . htmlspecialchars($row['price']) . "</td>";
                echo "<td>" . htmlspecialchars($row['barcode']) . "</td>";
                echo "<td>
                        <a href='scripts/delete_ticket.php?id=" . $row['ticket_id'] . "' onclick='return confirm(\"Удалить билет?\");'><button class='btn btn-delete'>Удалить</button></a>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='7'>Нет доступных данных</td></tr>";
        }
        ?>
    </tbody>
</table>
<div id="openModalAddType" class="modal">
    <img src="nevatrip_logo.svg" alt="" align="right">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Добавить новый тип билета</h3>
        <a href="#close" title="Закрыть" class="close">×</a>
      </div>
      <div class="modal-body">
        <form action="scripts/add_ticket_type.php" method="POST">
          <label for="name">Название типа билета:</label>
          <input type="text" id="name" name="name" required><br><br>
          
          <label for="discount">Скидка:</label>
          <input type="number" id="discount" name="discount" min="0" value="0"><br><br>
          
          <button type="submit">Добавить тип</button>
        </form>
      </div>
    </div>
  </div>
</div>

</body>
</html>
