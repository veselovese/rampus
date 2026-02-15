$(document).ready(function () {
    const url = window.location.pathname.split("/").filter(entry => entry !== "");
    renderCurrentPost(url[url.length - 1]);

    function renderCurrentPost(postId) {
        $.ajax({
            url: "../back-files/render-current-post",
            method: "POST",
            data: {
                'post-id': postId,
            },
            success: function (data) {
                setTimeout(() => {
                    $('#wall-loading-main').removeClass('loading');
                    $('#success-render-posts').html(data);
                }, 300)
            }
        });
    }
})