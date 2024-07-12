$(document).ready(function () {
    $('.unliked').on('click', function() {
        const postId = $(this).attr('id');
        $post = $(this);
        $.ajax({
            url: 'wall',
            type: 'post',
            data: {
                'liked': 1,
                'postId': postId
            },
            success: function (response) {
                $post.find('.like-counter').text(response);
                $post.siblings().find('.like-counter').text(response);
                $post.addClass('hide');
                $post.siblings().removeClass('hide');
            }
        })
    })

    $('.liked').on('click', function() {
        const postId = $(this).attr('id');
        $post = $(this);
        $.ajax({
            url: 'wall',
            type: 'post',
            data: {
                'unliked': 1,
                'postId': postId
            },
            success: function (response) {
                $post.find('.like-counter').text(response);
                $post.siblings().find('.like-counter').text(response);
                $post.addClass('hide');
                $post.siblings().removeClass('hide');
            }
        })
    })
})
