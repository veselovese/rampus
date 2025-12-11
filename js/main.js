$(document).ready(function () {
    $('.wall__user-posts.current-post').on('click', '.unliked', function () {
        const postId = $(this).attr('id');
        $post = $(this);
        $.ajax({
            url: '../wall',
            type: 'post',
            data: {
                'liked': 1,
                'postId': postId
            },
            success: function (response) {
                $post.find('.like-counter').text(response);
                $post.siblings().find('.like-counter').text(response);
                $post.addClass('hide');
                $post.siblings('.like-button').removeClass('hide');
            }
        })
    })

    $('.wall__user-posts.current-post').on('click', '.liked', function () {
        const postId = $(this).attr('id');
        $post = $(this);
        $.ajax({
            url: '../wall',
            type: 'post',
            data: {
                'unliked': 1,
                'postId': postId
            },
            success: function (response) {
                $post.find('.like-counter').text(response);
                $post.siblings().find('.like-counter').text(response);
                $post.addClass('hide');
                $post.siblings('.like-button').removeClass('hide');
            }
        })
    })

    $('.wall__user-posts.current-post').on('click', '.unreposted', function () {
        const postId = $(this).attr('id').split('-')[1];
        $post = $(this);
        $.ajax({
            url: '../wall',
            type: 'post',
            data: {
                'reposted': 1,
                'postId': postId
            },
            success: function (response) {
                $post.find('.repost-counter').text(response);
                $post.siblings().find('.repost-counter').text(response);
                $post.addClass('hide');
                $post.siblings('.repost-button').removeClass('hide');
            }
        })
    })

    $('.wall__user-posts.current-post').on('click', '.reposted', function () {
        const postId = $(this).attr('id').split('-')[1];
        $post = $(this);
        $.ajax({
            url: '../wall',
            type: 'post',
            data: {
                'unreposted': 1,
                'postId': postId
            },
            success: function (response) {
                $post.find('.repost-counter').text(response);
                $post.siblings().find('.repost-counter').text(response);
                $post.addClass('hide');
                $post.siblings('.repost-button').removeClass('hide');
            }
        })
    })

    $('.wall__user-posts.current-post').on('click', '.delete-post', function () {
        const postId = $(this).attr('id');
        $deletePost = $(this);
        $.ajax({
            url: '../back-files/delete-post',
            type: 'post',
            data: {
                'post_id': postId,
            },
            success: function (response) {
                window.location = '../wall'
            }
        })
    })

    $('.wall__user-posts.current-post').on('click', '.delete-comment', function () {
        const commentId = $(this).attr('id');
        $deleteComment = $(this);
        $.ajax({
            url: '../back-files/delete-comment',
            type: 'post',
            data: {
                'comment_id': commentId,
            },
            success: function (response) {
                $deleteComment.parent().parent().parent().addClass('deleted')
            }
        })
    })

    searchHashtag();

    function searchHashtag(query) {
        const get = $('#get-status').val();
        $.ajax({
            url: "back-files/search-hashtag",
            method: "POST",
            data: {
                'hashtag': query,
                'get': get
            },
            success: function (data) {
                $('#success-search-hashtag').html(data);
                $('#success-search-hashtag-in-header').html(data);
            }
        });
    }
    $('#search-hashtag').keyup(function () {
        var search = $(this).val();
        if (search != '') {
            searchHashtag(search);
        }
        else {
            searchHashtag();
        }
    })
    $('#search-hashtag-in-header').keyup(function () {
        var search = $(this).val();
        if (search != '') {
            searchHashtag(search);
        }
        else {
            searchHashtag();
        }
    })

    $('#icon-search-hashtag').on('click', function () {
        $('#search-hashtag').trigger('focus');
    })

    $('#icon-search-hashtag-in-header').on('click', function () {
        $('#search-hashtag-in-header').trigger('focus');
        $('#success-search-hashtag-in-header').addClass('focus');
    })

    $('#search-hashtag-in-header').focus(function () {
        $('#success-search-hashtag-in-header').addClass('focus');
    })

    $(document).click(function (e) {
        if ((!$('#search-hashtag-in-header').is(e.target)) && ($('#search-hashtag-in-header').has(e.target).length === 0)
            && (!$('#success-search-hashtag-in-header').is(e.target)) && ($('#success-search-hashtag-in-header').has(e.target).length === 0)
            && (!$('#icon-search-hashtag-in-header').is(e.target)) && ($('#icon-search-hashtag-in-header').has(e.target).length === 0)) {
            $('#success-search-hashtag-in-header').removeClass('focus');
        }
    })
})

function copyLinkToPost(i) {
    navigator.clipboard.writeText('http://localhost/rampus/post/' + i);
}

function showPopup(i) {
    $('#three-dots-popup_' + i).toggleClass('show');
    $('#div-show-three-dots-popup_' + i).toggleClass('show');
}

$(document).on('click', function (e) {
    if (!(e.target.classList[0] === 'div-show-three-dots-popup')) {
        $(this).find('.three-dots-popup').removeClass('show')
        $(this).find('.div-show-three-dots-popup').removeClass('show')
    }
})

function copyLinkToUser(i) {
    navigator.clipboard.writeText('https://rampus.ru/user/' + i);
}

function copyLinkToUserAddReturnMessage(i) {
    navigator.clipboard.writeText('https://rampus.ru/user/' + i);
    $('#copy-link-status').text('Ссылка скопирована').css('color', 'var(--main-accent-color)').css('background-color', 'var(--block-accent-color-210)');
    setTimeout(() => {
        $('#copy-link-status').text('Копировать ссылку').css('color', 'var(--else-text-color-50)').css('background-color', 'var(--block-accent-color-06)');
    }, 2000);
}

function copyLinkToOtherUser(id, username) {
    navigator.clipboard.writeText('https://rampus.ru/user/' + username);
    document.getElementById('three-dots-popup_other-user-info_' + id).classList.toggle('show');
}

function editPost(postId) {
    console.log(postId)
}