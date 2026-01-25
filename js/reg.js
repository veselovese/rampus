const passwordInput = document.getElementById('reg__password');
const emailInput = document.getElementById('reg__email');
const idInput = document.getElementById('reg__username');
const password8Sim = document.getElementById('reg__8-sim');
const passwordNum = document.getElementById('reg__num');
const passwordUniqSim = document.getElementById('reg__!?');
const regSubmitButton = document.getElementById('reg_submit-button');

function simpleStart() {
    $('#reg__email').focus();
}

simpleStart();

$(document).ready(function () {
    let emailValidFlag = false;
    let idValidFlag = false;
    let passwordValidFlag = false;

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
                    idValidFlag = false;
                } else if (flag == 0) {
                    $('#reg__id_on-or-off').text('Ура, такой ID свободен');
                    $('#reg__id_on-or-off').removeClass('off').addClass('show');
                    $('#reg__username').removeClass('off').addClass('on');
                    idValidFlag = true;
                } else if (flag == 'have') {
                    $('#reg__id_on-or-off').text('О нет, такой ID занят');
                    $('#reg__id_on-or-off').addClass('off').addClass('show');
                    $('#reg__username').removeClass('on').addClass('off');
                    idValidFlag = false;
                } else if (flag == 'rus') {
                    $('#reg__id_on-or-off').text('Английские буквы, цифры и _');
                    $('#reg__id_on-or-off').addClass('off').addClass('show');
                    $('#reg__username').removeClass('on').addClass('off');
                    idValidFlag = false;
                } else if (flag == 'length') {
                    $('#reg__id_on-or-off').text('Максимум 16 символов');
                    $('#reg__id_on-or-off').addClass('off').addClass('show');
                    $('#reg__username').removeClass('on').addClass('off');
                    idValidFlag = false;
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
                    // $('#reg__email').removeClass('off').removeClass('on');
                    emailValidFlag = false;
                } else if (flag == 0 && emailInput.validity.valid) {
                    $('#reg__email_on-or-off').text('Ага, такая почта свободна');
                    $('#reg__email_on-or-off').removeClass('off').addClass('show');
                    $('#reg__email').removeClass('off').addClass('on');
                    emailValidFlag = true;
                } else if (flag == 1 && emailInput.validity.valid) {
                    $('#reg__email_on-or-off').text('Эх, такая почта занята');
                    $('#reg__email_on-or-off').addClass('off').addClass('show');
                    $('#reg__email').removeClass('on').addClass('off');
                    emailValidFlag = false;
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

    regFormValid();

    function regFormValid() {
        passwordInput.addEventListener('input', () => {
            if (passwordInput.value == '') {
                passwordInput.classList.remove('off')
                passwordInput.classList.remove('on')
                passwordValidFlag = false;
            }

            if (passwordInput.value.length > 7) {
                password8Sim.classList.add('done');
            } else {
                passwordInput.classList.remove('off')
                passwordInput.classList.remove('on')
                password8Sim.classList.remove('done');
                passwordValidFlag = false;
            }

            if (/[0-9]/.test(passwordInput.value)) {
                passwordNum.classList.add('done');
            } else {
                passwordInput.classList.remove('off')
                passwordInput.classList.remove('on')
                passwordNum.classList.remove('done');
                passwordValidFlag = false;
            }

            if (/[!?]/.test(passwordInput.value)) {
                passwordUniqSim.classList.add('done');
            } else {
                passwordInput.classList.remove('off')
                passwordInput.classList.remove('on')
                passwordUniqSim.classList.remove('done');
                passwordValidFlag = false;
            }

            if ((passwordUniqSim.classList.contains('done')) && (passwordNum.classList.contains('done')) && (password8Sim.classList.contains('done'))) {
                passwordInput.classList.add('on');
                passwordValidFlag = true;
            }
        })
    }

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
                    $('#reg__notify-label').text('Зарегистрировали аккаунт @' + username + ', перенаправляем..')
                    $('#reg__notify-username').text('')
                    $('#reg__notify').addClass('success')
                    setTimeout(() => {
                        window.location.href = 'auth?reg=' + username
                    }, 2000)
                }
            },
        });
    }

    function submitValid() {
        const email = $('#reg__email');
        const username = $('#reg__username');
        const password = $('#reg__password');
        if (email.val() == '' && username.val() == '' && password.val() == '') {
            $('#reg__notify-label').text('Все поля должны быть заполнены')
            $('#reg__notify-username').text('')
            $('#reg__notify').addClass('reject')
            setTimeout(() => {
                $('#reg__notify').removeClass('reject')
            }, 2000)
            email.addClass('off')
            username.addClass('off')
            password.addClass('off')
            email.focus()
        } else if ((emailValidFlag == false || !emailInput.validity.valid) && (email.val() != '')) {
            $('#reg__notify').addClass('reject')
            email.addClass('off')
            username.removeClass('off')
            password.removeClass('off')
            email.focus()
            $('#reg__notify-label').text('Некорректная почта')
            $('#reg__notify-username').text('')
            setTimeout(() => {
                $('#reg__notify').removeClass('reject')
            }, 2000)
        } else if (email.val() == '') {
            $('#reg__notify').addClass('info')
            email.removeClass('off')
            username.removeClass('off')
            password.removeClass('off')
            email.focus()
            $('#reg__notify-label').text('Заполните поле почты')
            $('#reg__notify-username').text('')
            setTimeout(() => {
                $('#reg__notify').removeClass('info')
            }, 2000)
        } else {
            email.removeClass('off')
            username.focus()
            if ((idValidFlag == false) && (username.val() != '')) {
                $('#reg__notify').addClass('reject')
                username.addClass('off')
                email.removeClass('off')
                password.removeClass('off')
                username.focus()
                $('#reg__notify-label').text('Некорректный логин')
                $('#reg__notify-username').text('')
                setTimeout(() => {
                    $('#reg__notify').removeClass('reject')
                }, 2000)
            } else if (username.val() == '') {
                $('#reg__notify').addClass('info')
                // username.addClass('off')
                email.removeClass('off')
                password.removeClass('off')
                username.focus()
                $('#reg__notify-label').text('Заполните поле логина')
                $('#reg__notify-username').text('')
                setTimeout(() => {
                    $('#reg__notify').removeClass('info')
                }, 2000)
            } else {
                username.removeClass('off')
                password.focus()
                if ((passwordValidFlag == false) && (password.val() != '')) {
                    $('#reg__notify-label').text('Некорректный пароль')
                    $('#reg__notify-username').text('')
                    $('#reg__notify').addClass('reject')
                    password.addClass('off')
                    username.removeClass('off')
                    email.removeClass('off')
                    password.focus()
                    setTimeout(() => {
                        $('#reg__notify').removeClass('reject')
                    }, 2000)
                } else if (password.val() == '') {
                    $('#reg__notify').addClass('info')
                    // password.addClass('off')
                    username.removeClass('off')
                    email.removeClass('off')
                    password.focus()
                    $('#reg__notify-label').text('Заполните поле пароля')
                    $('#reg__notify-username').text('')
                    setTimeout(() => {
                        $('#reg__notify').removeClass('info')
                    }, 2000)
                } else {
                    password.removeClass('off')
                }
            }
        }
        if (idValidFlag == true && emailValidFlag == true && emailInput.validity.valid && passwordValidFlag == true) {
            signUp(email.val(), username.val(), password.val());
        }
    }

    $('#reg-button').click(() => { submitValid() })
    $('#reg__email').keypress(function (e) {
        if (e.which === 13 && !e.shiftKey) {
            e.preventDefault();
            submitValid();
        }
    });
    $('#reg__username').keypress(function (e) {
        if (e.which === 13 && !e.shiftKey) {
            e.preventDefault();
            submitValid();
        }
    });
    $('#reg__password').keypress(function (e) {
        if (e.which === 13 && !e.shiftKey) {
            e.preventDefault();
            submitValid();
        }
    });
})
