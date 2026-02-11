let params = new URLSearchParams(document.location.search);
let search = params.get('search');
let block_show = false;
let isUploading = false;

async function checkPosition() {
    const height = document.body.offsetHeight
    const screenHeight = window.innerHeight

    const scrolled = window.scrollY

    const threshold = height - screenHeight / 4
    const position = scrolled + screenHeight

    if (position >= threshold) {
        await renderPosts('main', search)
    }
}

function throttle(callee, timeout) {
    let timer = null

    return function perform(...args) {
        if (timer) return

        timer = setTimeout(() => {
            callee(...args)

            clearTimeout(timer)
            timer = null
        }, timeout)
    }
}

window.addEventListener('scroll', throttle(checkPosition, 250))
window.addEventListener('resize', throttle(checkPosition, 250))

let isLoading = false

let shouldLoad = true
let $target = $('#wall-loading-posts');
let page = $target.attr('data-page');
let maxPage = 0;

async function renderPosts(filter, search, cleanPage = false) {
    if (cleanPage) {
        $('#wall-loading-posts').addClass('rude-hide').removeClass('loading');
        $('#success-render-posts').html('')
        $('#wall-loading-main').addClass('loading');
        page = 1
        shouldLoad = true
    }

    if (isLoading || !shouldLoad) return
    isLoading = true

    $.ajax({
        url: "back-files/render-posts_wall?page=" + page,
        method: "POST",
        data: {
            'filter': filter,
            'search': search
        },
        success: function (data) {
            setTimeout(() => {
                $('#wall-loading-main').removeClass('loading');
                $('#success-render-posts').html($('#success-render-posts').html() + data);
                $('#wall-loading-posts').removeClass('rude-hide').addClass('loading');

                switch (filter) {
                    case 'main':
                        maxPage = $target.attr('data-max-main')
                        break;
                    case 'timetable':
                        maxPage = $target.attr('data-max-ts')
                        break;
                    case 'all':
                        maxPage = $target.attr('data-max-all')
                        break;
                }
                if (page == maxPage) {
                    $('#wall-loading-posts').removeClass('loading');
                    shouldLoad = false
                }
                page++;
                isLoading = false
                $target.attr('data-page', page);
            }, 300)
        }
    });

}

async function addPost(e) {
    e.preventDefault();

    isUploading = true;

    const form = document.getElementById('new-post-form');
    const formData = new FormData(form);

    const postContent = document.getElementById('textarea-post').innerHTML.trim();
    const hiddenInput = document.getElementById('textarea-post_input');
    hiddenInput.value = postContent;

    if (postContent === '' && formData.getAll('post-images[]').length === 0) {
        return;
    }

    const textareaPost = document.getElementById('textarea-post');
    textareaPost.setAttribute('contenteditable', false);

    const submitBtn = document.getElementById('textarea-post_sumbit');
    submitBtn.classList.remove('active');
    submitBtn.disabled = true;

    const addImageBtn = document.getElementById('add-image-button');
    addImageBtn.disabled = true;

    const postModeBtn = document.getElementById('post-mode-button');
    postModeBtn.disabled = true;

    const deleteImageBtns = document.querySelectorAll('.post-image-delete');
    deleteImageBtns.forEach((button) => {
        button.disabled = true;
    })

    const submitBtnLoading = document.getElementById('textarea-post_sumbit_loading');
    submitBtnLoading.classList.add('uploading');

    try {
        const response = await $.ajax({
            url: "back-files/add",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json'
        })
        if (response.success) {
            document.getElementById('textarea-post').innerHTML = '';
            document.getElementById('textarea-post').classList.remove('image-uploaded');
            document.getElementById('textarea-post_label').style.display = 'block';
            document.getElementById('add-image-button').classList.remove('mode__has-image');

            const previewDiv = document.querySelector('.current-post-images-div');
            if (previewDiv) {
                previewDiv.classList.remove('image-uploaded');
                previewDiv.innerHTML = '';
            }

            const fileInput = document.getElementById('post-image');
            if (fileInput) {
                fileInput.value = '';
            }

            const fotoCounter = document.getElementById('foto-counter-notification');
            if (fotoCounter) {
                fotoCounter.classList.remove('active');
                fotoCounter.textContent = '';
            }
            addPostToFeed(response.post);

            isUploading = false;
        }

    } finally {
        submitBtn.disabled = false;
        addImageBtn.disabled = false;
        postModeBtn.disabled = false;
        deleteImageBtns.forEach((button) => {
            button.disabled = false;
        })
        textareaPost.setAttribute('contenteditable', true);
        submitBtnLoading.classList.remove('uploading');
    };
}

