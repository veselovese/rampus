function openOtherFriendsPage(e, i) {
    if ((!$('.current-friend').is(e.target)) && ($('.current-friend').has(e.target).length === 0)) {
        window.location = i + "/friends";
    }
}