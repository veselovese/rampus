$(document).ready(function () {
    let usrnm = window.location.pathname.split("/").filter(entry => entry !== "");
    usrnm = usrnm[usrnm.length - 1]

    renderPosts('all');
    function renderPosts(query) {
        $.ajax({
            url: "back-files/render-posts_profile",
            method: "POST",
            data: {
                'filter': query
            },
            success: function (data) {
                setTimeout(() => {
                    $('#profile-loading-main').removeClass('loading');
                    $('#success-render-posts').html(data);
                }, 300)
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
                setTimeout(() => {
                    $('#profile-loading-main').removeClass('loading');
                    $('#success-render-other-posts').html(data);
                }, 300)
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