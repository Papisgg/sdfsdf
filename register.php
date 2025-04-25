<?php
session_start(); // Не забудьте инициализировать сессию
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

$pdo = new PDO('mysql:host=localhost;dbname=studgycu_detectiv', 'studgycu_1', 'Qwerty200544');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'login' && isset($_POST['username']) && !empty($_POST['username']) && isset($_POST['password'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];
        
            // Проверка на существование имени пользователя
            $checkStmt = $pdo->prepare("SELECT id, address, password, comment FROM users WHERE username = :username");
            $checkStmt->execute(['username' => $username]);
            $user = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
            if ($user && password_verify($password, $user['password'])) {
                // Сохранение userId, address и comment в сессии
                $_SESSION['userId'] = $user['id'];
                $_SESSION['address'] = $user['address'];
                $_SESSION['username'] = $username;
                $_SESSION['comment'] = $user['comment']; // Сохранение комментария в сессии
        
                // Ответ клиенту с сообщением об успешном входе
                echo json_encode(['message' => 'Успешный вход', 'redirect' => 'lk.html', 'address' => $user['address'], 'comment' => $user['comment']]);
            } else {
                echo json_encode(['error' => 'Неверное имя пользователя или пароль.']);
            }
        } elseif ($action === 'register' && isset($_POST['username']) && !empty($_POST['username']) && isset($_POST['password'])) {
        
            $username = $_POST['username'];
            $password = $_POST['password'];

            // Проверка на существование имени пользователя для регистрации
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
            $checkStmt->execute(['username' => $username]);
            $count = $checkStmt->fetchColumn();

            if ($count > 0) {
                echo json_encode(['error' => 'Имя пользователя занято.']);
            } else {
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                
                // Генерация уникального комментария
                $uniqueComment = generateUniqueComment();

                $stmt = $pdo->prepare("INSERT INTO users (username, password, comment) VALUES (:username, :password, :comment)");
                $stmt->execute(['username' => $username, 'password' => $hashedPassword, 'comment' => $uniqueComment]);

                // Сохранение username, userId и comment в сессии
                $_SESSION['username'] = $username;
                $_SESSION['userId'] = $pdo->lastInsertId();
                $_SESSION['comment'] = $uniqueComment; // Сохранение комментария в сессии
                $_SESSION['registered'] = true;

                // Генерация адреса
                $publicKey = 'xpub6D6f4jifGKaJNEf42J35owoqK7VZerXCipMJuwxcEN9u9CKiSzVAauHrnHyQn16GVkxvw1xv64ziBSscB3J1P5xDdn1gujDUQRfhUVJHhn9';
                $result = $pdo->query("SELECT index_value FROM address_index LIMIT 1");
                $row = $result->fetch(PDO::FETCH_ASSOC);

                if ($row) {
                    $index = $row['index_value'];
                } else {
                    $pdo->query("INSERT INTO address_index (index_value) VALUES (0)");
                    $index = 0;
                }

                $address = createAddress($publicKey, $index);
                $newIndex = $index + 1;
                $pdo->query("UPDATE address_index SET index_value = $newIndex");

                // Сохранение адреса в сессии
                $_SESSION['address'] = $address;

                // Сохранение нового адреса в базу данных (если он есть)
                if ($address) {
                    $addressStmt = $pdo->prepare("UPDATE users SET address = :address WHERE id = :userId");
                    $addressStmt->execute(['address' => $address, 'userId' => $_SESSION['userId']]);
                }

                // Возвращаем JSON-ответ
                echo json_encode(['message' => 'Успешная регистрация', 'redirect' => 'lk.html', 'address' => $address]);
            }
        }
    }
    exit(); // Прекращаем выполнение скрипта
}

