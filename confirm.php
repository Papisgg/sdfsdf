<?php
$servername = "localhost";
$username = "studgycu_detectiv"; // замените на ваше имя пользователя
$password = "Qwerty200544"; // замените на ваш пароль
$dbname = "studgycu_1"; // замените на имя вашей базы данных


// Создаем подключение к базе данных
$conn = new mysqli($servername, $username, $password, $dbname);

// Проверяем соединение
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Получаем данные из формы
$comment = $_POST['comment'];
$summa = $_POST['summa'];

// Находим текущее значение summa для указанного comment
$sql = "SELECT summa FROM users WHERE comment='$comment'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Получаем текущее значение summa
    $row = $result->fetch_assoc();
    $current_summa = $row['summa'];

    // Обновляем summa
    $new_summa = $current_summa + $summa;

    // Обновляем запись в таблице
    $sql = "UPDATE users SET summa='$new_summa' WHERE comment='$comment'";
    
    if ($conn->query($sql) === TRUE) {
        echo "Запись обновлена успешно";
    } else {
        echo "Ошибка при обновлении записи: " . $conn->error;
    }
} else {
    echo "Запись не найдена";
}

$conn->close();
?>
