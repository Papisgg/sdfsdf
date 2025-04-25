<?php
session_start();

// Проверка пользователя
if (!isset($_SESSION['user_id'])) {
    if (!isset($_COOKIE['user_id'])) {
        $user_id = sprintf('%06d', mt_rand(0, 999999));
        setcookie('user_id', $user_id, time() + 3600 * 24 * 365, "/"); // Добавлен параметр path
    } else {
        $user_id = $_COOKIE['user_id'];
    }
    $_SESSION['user_id'] = $user_id;
}

// Замените на ваш токен и chat_id
$token = "8148586139:AAGMLIqdizmVJx9wNW6_b311xsvWLLQhthA";
$chat_id = "5480279690";

// Подключение к базе данных
$mysqli = new mysqli('localhost', 'studgycu_1', 'Qwerty200544', 'studgycu_detectiv');

// Проверка подключения
if ($mysqli->connect_error) {
    die("Ошибка подключения: " . $mysqli->connect_error);
}

// Обработка формы отправки сообщения
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    header('Content-Type: application/json'); // Устанавливаем заголовок для JSON-ответа

    // Сохранение данных из формы
    if (isset($_POST['act']) && $_POST['act'] === 'form') {
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
        $country = isset($_POST['country']) ? trim($_POST['country']) : '';
        
        // Проверка обязательных полей
        if (!empty($name) && !empty($email) && !empty($country)) {
            // Форматирование данных для сохранения
            $data = "Имя: $name
Email: $email
Телефон: $phone
Страна: $country

";
            
            // Сохраняем данные в файл
            file_put_contents('data.txt', $data, FILE_APPEND | LOCK_EX);
            
            // Отправка данных в Telegram
            $txt = "<b>Новое сообщение о пользователе:</b> %0AИмя: {$name} %0AEmail: {$email} %0AТелефон: {$phone} %0AСтрана: {$country}";
            $url = "https://api.telegram.org/bot{$token}/sendMessage?chat_id={$chat_id}&parse_mode=html&text=" . urlencode($txt);
            
            $sendToTelegram = @file_get_contents($url);
            if ($sendToTelegram) {
                echo json_encode(['status' => 'success', 'message' => 'sucsess']);
            } else {
                echo json_encode(['status' => 'error', 'message' => '']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => '']);
        }

    } elseif (isset($_POST['act']) && $_POST['act'] === 'order') { // Обработка сообщений
        if (!empty($_POST['message'])) {
            $message = htmlspecialchars(trim($_POST['message']), ENT_QUOTES, 'UTF-8');
            $user_id = $_SESSION['user_id'];
    
            // Проверка на дубликаты
            $stmt = $mysqli->prepare("SELECT COUNT(*) FROM messages WHERE message = ? AND user_id = ?");
            $stmt->bind_param("ss", $message, $user_id);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();
    
            if ($count === 0) {
                // Вставка нового сообщения
                $stmt = $mysqli->prepare("INSERT INTO messages (user_id, message) VALUES (?, ?)");
                $stmt->bind_param("ss", $user_id, $message);
                $stmt->execute();
                $stmt->close();
    
                // Отправка сообщения в Telegram
                $txt = "<b>Идентификатор:</b> {$user_id} %0A<b>Сообщение:</b> {$message}";
                $url = "https://api.telegram.org/bot{$token}/sendMessage?chat_id={$chat_id}&parse_mode=html&text=" . urlencode($txt);
                
                $sendToTelegram = @file_get_contents($url);
                if ($sendToTelegram) {
                    echo json_encode(['status' => 'success', 'message' => 'Я: ' . $message]);
                } else {
                    echo json_encode(['status' => 'error', 'message' => '']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => '']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => '']);
        }
    }
    exit; // Завершаем выполнение скрипта после AJAX-ответа
}

// Закрытие соединения с базой данных
$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Jost:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>

<body>
<?php
    include 'header.php';
    include 'chengeballance.php';
    include 'assistent.php';
?>
<div id="content">
    
    <script>
        
    </script>
    <?php
    // При первом запуске, если в localStorage ничего нет, загружается содержимое сайта
    if (isset($_GET['page'])) {
        echo "<script>localStorage.setItem('lastPage', '{$_GET['page']}');</script>";
        include $_GET['page'];
    } elseif (isset($_GET['page']) && $_GET['page'] == 'sait.html') {
        include 'sait.html';
    } else {
        include 'sait.html';
    }
    ?>
    <input type="hidden" id="user_id" value="<?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : ''; ?>">

</div>


<script>
    // Функция для динамической загрузки скрипта
    function loadScript(scriptUrl) {
        var script = document.createElement('script');
        script.src = scriptUrl;
        document.body.appendChild(script);
    }

    // Загружаем script.js при загрузке нового контента
    $(document).ready(function() {
        loadScript('script.js'); // Замените на путь к вашему script.js
    });
</script>
    
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="script.js"></script>


<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

?>

<script>
    $(document).ready(function() {
        function get_transaction() {
            $.ajax({
                url: 'transaction.php',
                type: 'GET',
                success: function(data) {
                    // Обработка полученных данных
                    console.log(data);
                },
                error: function(xhr, status, error) {
                    console.error('Ошибка при выполнении запроса:', error);
                }
            });
        }

        get_transaction();
    });
</script>




<?php
// Проверка успешной регистрации и наличие username в сессии
if (isset($_SESSION['username'])) {
    include 'deposit.php'; // Если username есть, показываем deposit.php
} elseif (isset($_SESSION['registered']) && $_SESSION['registered'] === true) {
    // Если регистрация успешна, то также показываем deposit.php
    include 'deposit.php';
    unset($_SESSION['registered']); // Очищаем состояние после показа
} else {
    include 'register.php'; // Если нет username и регистрации, показываем register.php
}
?>

</body>
<html>