<?php
session_start();
require_once('connect.php');
require('blossoming.php');

ob_start();
header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => ''
];

if (!isset($_SESSION['user']['id'])) {
    $response['message'] = 'Необходима авторизация';
    ob_end_clean();
    echo json_encode($response);
    exit();
}

$user_id = $_SESSION['user']['id'];

if (!isset($_POST['comment_id'])) {
    $response['message'] = 'Отсутствует ID комментария';
    ob_end_clean();
    echo json_encode($response);
    exit();
}

$comment_id = (int)$_POST['comment_id'];

$check_query = $connect->query("SELECT user_id, post_id FROM comments WHERE id = $comment_id");
if ($check_query->num_rows === 0) {
    $response['message'] = 'Комментарий не найден';
    ob_end_clean();
    echo json_encode($response);
    exit();
}

$comment_data = $check_query->fetch_assoc();
if ($comment_data['user_id'] != $user_id) {
    $response['message'] = 'Вы не можете удалить этот комментарий';
    ob_end_clean();
    echo json_encode($response);
    exit();
}

$post_id = $comment_data['post_id'];
$post_info = $connect->query("SELECT user_id, for_friends FROM posts WHERE id = $post_id")->fetch_assoc();
$post_owner_id = $post_info['user_id'];
$for_friends_status = $post_info['for_friends'];

if ($for_friends_status == 0 && $user_id != $post_owner_id) {
    blossoming('delete-self-comment', $user_id, $connect);
    blossoming('comment-deleted-under-post-by', $post_owner_id, $connect);
}

$replies_query = $connect->query("SELECT c.id AS reply_id, c.user_id AS reply_author_id, p.user_id AS post_owner_id 
                                  FROM posts p 
                                  JOIN comments c ON p.id = c.post_id 
                                  WHERE c.reply_comment_id = $comment_id");

if ($replies_query->num_rows > 0) {
    while ($reply_row = $replies_query->fetch_assoc()) {
        $reply_id = $reply_row['reply_id'];
        $reply_author_id = $reply_row['reply_author_id'];

        $is_self_comment = ($reply_author_id == $post_owner_id);

        if ($for_friends_status == 0 && !$is_self_comment) {
            blossoming('delete-self-comment', $reply_author_id, $connect);
            blossoming('comment-deleted-under-post-by', $post_owner_id, $connect);
        }

        $likes_query = $connect->query("SELECT user_id FROM likes_on_comments WHERE comment_id = $reply_id");

        if ($likes_query->num_rows > 0) {
            while ($like_row = $likes_query->fetch_assoc()) {
                $liker_id = $like_row['user_id'];

                $is_self_like = ($reply_author_id == $liker_id);

                if ($for_friends_status == 0 && !$is_self_like) {
                    blossoming('dislike-comment', $liker_id, $connect);
                    blossoming('comment-is-disliked-by', $reply_author_id, $connect);
                }

                $connect->query("DELETE FROM likes_on_comments WHERE comment_id = $reply_id AND user_id = $liker_id LIMIT 1");
            }
        }

        $connect->query("DELETE FROM comments WHERE id = $reply_id AND user_id = $reply_author_id LIMIT 1");
    }
}

$main_likes_query = $connect->query("SELECT user_id FROM likes_on_comments WHERE comment_id = $comment_id");

if ($main_likes_query->num_rows > 0) {
    while ($like_row = $main_likes_query->fetch_assoc()) {
        $liker_id = $like_row['user_id'];

        if ($for_friends_status == 0 && $user_id != $liker_id) {
            blossoming('dislike-comment', $liker_id, $connect);
            blossoming('comment-is-disliked-by', $user_id, $connect);
        }

        $connect->query("DELETE FROM likes_on_comments WHERE comment_id = $comment_id AND user_id = $liker_id LIMIT 1");
    }
}

$delete_result = $connect->query("DELETE FROM comments WHERE id = $comment_id");

if ($delete_result) {
    $response['success'] = true;
    $response['message'] = 'Комментарий успешно удален';
    $response['post_id'] = $comment_data['post_id'];
} else {
    $response['message'] = 'Ошибка при удалении комментария: ' . mysqli_error($connect);
}

ob_end_clean();
echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit();
