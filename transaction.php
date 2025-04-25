<?php

// Запуск сессии
session_start();

// Проверка наличия username в сессии
if (!isset($_SESSION['username'])) {
    die("Ошибка: пользователь не авторизован.");
}

// Получение username из сессии
$username = $_SESSION['username'];

// Подключение к базе данных MySQL
try {
    $pdo = new PDO('mysql:host=localhost;dbname=u2995773_default', 'u2995773_u293290', 'Egor200544');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Получение адреса для отслеживания из базы данных
    $stmt = $pdo->prepare("SELECT address FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        die("Ошибка: адрес для отслеживания не найден.");
    }

    $addressToTrack = $user['address'];

    // Создание таблицы, если она не существует
    $pdo->exec("CREATE TABLE IF NOT EXISTS transactions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        address VARCHAR(255),
        amount DECIMAL(16,8),
        created_at DATETIME
    )");

    // Функция для отслеживания транзакций
    function trackIncomingTransactions($address, $pdo, $btcApiUrl) {
        // Получение информации о транзакциях по адресу
        $url = "$btcApiUrl/addresses/$address";
        $response = file_get_contents($url);
        $data = json_decode($response, true);
        
        if (isset($data['final_balance']) && $data['final_balance'] > 0) {
            foreach ($data['txrefs'] as $tx) {
                if ($tx['address'] === $address) {
                    $amount = $tx['value'] / 100000000; // Переводим сатоши в биткойн
                    $createdAt = date('Y-m-d H:i:s', strtotime($tx['confirmed_at']));

                    // Проверяем, была ли уже добавлена данная транзакция
                    $checkStmt = $pdo->prepare("SELECT * FROM transactions WHERE address = ? AND created_at = ?");
                    $checkStmt->execute([$tx['address'], $createdAt]);
                    
                    if ($checkStmt->rowCount() === 0) {
                        // Добавление информации о транзакции в базу данных
                        $stmt = $pdo->prepare("INSERT INTO transactions (address, amount, created_at) VALUES (?, ?, ?)");
                        $stmt->execute([$tx['address'], $amount, $createdAt]);
                        echo "Пополнение добавлено: {$tx['address']}, $amount BTC, $createdAt\n";

                        // Обновление баланса пользователя
                        $updateStmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE address = ?");
                        $updateStmt->execute([$amount, $tx['address']]);
                    }
                }
            }
        }
    }

    // URL API для получения данных о Bitcoin
    $btcApiUrl = 'https://api.blockcypher.com/v1/btc/main';

    // Выполнение одной проверки пополнений
    trackIncomingTransactions($addressToTrack, $pdo, $btcApiUrl);

} catch (Exception $e) {
    
}

?>