// Функция генерации уникального комментария из 5 символов
function generateUniqueComment($length = 5) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function createAddress($publicKey, $index) {
    // Приведение публичного ключа к байтовому массиву
    $publicKeyBytes = hex2bin($publicKey);
    $publicKeyBytes = hash_hmac('sha256', $publicKeyBytes . $index, '', true);
    $sha256Hash = hash('sha256', $publicKeyBytes, true);
    $ripemd160 = hash('ripemd160', $sha256Hash, true);
    $address = "\x00" . $ripemd160;
    $checksum = substr(hash('sha256', hash('sha256', $address, true), true), 0, 4);
    $addressWithChecksum = $address . $checksum;
    return base58_encode($addressWithChecksum);
}

function base58_encode($data) {
    $alphabet = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
    $base58 = '';
    $num = gmp_import($data);

    while (gmp_cmp($num, 0) > 0) {
        $rem = gmp_div_qr($num, 58);
        $base58 = $alphabet[gmp_intval($rem[1])] . $base58;
        $num = $rem[0];
    }

    // Добавление ведущих нулей
    foreach (str_split($data) as $byte) {
        if ($byte === "\x00") {
            $base58 = $alphabet[0] . $base58;
        } else {
            break;
        }
    }

    return $base58;
}
?>


<div class="opacity_form">
    <div class="registration_cont">
        <div class="register_button_cont">
            <button class="register_btn" id="register_btn1">Registration</button>
            <button class="register_btn" id="register_btn2">Log in</button>
        </div>
        <div class="registration_page open">
            <form id="registration_form">
                <label class="form_label" for="username">Come up with a username</label>
                <input class="form_input_register" type="text" name="username" placeholder="username" required id="username">
                <label class="form_label" for="password">Come up with a password</label>
                <input class="form_input_register" type="password" name="password" placeholder="password" required id="password">
                <input class="form_input_register" type="hidden" name="action" value="register">
                <button class="submit_btn" type="submit" id="submit_registr">Create an account
                    <div class="rotating-element register1"></div>
                </button>
            </form>
        </div>
        <div class="login_form">
            <form id="login-form">
                <label class="form_label" for="username">Enter your username</label>
                <input class="form_input_register" type="text" name="username" placeholder="username" required id="username">
                <label class="form_label" for="password">Enter your password</label>
                <input class="form_input_register" type="password" name="password" placeholder="password" required id="password">
                <input class="form_input_register" type="hidden" name="action" value="login">
                <button class="submit_btn" type="submit" id="submit_login">Log in to your account
                    <div class="rotating-element register2"></div>
                </button>
            </form>
        </div>
    </div>
</div>

<div id="response-message"></div>
<script>
    const rotatingElement = document.querySelector('.rotating-element.register1');
    const rotatingElement2 = document.querySelector('.rotating-element.register2');
    $(document).ready(function() {
        $('#registration_form').on('submit', function(event) {
            event.preventDefault(); // Останавливаем стандартное действие формы
            rotatingElement.classList.add('loading');

            $.ajax({
                url: 'register.php', // URL для отправки данных
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    console.log(response); // Для отладки
                    // Преобразуем JSON-ответ
                    response = JSON.parse(response);

                    $('#response-message').html(response.message || response.error).fadeIn();

                    // Сохраняем username в локальное хранилище
                    if (response.username) {
                        localStorage.setItem('username', response.username);
                    }

                    // Скрываем сообщение через 4 секунды и перезагружаем страницу
                    setTimeout(function() {
                        $('#response-message').fadeOut();
                        location.reload(); // Обновляем страницу
                    }, 4000);
                },
                error: function(xhr, status, error) {
                    console.error('Ошибка при отправке:', error);
                    $('#response-message').html('Произошла ошибка при отправке формы.').fadeIn();
                }
            });
        });

        $('#login-form').on('submit', function(e) {
            rotatingElement2.classList.add('loading');
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: 'register.php', // URL текущей страницы
                data: $(this).serialize(),
                success: function(response) {
                    const data = JSON.parse(response);
                    $('#response-message').html(data.message || data.error);

                    // Сохраняем username в локальное хранилище
                    if (data.username) {
                        localStorage.setItem('username', data.username);
                    }

                    // Обновление страницы
                    location.reload();
                },
                error: function() {
                    $('#response-message').html('Произошла ошибка.');
                }
            });
        });
    });
</script>

