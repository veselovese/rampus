$(document).ready(function () {
    const url = window.location.pathname.split("/").filter(entry => entry !== "");
    renderCurrentPost(url[url.length -1]);

    function renderCurrentPost(postId) {
        $.ajax({
            url: "../back-files/render-current-post",
            method: "POST",
            data: {
                'post-id': postId,
            },
            success: function (data) {
                $('#success-render-posts').html(data);
            }
        });
    }
})