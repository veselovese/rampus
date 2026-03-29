function copyLinkToPost(i) {
    const link = window.location.origin + '/post/' + i;
    navigator.clipboard.writeText(link);
}

function copyLinkToUser(i) {
    const link = window.location.origin + '/user/' + i;
    navigator.clipboard.writeText(link);
}

function copyLinkToUserAddReturnMessage(i) {
    const link = window.location.origin + '/user/' + i;
    navigator.clipboard.writeText(link);
    $('#copy-link-status').text('Ссылка скопирована').css('color', 'var(--main-accent-color)').css('background-color', 'var(--block-accent-color-210)');
    setTimeout(() => {
        $('#copy-link-status').text('Копировать ссылку').css('color', 'var(--else-text-color-50)').css('background-color', 'var(--block-accent-color-06)');
    }, 2000);
}

function copyLinkToOtherUser(id, username) {
    const link = window.location.origin + '/user/' + username;
    navigator.clipboard.writeText(link);
    document.getElementById('three-dots-popup_other-user-info_' + id).classList.toggle('show');
}