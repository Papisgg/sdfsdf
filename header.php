
<header class="header" id="header">
    <div class="header_cont">
        <div class="page">
            <div class="logo_cont">
                <img class="logo" src="img/logo.png" alt="" onclick="loadPage('sait.html')">
                <button class="header_btn" id="logo_cont_header_btn">
                    <img class="login_img" src="img/login.svg" alt="">
                    <p class="header_btn_text">Log in</p></button>
                <div class="burger">
                    <div class="chekbox_container">
                        <input class="burger_input" type="checkbox">
                        <div class="burger_lines"></div>
                        <div class="burger_lines_2"></div>
            
                        <div class="burger_menu" id="burger_menu">
                            <div class="page">
                                <a href="#" class="burger_link" onclick="loadPage('abot_us.html')">About us</a>
                                <a href="#" class="burger_link" onclick="loadPage('can_do.html')">What we can do?</a>
                                <a href="#" class="burger_link" onclick="loadPage('Our_employees.html')">Our employees</a>
                                <a href="#" class="burger_link" onclick="loadPage('we_work.html')">List of countries where we work</a>
                                <a href="#" class="burger_link" onclick="loadPage('comments.html')">Comments</a>
                                <button class="header_btn" id="logo_cont_header_btn_burger">
                                    <div class="header_btn_burger_inner">
                                        <img class="login_img" src="img/login.svg" alt="">
                                        <p class="header_btn_text">Log in</p>
                                    </div>
                                </button>
                            </div>
                        </div>
                        
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="page">
        <nav class="nav">
            <div class="nav_cont">
                <a href="#" class="nav_link" onclick="loadPage('abot_us.html')">About us</a>
                <a href="#" class="nav_link" onclick="loadPage('can_do.html')">What we can do?</a>
                <a href="#" class="nav_link" onclick="loadPage('Our_employees.html')">Our employees</a>
                <a href="#" class="nav_link" onclick="loadPage('we_work.html')">List of countries where we work</a>
                <a href="#" class="nav_link" onclick="loadPage('comments.html')">Comments</a>
            </div>
            
        </nav>
    </div>
</header>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('.register_btn');

            // Добавляем класс active первому элементу
            if (buttons.length > 0) {
                buttons[0].classList.add('active');
            }

            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    // Удаляем класс active у всех элементов
                    buttons.forEach(btn => {
                        btn.classList.remove('active');
                    });
                    // Добавляем класс active к текущему элементу
                    this.classList.add('active');
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const registerBtn1 = document.getElementById('register_btn1');
            const registerBtn2 = document.getElementById('register_btn2');
            const registrPage = document.querySelector('.registration_page');
            const loginForm = document.querySelector('.login_form');

            function updateClasses() {
                // Если у первого кнопки есть класс active, добавляем класс open к registr_page
                if (registerBtn1.classList.contains('active')) {
                    registrPage.classList.add('open');
                } else {
                    registrPage.classList.remove('open');
                }

                // Если у второго кнопки есть класс active, добавляем класс open к login_form
                if (registerBtn2.classList.contains('active')) {
                    loginForm.classList.add('open');
                } else {
                    loginForm.classList.remove('open');
                }
            }

            // Обработчик кликов для кнопок
            const buttons = document.querySelectorAll('.register_btn');
            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    // Убираем класс active у всех кнопок
                    buttons.forEach(btn => {
                        btn.classList.remove('active');
                    });
                    // Добавляем класс active к текущему элементу
                    this.classList.add('active');
                    // Обновляем классы open
                    updateClasses();
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const logoButton = document.getElementById('logo_cont_header_btn');
            if (logoButton) {
                logoButton.addEventListener('click', function() {
                    const registrationCont = document.querySelector('.registration_cont');
                    if (registrationCont) {
                        registrationCont.classList.add('open');
                    } else {
                        console.warn('Элемент с классом registration_cont не найден!');
                    }
                });
            } else {
                console.warn('Элемент с ID logo_cont_header_btn не найден!');
            }
        });

        function updateButton(username) {
            // Проверяем, что переменная username не пустая
            if (username && username.trim() !== '') {
                // Находим кнопку по ID
                var button = document.getElementById('logo_cont_header_btn');
                
                // Находим <p> с классом header_btn_text внутри кнопки
                var buttonText = button.querySelector('.header_btn_text'); // Используем querySelector
                // Находим <img> с классом login_img внутри кнопки
                var loginImage = button.querySelector('.login_img'); // Используем querySelector

                // Обновляем текст внутри <p>
                buttonText.innerText =  username;
                // Обновляем путь к изображению
                loginImage.src = 'img/user-profile-03.svg'; // Замените на нужный путь к изображению

                // Добавляем обработчик клика на кнопку
                button.onclick = function() {
                    loadPage("lk.html"); // Вызов функции loadPage при клике
                };
            } else {
                console.log('Username не задан или пуст'); // Логгируем сообщение, если username пустой
            }
        }

    </script>
    
    <div id="response-message">
        <?php
            // Проверка наличия username в сессии и его отображение
            
            if (isset($_SESSION['username'])) {
                $username = htmlspecialchars($_SESSION['username']);
                echo "<script>
                        var username = '$username'; // Сохраняем username в переменной
                        updateButton(username); // Вызов функции для обновления кнопки
                      </script>";
            }
        ?>
    </div>

    

    <script>
        // Функция для обновления состояния кнопки каждый 3 секунды
        setInterval(function() {
            // Проверка наличия username в сессии
            <?php
                if (isset($_SESSION['username'])) {
                    $username = htmlspecialchars($_SESSION['username']);
                    echo "var username = '$username'; 
                          updateButton(username);";
                }
            ?>
        }, 3000);
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const logoButton = document.getElementById('logo_cont_header_btn_burger');
            if (logoButton) {
                logoButton.addEventListener('click', function() {
                    const registrationCont = document.querySelector('.opacity_form');
                    if (registrationCont) {
                        registrationCont.classList.add('open');
                    } else {
                        console.warn('Элемент с классом opacity_form не найден!');
                    }
                });
            } else {
                console.warn('Элемент с ID logo_cont_header_btn не найден!');
            }
        });

        function updateButtonMobile(username) {
            // Проверяем, что переменная username не пустая
            if (username && username.trim() !== '') {
                // Находим кнопку по ID
                var button = document.getElementById('logo_cont_header_btn_burger');
                
                // Находим <p> с классом header_btn_text внутри кнопки
                var buttonText = button.querySelector('.header_btn_text'); // Используем querySelector
                // Находим <img> с классом login_img внутри кнопки
                var loginImage = button.querySelector('.login_img'); // Используем querySelector

                // Обновляем текст внутри <p>
                buttonText.innerText =  username;
                // Обновляем путь к изображению
                loginImage.src = 'img/user-profile-03.svg'; // Замените на нужный путь к изображению

                // Добавляем обработчик клика на кнопку
                button.onclick = function() {
                    loadPage("lk.html"); // Вызов функции loadPage при клике
                };
            } else {
                console.log('Username не задан или пуст'); // Логгируем сообщение, если username пустой
            }
        }

        updateButtonMobile(username);

        document.querySelector('.opacity_form').addEventListener('click', function(event) {
            if (event.target === this) {
                this.classList.remove('open');
            }
        });

        
    </script>
</header>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="script.js"></script>
