$(document).ready(function () {
    searchChats();

    function searchChats(query) {
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
    $('#search-chats').keyup(function () {
        var search = $(this).val();
        if (search != '') {
            searchChats(search);
        } else {
            searchChats();
        }
    })

    $('#icon-search-people').on('click', function () {
        $('#search-chats').trigger('focus');
    })
})

function openChatWithUser(e, i) {
    window.location = "chat/" + i;
}
