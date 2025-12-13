$(document).ready(function () {
    const usrnm = window.location.pathname.split('/')[3];

    renderPosts('all');
    function renderPosts(query) {
        $.ajax({
            url: "back-files/render-posts_profile",
            method: "POST",
            data: {
                'filter': query
            },
            success: function (data) {
                $('#success-render-posts').html(data);
            }
        });
    }

    renderOtherPosts('all');
    function renderOtherPosts(query) {
        $.ajax({
            url: "../back-files/render-posts_other-profile",
            method: "POST",
            data: {
                'filter': query,
                'username': usrnm
            },
            success: function (data) {
                $('#success-render-other-posts').html(data);
            }
        });
    }

    $('.profile__user-posts').on('click', '.unliked', function () {
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
                $post.siblings('.like-button').removeClass('hide');
            }
        })
    })

    $('.profile__user-posts').on('click', '.liked', function () {
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
                $post.siblings('.like-button').removeClass('hide');
            }
        })
    })

    $('.profile__user-posts').on('click', '.unreposted', function () {
        const postId = $(this).attr('id').split('-')[1];
        $post = $(this);
        $.ajax({
            url: 'wall',
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

    $('.profile__user-posts').on('click', '.reposted', function () {
        const postId = $(this).attr('id').split('-')[1];
        $post = $(this);
        $.ajax({
            url: 'wall',
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
                $deletePost.parent().parent().addClass('deleted')
            }
        })
    })

    $('.profile__other-user-posts').on('click', '.unliked', function () {
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

    $('.profile__other-user-posts').on('click', '.liked', function () {
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

    $('.profile__other-user-posts').on('click', '.unreposted', function () {
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

    $('.profile__other-user-posts').on('click', '.reposted', function () {
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

    $('#show-reposts').click(() => {
        renderPosts('reposts');
    })
    $('#show-posts').click(() => {
        renderPosts('all');
    })

    $('#show-other-reposts').click(() => {
        renderOtherPosts('reposts');
    })
    $('#show-other-posts').click(() => {
        renderOtherPosts('all');
    })
})

$('#textarea-post').keypress(function (e) {
    if (e.which === 13 && !e.shiftKey) {
        e.preventDefault();
        if ($('#textarea-post').text().trim(' ') != '') {
            $(this).closest('form').submit();
        }
    }
});

$('#create-new-post').on('click', () => {
    console.log($('#create-new-post-title').text())
    if ($('#create-new-post-title').text() == 'Написать пост') {
        $('#create-new-post-title').text('Закрыть')
        $('#create-new-post').addClass('close')
        $('#yours-posts-div').addClass('creating-new-post')
        $('#profile__new-post').addClass('creating-new-post-form')
    } else {
        $('#create-new-post-title').text('Написать пост')
        $('#create-new-post').removeClass('close')
        $('#yours-posts-div').removeClass('creating-new-post')
        $('#profile__new-post').removeClass('creating-new-post-form')
    }


})

function textareaPostPlaceholder(e) {
    if (e.key != ' ') {
        document.getElementById('textarea-post_label').style.display = 'none';
    }
    if ((document.getElementById('textarea-post').textContent.length < 2) && (e.key == "Backspace")) {
        document.getElementById('textarea-post_label').style.display = 'block';
    }
}

function textareaPost(e) {
    const obj = e.target;
    const div = document.getElementById(obj.id + '_input');
    if ($('#textarea-post').text().trim(' ') != '') {
        $('#textarea-post_sumbit').addClass('active');
        $('#textarea-post_sumbit').removeAttr('disabled');
    } else {
        $('#textarea-post_sumbit').removeClass('active');
        $('#textarea-post_sumbit').attr('disabled');
    }
    div.setAttribute('value', obj.textContent);
}

function showPopupUserInfo() {
    document.getElementById('three-dots-popup_user-info').classList.toggle('show');
}

function openFriendsPage(e) {
    if ((!$('.current-friend').is(e.target)) && ($('.current-friend').has(e.target).length === 0)) {
        window.location = "friends";
    }
}

function seeAllPosts() {
    $('.profile_user-post').toggleClass('hide');
    if ($('#see-all-posts').text() == 'Показать все посты') {
        $('#see-all-posts').text('Скрыть посты');
    } else {
        $('#see-all-posts').text('Показать все посты');
    }
}