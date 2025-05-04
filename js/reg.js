const passwordInput = document.getElementById('reg__password');
const emailInput = document.getElementById('reg__email');
const idInput = document.getElementById('reg__username');
const password8Sim = document.getElementById('reg__8-sim');
const passwordNum = document.getElementById('reg__num');
const passwordUniqSim = document.getElementById('reg__!?');
const regSubmitButton = document.getElementById('reg_submit-button');

$(document).ready(function () {
    onOrOffId();

    function onOrOffId(query) {
        $.ajax({
            url: "back-files/on-or-off_id",
            method: "POST",
            data: {
                'id': query,
            },
            success: function (flag) {
                if (flag == '') {
                    $('#reg__id_on-or-off').removeClass('show');
                    $('#reg__username').removeClass('off').removeClass('on');
                } else if (flag == 0) {
                    $('#reg__id_on-or-off').text('Ура, такой ID свободен');
                    $('#reg__id_on-or-off').removeClass('off').addClass('show');
                    $('#reg__username').removeClass('off').addClass('on');
                } else if (flag == 'have') {
                    $('#reg__id_on-or-off').text('О нет, такой ID занят');
                    $('#reg__id_on-or-off').addClass('off').addClass('show');
                    $('#reg__username').removeClass('on').addClass('off');
                } else if (flag == 'rus') {
                    $('#reg__id_on-or-off').text('Английские буквы, цифры и _');
                    $('#reg__id_on-or-off').addClass('off').addClass('show');
                    $('#reg__username').removeClass('on').addClass('off');
                } else if (flag == 'length') {
                    $('#reg__id_on-or-off').text('Максимум 16 символов');
                    $('#reg__id_on-or-off').addClass('off').addClass('show');
                    $('#reg__username').removeClass('on').addClass('off');
                }
            }
        });
    }
    $('#reg__username').keyup(function () {
        var search = $(this).val();
        if (search != '') {
            onOrOffId(search);
        }
        else {
            onOrOffId();
        }
    })

    onOrOffEmail();

    function onOrOffEmail(query) {
        $.ajax({
            url: "back-files/on-or-off_email",
            method: "POST",
            data: {
                'email': query,
            },
            success: function (flag) {
                if (flag == '') {
                    $('#reg__email_on-or-off').removeClass('show');
                    $('#reg__email').removeClass('off').removeClass('on');
                } else if (flag == 0) {
                    $('#reg__email_on-or-off').text('Ага, такая почта свободна');
                    $('#reg__email_on-or-off').removeClass('off').addClass('show');
                    $('#reg__email').removeClass('off').addClass('on');
                } else if (flag == 1) {
                    $('#reg__email_on-or-off').text('Эх, такая почта занята');
                    $('#reg__email_on-or-off').addClass('off').addClass('show');
                    $('#reg__email').removeClass('on').addClass('off');
                }
            }
        });
    }
    $('#reg__email').keyup(function () {
        var search = $(this).val();
        if (search != '') {
            onOrOffEmail(search);
        }
        else {
            onOrOffEmail();
        }
    })

    function signUp(email, username, password) {
        $.ajax({
            url: "back-files/sign-up",
            method: "POST",
            data: {
                'email': email,
                'username': username,
                'password': password,
            },
            success: function (flag) {
                if (flag == '@@@') {
                    $('#reg__notify-label').text('Проблемы с сетью, попробуйте позже')
                    $('#reg__notify-username').text('')
                    $('#reg__notify').addClass('reject')
                    setTimeout(() => {
                        $('#auth__notify').removeClass('reject')
                    }, 2000)
                } else if (flag == 'emailAlready') {
                    $('#reg__notify-label').text('Такая почта уже зарегистрирована')
                    $('#reg__notify-username').text('')
                    $('#reg__notify').addClass('reject')
                    setTimeout(() => {
                        $('#reg__notify').removeClass('reject')
                    }, 2000)
                } else if (flag == 'usernameAlready') {
                    $('#reg__notify-label').text('Такой логин уже зарегистрирован')
                    $('#reg__notify-username').text('')
                    $('#reg__notify').addClass('reject')
                    setTimeout(() => {
                        $('#reg__notify').removeClass('reject')
                    }, 2000)
                } else if (flag == 'done') {
                    $('#reg__notify-label').text('Зарегистрировали аккаунт, перенаправляем..')
                    $('#reg__notify-username').text('')
                    $('#reg__notify').addClass('success')
                    setTimeout(() => {
                        window.location.href = 'auth.php'
                    }, 1500)
                }
            },
        });
    }
    $('#reg-button').click(function () {
        const email = $('#reg__email');
        const username = $('#reg__username');
        const password = $('#reg__password');
        if (email.val() == '' || username.val() == '' || password.val() == '') {
            $('#reg__notify-label').text('Все поля должны быть заполнены')
            $('#reg__notify-username').text('')
            $('#reg__notify').addClass('reject')
            if (password.val() == '') {
                password.addClass('off')
                password.focus()
            } else {
                password.removeClass('off')
            }
            if (username.val() == '') {
                username.addClass('off')
                username.focus()
            } else {
                username.removeClass('off')
            }
            if (email.val() == '') {
                email.addClass('off')
                email.focus()
            } else {
                email.removeClass('off')
            }
            setTimeout(() => {
                $('#reg__notify').removeClass('reject')
            }, 2000)
        } else if ((onOrOffId(username.val()) == 0) && (onOrOffEmail(email.val()) == 0)) {
            console.log('dsfsd')
            signUp(email.val(), username.val(), password.val());
        } else {
            console.log(username.val())
            console.log(usernameFlag)
            console.log(onOrOffId(username.val()))
            console.log(email.val())
            console.log(onOrOffEmail(email.val()))
        }
    })
})

function regFormValid() {
    passwordInput.addEventListener('input', () => {
        if (passwordInput.value == '') {
            passwordInput.classList.remove('off')
            passwordInput.classList.remove('on')
        }
        if (passwordInput.value.length > 7) {
            password8Sim.classList.add('done');
        } else {
            passwordInput.classList.remove('off')
            passwordInput.classList.remove('on')
            password8Sim.classList.remove('done');
        }
        
        if (/[0-9]/.test(passwordInput.value)) {
            passwordNum.classList.add('done');
        } else {
            passwordInput.classList.remove('off')
            passwordInput.classList.remove('on')
            passwordNum.classList.remove('done');
        }
        
        if (/[!?]/.test(passwordInput.value)) {
            passwordUniqSim.classList.add('done');
        } else {
            passwordInput.classList.remove('off')
            passwordInput.classList.remove('on')
            passwordUniqSim.classList.remove('done');
        }

        if ((passwordUniqSim.classList.contains('done')) && (passwordNum.classList.contains('done')) && (password8Sim.classList.contains('done'))) {
            passwordInput.classList.add('on');
        }
    })
}

regFormValid();

