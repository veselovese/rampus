$(document).ready(function () {
    let params = new URLSearchParams(document.location.search);
    let search = params.get('search');
    renderPosts(search);

    function renderPosts(query) {
        $.ajax({
            url: "back-files/render-posts_wall",
            method: "POST",
            data: {
                'filter': query
            },
            success: function (data) {
                $('#success-render-posts').html(data);
            }
        });
    }

    $('#wall-filter-friends').click(() => {
        renderPosts('friends');
    })
    $('#wall-filter-all').click(() => {
        renderPosts('all');
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
    if ($('#textarea-post').text().trim(' ') != '') {
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

$(document).click(function (e) {
    if ((!$('.wall-filter-mobile').is(e.target)) && ($('.wall-filter-mobile').has(e.target).length === 0)) {
        document.getElementById('popup_wall-filter-mobile').classList.remove('show');
        document.getElementById('wall-filter-mobile').classList.remove('show');
        document.getElementById('wall-filter-mobile-info').classList.remove('show');
    }
})

$('#popup_wall-filter-mobile').on('click', () => {
    if ($('#wall-filter-all-mobile').is(':checked')) {
        $('#wall-filter-mobile div span').text('Все');
        $('#popup_wall-filter-mobile').removeClass('friends');
    } else if ($('#wall-filter-friends-mobile').is(':checked')) {
        $('#wall-filter-mobile div span').text('Друзья');
        $('#popup_wall-filter-mobile').addClass('friends');
    }
})