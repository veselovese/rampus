$(document).ready(function () {
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
})

$('#textarea-post').keypress(function (e) {
    if (e.which === 13 && !e.shiftKey) {
        e.preventDefault();
        if ($('#textarea-post').text().trim(' ') != '') {
            $(this).closest('form').submit();
        }
    }
});

function textareaPostPlaceholder(e) {
    document.getElementById('textarea-post_label').style.display = 'none';
    if ((document.getElementById('textarea-post').textContent.length < 2) && (e.key=="Backspace")) {
        document.getElementById('textarea-post_label').style.display = 'block';
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