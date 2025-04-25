<?php

session_start();
// Настройки подключения к базе данных
$host = 'localhost'; // Хост
$db = 'u2995773_default'; // Имя базы данных
$user = 'u2995773_u293290'; // Пользователь
$pass = 'Egor200544'; // Пароль

try {
    // Подключение к базе данных с использованием PDO
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Получение данных из POST запроса
    $data = json_decode(file_get_contents('php://input'), true);
    $amount = $data['amount'];
    $comment = $data['comment'];
    $userId = $_SESSION['userId'];
    $createdAt = $data['createdAt'];

    // Подготовленный запрос для вставки в таблицу transactions
    $stmt = $pdo->prepare("INSERT INTO transactions (amount, comment, id, created_at) VALUES (?, ?, ?, ?)");
    $stmt->execute([$amount, $comment, $userId, $createdAt]);

    // Успешный ответ
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    // Обработка ошибок
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
