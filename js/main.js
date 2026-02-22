ws = new WebSocket('ws://localhost:2346');

$(document).ready(function () {
    $('.wall__user-posts.current-post').on('click', '.unliked', function () {
        const postId = $(this).attr('id');
        $post = $(this);
        $.ajax({
            url: '../wall',
            type: 'post',
            data: {
                'liked': 1,
                'postId': postId
            },
            success: function (response) {
                $post.find('.like-counter').text(response);
                $post.siblings().find('.like-counter').text(response);
                $post.addClass('hide');
                $post.siblings('.like-button').removeClass('hide');
            }
        })
    })

    $('.wall__user-posts.current-post').on('click', '.liked', function () {
        const postId = $(this).attr('id');
        $post = $(this);
        $.ajax({
            url: '../wall',
            type: 'post',
            data: {
                'unliked': 1,
                'postId': postId
            },
            success: function (response) {
                $post.find('.like-counter').text(response);
                $post.siblings().find('.like-counter').text(response);
                $post.addClass('hide');
                $post.siblings('.like-button').removeClass('hide');
            }
        })
    })

    $('.wall__user-posts.current-post').on('click', '.unreposted', function () {
        const postId = $(this).attr('id').split('-')[1];
        $post = $(this);
        $.ajax({
            url: '../wall',
            type: 'post',
            data: {
                'reposted': 1,
                'postId': postId
            },
            success: function (response) {
                $post.find('.repost-counter').text(response);
                $post.siblings().find('.repost-counter').text(response);
                $post.addClass('hide');
                $post.siblings('.repost-button').removeClass('hide');
            }
        })
    })

    $('.wall__user-posts.current-post').on('click', '.reposted', function () {
        const postId = $(this).attr('id').split('-')[1];
        $post = $(this);
        $.ajax({
            url: '../wall',
            type: 'post',
            data: {
                'unreposted': 1,
                'postId': postId
            },
            success: function (response) {
                $post.find('.repost-counter').text(response);
                $post.siblings().find('.repost-counter').text(response);
                $post.addClass('hide');
                $post.siblings('.repost-button').removeClass('hide');
            }
        })
    })

    $('.wall__user-posts.current-post').on('click', '.delete-post', function () {
        const postId = $(this).attr('id');
        $deletePost = $(this);
        $.ajax({
            url: '../back-files/delete-post',
            type: 'post',
            data: {
                'post_id': postId,
            },
            success: function (response) {
                window.location = '../wall'
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
                setTimeout(() => {
                    $('#wall-loading-hashtags').removeClass('loading');
                    $('#success-search-hashtag').html(data);
                    $('#success-search-hashtag-in-header').html(data);
                }, 300)

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

    loadBlossomNotify();
    function loadBlossomNotify() {
        $.ajax({
            url: "back-files/blossom/render-blossom-notifications-widget",
            method: "POST",
            data: {},
            success: function (data) {
                $('#success-blossom-notifications-widget').html(data);
                $('#success-blossom-notifications-widget-mobile').html(data);
            }
        });
    }
})

function showPopup(i) {
    $('#three-dots-popup_' + i).toggleClass('show');
    $('#div-show-three-dots-popup_' + i).toggleClass('show');
}

$(document).on('click', function (e) {
    if (!(e.target.classList[0] === 'div-show-three-dots-popup')) {
        $(this).find('.three-dots-popup').removeClass('show')
        $(this).find('.div-show-three-dots-popup').removeClass('show')
    }
})

function editPost(postId) {
    console.log(postId)
}

function setViewportProperty() {
    let vh = window.innerHeight * 0.01;
    document.documentElement.style.setProperty('--vh', `${vh}px`);
}

window.addEventListener('resize', setViewportProperty);

setViewportProperty();

// $('.wall__user-posts').on('click', '.image-in-post', function () {
//     var src = $(this).attr('src');
//     src = src.replace('small_', '');

//     var modal;

//     function removeModal() {
//         modal.remove();
//         $('body').off('keyup.modal-close');
//     }
//     modal = $('<div>').css({
//         background: 'RGBA(0, 0, 0, 0.5)'
//     }).addClass('image-in-post-modal').click(function () {
//         removeModal();
//     }).appendTo('body');

//     imgDiv = $('<div>').addClass('image-in-post-modal-img-div').appendTo(modal);
//     img = $('<img>').attr('src', src).addClass(`image-in-post-modal-img`).appendTo(imgDiv);

//     closeBtn = $('<button>').attr('type', 'button').attr('title', 'Закрыть изображение').addClass('post-image-close').appendTo(imgDiv);

//     const svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
//     svg.setAttribute("width", "10");
//     svg.setAttribute("height", "10");
//     svg.setAttribute("viewBox", "0 0 10 10");
//     svg.setAttribute("fill", "none");
//     const path = document.createElementNS(svg.namespaceURI, "path");
//     path.setAttribute("d", "M0.191016 8.88671C-0.0636719 9.14141 -0.0636719 9.55428 0.191016 9.80898C0.445703 10.0637 0.858643 10.0637 1.11333 9.80898L0.191016 8.88671ZM5.46114 5.46114C5.71584 5.20644 5.71584 4.79357 5.46114 4.53888C5.20644 4.28418 4.79357 4.28418 4.53888 4.53888L5.46114 5.46114ZM4.53888 4.53888C4.28418 4.79357 4.28418 5.20644 4.53888 5.46114C4.79357 5.71584 5.20644 5.71584 5.46114 5.46114L4.53888 4.53888ZM9.80898 1.11333C10.0637 0.858644 10.0637 0.445703 9.80898 0.191016C9.55428 -0.0636719 9.14141 -0.0636719 8.88671 0.191016L9.80898 1.11333ZM5.46114 4.53888C5.20644 4.28418 4.79357 4.28418 4.53888 4.53888C4.28418 4.79357 4.28418 5.20644 4.53888 5.46114L5.46114 4.53888ZM8.88671 9.80898C9.14141 10.0637 9.55428 10.0637 9.80898 9.80898C10.0637 9.55428 10.0637 9.14141 9.80898 8.88671L8.88671 9.80898ZM4.53888 5.46114C4.79357 5.71584 5.20644 5.71584 5.46114 5.46114C5.71584 5.20644 5.71584 4.79357 5.46114 4.53888L4.53888 5.46114ZM1.11333 0.191016C0.858643 -0.0636719 0.445703 -0.0636719 0.191016 0.191016C-0.0636719 0.445703 -0.0636719 0.858644 0.191016 1.11333L1.11333 0.191016ZM1.11333 9.80898L5.46114 5.46114L4.53888 4.53888L0.191016 8.88671L1.11333 9.80898ZM5.46114 5.46114L9.80898 1.11333L8.88671 0.191016L4.53888 4.53888L5.46114 5.46114ZM4.53888 5.46114L8.88671 9.80898L9.80898 8.88671L5.46114 4.53888L4.53888 5.46114ZM5.46114 4.53888L1.11333 0.191016L0.191016 1.11333L4.53888 5.46114L5.46114 4.53888Z");
//     svg.appendChild(path);
//     closeBtn.append(svg);

//     $('body').on('keyup.modal-close', function (e) {
//         if (e.key === 'Escape') {
//             removeModal();
//         }
//     });
// });

$(document).on('click', '.image-in-post', function (e) {
    e.preventDefault();

    const $currentPostContainer = $(this).closest('.images-in-post-div');
    const $allImages = $currentPostContainer.find('.image-in-post');

    const currentIndex = $allImages.index(this);

    openImageModal($allImages, currentIndex);
});

function openImageModal($images, startIndex) {
    let startSrc = $images.eq(startIndex).attr('src');
    startSrc = startSrc.replace('small_', '');

    const modal = $('<div>').css({
        background: 'RGBA(0, 0, 0, 0.5)'
    }).addClass('image-in-post-modal').appendTo('body');

    const imgDiv = $('<div>').addClass('image-in-post-modal-img-div').appendTo(modal);

    const img = $('<img>').attr('src', startSrc).addClass('image-in-post-modal-img').appendTo(imgDiv);

    const closeBtn = $('<button>').attr('type', 'button').attr('title', 'Закрыть изображение').addClass('post-image-close').appendTo(imgDiv);
    const svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
    svg.setAttribute("width", "10");
    svg.setAttribute("height", "10");
    svg.setAttribute("viewBox", "0 0 10 10");
    svg.setAttribute("fill", "none");
    const path = document.createElementNS(svg.namespaceURI, "path");
    path.setAttribute("d", "M0.191016 8.88671C-0.0636719 9.14141 -0.0636719 9.55428 0.191016 9.80898C0.445703 10.0637 0.858643 10.0637 1.11333 9.80898L0.191016 8.88671ZM5.46114 5.46114C5.71584 5.20644 5.71584 4.79357 5.46114 4.53888C5.20644 4.28418 4.79357 4.28418 4.53888 4.53888L5.46114 5.46114ZM4.53888 4.53888C4.28418 4.79357 4.28418 5.20644 4.53888 5.46114C4.79357 5.71584 5.20644 5.71584 5.46114 5.46114L4.53888 4.53888ZM9.80898 1.11333C10.0637 0.858644 10.0637 0.445703 9.80898 0.191016C9.55428 -0.0636719 9.14141 -0.0636719 8.88671 0.191016L9.80898 1.11333ZM5.46114 4.53888C5.20644 4.28418 4.79357 4.28418 4.53888 4.53888C4.28418 4.79357 4.28418 5.20644 4.53888 5.46114L5.46114 4.53888ZM8.88671 9.80898C9.14141 10.0637 9.55428 10.0637 9.80898 9.80898C10.0637 9.55428 10.0637 9.14141 9.80898 8.88671L8.88671 9.80898ZM4.53888 5.46114C4.79357 5.71584 5.20644 5.71584 5.46114 5.46114C5.71584 5.20644 5.71584 4.79357 5.46114 4.53888L4.53888 5.46114ZM1.11333 0.191016C0.858643 -0.0636719 0.445703 -0.0636719 0.191016 0.191016C-0.0636719 0.445703 -0.0636719 0.858644 0.191016 1.11333L1.11333 0.191016ZM1.11333 9.80898L5.46114 5.46114L4.53888 4.53888L0.191016 8.88671L1.11333 9.80898ZM5.46114 5.46114L9.80898 1.11333L8.88671 0.191016L4.53888 4.53888L5.46114 5.46114ZM4.53888 5.46114L8.88671 9.80898L9.80898 8.88671L5.46114 4.53888L4.53888 5.46114ZM5.46114 4.53888L1.11333 0.191016L0.191016 1.11333L4.53888 5.46114L5.46114 4.53888Z");
    svg.appendChild(path);
    closeBtn.append(svg);

    if ($images && $images.length > 1) {
        const nextBtn = $('<button>').addClass('modal-nav-btn modal-next').attr('type', 'button').attr('title', 'Следующая');
        const prevBtn = $('<button>').addClass('modal-nav-btn modal-prev').attr('type', 'button').attr('title', 'Предыдущая');
        prevBtn.appendTo(imgDiv);
        nextBtn.appendTo(imgDiv);

        updateNavButtons($images.length, startIndex, nextBtn, prevBtn)

        modal.data('images', $images);

        prevBtn.on('click', function (e) {
            e.stopPropagation();
            changeImage(modal, -1);
        });

        nextBtn.on('click', function (e) {
            e.stopPropagation();
            changeImage(modal, 1);
        });

        $(document).on('keydown.modal', function (e) {
            if (e.key === 'ArrowLeft') changeImage(modal, -1, nextBtn, prevBtn);
            if (e.key === 'ArrowRight') changeImage(modal, 1, nextBtn, prevBtn);
        });
    }

    modal.data('currentIndex', startIndex);

    modal.on('click', '.post-image-close', removeModal);
    modal.on('click', function (e) {
        if (e.target === this) removeModal();
    });

    $(document).on('keydown.modal', function (e) {
        if (e.key === 'Escape') removeModal();
    });
}

function updateNavButtons(allImagesCounter, currentIndex, nextBtn, prevBtn) {
    if (currentIndex == 0) {
        prevBtn.addClass('hide')
        nextBtn.removeClass('hide')
    } else if (currentIndex == (allImagesCounter - 1)) {
        nextBtn.addClass('hide')
        prevBtn.removeClass('hide')
    } else {
        nextBtn.removeClass('hide')
        prevBtn.removeClass('hide')

    }
}

function changeImage(modal, direction, nextBtn, prevBtn) {
    const $images = modal.data('images');
    let index = modal.data('currentIndex');
    const total = $images.length;

    index = (index + direction + total) % total;

    modal.data('currentIndex', index);

    let newSrc = $images.eq(index).attr('src');
    newSrc = newSrc.replace('small_', '');

    $('.image-in-post-modal-img').attr('src', newSrc);

    updateNavButtons(total, index, nextBtn, prevBtn)
}

function removeModal() {
    $('.image-in-post-modal').fadeOut(200, function () {
        $(this).remove();
    });
    $(document).off('keydown.modal');
}