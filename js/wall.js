$('#textarea-post').keypress(function (e) {
    if (e.which === 13 && !e.shiftKey) {
        e.preventDefault();
        if ($('#textarea-post').text() != '') {
            $(this).closest('form').submit();
        }
    }
});

$('.textarea-comment').keypress(function (e) {
    if (e.which === 13 && !e.shiftKey) {
        e.preventDefault();
        if ($('.textarea-comment').text() != '') {
            $(this).closest('form').submit();
        }
    }
});

function textareaPost(e) {
    const obj = e.target;
    const div = document.getElementById(obj.id + '_input');
    if (document.getElementById('textarea-post').textContent != '') {
        document.getElementById('textarea-post_label').style.display = 'none';
        $('#textarea-post_sumbit').addClass('active');
        $('#textarea-post_sumbit').removeAttr('disabled');
    } else {
        document.getElementById('textarea-post_label').style.display = 'block';
        $('#textarea-post_sumbit').removeClass('active');
        $('#textarea-post_sumbit').Attr('disabled');
    }
    div.setAttribute('value', obj.textContent);
}

function textareaComment(e, i) {
    const obj = e.target;
    const div = document.getElementById('textarea-comment_input_' + i);
    if (document.getElementById('textarea-comment_' + i).textContent != '') {
        document.getElementById('textarea-comment_label_' + i).style.display = 'none';
    } else {
        document.getElementById('textarea-comment_label_' + i).style.display = 'block';
    }
    div.setAttribute('value', obj.textContent);
}

