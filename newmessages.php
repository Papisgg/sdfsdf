<?php
// Настройки подключения к базе данных
$host = 'localhost'; // хост
$db = 'u2995773_default'; // имя базы данных
$user = 'u2995773_u293290'; // имя пользователя
$pass = 'Egor200544'; // пароль пользователя

// Подключение к базе данных
$mysqli = new mysqli($host, $user, $pass, $db);

// Проверка подключения
if ($mysqli->connect_error) {
    die('Ошибка подключения (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

// Получаем user_id из куки
$user_id = isset($_COOKIE['user_id']) ? $_COOKIE['user_id'] : null;

if ($user_id) {
    // Запрос на получение новых сообщений
    $query = "SELECT message FROM messages WHERE user_id = $user_id"; // is_read - флаг, указывающий на прочитано ли сообщение
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Проверка на наличие новых сообщений
    if ($result->num_rows > 0) {
        // Получаем все новые сообщения
        $messages = [];
        while ($row = $result->fetch_assoc()) {
            $messages[] = $row['text'];
        }

        // Возвращаем новые сообщения
        echo json_encode($messages);
    } else {
        echo json_encode([]); // Нет новых сообщений
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'Пользователь не идентифицирован']); // user_id не найден в куки
}

// Закрываем соединение
$mysqli->close();
?>
