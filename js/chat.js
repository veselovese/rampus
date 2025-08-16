setInterval(() => {
    const usrnm = window.location.pathname.split('/')[3];
    loadChat(usrnm);

    function loadChat(query) {
        $.ajax({
            url: "../back-files/render-messages",
            method: "POST",
            data: {
                'username': query
            },
            success: function (data) {
                $('#success-load-chat').html(data);
            }
        });
    }
}, 500)