function createPostElement(postData) {
    const postDiv = document.createElement('div');
    postDiv.className = 'user-post new-post';
    postDiv.id = `post-${postData.id}`;

    let postHTML = `
        <div>
            <div class="wall__user-info">
                <a href="./user/${postData.username}">
                    <img class="avatar" src="uploads/avatar/thin_${postData.avatar}">
                </a>
                ${postData.type === 'repost' ?
            `<a href="./user/${postData.repost_username}" class="avatar-repost-link">
                        <img class="avatar repost" src="uploads/avatar/thin_${postData.repost_avatar}">
                    </a>` : ''}
                
                <div class="name-and-date ${postData.type === 'repost' ? 'repost' : ''}">
                    <div class="f-and-s-names-and-plat">
    `;

    if (postData.first_name || postData.second_name) {
        const trustClass = postData.verify_status == 1 ? ' trust' : '';
        postHTML += `<a href="./user/${postData.username}" class="first-and-second-names${trustClass}">${postData.first_name} ${postData.second_name}</a>`;
    } else {
        const trustClass = postData.verify_status == 1 ? ' trust' : '';
        postHTML += `<a href="./user/${postData.username}" class="first-and-second-names${trustClass}">@${postData.username}</a>`;
    }

    postHTML += `
                    </div>
                    <div class="extra-post-info">
                        <a href='./post/${postData.id}' class='date-info'>${postData.date}</a>
    `;

    if (postData.type === 'repost') {
        postHTML += `
            <span class="repost-info dot">•</span>
            <a href="./post/${postData.repost_id}" class="repost-info">репост @${postData.repost_username}</a>
        `;
    }

    postHTML += `
                    </div>
                </div>
    `;

    if (postData.for_friends) {
        postHTML += `
            <div class="for-friends">
                <svg width='28' height='31' viewBox='0 0 28 31' fill='none' xmlns='http://www.w3.org/2000/svg'>
                    <path d='M25 19.6055C25 18.278 24.9991 17.3577 24.9404 16.6426C24.883 15.9425 24.7759 15.5499 24.626 15.2568V15.2559C24.2972 14.6137 23.7721 14.0909 23.125 13.7627C22.7942 13.595 22.343 13.4837 21.4863 13.4316C20.7964 13.3897 19.9321 13.3887 18.75 13.3887H8.75C7.56785 13.3887 6.70365 13.3897 6.01367 13.4316C5.15689 13.4837 4.70568 13.595 4.375 13.7627C3.72775 14.091 3.2028 14.6144 2.87402 15.2568C2.72409 15.5499 2.61704 15.9425 2.55957 16.6426C2.50088 17.3577 2.5 18.278 2.5 19.6055V21.7832C2.5 23.1107 2.50088 24.0309 2.55957 24.7461C2.61705 25.4465 2.72401 25.8397 2.87402 26.1328C3.20282 26.7751 3.72786 27.2987 4.375 27.627C4.67121 27.7772 5.06793 27.8831 5.77246 27.9404C6.49167 27.9989 7.41684 28 8.75 28H18.75C20.0832 28 21.0083 27.9989 21.7275 27.9404C22.432 27.8831 22.8287 27.7772 23.125 27.627C23.7721 27.2987 24.2972 26.7751 24.626 26.1328C24.776 25.8397 24.8829 25.4465 24.9404 24.7461C24.9991 24.0309 25 23.1107 25 21.7832V19.6055ZM20.3125 9.02734C20.3123 5.4276 17.3794 2.5 13.75 2.5C10.1205 2.5 7.18774 5.4276 7.1875 9.02734V10.8936C7.662 10.888 8.18153 10.8887 8.75 10.8887H18.75C19.3185 10.8887 19.838 10.888 20.3125 10.8936V9.02734ZM22.8125 11.0684C23.3248 11.1602 23.8023 11.3032 24.2559 11.5332C25.3726 12.0996 26.2816 13.0035 26.8516 14.1172C27.2125 14.8224 27.3614 15.5825 27.4316 16.4385C27.5007 17.2796 27.5 18.3195 27.5 19.6055V21.7832C27.5 23.0691 27.5006 24.1091 27.4316 24.9502C27.3614 25.8061 27.2125 26.5663 26.8516 27.2715C26.2816 28.3852 25.3726 29.29 24.2559 29.8564C23.5495 30.2147 22.7881 30.3628 21.9297 30.4326C21.0857 30.5012 20.0419 30.5 18.75 30.5H8.75C7.45813 30.5 6.41432 30.5012 5.57031 30.4326C4.71193 30.3628 3.9505 30.2147 3.24414 29.8564C2.19707 29.3253 1.33243 28.4969 0.758789 27.4775L0.648438 27.2715C0.287543 26.5663 0.138605 25.8061 0.0683596 24.9502C-0.000638187 24.1091 2.29428e-07 23.0691 2.29428e-07 21.7832V19.6055C1.85029e-07 18.3195 -0.000654526 17.2796 0.0683596 16.4385C0.138605 15.5825 0.287543 14.8224 0.648438 14.1172C1.21847 13.0035 2.12749 12.0996 3.24414 11.5332C3.69773 11.3031 4.17518 11.1602 4.6875 11.0684V9.02734C4.68774 4.0364 8.75033 0 13.75 0C18.7496 0 22.8123 4.0364 22.8125 9.02734V11.0684Z'/>
                </svg>
                <span class='for-friends'>Для друзей</span>
            </div>
        `;
    }

    if (postData.verify_status == 1) {
        postHTML += `<img class='user-status' src='pics/SuperUserIcon.svg'>`;
    } else if (postData.user_in_top > 0) {
        let statusIcon = '';
        switch (postData.user_in_top) {
            case 1:
                statusIcon = 'pics/BlossomFirstIcon.svg';
                break;
            case 2:
                statusIcon = 'pics/BlossomSecondIcon.svg';
                break;
            case 3:
                statusIcon = 'pics/BlossomThirdIcon.svg';
                break;
        }
        if (statusIcon) {
            postHTML += `<img class='user-status' src='${statusIcon}'>`;
        }
    }

    postHTML += `
            </div>
            <div class='div-show-three-dots-popup' onclick='showPopup(${postData.id})' id='div-show-three-dots-popup_${postData.id}'>
                <img src='pics/ThreeDotsIcon.svg' class='show-three-dots-popup'>
            </div>
            <div class='three-dots-popup' id='three-dots-popup_${postData.id}'>
    `;

    if (postData.author_id == postData.current_user_id) {
        postHTML += `<span class='three-dots-popup-li edit' onclick='editPost(${postData.id})'>Редактировать</span>`;
    }

    postHTML += `
                <span class='three-dots-popup-li copy-link' onclick='copyLinkToPost(${postData.id})'>Копировать ссылку</span>
                <a class='three-dots-popup-li open-profile' href='./user/${postData.username}'>Открыть профиль</a>
    `;

    if (postData.author_id == postData.current_user_id) {
        postHTML += `<span class='three-dots-popup-li delete-post' id='${postData.id}'>Удалить</span>`;
    }

    postHTML += `
            </div>
        </div>
    `;

    if (postData.hashtag_name) {
        postHTML += `<p class='main-text'>${postData.text} <a href='?search=${postData.hashtag_name}'>#${postData.hashtag_name}</a></p>`;
    } else {
        postHTML += `<p class='main-text'>${postData.text}</p>`;
    }

    if (postData.images && postData.images.length > 0) {
        const imagesCounter = postData.images.length;
        const imagesMark = imagesCounter > 1 ? `more-images images-${imagesCounter}` : '';

        postHTML += `<div class='images-in-post-div ${imagesMark}'>`;

        postData.images.forEach((imageUrl, index) => {
            if (imagesCounter === 1 && index === 0) {
                postHTML += `
                    <div class='image-in-post-div'>
                        <img class='image-in-post-hide' src='./uploads/post-image/small_${imageUrl}'>
                        <img class='image-in-post' src='./uploads/post-image/small_${imageUrl}'>
                    </div>
                `;
            } else {
                postHTML += `
                    <div class='image-in-post-div'>
                        <img class='image-in-post' src='./uploads/post-image/small_${imageUrl}'>
                    </div>
                `;
            }
        });

        postHTML += `</div>`;
    }

    postHTML += `
        <div class='post-buttons'>
    `;

    const isLiked = postData.is_liked;
    const likeButtonHTML = isLiked ? 'liked' : 'unliked';
    const hideClass = isLiked ? '' : 'hide';

    postHTML += `
        <button id='${postData.id}' class='like-button ${likeButtonHTML}'>
            <svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
                <path fill-rule="evenodd" clip-rule="evenodd" d='M21.3345 8.71342C21.0727 9.33749 20.6942 9.9081 20.2183 10.3954L20.148 10.4648L13.6656 16.8654C12.4711 18.0449 10.5278 18.0449 9.33329 16.8654L9.33326 16.8654L2.85134 10.4657C2.34261 9.96338 1.93992 9.36791 1.66547 8.7137L0.7102 9.11444L1.66547 8.71369C1.39104 8.05952 1.25 7.35894 1.25 6.65182C1.25 5.94469 1.39104 5.24411 1.66547 4.58994C1.93992 3.93572 2.34261 3.34025 2.85134 2.83794C3.36011 2.33559 3.96496 1.93628 4.63177 1.66356C5.29861 1.39084 6.01386 1.25027 6.73655 1.25027C7.45924 1.25027 8.17449 1.39084 8.84133 1.66356C9.50754 1.93603 10.1119 2.33487 10.6204 2.83658L11.4989 3.70337L11.4998 3.7025L11.5009 3.7036L12.3791 2.83686C12.8877 2.33489 13.4922 1.93586 14.1587 1.66329C14.8255 1.39057 15.5408 1.25 16.2634 1.25C16.9861 1.25 17.7014 1.39057 18.3682 1.66329C19.035 1.93601 19.6399 2.33532 20.1487 2.83767C20.6574 3.33999 21.0601 3.93546 21.3345 4.58967C21.609 5.24384 21.75 5.94442 21.75 6.65155C21.75 7.35867 21.609 8.05925 21.3345 8.71342Z' </svg>
        </button>
        <button id='${postData.id}' class='like-button ${isLiked ? 'unliked' : 'liked'} ${hideClass}'>
            <svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
                <path fill-rule="evenodd" clip-rule="evenodd" d='M21.3345 8.71342C21.0727 9.33749 20.6942 9.9081 20.2183 10.3954L20.148 10.4648L13.6656 16.8654C12.4711 18.0449 10.5278 18.0449 9.33329 16.8654L9.33326 16.8654L2.85134 10.4657C2.34261 9.96338 1.93992 9.36791 1.66547 8.7137L0.7102 9.11444L1.66547 8.71369C1.39104 8.05952 1.25 7.35894 1.25 6.65182C1.25 5.94469 1.39104 5.24411 1.66547 4.58994C1.93992 3.93572 2.34261 3.34025 2.85134 2.83794C3.36011 2.33559 3.96496 1.93628 4.63177 1.66356C5.29861 1.39084 6.01386 1.25027 6.73655 1.25027C7.45924 1.25027 8.17449 1.39084 8.84133 1.66356C9.50754 1.93603 10.1119 2.33487 10.6204 2.83658L11.4989 3.70337L11.4998 3.7025L11.5009 3.7036L12.3791 2.83686C12.8877 2.33489 13.4922 1.93586 14.1587 1.66329C14.8255 1.39057 15.5408 1.25 16.2634 1.25C16.9861 1.25 17.7014 1.39057 18.3682 1.66329C19.035 1.93601 19.6399 2.33532 20.1487 2.83767C20.6574 3.33999 21.0601 3.93546 21.3345 4.58967C21.609 5.24384 21.75 5.94442 21.75 6.65155C21.75 7.35867 21.609 8.05925 21.3345 8.71342Z' />
            </svg>
            <span class='like-counter'></span>
        </button>
    `;

    const commentCount = postData.comments_count || 0;
    postHTML += `
        <button onclick='commentButtonClick(${postData.id})' class='comment-button comment'>
            <svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
                <path d='M4 1.25L19 1.25C20.5188 1.25 21.75 2.48122 21.75 4L21.75 17.75L4 17.75C2.48122 17.75 1.25 16.5188 1.25 15L1.25 4C1.25 2.48122 2.48122 1.25 4 1.25Z' />
            </svg>
        </button>
    `;

    if (postData.for_friends == 0 && postData.type !== 'repost') {
        const isReposted = postData.is_reposted;
        const repostButtonClass = isReposted ? 'reposted' : 'unreposted';
        const repostHideClass = isReposted ? '' : 'hide';

        postHTML += `
            <button id='repost-${postData.id}' class='repost-button ${repostButtonClass}'>
                <svg width='27' height='22' viewBox='0 0 27 22' fill='none' xmlns='http://www.w3.org/2000/svg'>
                    <path d='M22.2501 4.41667V2.30556C22.2501 2.0256 22.0921 1.75712 21.8108 1.55917C21.5295 1.36121 21.1479 1.25 20.7501 1.25H5.75013C5.3523 1.25 4.97077 1.36121 4.68947 1.55917C4.40816 1.75712 4.25013 2.0256 4.25013 2.30556V12.8611M4.25013 12.8611L7.25012 10.75M4.25013 12.8611L1.25012 10.75M4.25012 17.0833V19.1944C4.25012 19.4744 4.40816 19.7429 4.68946 19.9408C4.97077 20.1388 5.3523 20.25 5.75012 20.25H20.7501C21.1479 20.25 21.5295 20.1388 21.8108 19.9408C22.0921 19.7429 22.2501 19.4744 22.2501 19.1944V8.63889M22.2501 8.63889L19.2501 10.75M22.2501 8.63889L25.2501 10.75' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'/>
                </svg>
            </button>
            <button id='repost-${postData.id}' class='repost-button ${isReposted ? 'unreposted' : 'reposted'} ${repostHideClass}'>
                <svg width='27' height='22' viewBox='0 0 27 22' fill='none' xmlns='http://www.w3.org/2000/svg'>
                    <path d='M22.2501 4.41667V2.30556C22.2501 2.0256 22.0921 1.75712 21.8108 1.55917C21.5295 1.36121 21.1479 1.25 20.7501 1.25H5.75013C5.3523 1.25 4.97077 1.36121 4.68947 1.55917C4.40816 1.75712 4.25013 2.0256 4.25013 2.30556V12.8611M4.25013 12.8611L7.25012 10.75M4.25013 12.8611L1.25012 10.75M4.25012 17.0833V19.1944C4.25012 19.4744 4.40816 19.7429 4.68946 19.9408C4.97077 20.1388 5.3523 20.25 5.75012 20.25H20.7501C21.1479 20.25 21.5295 20.1388 21.8108 19.9408C22.0921 19.7429 22.2501 19.4744 22.2501 19.1944V8.63889M22.2501 8.63889L19.2501 10.75M22.2501 8.63889L25.2501 10.75' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'/>
                </svg>
                <span class='repost-counter'>${postData.reposts}</span>
            </button>
        `;
    }

    postHTML += `
        </div>
        <div class='div-line'></div>     
        <div class='wall__comments'>
        <div class='other-users'></div>
            <div class='current-user'>
                <form action='' class='new-comment-form' method='post' autocomplete='off'>
                    <div contenteditable='true' class='textarea-comment' id='textarea-comment_${postData.id}' role='textbox' onkeyup='textareaComment(event, ${postData.id})' onkeydown='textareaCommentPlaceholder(event, ${postData.id})'></div>
                    <label for='textarea-comment' class='textarea-comment_label' id='textarea-comment_label_${postData.id}'>Ответить..</label>
                    <input type='hidden' required name='comment' class='textarea-comment_input' id='textarea-comment_input_${postData.id}' value=''>
                    <input type='hidden' name='comment_id' value='${postData.id}'>
                    <button type='submit' id='textarea-comment_submit_${postData.id}' class='textarea-comment_sumbit' disabled>
                        <svg width='28' height='28' viewBox='0 0 28 28' fill='none' xmlns='http://www.w3.org/2000/svg'>
                            <path fill-rule='evenodd' clip-rule='evenodd' d='M0 14C0 6.26801 6.26801 0 14 0C21.7319 0 28 6.26801 28 14C28 21.7319 21.7319 28 14 28C6.26801 28 0 21.7319 0 14ZM12.6 19.6C12.6 20.3732 13.2268 21 14 21C14.7732 21 15.4 20.3732 15.4 19.6V11.7799L17.2101 13.5899C17.7568 14.1366 18.6432 14.1366 19.1899 13.5899C19.7366 13.0432 19.7366 12.1568 19.1899 11.6101L15.1117 7.5319C15.0907 7.5108 15.0692 7.49043 15.0472 7.47078C14.7907 7.18197 14.4166 7 14 7C13.5834 7 13.2093 7.18197 12.9528 7.47078C12.9308 7.49042 12.9093 7.5108 12.8883 7.5319L8.81005 11.6101C8.26332 12.1568 8.26332 13.0432 8.81005 13.5899C9.35679 14.1366 10.2432 14.1366 10.79 13.5899L12.6 11.7799V19.6Z' />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    `;

    postDiv.innerHTML = postHTML;
    return postDiv;
}

