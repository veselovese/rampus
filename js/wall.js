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
    if ((document.getElementById('textarea-post').textContent.length < 2) && (e.key=="Backspace")) {
        document.getElementById('textarea-post_label').style.display = 'block';
    }
}

function textareaCommentPlaceholder(e, i) {
    document.getElementById('textarea-comment_label_' + i).style.display = 'none';
    if ((document.getElementById('textarea-comment_' + i).textContent.length < 2) && (e.key=="Backspace")) {
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

function showPopup(i) {
    document.getElementById('three-dots-popup_' + i).classList.toggle('show');
}