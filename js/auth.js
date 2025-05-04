const emailOrUsernameInput = document.getElementById('email_or_username');
const passwordInput = document.getElementById('password');
const authButton = document.getElementById('auth-button');

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
                    $('#auth__notify-label').text('Привет, ')
                    $('#auth__notify-username').text('@' + username)
                    $('#auth__notify').addClass('success')
                    setTimeout(() => {
                        window.location.href = 'wall.php'
                    }, 1500)
                } else if (username === '') {
                    $('#auth__notify-label').text('Неверный логин, почта или пароль')
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
        const emailOrUsername = $('#email_or_username');
        const password = $('#password');
        if (emailOrUsername.val() == '' || password.val() == '') {
            $('#auth__notify-label').text('Все поля должны быть заполнены')
            $('#auth__notify-username').text('')
            $('#auth__notify').addClass('reject')
            if (password.val() == '') {
                password.addClass('off')
                password.focus()
            } else {
                password.removeClass('off')
            }
            if (emailOrUsername.val() == '') {
                emailOrUsername.addClass('off')
                emailOrUsername.focus()
            } else {
                emailOrUsername.removeClass('off')
            }
            setTimeout(() => {
                $('#auth__notify').removeClass('reject')
            }, 2000)
        } else {
            signIn(emailOrUsername.val(), password.val());
        }
    })
})

function authFormValid() {
    emailOrUsernameInput.addEventListener('input', () => {
        if (emailOrUsernameInput.value != '') {
            emailOrUsernameInput.classList.remove('off')
            emailOrUsernameInput.classList.add('on')
        } else {
            emailOrUsernameInput.classList.remove('on')
        }
    })

    passwordInput.addEventListener('input', () => {
        if (passwordInput.value == '') {
            passwordInput.classList.remove('off')
            passwordInput.classList.remove('on')
        } else if (passwordInput.value != '' && passwordInput.value.length > 7) {
            passwordInput.classList.remove('off')
            passwordInput.classList.add('on')
        } else {
            passwordInput.classList.remove('on')
            passwordInput.classList.add('off')
        }
    })
}

authFormValid()