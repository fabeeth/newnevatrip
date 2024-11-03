<?php
require("connect.php");


// Получение списка фильмов
$events = $mysqli->query("SELECT event_id, event_name, event_date, event_time FROM events ORDER BY event_name, event_date, event_time")->fetch_all(MYSQLI_ASSOC);

$groupedEvents = [];
foreach ($events as $event) {
    $groupedEvents[$event['event_name']][$event['event_date']][] = $event;
}

// API бронирования и подтверждения
function Apishka($barcode) {
    $responses = [
        ['message' => 'order successfully booked'],
        ['error' => 'barcode already exists']
    ];
    return $responses[array_rand($responses)];
}

function ApishkaApprove($barcode) {
    $responses = [
        ['message' => 'order successfully approved'],
        ['error' => 'event cancelled'],
        ['error' => 'no tickets'],
        ['error' => 'no seats'],
        ['error' => 'fan removed']
    ];
    return $responses[array_rand($responses)];
}

// Функция для добавления заказа и генерации уникальных штрих-кодов
function addOrder($event_id, $ticket_quantities, $mysqli) {
    // Получение данных фильма
    $event = $mysqli->query("SELECT event_date, cost FROM events WHERE event_id = $event_id");
    if (!$event) {
        die("Ошибка запроса к events: " . $mysqli->error);
    }
    $event_data = $event->fetch_assoc();
    $base_cost = $event_data['cost'];

    $total_price = 0;

    // Вставка заказа
    $stmt = $mysqli->prepare("INSERT INTO orders (event_id, equal_price, created) VALUES (?, ?, NOW())");
    $stmt->bind_param("ii", $event_id, $total_price);
    if (!$stmt->execute()) {
        die("Ошибка вставки в orders: " . $stmt->error);
    }
    $order_id = $mysqli->insert_id; // Получаем ID нового заказа
    $stmt->close();

    // Подготовка запроса для вставки билетов
    $stmt_ticket = $mysqli->prepare("INSERT INTO tickets (order_id, ticket_type_id, price, barcode) VALUES (?, ?, ?, ?)");

    // Генерация и добавление билетов
    foreach ($ticket_quantities as $type_id => $quantity) {
        if ($quantity > 0) {
            $type = $mysqli->query("SELECT discount FROM ticket_types WHERE id = $type_id");
            if (!$type) {
                die("Ошибка запроса к ticket_types: " . $mysqli->error);
            }
            $type_data = $type->fetch_assoc();
            $price = $base_cost - ($base_cost * ($type_data['discount'] / 100));
            $total_price += $price * $quantity;

            for ($i = 0; $i < $quantity; $i++) {
                $success = false;
                $attempts = 0;

                while (!$success && $attempts < 5) {
                    $attempts++;
                    do {
                        $barcode = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
                        $result = $mysqli->query("SELECT * FROM tickets WHERE barcode = '$barcode'");
                    } while ($result && $result->num_rows > 0);

                    $response = Apishka($barcode);
                    if (isset($response['message']) && $response['message'] === 'order successfully booked') {
                        ApishkaApprove($barcode);
                        $stmt_ticket->bind_param("iiis", $order_id, $type_id, $price, $barcode);
                        if ($stmt_ticket->execute()) {
                            $success = true;
                        }
                    }
                }
            }
        }
    }

    $stmt = $mysqli->prepare("UPDATE orders SET equal_price = ? WHERE id = ?");
    $stmt->bind_param("ii", $total_price, $order_id);
    $stmt->execute();
    $stmt->close();
    $stmt_ticket->close();

    $randomResponse = ApishkaApprove('');
    $message = isset($randomResponse['message']) ? $randomResponse['message'] : $randomResponse['error'];

    return $message;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $event_id = $_POST['event_id'];
    $ticket_quantities = [];
    if (isset($_POST['ticket_types']) && isset($_POST['ticket_quantity'])) {
        foreach ($_POST['ticket_types'] as $type_id) {
            $quantity = isset($_POST['ticket_quantity'][$type_id]) ? (int)$_POST['ticket_quantity'][$type_id] : 0;
            if ($quantity > 0) {
                $ticket_quantities[$type_id] = $quantity;
            }
        }
    }

    try {
        $message = addOrder($event_id, $ticket_quantities, $mysqli);
        echo "<script>alert('$message');</script>";
    } catch (Exception $e) {
        echo "<script>alert('Ошибка: " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавление заказа</title>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll("input[type=checkbox]").forEach((checkbox) => {
                checkbox.addEventListener("change", function() {
                    const quantityField = this.nextElementSibling.nextElementSibling;
                    quantityField.style.display = this.checked ? "inline" : "none";
                    if (!this.checked) {
                        quantityField.value = '';
                    }
                });
            });
        });
    </script>
