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

function createCommentElement(commentData, postId, currentUserId) {
    const commentDiv = document.createElement('div');
    commentDiv.className = 'user-comment'; // Класс 'new-comment' убран для соответствия PHP, можно добавить обратно, если нужна анимация
    commentDiv.id = `comment-${commentData.id}`;

    const nameClass = commentData.verify_status ? 'first-and-second-names trust' : 'first-and-second-names';

    let displayName;
    if (commentData.first_name || commentData.second_name) {
        displayName = `${commentData.first_name} ${commentData.second_name}`;
    } else {
        displayName = `@${commentData.username}`;
    }

    let deleteButtonHtml = `<button type='button' class='delete-comment' id='${commentData.id}'>Удалить</button>`;

    const replyId = `${commentData.username}_${commentData.id}_${postId}`;
    const replyButtonHtml = `<button type='button' class='reply-to-comment' id='${replyId}'>Ответить</button>`;

    const svgEmpty = `<svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'><path d='M21.3345 8.71342C21.0727 9.33749 20.6942 9.9081 20.2183 10.3954L20.148 10.4648L13.6656 16.8654C12.4711 18.0449 10.5278 18.0449 9.33329 16.8654L9.33326 16.8654L2.85134 10.4657C2.34261 9.96338 1.93992 9.36791 1.66547 8.7137L0.7102 9.11444L1.66547 8.71369C1.39104 8.05952 1.25 7.35894 1.25 6.65182C1.25 5.94469 1.39104 5.24411 1.66547 4.58994C1.93992 3.93572 2.34261 3.34025 2.85134 2.83794C3.36011 2.33559 3.96496 1.93628 4.63177 1.66356C5.29861 1.39084 6.01386 1.25027 6.73655 1.25027C7.45924 1.25027 8.17449 1.39084 8.84133 1.66356C9.50754 1.93603 10.1119 2.33487 10.6204 2.83658L11.4989 3.70337L11.4998 3.7025L11.5009 3.7036L12.3791 2.83686C12.8877 2.33489 13.4922 1.93586 14.1587 1.66329C14.8255 1.39057 15.5408 1.25 16.2634 1.25C16.9861 1.25 17.7014 1.39057 18.3682 1.66329C19.035 1.93601 19.6399 2.33532 20.1487 2.83767C20.6574 3.33999 21.0601 3.93546 21.3345 4.58967C21.609 5.24384 21.75 5.94442 21.75 6.65155C21.75 7.35867 21.609 8.05925 21.3345 8.71342Z' /></svg>`;
    const svgFilled = `<svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'><path fill-rule='evenodd' clip-rule='evenodd' d='M15.3643 17.1232L21.0488 11.4387L21.0494 11.4394C21.6548 10.834 22.135 10.1153 22.4626 9.32432C22.7903 8.53335 22.9589 7.6856 22.9589 6.82947C22.9589 5.97334 22.7903 5.12559 22.4626 4.33463C22.135 3.54366 21.6548 2.82498 21.0494 2.2196C20.4441 1.61423 19.7254 1.13402 18.9344 0.806393C18.1434 0.478767 17.2957 0.310142 16.4396 0.310145C15.5834 0.310148 14.7357 0.478778 13.9447 0.806409C13.1541 1.13391 12.4356 1.61388 11.8304 2.21893L11.8289 2.21742L11.8279 2.21836C11.2229 1.61375 10.5048 1.1341 9.71455 0.806772C8.92359 0.479147 8.07584 0.310521 7.2197 0.310524C6.36357 0.310526 5.51582 0.479157 4.72486 0.806787C3.93389 1.13442 3.2152 1.61463 2.60982 2.22001C2.00444 2.82539 1.52423 3.54408 1.1966 4.33504C0.868969 5.12601 0.700339 5.97376 0.700335 6.82989C0.700332 7.68602 0.868959 8.53377 1.19658 9.32474C1.52421 10.1157 2.00442 10.8344 2.60979 11.4398L2.60985 11.4397L8.29331 17.1232C10.2459 19.0758 13.4117 19.0758 15.3643 17.1232Z'/></svg>`;

    const likesCount = commentData.likes || 0;

    const unlikedCounterHtml = likesCount > 0 ? `<span class='like-counter'>${likesCount}</span>` : "";
    const likedCounterHtml = `<span class='like-counter'>${likesCount}</span>`;

    commentDiv.innerHTML = `
        <a href='./user/${commentData.username}'><img class='comment-avatar' src='uploads/avatar/thin_${commentData.avatar}'></a>
        <div class='comment-div'>
            <div>
                <a href='./user/${commentData.username}' class='${nameClass}'>${displayName}</a>
                <span class='date'>${commentData.date}</span>
            </div>
            <p class='comment-text main-text'>${commentData.text}</p>
            
            <div class='comment-buttons'>
                ${replyButtonHtml}
                ${deleteButtonHtml}
            </div>

            <button data-post-id='${postId}' id='${commentData.id}' class='comment_like-button liked-comment hide'>${svgFilled}${likedCounterHtml}</button>
            <button data-post-id='${postId}' id='${commentData.id}' class='comment_like-button unliked-comment'>${svgEmpty}${unlikedCounterHtml}</button>
        </div>
    `;

    return commentDiv;
}

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

async function deleteComment(commentId, postId) {
    try {
        const response = await $.ajax({
            url: "back-files/delete-comment",
            method: "POST",
            data: { comment_id: commentId },
            dataType: 'json'
        });

        if (response.success) {
            const commentElement = document.querySelector(`#comment-${commentId}`);
            commentElement.classList.remove('new-comment')
            commentElement.classList.add('deleted')
            setTimeout(() => {
                commentElement.classList.add('hide')
            }, 700)

            updateCommentCounter(postId, 'decrement');
        } else {
            throw new Error(response.message || 'Ошибка при удалении комментария');
        }
    } catch (error) {
        console.error('Ошибка при удалении комментария:', error);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('submit', function (e) {
        if (e.target.matches('.new-comment-form')) {
            e.preventDefault();
            addComment(e);
        }
    });

    $('.wall__user-posts').on('click', '.delete-comment', function (e) {
        const deleteBtn = e.target.closest('.delete-comment');
        const commentElement = deleteBtn.closest('.user-comment');
        const postContainer = commentElement.closest('.user-post');
        const postId = postContainer ? postContainer.id.replace('post-', '') : null;
        if (postId) {
            deleteComment(deleteBtn.id, postId);
        }
    })
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