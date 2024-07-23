$(document).ready(function () {
    onOrOffId();

    function onOrOffId(query) {
        $.ajax({
            url: "on-or-off_id",
            method: "POST",
            data: {
                'id': query,
            },
            success: function (flag) {
                if (flag == '') {
                    $('#reg__id_on-or-off').removeClass('show');
                } else if (flag == 0) {
                    $('#reg__id_on-or-off').text('Ура, такой ID свободен');
                    $('#reg__id_on-or-off').removeClass('off').addClass('show');
                    $('#reg__id').removeClass('off');
                } else if (flag == 1) {
                    $('#reg__id_on-or-off').text('О нет, такой ID занят');
                    $('#reg__id_on-or-off').addClass('off').addClass('show');
                    $('#reg__id').addClass('off');
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
            url: "on-or-off_email",
            method: "POST",
            data: {
                'email': query,
            },
            success: function (flag) {
                if (flag == '') {
                    $('#reg__email_on-or-off').removeClass('show');
                } else if (flag == 0) {
                    $('#reg__email_on-or-off').text('Ага, такая почта свободна');
                    $('#reg__email_on-or-off').removeClass('off').addClass('show');
                    $('#reg__email').removeClass('off');
                } else if (flag == 1) {
                    $('#reg__email_on-or-off').text('Эх, такая почта занята');
                    $('#reg__email_on-or-off').addClass('off').addClass('show');
                    $('#reg__email').addClass('off');
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

function passwordValid() {
    const passwordInput = document.getElementById('reg__password_1');
    const password8Sim = document.getElementById('reg__8-sim');
    const passwordNum = document.getElementById('reg__num');
    const passwordUniqSim = document.getElementById('reg__!?');


    passwordInput.addEventListener('input', () => {
        if (passwordInput.value.length > 7) {
            password8Sim.classList.add('done');
        } else {
            password8Sim.classList.remove('done');
        }

        if (/[0-9]/.test(passwordInput.value)) {
            passwordNum.classList.add('done');
        } else {
            passwordNum.classList.remove('done');
        }

        if (/[!?]/.test(passwordInput.value)) {
            passwordUniqSim.classList.add('done');
        } else {
            passwordUniqSim.classList.remove('done');
        }
    })
}

passwordValid();