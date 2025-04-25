<button class="header_btn asistent"><span>Online Assistant</span><img src="img/asitent_btn.png">
    <input class="asistent_input" type="checkbox" name="" id="myCheckbox">
    <div class="asistent_cont" id="block2">
        <div class="shapka">
            <img class="asistent_logo" src="img/Frame_74.png" alt="none">
            <div>
                <h1 class="asistent_title">Assistant</h1>
                <p class="asistent_subtitle">Online</p>
            </div>
            <div class="close" id="block1"></div>
        </div>
        <div class="message" id="messages">
            <div class="rotating-element other-element"></div>
        </div>
        <form class="form" id="contactForm" method="post">
            <div class="form__item">
                <textarea class="form__input" id="form__input" name="message" required placeholder="Write a message"></textarea>
            </div>
            <input type="submit" class="form__input btn" id="submit" value="Send">
            <input type="hidden" name="act" value="order">
        </form>
    </div>
</button>
<script>
    $(document).ready(function() {
    $('#contactForm').submit(function(e) {
    e.preventDefault(); 
    
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
</script>