function addPostToFeed(postData) {
    const feedContainer = document.querySelector('#success-render-posts');

    if (!feedContainer) {
        console.error('Контейнер ленты не найден');
        return;
    }

    const postElement = createPostElement(postData);

    if (feedContainer.firstChild) {
        feedContainer.insertBefore(postElement, feedContainer.firstChild);
    } else {
        feedContainer.appendChild(postElement);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('new-post-form');
    if (form) {
        form.addEventListener('submit', addPost);
    }
});

async function addComment(e) {
    e.preventDefault();

    const form = e.target;
    const postId = form.querySelector('input[name="comment_id"]').value;
    const commentTextarea = form.querySelector('.textarea-comment');
    const commentInput = form.querySelector('.textarea-comment_input');
    const commentLabel = form.querySelector('.textarea-comment_label');

    const commentText = commentTextarea.innerHTML.trim();
    commentInput.value = commentText;

    if (!commentText) {
        return;
    }

    const textareaPost = document.getElementById('textarea-comment_' + postId);
    textareaPost.setAttribute('contenteditable', false);

    const submitBtn = document.getElementById('textarea-comment_submit_' + postId);
    submitBtn.classList.remove('active');
    submitBtn.disabled = true;

    const submitBtnLoading = document.getElementById('textarea-comment_sumbit_loading_' + postId);
    submitBtnLoading.classList.add('uploading');

    try {
        const formData = new FormData(form);

        const response = await $.ajax({
            url: "back-files/comment",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json'
        });

        if (response.success) {
            commentTextarea.innerHTML = '';
            commentLabel.style.display = 'block';

            $('.comment_div-line_' + postId).removeClass('hide');
            $('.comment_user-comment_' + postId).removeClass('hide');
            $('#have-else-comments_dot_' + postId).removeClass('hide');
            $('#have-else-comments_a_' + postId).removeClass('hide');
            if ($('#see-all-comments_' + postId).text() == 'Показать комментарии') {
                $('#see-all-comments_' + postId).text('Скрыть');
            }

            addCommentToPost(postId, response.comment);
            updateCommentCounter(postId, 'increment');
        } else {
            throw new Error(response.message || 'Ошибка при отправке комментария');
        }
    } catch (error) {
        console.error('Ошибка при отправке комментария:', error);
    } finally {
        textareaPost.setAttribute('contenteditable', true);
        submitBtn.disabled = false;
        submitBtnLoading.classList.remove('uploading');

    }
}

