$(document).ready(function () {
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

    searchHashtag();

    function searchHashtag(query) {
        const get = $('#get-status').val();
        $.ajax({
            url: "back-files/search-hashtag",
            method: "POST",
            data: {
                'hashtag': query,
                'get': get
            },
            success: function (data) {
                $('#success-search-hashtag').html(data);
                $('#success-search-hashtag-in-header').html(data);
            }
        });
    }
    $('#search-hashtag').keyup(function () {
        var search = $(this).val();
        if (search != '') {
            searchHashtag(search);
        }
        else {
            searchHashtag();
        }
    })
    $('#search-hashtag-in-header').keyup(function () {
        var search = $(this).val();
        if (search != '') {
            searchHashtag(search);
        }
        else {
            searchHashtag();
        }
    })

    $('#icon-search-hashtag').on('click', function () {
        $('#search-hashtag').trigger('focus');
    })

    $('#icon-search-hashtag-in-header').on('click', function () {
        $('#search-hashtag-in-header').trigger('focus');
        $('#success-search-hashtag-in-header').addClass('focus');
    })

    $('#search-hashtag-in-header').focus(function () {
        $('#success-search-hashtag-in-header').addClass('focus');
    })

    $(document).click(function (e) {
        if ((!$('#search-hashtag-in-header').is(e.target)) && ($('#search-hashtag-in-header').has(e.target).length === 0)
            && (!$('#success-search-hashtag-in-header').is(e.target)) && ($('#success-search-hashtag-in-header').has(e.target).length === 0)
            && (!$('#icon-search-hashtag-in-header').is(e.target)) && ($('#icon-search-hashtag-in-header').has(e.target).length === 0)) {
            $('#success-search-hashtag-in-header').removeClass('focus');
        }
    })
})

function copyLinkToPost(i) {
    navigator.clipboard.writeText('https://rampus.ru/post/' + i);
}

function showPopup(i) {
    $('#three-dots-popup_' + i).toggleClass('show');
    $('#div-show-three-dots-popup_' + i).toggleClass('show');
}

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

$(document).on('click', function (e) {
    if (!(e.target.classList[0] === 'div-show-three-dots-popup')) {
        $(this).find('.three-dots-popup').removeClass('show')
        $(this).find('.div-show-three-dots-popup').removeClass('show')
    }
})

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

function copyLinkToUser(i) {
    navigator.clipboard.writeText('https://rampus.ru/user/' + i);
}

function copyLinkToOtherUser(id, username) {
    navigator.clipboard.writeText('https://rampus.ru/user/' + username);
    document.getElementById('three-dots-popup_other-user-info_' + id).classList.toggle('show');
}

function requestToFriends(from, to) {
    $.post('../back-files/request-to-friends', { id_from: from, id_to: to });
    $('#request-to-friends_' + to).addClass('hide');
    $('#unrequest-to-friends_' + to).removeClass('hide');
}

function unrequestToFriends(from, to) {
    $.post('../back-files/unrequest-to-friends', { id_from: from, id_to: to });
    $('#unrequest-to-friends_' + to).addClass('hide');
    $('#request-to-friends_' + to).removeClass('hide');
}

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