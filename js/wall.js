let params = new URLSearchParams(document.location.search);
let search = params.get('search');
let block_show = false;
let isUploading = false;

async function checkPosition() {
    const height = document.body.offsetHeight
    const screenHeight = window.innerHeight

    const scrolled = window.scrollY

    const threshold = height - screenHeight / 4
    const position = scrolled + screenHeight

    if (position >= threshold) {
        await renderPosts('main', search)
    }
}

function throttle(callee, timeout) {
    let timer = null

    return function perform(...args) {
        if (timer) return

        timer = setTimeout(() => {
            callee(...args)

            clearTimeout(timer)
            timer = null
        }, timeout)
    }
}

window.addEventListener('scroll', throttle(checkPosition, 250))
window.addEventListener('resize', throttle(checkPosition, 250))

let isLoading = false

let shouldLoad = true
let $target = $('#wall-loading-posts');
let page = $target.attr('data-page');
let maxPage = 0;

async function renderPosts(filter, search, cleanPage = false) {
    if (cleanPage) {
        $('#wall-loading-posts').addClass('rude-hide').removeClass('loading');
        $('#success-render-posts').html('')
        $('#wall-loading-main').addClass('loading');
        page = 1
        shouldLoad = true
    }

    if (isLoading || !shouldLoad) return
    isLoading = true

    $.ajax({
        url: "back-files/render-posts_wall?page=" + page,
        method: "POST",
        data: {
            'filter': filter,
            'search': search
        },
        success: function (data) {
            setTimeout(() => {
                $('#wall-loading-main').removeClass('loading');
                $('#success-render-posts').html($('#success-render-posts').html() + data);
                $('#wall-loading-posts').removeClass('rude-hide').addClass('loading');

                switch (filter) {
                    case 'main':
                        maxPage = $target.attr('data-max-main')
                        break;
                    case 'timetable':
                        maxPage = $target.attr('data-max-ts')
                        break;
                    case 'all':
                        maxPage = $target.attr('data-max-all')
                        break;
                }
                if (page == maxPage) {
                    $('#wall-loading-posts').removeClass('loading');
                    shouldLoad = false
                }
                page++;
                isLoading = false
                $target.attr('data-page', page);
            }, 300)
        }
    });

}

