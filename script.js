

function get_address() {
    $.ajax({
        url: 'newadres.php',
        type: 'GET',
        success: function(data) {
            ;
        }
    });
}

function get_transaction() {
    $.ajax({
        url: 'transaction.php',
        type: 'GET',
        success: function(data) {
            ;
        }
    });
}

get_transaction()

$(document).ready(function(){
    $('#balans_form').on('submit', function(event){
        event.preventDefault(); // Останавливаем стандартное действие формы
        this.submit(); // Отправляем форму
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // Функция для обновления высоты и установки margin-top
    function updateElementPosition() {
        const element = document.getElementById('header');
        if (element) {
            const elementHeight = element.offsetHeight;
            console.log('Высота элемента:', elementHeight);
            const element2 = document.getElementById('burger_menu');
            if (element2) {
                element2.style.top = (elementHeight) + 'px';
            } else {
                console.log('Элемент с ID "burger_menu" не найден');
            }
        } else {
            console.log('Элемент с ID "header" не найден');
        }
    }

    // Инициализация
    updateElementPosition();
    setInterval(updateElementPosition, 1500);
    const rotatingElement3 = document.querySelector('.rotating-element.other-element');
    // Функция для инициализации формы "Online Assistant"
    function initContactForm() {
        $('#contactForm').on('submit', function(event) {
            rotatingElement3.classList.add('loading');
            event.preventDefault();
            $.ajax({
                type: 'POST',
                url: '', 
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        const message = $('textarea[name="message"]').val();
                        $('#messages').append('<div class="my_message"><h1 class="message_text">' + message + '</h1></div>');
                        $('textarea[name="message"]').val(''); // Очистка текстового поля
                    } else {
                        $('#messages').append('<div class="asisten_message"><h1 class="message_text">' + response.message + '</h1></div>');
                    }
                },
                error: function(xhr, status, error) {
                    $('#messages').append('<div><b>Ошибка: ' + error + '</b></div>');
                }
            });
        });

        function getMessages() {
            $.ajax({
                url: 'get_messages.php',
                type: 'GET',
                success: function(data) {
                    $('#messages').append(data);
                    // Запускаем следующую проверку только после успешного ответа
                    setTimeout(getMessages, 10000);
                },
                error: function() {
                    // В случае ошибки можно снова вызвать getMessages через 5 секунд
                    setTimeout(getMessages, 5000);
                }
            });
        }
        
        // Запускаем первый вызов функции
        getMessages();
    }

   

    function bindReviewNavigation() {
        const containers = document.querySelectorAll('.revive_container');
        let currentIndex = 0;
    
        function updateDisplay() {
            containers.forEach((div, index) => {
                div.classList.remove('active');
                if (index === currentIndex) {
                    div.classList.add('active');
                }
            });
        }
    
        document.getElementById('rightButton').addEventListener('click', function() {
            currentIndex = (currentIndex + 1) % containers.length;
            updateDisplay();
        });
    
        document.getElementById('leftButton').addEventListener('click', function() {
            currentIndex = (currentIndex - 1 + containers.length) % containers.length;
            updateDisplay();
        });
    
        updateDisplay();
    }
    
    function asistentClose() {
        const block1 = document.getElementById('block1');
        const checkbox = document.getElementById('myCheckbox');

        block1.addEventListener('click', () => {
            // Скрыть block2

            // Убрать галочку с инпута
            checkbox.checked = false;
        });
    }
    
    // Привязываем обработчики событий при загрузке страницы
    asistentClose();
    bindReviewNavigation();

    

    // Корректировка отступа
    function adjustMargin() {
        var height = document.querySelector('.header').offsetHeight;
        document.querySelector('.main').style.marginTop = height + "px";
    }

    window.addEventListener('load', adjustMargin);
    setInterval(adjustMargin, 1500);

    // Поведение для формы отправки данных
    function formCuntry() {
        const forms = document.querySelectorAll('.form_write');
    
        forms.forEach(form => {
            form.addEventListener('submit', function(event) {
                event.preventDefault(); // Предотвращаем обновление страницы
        
                // Создаем объект FormData для отправки данных формы
                const formData = new FormData(form);
                formData.append('act', 'form'); // Добавляем действие
        
                // Отправляем запрос на сервер
                fetch('index.php', { // Замените на путь к вашему PHP скрипту
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    // Обработка ответа от сервера
                    if (data.status === 'success') {
                        alert(data.message); // Выводим сообщение об успехе
                        form.reset(); // Очищаем форму
                    } else {
                        alert(data.message); // Выводим сообщение об ошибке
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    alert('Произошла ошибка при отправке формы. Пожалуйста, попробуйте еще раз.');
                });
            });
        });
    }

    function usernameChange(username) {
        var usernameElement = document.querySelector('.username');
        if (usernameElement) {
            usernameElement.textContent = username; // Устанавливаем значение сразу после загрузки
        } else {
            console.error('.username элемент не найден');
        }
    }

    function setupReplenishButton() {
        const replenishButton = document.querySelector('.replenish');
        const transactionForm = document.querySelector('.transaction_form_cont');
    
        replenishButton.addEventListener('click', function() {
            transactionForm.classList.toggle('open');
        });
    }

    function closeReplenishButton() {
        const replenishButton = document.querySelector('.replenish_close');
        const transactionForm = document.querySelector('.transaction_form_cont');
    
        replenishButton.addEventListener('click', function() {
            transactionForm.classList.remove('open');
        });
    }

    function copyToClipboard() {
        const inputs = document.querySelectorAll('.transaction_form_input'); // Получаем все элементы
    
        inputs.forEach(function(input1) { // Перебираем каждый элемент
            input1.addEventListener('click', function() {
                // Получение значения из input1
                const textToCopy = input1.value;
    
                // Копирование текста в буфер обмена
                navigator.clipboard.writeText(textToCopy).then(function() {
                    alert('Текст скопирован в буфер обмена: ' + textToCopy);
                }, function(err) {
                    alert('Ошибка при копировании текста: ' + err);
                });
            });
        });
    }
    
    

    function updateBalance(summa) {
        // Элемент с классом balance
        var balanceElement = document.querySelector('.balance');
    
        if (summa !== null && summa !== 0) {
            if (balanceElement) {
                balanceElement.textContent = summa;
            } else {
                console.error("Элемент с классом balance не найден.");
            }
        } else {
            console.error("Значение summa не найдено или равно нулю.");
        }
    }

    function initTransactionForm() {
        document.getElementById('transaction-form').addEventListener('submit', function(event) {
            event.preventDefault(); // Предотвратить перезагрузку страницы
            console.log('Форма отправлена');
    
            const amount = event.target.amount.value;
            const comment = event.target.comment.value;
            const userId = 0; // Получите userId из сессии, например, через PHP
            const createdAt = new Date().toISOString(); // Получить текущее время
    
            // Отправка данных на сервер
            fetch('savetransaqtion.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    amount: amount,
                    comment: comment,
                    userId: userId,
                    createdAt: createdAt
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Ответ от сервера:', data);
                if (data.success) {
                    // Показать уведомление
                    const successDiv = document.querySelector('.sucsess');
                    successDiv.innerText = 'Ожидайте подтверждения транзакции, сразу после подтверждения ваш баланс будет обновлён.';
                    successDiv.style.display = 'block';
    
                    // Скрыть уведомление через 4 секунды
                    setTimeout(() => {
                        successDiv.style.display = 'none';
                    }, 4000);
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
            });
        });
    }

    function insertValue(newValue) {
        const inputElement = document.querySelector('.comment'); // Находим элемент с классом comment
        if (inputElement) {
            inputElement.value = newValue; // Устанавливаем новое значение
        }
    }
    
     // Загрузка страницы
    window.loadPage = function(page) {
        fetch(page)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.text();
            })
            .then(data => {
                document.getElementById('content').innerHTML = data;
                localStorage.setItem('lastPage', page); // Сохранение последней загруженной страницы
                bindReviewNavigation();
                formCuntry();  
                asistentClose();
                usernameChange(username);
                updateBalance(summa);
                insertValue(comment);
                setupReplenishButton();
                closeReplenishButton()
                copyToClipboard();
                initTransactionForm();
            })
            .catch(error => {
                console.error('Ошибка при загрузке страницы:', error);
            });
        
    };
    
    window.onload = function() {
    formCuntry();  
    asistentClose();
    bindReviewNavigation();
    initContactForm(); // Инициализация формы на главной странице
    usernameChange(username);
    updateBalance(summa);
    insertValue(comment);
    setupReplenishButton();
    closeReplenishButton()
    copyToClipboard();
    initTransactionForm();
};
});



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
    const form = document.getElementById('registration_form');
    const button = document.getElementById('submit_registr');

    form.addEventListener('input', function() {
        const allFilled = Array.from(form.elements).every(input => input.value.trim() !== '');
        button.style.backgroundColor = allFilled ? 'rgba(91, 39, 211, .53)' : '#5B27D3';
    });

});

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('login-form');
    const button = document.getElementById('submit_login');

    form.addEventListener('input', function() {
        const allFilled = Array.from(form.elements).every(input => input.tagName !== 'BUTTON' && input.value.trim() !== '');
        button.style.backgroundColor = allFilled ? 'rgba(91, 39, 211, .53)' : '#5B27D3'; // Замените '#CCCCCC' на нужный цвет для неактивного состояния
    });

});

document.addEventListener('DOMContentLoaded', function() {
    const logoButton = document.getElementById('logo_cont_header_btn');
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

document.querySelector('.opacity_form').addEventListener('click', function(event) {
    if (event.target === this) {
        this.classList.remove('open');
    }
});

function updateButton(username) {
    // Проверяем, что переменная username не пустая
    if (username && username.trim() !== '') {
        // Находим кнопку по ID
        var button = document.getElementById('logo_cont_header_btn');
        // Находим <p> с классом header_btn_text внутри кнопки
        var buttonText = button.querySelectorAll('.header_btn_text');
        // Находим <img> с классом login_img внутри кнопки
        var loginImage = button.querySelectorAll('.login_img');

        // Обновляем текст внутри <p>
        buttonText.innerText = 'Добро пожаловать, ' + username + '!';
        // Обновляем путь к изображению
        loginImage.src = 'path/to/your/image.png'; // Замените на нужный путь к изображению

        // Добавляем обработчик клика на кнопку
        button.onclick = function() {
            loadPage("lk.html"); // Вызов функции loadPage при клике
        };
    } else {
        console.log('Username не задан или пуст'); // Логгируем сообщение, если username пустой
    }
}


