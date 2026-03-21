if (!window.ws) {
    window.ws = new WebSocket('ws://localhost:2346');
    // window.ws = new WebSocket('wss://rampus.ru/ws');
}

$(document).ready(function () {
    searchHashtag();

    function searchHashtag(query) {
        const get = $('#get-status').val();
        $.ajax({
            url: "backfiles/search-hashtag",
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
            url: "backfiles/blossom/render-blossom-notifications-widget",
            method: "POST",
            data: {},
            success: function (data) {
                $('#success-blossom-notifications-widget').html(data);
                $('#success-blossom-notifications-widget-mobile').html(data);
            }
        });
    }

    const observerOptions = {
        root: null,
        rootMargin: '-72px 0px -122px 0px',
        threshold: 0.5
    };

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const post = $(entry.target);
                const postId = post[0].id.split('-')[1];

                setTimeout(() => {
                    incrementPostView(postId, post);
                }, 1400);

                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    function incrementPostView(postId, $postElement) {
        const url = '/backfiles/wall/increment-view';

        $.post(url, { post_id: postId }, function (response) {
            const $counter = $postElement.find('.post-views-counter');
            let currentViews = parseInt($counter.text());
            if (!isNaN(currentViews)) {
                $counter.text(currentViews + 1);
            }
        });
    }

    window.trackPosts = function () {
        $('.user-post:not(.view-tracked)').each(function () {
            observer.observe(this);
            $(this).addClass('view-tracked');
        });
    };

    window.trackPosts();
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
            changeImage(modal, -1, nextBtn, prevBtn);
        });

        nextBtn.on('click', function (e) {
            e.stopPropagation();
            changeImage(modal, 1, nextBtn, prevBtn);
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