function addCommentToPost(postId, commentData) {
    const postContainer = document.querySelector(`#post-${postId}`);
    if (!postContainer) return;

    const commentsContainer = postContainer.querySelector('.other-users');
    const currentUserForm = postContainer.querySelector('.current-user');

    const commentElement = createCommentElement(commentData, postId);

    if (commentsContainer) {
        if (!commentsContainer.querySelector('.user-comment')) {
            commentsContainer.innerHTML = '';
        }

        commentsContainer.appendChild(commentElement);

        commentsContainer.style.display = 'block';

    }
}

// Функция для создания элемента комментария
function createCommentElement(commentData, postId) {
    const commentDiv = document.createElement('div');
    commentDiv.className = 'user-comment new-comment';
    commentDiv.id = `comment-${commentData.id}`;

    // Определяем класс для имени пользователя
    const nameClass = commentData.verify_status ? 'first-and-second-names trust' : 'first-and-second-names';

    // Определяем отображаемое имя
    let displayName;
    if (commentData.first_name || commentData.second_name) {
        displayName = `${commentData.first_name} ${commentData.second_name}`;
    } else {
        displayName = `@${commentData.username}`;
    }

    // Создаем кнопку удаления если это комментарий текущего пользователя
    const deleteButton =
        `<span class='delete-comment' id='${commentData.id}'>
            <svg width='10' height='10' viewBox='0 0 10 10' fill='none' xmlns='http://www.w3.org/2000/svg'>
                <path d='M0.191016 8.88671C-0.0636719 9.14141 -0.0636719 9.55428 0.191016 9.80898C0.445703 10.0637 0.858643 10.0637 1.11333 9.80898L0.191016 8.88671ZM5.46114 5.46114C5.71584 5.20644 5.71584 4.79357 5.46114 4.53888C5.20644 4.28418 4.79357 4.28418 4.53888 4.53888L5.46114 5.46114ZM4.53888 4.53888C4.28418 4.79357 4.28418 5.20644 4.53888 5.46114C4.79357 5.71584 5.20644 5.71584 5.46114 5.46114L4.53888 4.53888ZM9.80898 1.11333C10.0637 0.858644 10.0637 0.445703 9.80898 0.191016C9.55428 -0.0636719 9.14141 -0.0636719 8.88671 0.191016L9.80898 1.11333ZM5.46114 4.53888C5.20644 4.28418 4.79357 4.28418 4.53888 4.53888C4.28418 4.79357 4.28418 5.20644 4.53888 5.46114L5.46114 4.53888ZM8.88671 9.80898C9.14141 10.0637 9.55428 10.0637 9.80898 9.80898C10.0637 9.55428 10.0637 9.14141 9.80898 8.88671L8.88671 9.80898ZM4.53888 5.46114C4.79357 5.71584 5.20644 5.71584 5.46114 5.46114C5.71584 5.20644 5.71584 4.79357 5.46114 4.53888L4.53888 5.46114ZM1.11333 0.191016C0.858643 -0.0636719 0.445703 -0.0636719 0.191016 0.191016C-0.0636719 0.445703 -0.0636719 0.858644 0.191016 1.11333L1.11333 0.191016ZM1.11333 9.80898L5.46114 5.46114L4.53888 4.53888L0.191016 8.88671L1.11333 9.80898ZM5.46114 5.46114L9.80898 1.11333L8.88671 0.191016L4.53888 4.53888L5.46114 5.46114ZM4.53888 5.46114L8.88671 9.80898L9.80898 8.88671L5.46114 4.53888L4.53888 5.46114ZM5.46114 4.53888L1.11333 0.191016L0.191016 1.11333L4.53888 5.46114L5.46114 4.53888Z' />
            </svg>
        </span>`;

    commentDiv.innerHTML = `
        <a href='./user/${commentData.username}'><img class='comment-avatar' src='uploads/avatar/thin_${commentData.avatar}'></a>
        <div class='comment-div'>
            <div>
                <a href='./user/${commentData.username}' class='${nameClass}'>${displayName}</a>
                <span class='date'>${commentData.date}</span>
                ${deleteButton}
            </div>
            <p class='comment-text main-text'>${commentData.text}</p>
            <span class='date mobile'>${commentData.date}</span>
        </div>
    `;

    return commentDiv;
}

