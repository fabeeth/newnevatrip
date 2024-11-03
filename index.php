<?php
include 'scripts/connect.php';
include("scripts/add_order.php");

$query = "
SELECT 
    e.event_name,
    e.event_date,
    e.event_time,
    e.cost,
    o.equal_price,
    MIN(o.id) AS id,
    tt.name AS ticket_type_name,
    COUNT(t.ticket_type_id) AS ticket_type_count,
    t.price,
    GROUP_CONCAT(t.barcode SEPARATOR ', ') AS barcodes,
    o.created
FROM 
    events e
JOIN 
    orders o ON e.event_id = o.event_id
JOIN 
    tickets t ON o.id = t.order_id
JOIN 
    ticket_types tt ON t.ticket_type_id = tt.id
GROUP BY 
    e.event_name, e.event_date, e.event_time, e.cost, o.equal_price, tt.name, t.price, o.created
ORDER BY 
    e.event_date, e.event_time;
";


$result = $mysqli->query($query);

// Проверка на ошибки запроса
if (!$result) {
    die("Ошибка выполнения запроса: " . $mysqli->error);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <div class="description2">
<div class="description">
    <img src="nevatrip_logo.svg" alt="">
    <title>Список заказов</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>

<h2>Список заказов</h2>
</div>
</div>
<a href="#openModal1">  
        <button class="Add">Новый заказ</button>  
    </a> 

    <a href="films.php">  
        <button class="Add2">Фильмы</button>  
    </a> 

    <a href="tickets.php">  
        <button class="Tick">Билеты</button>  
    </a> 
    <div class="description2">
<div class="description">
    <table>
    <thead>
        <tr>
            <th>Номер заказа</th>
            <th>Название фильма</th>
            <th>Дата фильма</th>
            <th>Время фильма</th>
            <th>Стоимость</th>
            <th>Цена заказа</th>
            <th>Тип билета</th>
            <th>Количество</th>
            <th>Цена билета</th>
            <th>Штрихкоды</th>
            <th>Создано</th>
            <th>Удалить</th>
        </tr>
    </thead>
    <tbody>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
    <td><?= htmlspecialchars($row['id']) ?></td>
        <td><?= htmlspecialchars($row['event_name']) ?></td>
        <td><?= htmlspecialchars($row['event_date']) ?></td>
        <td><?= htmlspecialchars($row['event_time']) ?></td>
        <td><?= htmlspecialchars($row['cost']) ?> руб.</td>
        <td><?= htmlspecialchars($row['equal_price']) ?> руб.</td>
        <td><?= htmlspecialchars($row['ticket_type_name']) ?></td>
        <td><?= htmlspecialchars($row['ticket_type_count']) ?></td>
        <td><?= htmlspecialchars($row['price']) ?> руб.</td>
        <td>
            <button onclick="toggleBarcodes(this)" class="toggle-button">Показать<br> штрихкоды</button>
            <div class="barcode-list" style="display: none;">
                <?php foreach (explode(', ', $row['barcodes']) as $barcode): ?>
                    <div><?= htmlspecialchars($barcode) ?></div>
                <?php endforeach; ?>
            </div>
        </td>
        <td><?= htmlspecialchars($row['created']) ?></td>
        <td>
    <a href="scripts/delete_order.php?id=<?= $row['id'] ?>" onclick="return confirm('Вы уверены, что хотите удалить этот заказ?');">
        <button class="btn btn-delete">Удалить</button>
    </a>
</td>

    </tr>
    <?php endwhile; ?>
    </tbody>
    <script>
function toggleBarcodes(button) {
    const barcodeList = button.nextElementSibling;

    if (barcodeList.style.display === "none" || !barcodeList.style.display) {
        // Показать штрихкоды
        barcodeList.style.display = "block";
        button.textContent = "Скрыть";
    } else {
        // Скрыть штрихкоды
        barcodeList.style.display = "none";
        button.textContent = "Показать штрихкоды";
    }
}
</script>


</table>

<?php
$result->free();
$mysqli->close();
?>
</body>
</html>
