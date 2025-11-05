$(document).ready(function () {
    let params = new URLSearchParams(document.location.search);
    let search = params.get('search');
    renderPosts('all', search);

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
                $post.siblings().removeClass('hide');
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
                $post.siblings().removeClass('hide');
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

    $('#wall-filter-friends').click(() => {
        renderPosts('friends', search);
    })
    $('#wall-filter-all').click(() => {
        renderPosts('all', search);
    })
    $('#wall-filter-friends__mobile').click(() => {
        renderPosts('friends', search);
    })
    $('#wall-filter-all__mobile').click(() => {
        renderPosts('all', search);
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
    const file = e.target.files;

    if (file.length > 0) {
        const [currentFile] = file;
        const fileType = currentFile.type.startsWith('image');
        const reader = new FileReader();
        if (fileType) {
            reader.onload = () => {
                $('#textarea-post').addClass('image-uploaded');
                $('#textarea-post_sumbit').addClass('image-uploaded');
                $('.current-post-image-div').addClass('image-uploaded');
                $('.post-image-delete').css('display', 'flex');
                document.getElementById('current-post-image').src = reader.result;
                if (($('#textarea-post').text().trim(' ') != '') || ($('#post-image').val().length)) {
                    $('#textarea-post_sumbit').addClass('active');
                    $('#textarea-post_sumbit').removeAttr('disabled');
                } else {
                    $('#textarea-post_sumbit').removeClass('active');
                    $('#textarea-post_sumbit').attr('disabled');
                }
                $('#textarea-post').trigger('focus');
            }
        }
        reader.readAsDataURL(currentFile);
    }
})

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