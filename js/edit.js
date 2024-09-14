function uploadAvatar() {
    $('#user-avatar').trigger('click');
}

const userAvatar = document.getElementById('user-avatar');
userAvatar.addEventListener('change', (e) => {
    const file = e.target.files;

    if (file.length > 0) {
        const [currentFile] = file;
        const fileType = currentFile.type.startsWith('image');
        const reader = new FileReader();
        if (fileType) {
            reader.onload = () => {
                document.getElementById('current-avatar').src = reader.result;
            }
        }
        reader.readAsDataURL(currentFile);
    }
})


const idInput = document.getElementById('reg__id');

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
                    if ($(reg__id).val() == '') {
                        $('#reg__id_on-or-off').text('Ну, не. Это не ID');
                        $('#reg__id_on-or-off').addClass('off').addClass('show');
                        $('#reg__id').removeClass('on').addClass('off');
                    } else {
                        $('#reg__id_on-or-off').removeClass('show');
                        $('#reg__id').removeClass('off').removeClass('on');
                    }
                } else if (flag == 0) {
                    $('#reg__id_on-or-off').text('Ура, такой ID свободен');
                    $('#reg__id_on-or-off').removeClass('off').addClass('show');
                    $('#reg__id').removeClass('off').addClass('on');
                } else if (flag == 'have') {
                    if ($('#reg__id').data()['username'] == query) {
                        $('#reg__id_on-or-off').removeClass('show');
                        $('#reg__id').removeClass('off').removeClass('on');
                    } else {
                        $('#reg__id_on-or-off').text('О нет, такой ID занят');
                        $('#reg__id_on-or-off').addClass('off').addClass('show');
                        $('#reg__id').removeClass('on').addClass('off');
                    }
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
})