<?php
session_start();
require_once('connect.php');

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

// Проверяем, принадлежит ли комментарий пользователю
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

// Удаляем комментарий
$delete_result = $connect->query("DELETE FROM comments WHERE id = $comment_id");
$other_id = $connect->query("SELECT p.user_id FROM posts p LEFT JOIN comments c ON p.id = comments.post_id WHERE c.id = $comment_id")->fetch_assoc()['user_id'];

blossoming('delete-self-comment', $user_id,  $connect);
blossoming('comment-deleted-under-post-by', $other_id, $connect);

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
