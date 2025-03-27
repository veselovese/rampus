$(document).ready(function () {
    searchPeople();

    function searchPeople(query) {
        $.ajax({
            url: "back-files/search-people",
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
        && (!$('.three-dots-popup').is(e.target)) && ($('.three-dots-popup').has(e.target).length === 0)
        && (!$('.friend-buttons-div').is(e.target)) && ($('.friend-buttons-div').has(e.target).length === 0)
        && (!$('.friend-buttons').is(e.target)) && ($('.friend-buttons').has(e.target).length === 0)
        && (!$('.answer-to-request-div').is(e.target)) && ($('.answer-to-request-div').has(e.target).length === 0)) {
        window.location = "user/" + i;
    }
}

function openOtherUserProfileFromOtherProfile(e, i) {
    window.location = "../" + i;
}

function showPopupPeopleFilter() {
    document.getElementById('popup_people-filter').classList.toggle('show');
    document.getElementById('people-filter').classList.toggle('show');
    document.getElementById('active').classList.toggle('show');
}

function showPopupPeopleFilterMobile() {
    document.getElementById('people-filter-mobile').classList.toggle('show');
    document.getElementById('people-filter-mobile-info').classList.toggle('show');
    document.getElementById('popup_people-filter-mobile').classList.toggle('show');
}

$('#people-filter-all').on('click', () => {
    $('#users-filter-all').css('display', 'block');
    $('#users-filter-top').css('display', 'none')
})

$('#people-filter-all__mobile').on('click', () => {
    $('#users-filter-all').css('display', 'block');
    $('#users-filter-top').css('display', 'none')
})

$('#people-filter-top').on('click', () => {
    $('#users-filter-top').css('display', 'block');
    $('#users-filter-all').css('display', 'none')
})

$('#people-filter-top__mobile').on('click', () => {
    $('#users-filter-top').css('display', 'block');
    $('#users-filter-all').css('display', 'none')
})