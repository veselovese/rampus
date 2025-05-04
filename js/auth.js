$(document).ready(function () {
    function signIn(eOrP, password, request) {
        $.ajax({
            url: "back-files/sign-in?request=profile",
            method: "POST",
            data: {
                'email_or_username': eOrP,
                'password': password,
            },
            success: function (username) {
                if (username == '@@@') {
                    $('#auth__notify-label').text('Проблемы с сетью, попробуйте позже')
                    $('#auth__notify-username').text('')
                    $('#auth__notify').addClass('reject')
                    setTimeout(() => {
                        $('#auth__notify').removeClass('reject')
                    }, 2000)
                }
                if (username !== '') {
                    $('#auth__notify-label').text('Вы авторизировались под пользователем ')
                    $('#auth__notify-username').text(username)
                    $('#auth__notify').addClass('success')
                    setTimeout(() => {
                        window.location.href = 'wall.php'
                    }, 1500)
                } else if (username === '') {
                    $('#auth__notify-label').text('Неверный ID, почта или пароль')
                    $('#auth__notify-username').text('')
                    $('#auth__notify').addClass('reject')
                    setTimeout(() => {
                        $('#auth__notify').removeClass('reject')
                    }, 2000)
                }
            },
        });
    }
    $('#auth-button').click(function () {
        const email_or_username = $('#email_or_username').val();
        const password = $('#password').val();
        signIn(email_or_username, password);
    })
})