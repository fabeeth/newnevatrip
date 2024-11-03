<?php
// Подключение к базе данных
include 'connect.php';

// Проверка, был ли передан параметр id
if (isset($_GET['id'])) {
    $order_id = (int)$_GET['id'];

    // Запрос на удаление заказа из базы данных
    $query = "DELETE FROM orders WHERE id = $order_id";

    if ($mysqli->query($query)) {
        // Перенаправление обратно на главную страницу после успешного удаления
        header("Location: ../index.php");
        exit;
    } else {
        // Вывод подробной информации об ошибке
        echo "Ошибка при удалении заказа: " . $mysqli->error;
    }
} else {
    echo "Неверный запрос. Параметр 'id' не передан.";
}
?>
