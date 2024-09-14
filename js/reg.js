const passwordInput1 = document.getElementById('reg__password_1');
    const passwordInput2 = document.getElementById('reg__password_2');
    const firstNameInput = document.getElementById('reg__first_name');
    const secondNameInput = document.getElementById('reg__second_name');
    const emailInput = document.getElementById('reg__email');
    const idInput = document.getElementById('reg__id');
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
                    $('#reg__id').removeClass('off').removeClass('on');
                } else if (flag == 0) {
                    $('#reg__id_on-or-off').text('Ура, такой ID свободен');
                    $('#reg__id_on-or-off').removeClass('off').addClass('show');
                    $('#reg__id').removeClass('off').addClass('on');
                } else if (flag == 'have') {
                    $('#reg__id_on-or-off').text('О нет, такой ID занят');
                    $('#reg__id_on-or-off').addClass('off').addClass('show');
                    $('#reg__id').removeClass('on').addClass('off');
                } else if (flag == 'rus') {
                    $('#reg__id_on-or-off').text('Только английские буквы, цифры и _');
                    $('#reg__id_on-or-off').addClass('off').addClass('show');
                    $('#reg__id').removeClass('on').addClass('off');
                } else if (flag == 'length') {
                    $('#reg__id_on-or-off').text('Максимум 16 символов');
                    $('#reg__id_on-or-off').addClass('off').addClass('show');
                    $('#reg__id').removeClass('on').addClass('off');
                }
            }
        });
    }
    $('#reg__id').keyup(function () {
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
})

function regFormValid() {
    firstNameInput.addEventListener('input', () => {
        if (firstNameInput.value != '') {
            firstNameInput.classList.add('on')
        }
    })

    secondNameInput.addEventListener('input', () => {
        if (secondNameInput.value != '') {
            secondNameInput.classList.add('on')
        }
    })


    passwordInput1.addEventListener('input', () => {
        if (passwordInput1.value.length > 7) {
            password8Sim.classList.add('done');
        } else {
            password8Sim.classList.remove('done');
        }

        if (/[0-9]/.test(passwordInput1.value)) {
            passwordNum.classList.add('done');
        } else {
            passwordNum.classList.remove('done');
        }

        if (/[!?]/.test(passwordInput1.value)) {
            passwordUniqSim.classList.add('done');
        } else {
            passwordUniqSim.classList.remove('done');
        }

        if ((passwordUniqSim.classList.contains('done')) && (passwordNum.classList.contains('done')) && (password8Sim.classList.contains('done'))) {
            passwordInput1.classList.add('on');
        }
    })

    passwordInput2.addEventListener('input', () => {
        if ((passwordInput2.value == passwordInput1.value) && (passwordInput1.value != '')) {
            passwordInput2.classList.add('on');
        }
    })
}

regFormValid();

