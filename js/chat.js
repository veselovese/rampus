const ws = new WebSocket('ws://localhost:2346');
const currentUserId = $('#current-user-id').val();
const chatId = $('#chatid-message_input').val();

function simpleStart() {
    $('#textarea-message').focus();
}
simpleStart();

const usrnm = window.location.pathname.split('/')[3];
loadChat(usrnm, '');

function loadChat(query, status = '') {
    $.ajax({
        url: "../back-files/render-messages",
        method: "POST",
        data: {
            'username': query
        },
        success: function (data) {
            $('#success-load-chat').html(data);
            $('#success-load-chat').removeClass('loading');
            $('#chat-loading').removeClass('loading');
            console.log(status)
            if (status != 'opened') {
                console.log(status)
                let sendedData = {
                    action: 'open_chat',
                    current_user_id: currentUserId,
                    chat_id: chatId,
                }
                ws.send(JSON.stringify(sendedData))
            }
        }
    });
}

function loadChatList(query) {
    $.ajax({
        url: "back-files/search-chats",
        method: "POST",
        data: {
            'people': query
        },
        success: function (data) {
            $('#success-search-chats').html(data);
        }
    });
}

function sendMessage(chatId, message, userIdTo) {
    let sendedData = {
        action: 'send_message',
        chat_id: chatId,
        message: message,
        user_id_to: userIdTo
    }
    ws.send(JSON.stringify(sendedData))
    $.ajax({
        url: "../back-files/send-message",
        method: "POST",
        data: {
            'message': message,
            'user_id_to': userIdTo
        },
        success: function (data) {
            // $('#success-load-chat').html(data);
        }
    });
    $('#textarea-message').text('')
    $('#textarea-message').trigger('focus')
}

ws.onmessage = (response) => {
    let responsedData = JSON.parse(response.data)
    if (responsedData.action == 'open_chat') {
        if (responsedData.chat_id == chatId && responsedData.current_user_id != currentUserId) {
            loadChat(usrnm, 'opened')
        }
    } else if (responsedData.action == 'send_message') {
        if (responsedData.chat_id == chatId) {
            loadChat(usrnm, '')
        }
        if (responsedData.user_id_to == currentUserId) {
            var search = $('#search-chats').val();
            if (search != '') {
                loadChatList(search);
            } else {
                loadChatList();
            }
        }
    }
}

$('#textarea-message').keypress(function (e) {
    if (e.which === 13 && !e.shiftKey) {
        e.preventDefault();
        if (($('#textarea-message').text().trim(' ') != '') || ($('#message-image').val().length)) {
            sendMessage(chatId, $('#textarea-message').text(), $('#useridto-message_input').val());

        }
    }
});

$('#textarea-message_sumbit').click(function (e) {
    e.preventDefault();
    if (($('#textarea-message').text().trim(' ') != '') || ($('#message-image').val().length)) {
        sendMessage(chatId, $('#textarea-message').text(), $('#useridto-message_input').val());
    }
});

function textareaMessage(e) {
    const obj = e.target;
    const div = document.getElementById('textarea-message_input');
    if (($('#textarea-message').text().trim(' ') != '') || ($('#message-image').val().length)) {
        $('#textarea-message_sumbit').addClass('active');
        $('#textarea-message_sumbit').removeAttr('disabled');
    } else {
        $('#textarea-message_sumbit').removeClass('active');
        $('#textarea-message_sumbit').attr('disabled');
    }
    div.setAttribute('value', obj.textContent);
}

function textareaMessagePlaceholder(e) {
    if (e.key != ' ') {
        document.getElementById('textarea-message_label').style.display = 'none';
    }
    if ((document.getElementById('textarea-message').textContent.length < 2) && (e.key == "Backspace")) {
        document.getElementById('textarea-message_label').style.display = 'block';
    }
}
function addImageToMessage() {
    $('#message-image').trigger('click');
}

const messageImage = document.getElementById('message-image');
messageImage.addEventListener('change', (e) => {
    const file = e.target.files;

    if (file.length > 0) {
        const [currentFile] = file;
        const fileType = currentFile.type.startsWith('image');
        const reader = new FileReader();
        if (fileType) {
            reader.onload = () => {
                $('#textarea-message').addClass('image-uploaded');
                $('.message-image-delete').css('display', 'flex');
                document.getElementById('current-message-image').src = reader.result;
                if (($('#textarea-message').text().trim(' ') != '') || ($('#message-image').val().length)) {
                    $('#textarea-message_sumbit').addClass('active');
                    $('#textarea-message_sumbit').removeAttr('disabled');
                } else {
                    $('#textarea-message_sumbit').removeClass('active');
                    $('#textarea-message_sumbit').attr('disabled');
                }
                $('#textarea-message').trigger('focus');
            }
        }
        reader.readAsDataURL(currentFile);
    }
})

function clearMessageImage() {
    document.getElementById('current-message-image').src = '';
    $('#message-image').val('');
    $('#textarea-message').removeClass('image-uploaded');
    $('.message-image-delete').css('display', 'none');
    $('#textarea-message_sumbit').removeClass('active');
    $('#textarea-message_sumbit').attr('disabled');
}