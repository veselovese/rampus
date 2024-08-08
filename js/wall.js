$('#textarea-post').keypress(function (e) {
    if (e.which === 13 && !e.shiftKey) {
        e.preventDefault();
        if ($('#textarea-post').text().trim(' ') != '') {
            $(this).closest('form').submit();
        }
    }
});

$('.textarea-comment').keypress(function (e) {
    if (e.which === 13 && !e.shiftKey) {
        e.preventDefault();
        if ($('.textarea-comment').text().trim(' ') != '') {
            $(this).closest('form').submit();
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
    if (document.getElementById('textarea-post').textContent != '') {
        $('#textarea-post_sumbit').addClass('active');
        $('#textarea-post_sumbit').removeAttr('disabled');
    } else {
        $('#textarea-post_sumbit').removeClass('active');
        $('#textarea-post_sumbit').Attr('disabled');
    }
    div.setAttribute('value', obj.textContent);
}

function textareaPostPlaceholder(e) {
    document.getElementById('textarea-post_label').style.display = 'none';
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
    if (document.getElementById('textarea-comment_' + i).textContent != '') {
        $('#textarea-comment_submit_' + i).addClass('active');
        $('#textarea-comment_submit_' + i).removeAttr('disabled');
    } else {
        $('#textarea-comment_submit_' + i).removeClass('active');
        $('#textarea-comment_submit_' + i).Attr('disabled');
    }
    div.setAttribute('value', obj.textContent);
}

function showPopupWallFilter() {
    document.getElementById('popup_wall-filter').classList.toggle('show');
    document.getElementById('wall-filter').classList.toggle('show');
    document.getElementById('active').classList.toggle('show');
}

$(document).click(function (e) {
    if ((!$('.wall-filter-div').is(e.target)) && ($('.wall-filter-div').has(e.target).length === 0)) {
        document.getElementById('popup_wall-filter').classList.remove('show');
        document.getElementById('wall-filter').classList.remove('show');
        document.getElementById('active').classList.remove('show');
    }
})


$('#popup_wall-filter').on('click', () => {
    if ($('#wall-filter-all').is(':checked')) {
        $('#wall-filter div span').text('все');
    } else if ($('#wall-filter-friends').is(':checked')) {
        $('#wall-filter div span').text('друзья');
    }
})