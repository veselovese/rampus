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

function createCommentElement(commentData, postId) {
    const commentDiv = document.createElement('div');
    commentDiv.className = 'user-comment new-comment';
    commentDiv.id = `comment-${commentData.id}`;

    const nameClass = commentData.verify_status ? 'first-and-second-names trust' : 'first-and-second-names';

    let displayName;
    if (commentData.first_name || commentData.second_name) {
        displayName = `${commentData.first_name} ${commentData.second_name}`;
    } else {
        displayName = `@${commentData.username}`;
    }

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