$(document).ready(function () {
    renderPosts('main', search);

    $('.wall__user-posts').on('click', '.unliked', function () {
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

    $('.wall__user-posts').on('click', '.liked', function () {
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

    $('.wall__user-posts').on('click', '.unreposted', function () {
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

    $('.wall__user-posts').on('click', '.reposted', function () {
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

    $('#wall-filter-main').click(() => {
        renderPosts('main', search, true);
        $unreadMainPost = Number($('#notification-in-filter__unread-main-posts').text())
        $('#notification__unread-posts').text(Number($('#notification__unread-posts').text()) - $unreadMainPost > 0 ? Number($('#notification__unread-posts').text()) - $unreadMainPost : '')
        $('#notification-in-filter__unread-main-posts').addClass('hide')
    })
    $('#wall-filter-all').click(() => {
        renderPosts('all', search, true);
        $unreadAllPost = Number($('#notification-in-filter__unread-all-posts').text())
        $('#notification__unread-posts').text(Number($('#notification__unread-posts').text()) - $unreadAllPost > 0 ? Number($('#notification__unread-posts').text()) - $unreadAllPost : '')
        $('#notification-in-filter__unread-all-posts').addClass('hide')
    })
    $('#wall-filter-timetable').click(() => {
        renderPosts('timetable', search, true);
        $unreadThirtySeventhPost = Number($('#notification-in-filter__unread-thirty-seventh-posts').text())
        $('#notification__unread-posts').text(Number($('#notification__unread-posts').text()) - $unreadThirtySeventhPost > 0 ? Number($('#notification__unread-posts').text()) - $unreadThirtySeventhPost : '')
        $('#notification-in-filter__unread-thirty-seventh-posts').addClass('hide')
    })
    $('#wall-filter-main__mobile').click(() => {
        renderPosts('main', search, true);
        $unreadMainPost = Number($('#notification-in-filter__unread-main-posts-mobile').text())
        if (Number($('#notification__unread-posts-mobile').text()) - $unreadMainPost > 0) {
            $('#notification__unread-posts-mobile').text(Number($('#notification__unread-posts-mobile').text()) - $unreadMainPost)
        } else {
            $('#notification__unread-posts-mobile').text('')
            $('#notification__unread-posts-mobile').removeClass('active')
        }
        $('#notification-in-filter__unread-main-posts-mobile').addClass('hide')
    })
    $('#wall-filter-all__mobile').click(() => {
        renderPosts('all', search, true);
        $unreadAllPost = Number($('#notification-in-filter__unread-all-posts-mobile').text())
        if (Number($('#notification__unread-posts-mobile').text()) - $unreadAllPost > 0) {
            $('#notification__unread-posts-mobile').text(Number($('#notification__unread-posts-mobile').text()) - $unreadAllPost)
        } else {
            $('#notification__unread-posts-mobile').text('')
            $('#notification__unread-posts-mobile').removeClass('active')
        }
        $('#notification-in-filter__unread-all-posts-mobile').addClass('hide')
    })
    $('#wall-filter-timetable__mobile').click(() => {
        renderPosts('timetable', search, true);
        $unreadThirtySeventhPost = Number($('#notification-in-filter__unread-thirty-seventh-mobile').text())
        if (Number($('#notification__unread-posts-mobile').text()) - $unreadThirtySeventhPost > 0) {
            $('#notification__unread-posts-mobile').text(Number($('#notification__unread-posts-mobile').text()) - $unreadThirtySeventhPost)
        } else {
            $('#notification__unread-posts-mobile').text('')
            $('#notification__unread-posts-mobile').removeClass('active')
        }
        $('#notification-in-filter__unread-thirty-seventh-posts-mobile').addClass('hide')
    })
})

function commentButtonClick(i) {
    $('#textarea-comment_' + i).trigger('focus');
}

function seeAllComments(i) {
    $('.comment_div-line_' + i).toggleClass('hide');
    $('.comment_user-comment_' + i).toggleClass('hide');
    $('#have-else-comments_dot_' + i).toggleClass('hide');
    $('#have-else-comments_a_' + i).toggleClass('hide');
    if ($('#see-all-comments_' + i).text() == 'Показать комментарии') {
        $('#see-all-comments_' + i).text('Скрыть');
    } else {
        $('#see-all-comments_' + i).text('Показать комментарии');
    }
}

function textareaPost(e) {
    const obj = e.target;
    const div = document.getElementById(obj.id + '_input');
    if (!isUploading && (($('#textarea-post').text().trim(' ') != '') || ($('#post-image').val().length))) {
        $('#textarea-post_sumbit').addClass('active');
        $('#textarea-post_sumbit').removeAttr('disabled');
    } else {
        $('#textarea-post_sumbit').removeClass('active');
        $('#textarea-post_sumbit').attr('disabled');
    }
    div.setAttribute('value', obj.textContent);
}

function textareaPostPlaceholder(e) {
    if (e.key != ' ') {
        document.getElementById('textarea-post_label').style.display = 'none';
    }
    if ((document.getElementById('textarea-post').textContent.length < 2) && (e.key == "Backspace")) {
        document.getElementById('textarea-post_label').style.display = 'block';
    }
}

function textareaCommentPlaceholder(e, i) {
    document.getElementById('textarea-comment_label_' + i).style.display = 'none';
    if ((document.getElementById('textarea-comment_' + i).textContent.length < 2) && (e.key == "Backspace")) {
        document.getElementById('textarea-comment_label_' + i).style.display = 'block';
    }
}

function textareaComment(e, i) {
    const obj = e.target;
    const div = document.getElementById('textarea-comment_input_' + i);
    if ($('#textarea-comment_' + i).text().trim(' ') != '') {
        $('#textarea-comment_submit_' + i).addClass('active');
        $('#textarea-comment_submit_' + i).removeAttr('disabled');
    } else {
        $('#textarea-comment_submit_' + i).removeClass('active');
        $('#textarea-comment_submit_' + i).attr('disabled');
    }
    div.setAttribute('value', obj.textContent);
}

function showPopupWallFilter() {
    document.getElementById('popup_wall-filter').classList.toggle('show');
    document.getElementById('wall-filter').classList.toggle('show');
    document.getElementById('active').classList.toggle('show');
}

function showPopupWallFilterMobile() {
    document.getElementById('wall-filter-mobile').classList.toggle('show');
    document.getElementById('wall-filter-mobile-info').classList.toggle('show');
    document.getElementById('popup_wall-filter-mobile').classList.toggle('show');
}
