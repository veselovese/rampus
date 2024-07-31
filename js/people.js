$(document).ready(function () {
    searchPeople();

    function searchPeople(query) {
        $.ajax({
            url: "search-people",
            method: "POST",
            data: {
                'people': query
            },
            success: function (data) {
                $('#success-search-people').html(data);
            }
        });
    }
    $('#search-people').keyup(function () {
        var search = $(this).val();
        if (search != '') {
            searchPeople(search);
        }
        else {
            searchPeople();
        }
    })

    $('#icon-search-people').on('click', function () {
        $('#search-people').trigger('focus');
    })
})

function showPopupOtherUserInfo(i) {
    document.getElementById('three-dots-popup_other-user-info_' + i).classList.toggle('show');
}

function showPopupUserInfo() {
    document.getElementById('three-dots-popup_user-info').classList.toggle('show');
}

function openOtherUserProfile(e, i) {
    if ((!$('.show-three-dots-popup').is(e.target)) && ($('.show-three-dots-popup').has(e.target).length === 0)
        && (!$('.three-dots-popup').is(e.target)) && ($('.three-dots-popup').has(e.target).length === 0)) {
        window.location = "user/" + i;
    }
}