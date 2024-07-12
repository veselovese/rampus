$(document).ready(function(){
    $('.like-button').click(function() {
        const postId = $(this).attr('id');
        $.ajax({
            url: 'wall',
            type: 'post',
            async: false,
            data: {
                'liked': 1,
                'postId': postId
            },
            success: function() {

            }
        })
    })
})