</head>
<body>
    <div id="openModal1" class="modal">
    <img src="nevatrip_logo.svg" alt="" align="right">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
      
        <h3 class="modal-title">Добавить новый заказ</h3>
        <a href="#close" title="Close" class="close">×</a>
      </div>
      <div class="modal-body">   
    <form method="POST" action="">
        <label>Фильм:</label>
        <select name="event_name" id="eventSelect" required onchange="updateEventDates()">
            <option value="">Выберите фильм</option>
            <?php foreach ($groupedEvents as $eventName => $dates): ?>
                <option value="<?= htmlspecialchars($eventName) ?>"><?= htmlspecialchars($eventName) ?></option>
            <?php endforeach; ?>
        </select><br>

        <label>Дата:</label>
        <select id="dateSelect" name="event_date" required onchange="updateEventTimes()">
            <option value="">Выберите дату</option>
        </select><br>

        <label>Время:</label>
        <select id="timeSelect" name="event_id" required>
            <option value="">Выберите время</option>
        </select><br>

        <label>Типы билетов:</label><br>
        <?php
        $ticketTypes = $mysqli->query("SELECT id, name, discount FROM ticket_types")->fetch_all(MYSQLI_ASSOC);
        foreach ($ticketTypes as $type):
        ?>
            <input type="checkbox" name="ticket_types[]" value="<?= $type['id'] ?>" id="ticketType<?= $type['id'] ?>">
            <label for="ticketType<?= $type['id'] ?>"><?= htmlspecialchars($type['name']) ?></label>
            <input type="number" name="ticket_quantity[<?= $type['id'] ?>]" placeholder="Количество" min="0" style="display:none;"><br>
        <?php endforeach; ?>

        <button type="submit">Добавить заказ</button>
    </form>

    </div>
    </div>
  </div>
</div>

    <script>
        const groupedEvents = <?= json_encode($groupedEvents) ?>;

        function updateEventDates() {
            const eventName = document.getElementById("eventSelect").value;
            const dateSelect = document.getElementById("dateSelect");

            dateSelect.innerHTML = '<option value="">Выберите дату</option>';
            document.getElementById("timeSelect").innerHTML = '<option value="">Выберите время</option>';

            if (eventName && groupedEvents[eventName]) {
                for (const date in groupedEvents[eventName]) {
                    const option = document.createElement("option");
                    option.value = date;
                    option.textContent = date;
                    dateSelect.appendChild(option);
                }
            }
        }

        function updateEventTimes() {
            const eventName = document.getElementById("eventSelect").value;
            const eventDate = document.getElementById("dateSelect").value;
            const timeSelect = document.getElementById("timeSelect");

            timeSelect.innerHTML = '<option value="">Выберите время</option>';

            if (eventName && eventDate && groupedEvents[eventName][eventDate]) {
                for (const event of groupedEvents[eventName][eventDate]) {
                    const option = document.createElement("option");
                    option.value = event.event_id;
                    option.textContent = event.event_time;
                    timeSelect.appendChild(option);
                }
            }
        }
    </script>
</body>
</html>
