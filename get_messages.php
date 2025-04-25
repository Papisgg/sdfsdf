<?php
session_start();

$token = '8148586139:AAGMLIqdizmVJx9wNW6_b311xsvWLLQhthA'; // Замените на ваш токен
$url = "https://api.telegram.org/bot{$token}/getUpdates";

$offset = isset($_SESSION['last_update_id']) ? $_SESSION['last_update_id'] + 1 : 0; // Изменено:  0 вместо null

$url .= "?offset={$offset}&timeout=60"; // Добавлено timeout для предотвращения блокировки

$response = @file_get_contents($url);

if ($response === FALSE) {
    error_log("Ошибка при получении обновлений: " . error_get_last()['message']); // Логирование ошибки в файл
    die('Ошибка при получении обновлений.'); //  Более информативное сообщение об ошибке
}

$data = json_decode($response, true);

if (!isset($data['result'])) {
    error_log("Некорректный ответ от API Telegram: " . json_encode($data)); // Логирование ошибки
    die('Некорректный ответ от API Telegram.'); // Более информативное сообщение об ошибке
}

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$messages = [];

foreach ($data['result'] as $update) {
    if (isset($update['message']['text'])) {
        $message = htmlspecialchars($update['message']['text'], ENT_QUOTES, 'UTF-8');
        $from = htmlspecialchars($update['message']['from']['first_name'], ENT_QUOTES, 'UTF-8');

        if ($user_id !== null && strpos($message, "Идентификатор: {$user_id}") === 0) {
            $message = str_replace("Идентификатор: {$user_id} ", "", $message); // Убрал лишнее условие
            if (!in_array($message, $messages)) {
                echo "<div class=\"asisten_message\"><b>{$from}:</b><h1 class=\"message_text\">{$message}</h1></div>";
                $messages[] = $message;
            }
            $_SESSION['last_update_id'] = $update['update_id'];
        }
    }
}
?>