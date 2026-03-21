function copyLinkToPost(i) {
    navigator.clipboard.writeText('/post/' + i);
}

function copyLinkToUser(i) {
    navigator.clipboard.writeText('/user/' + i);
}

function copyLinkToUserAddReturnMessage(i) {
    navigator.clipboard.writeText('/user/' + i);
    $('#copy-link-status').text('Ссылка скопирована').css('color', 'var(--main-accent-color)').css('background-color', 'var(--block-accent-color-210)');
    setTimeout(() => {
        $('#copy-link-status').text('Копировать ссылку').css('color', 'var(--else-text-color-50)').css('background-color', 'var(--block-accent-color-06)');
    }, 2000);
}

function copyLinkToOtherUser(id, username) {
    navigator.clipboard.writeText('/user/' + username);
    document.getElementById('three-dots-popup_other-user-info_' + id).classList.toggle('show');
}