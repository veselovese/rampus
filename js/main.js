$(document).ready(function () {
    $('.unliked').on('click', function () {
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

    $('.liked').on('click', function () {
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
            url: "search-hashtag",
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
    navigator.clipboard.writeText('https://localhost/rampus/post/' + i);
}

function showPopup(i) {
    document.getElementById('three-dots-popup_' + i).classList.toggle('show');
}

const showPopupButton = $('.show-three-dots-popup');
const popupDiv = $('.three-dots-popup');

$(document).click(function (e) {
    let flag = 0;
    showPopupButton.each(function () {
        if (($(this).is(e.target)) && ($(this).has(e.target).length === 0)) {
            flag = 1;
        }
    })
    if (flag === 0) {
        popupDiv.each(function () {
            $(this).removeClass('show');
        })
    }
})

function copyLinkToUser(i) {
    navigator.clipboard.writeText('https://localhost/rampus/user/' + i);
}

function copyLinkToOtherUser(id, username) {
    navigator.clipboard.writeText('https://localhost/rampus/user/' + username);
    document.getElementById('three-dots-popup_other-user-info_' + id).classList.toggle('show');
}

function requestToFriends(from, to) {
    $.post('../request-to-friends', {id_from: from, id_to: to})
    $('#request-to-friends').addClass('hide');
    $('#unrequest-to-friends').removeClass('hide');
}

function unrequestToFriends(from, to) {
    $.post('../unrequest-to-friends', {id_from: from, id_to: to})
    $('#unrequest-to-friends').addClass('hide');
    $('#unrequest-from-friends').addClass('hide');
    $('#add-to-friends').addClass('hide');
    $('#request-to-friends').removeClass('hide');
}

function addToFriends(from, to) {
    $.post('../add-to-friends', {id_from: from, id_to: to})
    $('#add-to-friends').addClass('hide');
    $('#unrequest-to-friends').addClass('hide');
    $('#unrequest-from-friends').addClass('hide');
    $('#request-to-friends').addClass('hide');
    $('#you-are-friends').removeClass('hide');
    $('#delete-from-friends').removeClass('hide');
}

function deleteFromFriends(from, to) {
    $.post('../delete-from-friends', {id_from: from, id_to: to})
    $('#you-are-friends').addClass('hide');
    $('#delete-from-friends').addClass('hide');
    $('#request-to-friends').removeClass('hide');
}