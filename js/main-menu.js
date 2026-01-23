const currentUserIdMenu = document.getElementById("menu-script").getAttribute("data-who_is_me");

function updateUnreadChats(path = '', userIdTo = currentUserIdMenu) {
    if (userIdTo == currentUserIdMenu) {
        $.ajax({
            url: path + "back-files/chats/update-user-unread-chats",
            method: "POST",
            data: {},
            success: function (data) {
                $('#notification__unread-chats').text(data)
                $('#notification__unread-chats-mobile').text(data)
            }
        });
    }
}

ws.onmessage = async (response) => {
    let responsedData = JSON.parse(response.data)
    switch (responsedData.action) {
        case 'message_has_sended':
            if (responsedData.user_id_to == currentUserIdMenu) {
                updateUnreadChats()
            }
            break;
    }
}