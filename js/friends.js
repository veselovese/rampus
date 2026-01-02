$(document).ready(function () {
    $('#success-load-chat').on('click', "button[id^='request-to-friends_']", () => {
        $.ajax({
            url: '../back-files/request-to-friends',
            type: 'post',
            data: {
                'user_id_to': userIdTo,
            },
            success: function (response) {
                $('#request-to-friends_' + userIdTo).addClass('hide');
                $('#request-sended_' + userIdTo).removeClass('hide');
                $('.request-buttons').removeClass('hide');
                $('#unsend-request-to-friends_' + userIdTo).removeClass('hide');
            }
        })
    })

    $('#other-user-friends-buttons').on('click', "button[id^='request-to-friends_']", () => {
        const userIdTo = Number($("button[id^='request-to-friends_']").attr('id').split('_')[1]);
        $.ajax({
            url: '../back-files/request-to-friends',
            type: 'post',
            data: {
                'user_id_to': userIdTo,
            },
            success: function (response) {
                $('#request-to-friends_' + userIdTo).addClass('hide');
                $('#request-sended_' + userIdTo).removeClass('hide');
                $('.request-buttons').removeClass('hide');
                $('#unsend-request-to-friends_' + userIdTo).removeClass('hide');
            }
        })
    })

    $('#success-load-chat').on('click', "button[id^='unrequest-to-friends_']", () => {
        $.ajax({
            url: '../back-files/unrequest-to-friends',
            type: 'post',
            data: {
                'user_id_2': userIdTo,
            },
            success: function (response) {
                $('.request-buttons').addClass('hide');
                $('#apply-request-to-friends_' + userIdTo).addClass('hide');
                $('#unrequest-to-friends_' + userIdTo).addClass('hide');
                $('#request-to-friends_' + userIdTo).removeClass('hide');
            }
        })
    })

    $('#other-user-friends-buttons').on('click', "button[id^='unrequest-to-friends_']", () => {
        const userIdTo = Number($("button[id^='unrequest-to-friends_']").attr('id').split('_')[1]);
        $.ajax({
            url: '../back-files/unrequest-to-friends',
            type: 'post',
            data: {
                'user_id_2': userIdTo,
            },
            success: function (response) {
                $('.request-buttons').addClass('hide');
                $('#apply-request-to-friends_' + userIdTo).addClass('hide');
                $('#unrequest-to-friends_' + userIdTo).addClass('hide');
                $('#request-to-friends_' + userIdTo).removeClass('hide');
            }
        })
    })

    $('#success-load-chat').on('click', "button[id^='unsend-request-to-friends_']", () => {
        $.ajax({
            url: '../back-files/unrequest-to-friends',
            type: 'post',
            data: {
                'user_id_2': userIdTo,
            },
            success: function (response) {
                $('.request-buttons').addClass('hide');
                $('#request-sended_' + userIdTo).addClass('hide');
                $('#unrequest-to-friends_' + userIdTo).addClass('hide');
                $('#request-to-friends_' + userIdTo).removeClass('hide');
            }
        })
    })

    $('#other-user-friends-buttons').on('click', "button[id^='unsend-request-to-friends_']", () => {
        const userIdTo = Number($("button[id^='unsend-request-to-friends_']").attr('id').split('_')[1]);
        $.ajax({
            url: '../back-files/unrequest-to-friends',
            type: 'post',
            data: {
                'user_id_2': userIdTo,
            },
            success: function (response) {
                $('.request-buttons').addClass('hide');
                $('#request-sended_' + userIdTo).addClass('hide');
                $('#unrequest-to-friends_' + userIdTo).addClass('hide');
                $('#request-to-friends_' + userIdTo).removeClass('hide');
            }
        })
    })

    $('#success-load-chat').on('click', "button[id^='apply-request-to-friends_']", () => {
        $.ajax({
            url: '../back-files/add-to-friends',
            type: 'post',
            data: {
                'user_id_2': userIdTo,
            },
            success: function (response) {
                $('.request-buttons').addClass('hide');
                $('#apply-request-to-friends_' + userIdTo).addClass('hide');
                $('#unrequest-to-friends_' + userIdTo).addClass('hide');
                loadChat(usrnm);
            }
        })
    })

    $('#other-user-friends-buttons').on('click', "button[id^='apply-request-to-friends_']", () => {
        const userIdTo = Number($("button[id^='apply-request-to-friends_']").attr('id').split('_')[1]);
        $.ajax({
            url: '../back-files/add-to-friends',
            type: 'post',
            data: {
                'user_id_2': userIdTo,
            },
            success: function (response) {
                $('.request-buttons').addClass('hide');
                $('#apply-request-to-friends_' + userIdTo).addClass('hide');
                $('#unrequest-to-friends_' + userIdTo).addClass('hide');
                console.log($('#already-in-friends_' + userIdTo))
                $('#already-in-friends_' + userIdTo).removeClass('hide');
            }
        })
    })

})
function showPopupAnswerToUser(i) {
    $('#popup_answer-to-request_' + i).toggleClass('show');
    $('#answer-to-request_' + i).toggleClass('show');
}

function showPopupDeleteUser(i) {
    $('#popup_delete-from-friends_' + i).toggleClass('show');
    $('#delete-from-friends_' + i).toggleClass('show');
}

function showPopupUnrequestToUser(i) {
    $('#popup_unrequest-to-friends_' + i).toggleClass('show');
    $('#unrequest-to-friends_' + i).toggleClass('show');
}

const showPopupButtonRequest = $('.answer-to-request');
const popupDivRequest = $('.answer-to-requests-popup');

$(document).click(function (e) {
    let flag = 0;
    showPopupButtonRequest.each(function () {
        if (($(this).is(e.target)) && ($(this).has(e.target).length === 0)) {
            flag = 1;
        }
    })
    if (flag === 0) {
        popupDivRequest.each(function () {
            $(this).removeClass('show');
        })
        showPopupButtonRequest.each(function () {
            $(this).removeClass('show');
        })
    }
})

function unrequestFromFriends(from, to) {
    $.post('../back-files/unrequest-to-friends', { id_from: from, id_to: to });
    $('#answer-to-request_' + from).addClass('hide').removeClass('show');
    $('#request-to-friends_' + from).removeClass('hide');
}

function unrequestToFriendsRequestPage(from, to) {
    $.post('./back-files/unrequest-to-friends', { id_from: from, id_to: to });
    $('#popup_answer-to-request_' + from).removeClass('show');
    $('#answer-to-request_' + from).removeClass('show').addClass('unrequested');
    $('#answer-to-request_' + from).text('Отклонена');
}

function addToFriendsRequestPage(from, to) {
    $.post('./back-files/add-to-friends', { id_from: from, id_to: to });
    $('#popup_answer-to-request_' + from).removeClass('show');
    $('#answer-to-request_' + from).removeClass('show').addClass('unrequested');
    $('#answer-to-request_' + from).text('Принята');
}

function addToFriends(from, to) {
    $.post('../back-files/add-to-friends', { id_from: from, id_to: to });
    $('#popup_answer-to-request_' + from).removeClass('show');
    $('#answer-to-request_' + from).addClass('hide').removeClass('show');
    $('#delete-from-friends_' + from).removeClass('hide');
}

function deleteFromFriends(from, to) {
    $.post('../back-files/delete-from-friends', { id_from: from, id_to: to });
    $('#popup_delete-from-friends' + from).removeClass('show');
    $('#delete-from-friends_' + from).removeClass('show').addClass('hide');
    $('#request-to-friends_' + from).removeClass('hide');
}
