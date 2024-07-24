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
    })

    $('#search-hashtag-in-header').focus(function () {
        $('#success-search-hashtag-in-header').toggleClass('focus');
    })
})

function copyLinkToPost(i) {
    navigator.clipboard.writeText('https://localhost/rampus/wall#post-' + i);
    document.getElementById('three-dots-popup_' + i).classList.toggle('show');
}

function showPopup(i) {
    document.getElementById('three-dots-popup_' + i).classList.toggle('show');
}