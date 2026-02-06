$(document).ready(function () {
    let params = new URLSearchParams(document.location.search);
    let search = params.get('search');
    renderPosts('main', search);

    function renderPosts(query, search) {
        $.ajax({
            url: "back-files/render-posts_wall",
            method: "POST",
            data: {
                'filter': query,
                'search': search
            },
            success: function (data) {
                $('#success-render-posts').html(data);
            }
        });
    }

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

    $('.wall__user-posts').on('click', '.delete-post', function () {
        const postId = $(this).attr('id');
        $deletePost = $(this);
        $.ajax({
            url: 'back-files/delete-post',
            type: 'post',
            data: {
                'post_id': postId,
            },
            success: function (response) {
                $deletePost.parent().parent().parent().addClass('deleted')
            }
        })
    })

    $('.wall__user-posts').on('click', '.delete-comment', function () {
        const commentId = $(this).attr('id');
        $deleteComment = $(this);
        $.ajax({
            url: 'back-files/delete-comment',
            type: 'post',
            data: {
                'comment_id': commentId,
            },
            success: function (response) {
                $deleteComment.parent().parent().parent().addClass('deleted')
            }
        })
    })

    $('#wall-filter-main').click(() => {
        renderPosts('main', search);
        $unreadMainPost = Number($('#notification-in-filter__unread-main-posts').text())
        $('#notification__unread-posts').text(Number($('#notification__unread-posts').text()) - $unreadMainPost > 0 ? Number($('#notification__unread-posts').text()) - $unreadMainPost : '')
        $('#notification-in-filter__unread-main-posts').addClass('hide')
    })
    $('#wall-filter-all').click(() => {
        renderPosts('all', search);
        $unreadAllPost = Number($('#notification-in-filter__unread-all-posts').text())
        $('#notification__unread-posts').text(Number($('#notification__unread-posts').text()) - $unreadAllPost > 0 ? Number($('#notification__unread-posts').text()) - $unreadAllPost : '')
        $('#notification-in-filter__unread-all-posts').addClass('hide')
    })
    $('#wall-filter-timetable').click(() => {
        renderPosts('timetable', search);
        $unreadThirtySeventhPost = Number($('#notification-in-filter__unread-thirty-seventh-posts').text())
        $('#notification__unread-posts').text(Number($('#notification__unread-posts').text()) - $unreadThirtySeventhPost > 0 ? Number($('#notification__unread-posts').text()) - $unreadThirtySeventhPost : '')
        $('#notification-in-filter__unread-thirty-seventh-posts').addClass('hide')
    })
    $('#wall-filter-main__mobile').click(() => {
        renderPosts('main', search);
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
        renderPosts('all', search);
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
        renderPosts('timetable', search);
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

$('#textarea-post').keypress(function (e) {
    if (e.which === 13 && !e.shiftKey) {
        e.preventDefault();
        if (($('#textarea-post').text().trim(' ') != '') || ($('#post-image').val().length)) {
            $(this).closest('form').submit();
        }
    }
});

$('.wall__user-posts').keypress('.textarea-comment', function (e) {
    if (e.which === 13 && !e.shiftKey) {
        e.preventDefault();
        if ($('.textarea-comment').text().trim(' ') != '') {
            $(`#${e.target.id}`).closest('form').submit();
        }
    }
});

function commentButtonClick(i) {
    $('#textarea-comment_' + i).trigger('focus');
}

function seeAllComments(i) {
    $('.comment_div-line_' + i).toggleClass('hide');
    $('.comment_user-comment_' + i).toggleClass('hide');
    if ($('#see-all-comments_' + i).text() == 'Показать все комментарии') {
        $('#see-all-comments_' + i).text('Скрыть комментарии');
    } else {
        $('#see-all-comments_' + i).text('Показать все комментарии');
    }
}

function textareaPost(e) {
    const obj = e.target;
    const div = document.getElementById(obj.id + '_input');
    if (($('#textarea-post').text().trim(' ') != '') || ($('#post-image').val().length)) {
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

function addImageToPost() {
    $('#post-image').trigger('click');
}

const postImage = document.getElementById('post-image');
postImage.addEventListener('change', (e) => {
    const files = Array.from(e.target.files);
    console.log(files)

    if (files.length > 0) {
        files.forEach((file, index) => {
            const fileType = file.type.startsWith('image');

            if (fileType) {
                const reader = new FileReader();

                reader.onload = (event) => {
                    const img = document.createElement('img');
                    img.src = event.target.result;
                    img.className = 'current-post-image';

                    const currentPostImageDiv = document.createElement('div');
                    currentPostImageDiv.className = 'current-post-image-div';

                    const postImageDelete = document.createElement('div');
                    postImageDelete.className = 'post-image-delete';
                    postImageDelete.setAttribute('onClick', 'clearPostImage()');

                    const svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
                    svg.setAttribute("width", "10");
                    svg.setAttribute("height", "10");
                    svg.setAttribute("viewBox", "0 0 10 10");
                    svg.setAttribute("fill", "none");
                    const path = document.createElementNS(svg.namespaceURI, "path");
                    path.setAttribute("d", "M0.191016 8.88671C-0.0636719 9.14141 -0.0636719 9.55428 0.191016 9.80898C0.445703 10.0637 0.858643 10.0637 1.11333 9.80898L0.191016 8.88671ZM5.46114 5.46114C5.71584 5.20644 5.71584 4.79357 5.46114 4.53888C5.20644 4.28418 4.79357 4.28418 4.53888 4.53888L5.46114 5.46114ZM4.53888 4.53888C4.28418 4.79357 4.28418 5.20644 4.53888 5.46114C4.79357 5.71584 5.20644 5.71584 5.46114 5.46114L4.53888 4.53888ZM9.80898 1.11333C10.0637 0.858644 10.0637 0.445703 9.80898 0.191016C9.55428 -0.0636719 9.14141 -0.0636719 8.88671 0.191016L9.80898 1.11333ZM5.46114 4.53888C5.20644 4.28418 4.79357 4.28418 4.53888 4.53888C4.28418 4.79357 4.28418 5.20644 4.53888 5.46114L5.46114 4.53888ZM8.88671 9.80898C9.14141 10.0637 9.55428 10.0637 9.80898 9.80898C10.0637 9.55428 10.0637 9.14141 9.80898 8.88671L8.88671 9.80898ZM4.53888 5.46114C4.79357 5.71584 5.20644 5.71584 5.46114 5.46114C5.71584 5.20644 5.71584 4.79357 5.46114 4.53888L4.53888 5.46114ZM1.11333 0.191016C0.858643 -0.0636719 0.445703 -0.0636719 0.191016 0.191016C-0.0636719 0.445703 -0.0636719 0.858644 0.191016 1.11333L1.11333 0.191016ZM1.11333 9.80898L5.46114 5.46114L4.53888 4.53888L0.191016 8.88671L1.11333 9.80898ZM5.46114 5.46114L9.80898 1.11333L8.88671 0.191016L4.53888 4.53888L5.46114 5.46114ZM4.53888 5.46114L8.88671 9.80898L9.80898 8.88671L5.46114 4.53888L4.53888 5.46114ZM5.46114 4.53888L1.11333 0.191016L0.191016 1.11333L4.53888 5.46114L5.46114 4.53888Z");
                    svg.appendChild(path);
                    postImageDelete.append(svg);

                    currentPostImageDiv.append(postImageDelete);
                    currentPostImageDiv.append(img);
                    $('.current-post-images-div').append(currentPostImageDiv);

                    $('.current-post-images-div').addClass('image-uploaded');
                    $('#textarea-post').addClass('image-uploaded');
                    $('#textarea-post_sumbit').addClass('image-uploaded');
                    $('.current-post-image-div').addClass('image-uploaded');
                    $('.post-image-delete').css('display', 'flex');

                    if (($('#textarea-post').text().trim() !== '') || files.length > 0) {
                        $('#textarea-post_sumbit').addClass('active');
                        $('#textarea-post_sumbit').prop('disabled', false);
                    } else {
                        $('#textarea-post_sumbit').removeClass('active');
                        $('#textarea-post_sumbit').prop('disabled', true);
                    }

                    $('#textarea-post').trigger('focus');
                };

                reader.readAsDataURL(file);
            }
        });
    }
});

function clearPostImage() {
    document.getElementById('current-post-image').src = '';
    $('#post-image').val('');
    $('#textarea-post').removeClass('image-uploaded');
    $('#textarea-post_sumbit').removeClass('image-uploaded');
    $('.current-post-image-div').removeClass('image-uploaded');
    $('.post-image-delete').css('display', 'none');
    $('#textarea-post_sumbit').removeClass('active');
    $('#textarea-post_sumbit').attr('disabled');
}

function showPostModePopup() {
    $('#post-mode-fieldset').toggleClass('show');
}

$(document).on('click', function (e) {
    if (($(e.target).closest('#mode__for-friends')).length) {
        $('.post-mode-div').addClass('mode__for-friends')
    } else if (($(e.target).closest('#mode__for-all')).length) {
        $('.post-mode-div').removeClass('mode__for-friends')
    }
    if (!($(e.target).closest('.post-mode-div').length)) {
        $(this).find('#post-mode-fieldset').removeClass('show')
    }
})