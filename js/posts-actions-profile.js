document.addEventListener('DOMContentLoaded', function () {
    $('.profile__user-posts').on('click', '.delete-post', function () {
        const postId = $(this).attr('id');
        $deletePost = $(this);
        $.ajax({
            url: 'back-files/delete-post',
            type: 'post',
            data: {
                'post_id': postId,
            },
            success: function (response) {
                $deletePost.parent().parent().removeClass('new-post')
                $deletePost.parent().parent().addClass('deleted')
                setTimeout(() => {
                    $deletePost.parent().parent().addClass('hide')
                }, 700)
            }
        })
    })
});

