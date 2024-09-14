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

$(document).click(function (e) {
    if ((!$('.people-filter-div').is(e.target)) && ($('.people-filter-div').has(e.target).length === 0)) {
        document.getElementById('popup_people-filter').classList.remove('show');
        document.getElementById('people-filter').classList.remove('show');
        document.getElementById('active').classList.remove('show');
    }
})

$('#popup_people-filter').on('click', () => {
    if ($('#people-filter-all').is(':checked')) {
        $('#people-filter div span').text('все');
    } else if ($('#people-filter-top').is(':checked')) {
        $('#people-filter div span').text('рейтинг');
    }
})

function showPopupPeopleFilterMobile() {
    document.getElementById('people-filter-mobile').classList.toggle('show');
    document.getElementById('people-filter-mobile-info').classList.toggle('show');
    document.getElementById('popup_people-filter-mobile').classList.toggle('show');
}

$(document).click(function (e) {
    if ((!$('.people-filter-mobile').is(e.target)) && ($('.people-filter-mobile').has(e.target).length === 0)) {
        document.getElementById('popup_people-filter-mobile').classList.remove('show');
        document.getElementById('people-filter-mobile').classList.remove('show');
        document.getElementById('people-filter-mobile-info').classList.remove('show');
    }
})

$('#popup_people-filter-mobile').on('click', () => {
    if ($('#people-filter-all-mobile').is(':checked')) {
        $('#people-filter-mobile div span').text('Все');
        $('#popup_people-filter-mobile').addClass('all');
    } else if ($('#people-filter-top-mobile').is(':checked')) {
        $('#people-filter-mobile div span').text('Рейтинг');
        $('#popup_people-filter-mobile').removeClass('all');
    }
})