// Функция для обновления счетчика комментариев
function updateCommentCounter(postId, action = 'increment') {
    const commentButton = document.querySelector(`#post-${postId} .comment-button`);
    if (!commentButton) return;

    const counterSpan = commentButton.querySelector('.comment-counter');
    let currentCount = 0;

    if (counterSpan) {
        currentCount = parseInt(counterSpan.textContent);
    }

    if (action === 'increment') {
        currentCount++;
    } else if (action === 'decrement') {
        currentCount = Math.max(0, currentCount - 1);
    }

    if (currentCount > 0) {
        if (!counterSpan) {
            const newCounter = document.createElement('span');
            newCounter.className = 'comment-counter';
            newCounter.textContent = currentCount;
            commentButton.appendChild(newCounter);
        } else {
            counterSpan.textContent = currentCount;
        }
    } else if (counterSpan) {
        counterSpan.remove();
    }
}

document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('submit', function (e) {
        if (e.target.matches('.new-comment-form')) {
            e.preventDefault();
            addComment(e);
        }
    });
});

$(document).ready(function () {
    renderPosts('main', search);

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
                $post.siblings('.like-button').removeClass('hide');
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
                $post.siblings('.like-button').removeClass('hide');
            }
        })
    })

    $('.wall__user-posts').on('click', '.unreposted', function () {
        const postId = $(this).attr('id').split('-')[1];
        $post = $(this);
        $.ajax({
            url: 'wall',
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

    $('.wall__user-posts').on('click', '.reposted', function () {
        const postId = $(this).attr('id').split('-')[1];
        $post = $(this);
        $.ajax({
            url: 'wall',
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

    $('.wall__user-posts').on('click', '.delete-post', function () {
        const postId = $(this).attr('id');
        $deletePost = $(this);
        $.ajax({
            url: 'back-files/delete-post',
            type: 'post',
            data: {
                'post_id': postId,
            },
            success: function (response) {
                $deletePost.parent().parent().parent().removeClass('new-post')
                $deletePost.parent().parent().parent().addClass('deleted')
                setTimeout(() => {
                    $deletePost.parent().parent().parent().addClass('hide')
                }, 700)
            }
        })
    })

    $('.wall__user-posts').on('click', '.delete-comment', function () {
        const commentId = $(this).attr('id');
        $deleteComment = $(this);
        $.ajax({
            url: 'back-files/delete-comment',
            type: 'post',
            data: {
                'comment_id': commentId,
            },
            success: function (response) {
                $deleteComment.parent().parent().parent().removeClass('new-comment')
                $deleteComment.parent().parent().parent().addClass('deleted')
                setTimeout(() => {
                    $deleteComment.parent().parent().parent().addClass('hide')
                }, 700)
            }
        })
    })

    $('#wall-filter-main').click(() => {
        renderPosts('main', search, true);
        $unreadMainPost = Number($('#notification-in-filter__unread-main-posts').text())
        $('#notification__unread-posts').text(Number($('#notification__unread-posts').text()) - $unreadMainPost > 0 ? Number($('#notification__unread-posts').text()) - $unreadMainPost : '')
        $('#notification-in-filter__unread-main-posts').addClass('hide')
    })
    $('#wall-filter-all').click(() => {
        renderPosts('all', search, true);
        $unreadAllPost = Number($('#notification-in-filter__unread-all-posts').text())
        $('#notification__unread-posts').text(Number($('#notification__unread-posts').text()) - $unreadAllPost > 0 ? Number($('#notification__unread-posts').text()) - $unreadAllPost : '')
        $('#notification-in-filter__unread-all-posts').addClass('hide')
    })
    $('#wall-filter-timetable').click(() => {
        renderPosts('timetable', search, true);
        $unreadThirtySeventhPost = Number($('#notification-in-filter__unread-thirty-seventh-posts').text())
        $('#notification__unread-posts').text(Number($('#notification__unread-posts').text()) - $unreadThirtySeventhPost > 0 ? Number($('#notification__unread-posts').text()) - $unreadThirtySeventhPost : '')
        $('#notification-in-filter__unread-thirty-seventh-posts').addClass('hide')
    })
    $('#wall-filter-main__mobile').click(() => {
        renderPosts('main', search, true);
        $unreadMainPost = Number($('#notification-in-filter__unread-main-posts-mobile').text())
        if (Number($('#notification__unread-posts-mobile').text()) - $unreadMainPost > 0) {
            $('#notification__unread-posts-mobile').text(Number($('#notification__unread-posts-mobile').text()) - $unreadMainPost)
        } else {
            $('#notification__unread-posts-mobile').text('')
            $('#notification__unread-posts-mobile').removeClass('active')
        }
        $('#notification-in-filter__unread-main-posts-mobile').addClass('hide')
    })
    $('#wall-filter-all__mobile').click(() => {
        renderPosts('all', search, true);
        $unreadAllPost = Number($('#notification-in-filter__unread-all-posts-mobile').text())
        if (Number($('#notification__unread-posts-mobile').text()) - $unreadAllPost > 0) {
            $('#notification__unread-posts-mobile').text(Number($('#notification__unread-posts-mobile').text()) - $unreadAllPost)
        } else {
            $('#notification__unread-posts-mobile').text('')
            $('#notification__unread-posts-mobile').removeClass('active')
        }
        $('#notification-in-filter__unread-all-posts-mobile').addClass('hide')
    })
    $('#wall-filter-timetable__mobile').click(() => {
        renderPosts('timetable', search, true);
        $unreadThirtySeventhPost = Number($('#notification-in-filter__unread-thirty-seventh-mobile').text())
        if (Number($('#notification__unread-posts-mobile').text()) - $unreadThirtySeventhPost > 0) {
            $('#notification__unread-posts-mobile').text(Number($('#notification__unread-posts-mobile').text()) - $unreadThirtySeventhPost)
        } else {
            $('#notification__unread-posts-mobile').text('')
            $('#notification__unread-posts-mobile').removeClass('active')
        }
        $('#notification-in-filter__unread-thirty-seventh-posts-mobile').addClass('hide')
    })
})

$('#textarea-post').keypress(function (e) {
    if (e.which === 13 && !e.shiftKey) {
        e.preventDefault();
        if (($('#textarea-post').text().trim(' ') != '') || ($('#post-image').val().length)) {
            addPost(e)
        }
    }
});

$(document).on('keypress', '.textarea-comment', function (e) {
    if (e.which === 13 && !e.shiftKey) {
        e.preventDefault();
        const $textarea = $(this);
        const commentText = $textarea.text().trim();

        if (commentText !== '') {
            const $form = $textarea.closest('form');

            const submitEvent = new Event('submit', {
                bubbles: true,
                cancelable: true
            });

            $form[0].dispatchEvent(submitEvent);
        }
    }
});

function commentButtonClick(i) {
    $('#textarea-comment_' + i).trigger('focus');
}

function seeAllComments(i) {
    $('.comment_div-line_' + i).toggleClass('hide');
    $('.comment_user-comment_' + i).toggleClass('hide');
    $('#have-else-comments_dot_' + i).toggleClass('hide');
    $('#have-else-comments_a_' + i).toggleClass('hide');
    if ($('#see-all-comments_' + i).text() == 'Показать комментарии') {
        $('#see-all-comments_' + i).text('Скрыть');
    } else {
        $('#see-all-comments_' + i).text('Показать комментарии');
    }
}

function textareaPost(e) {
    const obj = e.target;
    const div = document.getElementById(obj.id + '_input');
    if (!isUploading && (($('#textarea-post').text().trim(' ') != '') || ($('#post-image').val().length))) {
        $('#textarea-post_sumbit').addClass('active');
        $('#textarea-post_sumbit').removeAttr('disabled');
    } else {
        $('#textarea-post_sumbit').removeClass('active');
        $('#textarea-post_sumbit').attr('disabled');
    }
    div.setAttribute('value', obj.textContent);
}

function textareaPostPlaceholder(e) {
    if (e.key != ' ') {
        document.getElementById('textarea-post_label').style.display = 'none';
    }
    if ((document.getElementById('textarea-post').textContent.length < 2) && (e.key == "Backspace")) {
        document.getElementById('textarea-post_label').style.display = 'block';
    }
}

function textareaCommentPlaceholder(e, i) {
    document.getElementById('textarea-comment_label_' + i).style.display = 'none';
    if ((document.getElementById('textarea-comment_' + i).textContent.length < 2) && (e.key == "Backspace")) {
        document.getElementById('textarea-comment_label_' + i).style.display = 'block';
    }
}

function textareaComment(e, i) {
    const obj = e.target;
    const div = document.getElementById('textarea-comment_input_' + i);
    if ($('#textarea-comment_' + i).text().trim(' ') != '') {
        $('#textarea-comment_submit_' + i).addClass('active');
        $('#textarea-comment_submit_' + i).removeAttr('disabled');
    } else {
        $('#textarea-comment_submit_' + i).removeClass('active');
        $('#textarea-comment_submit_' + i).attr('disabled');
    }
    div.setAttribute('value', obj.textContent);
}

function showPopupWallFilter() {
    document.getElementById('popup_wall-filter').classList.toggle('show');
    document.getElementById('wall-filter').classList.toggle('show');
    document.getElementById('active').classList.toggle('show');
}

function showPopupWallFilterMobile() {
    document.getElementById('wall-filter-mobile').classList.toggle('show');
    document.getElementById('wall-filter-mobile-info').classList.toggle('show');
    document.getElementById('popup_wall-filter-mobile').classList.toggle('show');
}

$('#add-image-button').click(() => {
    $('#post-image').trigger('click');
})

const postImage = document.getElementById('post-image');
const imagesContainer = document.querySelector('.current-post-images-div');
const addImageButton = document.getElementById('add-image-button');
const fotoCounterNotification = document.getElementById('foto-counter-notification');
const MAX_IMAGES = 10;

postImage.addEventListener('change', (e) => {
    const files = Array.from(e.target.files);
    const currentImagesCount = getCurrentImagesCount();

    imagesContainer.innerHTML = '';

    if (currentImagesCount + files.length > MAX_IMAGES) {

        const allowedFiles = files.slice(0, MAX_IMAGES - currentImagesCount);
        if (allowedFiles.length === 0) {
            postImage.value = '';
            return;
        }

        const dataTransfer = new DataTransfer();
        allowedFiles.forEach(file => dataTransfer.items.add(file));
        postImage.files = dataTransfer.files;

        processFiles(allowedFiles);
    } else {
        processFiles(files);
    }

});

function processFiles(files) {
    if (files.length > 0) {
        files.forEach((file, index) => {
            const fileType = file.type.startsWith('image');

            if (fileType) {
                const reader = new FileReader();

                reader.onload = (event) => {
                    const currentImagesCount = getCurrentImagesCount();
                    const imageId = 'post-image-' + Date.now() + '-' + index + '-' + Math.random().toString(36);

                    const imageContainer = document.createElement('div');
                    imageContainer.className = 'current-post-image-div';
                    imageContainer.dataset.index = currentImagesCount + index;
                    imageContainer.dataset.fileId = imageId;

                    const img = document.createElement('img');
                    img.src = event.target.result;
                    img.className = 'current-post-image';
                    img.id = imageId;

                    const deleteBtn = document.createElement('button');
                    deleteBtn.className = 'post-image-delete';
                    deleteBtn.title = 'Удалить изображение';
                    deleteBtn.dataset.action = 'delete-image';
                    deleteBtn.dataset.targetId = imageId;
                    deleteBtn.dataset.index = currentImagesCount + index;

                    const svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
                    svg.setAttribute("width", "10");
                    svg.setAttribute("height", "10");
                    svg.setAttribute("viewBox", "0 0 10 10");
                    svg.setAttribute("fill", "none");
                    const path = document.createElementNS(svg.namespaceURI, "path");
                    path.setAttribute("d", "M0.191016 8.88671C-0.0636719 9.14141 -0.0636719 9.55428 0.191016 9.80898C0.445703 10.0637 0.858643 10.0637 1.11333 9.80898L0.191016 8.88671ZM5.46114 5.46114C5.71584 5.20644 5.71584 4.79357 5.46114 4.53888C5.20644 4.28418 4.79357 4.28418 4.53888 4.53888L5.46114 5.46114ZM4.53888 4.53888C4.28418 4.79357 4.28418 5.20644 4.53888 5.46114C4.79357 5.71584 5.20644 5.71584 5.46114 5.46114L4.53888 4.53888ZM9.80898 1.11333C10.0637 0.858644 10.0637 0.445703 9.80898 0.191016C9.55428 -0.0636719 9.14141 -0.0636719 8.88671 0.191016L9.80898 1.11333ZM5.46114 4.53888C5.20644 4.28418 4.79357 4.28418 4.53888 4.53888C4.28418 4.79357 4.28418 5.20644 4.53888 5.46114L5.46114 4.53888ZM8.88671 9.80898C9.14141 10.0637 9.55428 10.0637 9.80898 9.80898C10.0637 9.55428 10.0637 9.14141 9.80898 8.88671L8.88671 9.80898ZM4.53888 5.46114C4.79357 5.71584 5.20644 5.71584 5.46114 5.46114C5.71584 5.20644 5.71584 4.79357 5.46114 4.53888L4.53888 5.46114ZM1.11333 0.191016C0.858643 -0.0636719 0.445703 -0.0636719 0.191016 0.191016C-0.0636719 0.445703 -0.0636719 0.858644 0.191016 1.11333L1.11333 0.191016ZM1.11333 9.80898L5.46114 5.46114L4.53888 4.53888L0.191016 8.88671L1.11333 9.80898ZM5.46114 5.46114L9.80898 1.11333L8.88671 0.191016L4.53888 4.53888L5.46114 5.46114ZM4.53888 5.46114L8.88671 9.80898L9.80898 8.88671L5.46114 4.53888L4.53888 5.46114ZM5.46114 4.53888L1.11333 0.191016L0.191016 1.11333L4.53888 5.46114L5.46114 4.53888Z");
                    svg.appendChild(path);
                    deleteBtn.append(svg);

                    imageContainer.appendChild(img);
                    imageContainer.appendChild(deleteBtn);

                    imagesContainer.appendChild(imageContainer);

                    updateUIForImages();
                };

                reader.readAsDataURL(file);
            }
        });
    }
}

document.addEventListener('click', function (e) {
    const target = e.target;
    const deleteBtn = target.closest('[data-action="delete-image"]');

    if (deleteBtn) {
        const imageId = deleteBtn.dataset.targetId;
        const index = parseInt(deleteBtn.dataset.index);
        removePostImage(imageId, index);
        e.preventDefault();
        e.stopPropagation();
    }
});

function removePostImage(imageId, index) {
    const container = document.querySelector(`.current-post-image-div[data-file-id="${imageId}"]`);

    if (container) {
        container.remove();
        removeFileFromInput(index);
        updateUIForImages();
        updateImageIndexes();
    }
}

function removeFileFromInput(index) {
    const input = document.getElementById('post-image');
    const files = Array.from(input.files);

    if (files.length > index) {
        files.splice(index, 1);

        const dataTransfer = new DataTransfer();
        files.forEach(file => dataTransfer.items.add(file));

        input.files = dataTransfer.files;
    }
}

function updateImageIndexes() {
    const containers = document.querySelectorAll('.current-post-image-div');

    containers.forEach((container, newIndex) => {
        container.dataset.index = newIndex;

        const deleteBtn = container.querySelector('[data-action="delete-image"]');
        if (deleteBtn) {
            deleteBtn.dataset.index = newIndex;
        }
    });
}

function getCurrentImagesCount() {
    return document.querySelectorAll('.current-post-image-div').length;
}

function updateUIForImages() {
    const currentCount = getCurrentImagesCount();
    const hasText = $('#textarea-post').text().trim() !== '';

    fotoCounterNotification.textContent = currentCount > 0 ? currentCount : '';

    if (currentCount > 0) {
        fotoCounterNotification.classList.add('active');
    } else {
        fotoCounterNotification.classList.remove('active');
    }

    if (currentCount > 0) {
        $('.current-post-images-div').addClass('image-uploaded');
        $('#textarea-post').addClass('image-uploaded');
        $('.post-image-icon').addClass('mode__has-image');

        $('#textarea-post_sumbit').addClass('image-uploaded');
    } else {
        $('.current-post-images-div').removeClass('image-uploaded');
        $('#textarea-post').removeClass('image-uploaded');
        $('.post-image-icon').removeClass('mode__has-image');
        $('#textarea-post_sumbit').removeClass('image-uploaded');
    }

    if (currentCount >= MAX_IMAGES) {
        addImageButton.classList.add('disactive');
        addImageButton.disabled = true;
        addImageButton.title = `Максимум ${MAX_IMAGES} изображений`;
    } else {
        addImageButton.classList.remove('disactive');
        addImageButton.disabled = false;
        addImageButton.title = 'Добавить фото';
    }

    if (hasText || currentCount > 0) {
        $('#textarea-post_sumbit').addClass('active').prop('disabled', false);
    } else {
        $('#textarea-post_sumbit').removeClass('active').prop('disabled', true);
    }

    if (currentCount > 0) {
        fotoCounterNotification.textContent = `${currentCount} из ${MAX_IMAGES}`;
    }

    $('#textarea-post').trigger('focus');
}

document.addEventListener('DOMContentLoaded', function () {
    updateUIForImages();
});

function showPostModePopup() {
    $('#post-mode-fieldset').toggleClass('show');
}

$(document).on('click', function (e) {
    if (($(e.target).closest('#mode__for-friends')).length) {
        $('.post-mode-div').addClass('mode__for-friends')
    } else if (($(e.target).closest('#mode__for-all')).length) {
        $('.post-mode-div').removeClass('mode__for-friends')
    }
    if (!($(e.target).closest('.post-mode-div').length)) {
        $(this).find('#post-mode-fieldset').removeClass('show')
    }
})
