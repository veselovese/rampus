<?php
session_start();
require_once('connect.php');
require('blossoming.php');

ob_start();
header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => '',
    'comment' => null
];

if (!isset($_SESSION['user']['id'])) {
    $response['message'] = 'Необходима авторизация';
    ob_end_clean();
    echo json_encode($response);
    exit();
}

$user_id = $_SESSION['user']['id'];

if (!isset($_POST['comment']) || !isset($_POST['comment_id'])) {
    $response['message'] = 'Отсутствуют обязательные данные';
    ob_end_clean();
    echo json_encode($response);
    exit();
}

$comment = mysqli_real_escape_string($connect, trim($_POST['comment']));
$post_id = (int)$_POST['comment_id'];

if (empty($comment)) {
    $response['message'] = 'Комментарий не может быть пустым';
    ob_end_clean();
    echo json_encode($response);
    exit();
}

$user_query = $connect->query("SELECT * FROM users WHERE id = $user_id");
if ($user_query->num_rows === 0) {
    $response['message'] = 'Пользователь не найден';
    ob_end_clean();
    echo json_encode($response);
    exit();
}

$user_data = $user_query->fetch_assoc();
$current_user_username = $user_data['username'];
$current_user_first_name = $user_data['first_name'];
$current_user_second_name = $user_data['second_name'];
$current_user_avatar = $user_data['avatar'];
$current_user_verify_status = $user_data['verify_status'];

$result = $connect->query("INSERT INTO comments (post_id, user_id, text) VALUES ($post_id, $user_id, '$comment')");

if (!$result) {
    $response['message'] = 'Ошибка при добавлении комментария: ' . mysqli_error($connect);
    ob_end_clean();
    echo json_encode($response);
    exit();
}

$comment_id = $connect->insert_id;

$post_author_result = $connect->query("SELECT user_id FROM posts WHERE id = $post_id");
if ($post_author_result->num_rows > 0) {
    $post_author = $post_author_result->fetch_assoc()['user_id'];
    
    if ($user_id != $post_author) {
        blossoming('is-commented-by', $post_author, $connect);
        blossoming('has-commented', $user_id, $connect);
    }
}

$comment_date = 'только что';

if (!function_exists('formatCommentDate')) {
    function formatCommentDate($date) {
        $timestamp = strtotime($date);
        $now = time();
        $diff = $now - $timestamp;
        
        if ($diff < 60) {
            return 'только что';
        } elseif ($diff < 3600) {
            $minutes = floor($diff / 60);
            return $minutes . ' ' . getNoun($minutes, 'минуту', 'минуты', 'минут') . ' назад';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . ' ' . getNoun($hours, 'час', 'часа', 'часов') . ' назад';
        } elseif ($diff < 2592000) {
            $days = floor($diff / 86400);
            return $days . ' ' . getNoun($days, 'день', 'дня', 'дней') . ' назад';
        } else {
            return date('j.m.Y в H:i', $timestamp);
        }
    }
    
    function getNoun($number, $one, $two, $five) {
        $number = abs($number);
        $number %= 100;
        if ($number >= 5 && $number <= 20) {
            return $five;
        }
        $number %= 10;
        if ($number == 1) {
            return $one;
        }
        if ($number >= 2 && $number <= 4) {
            return $two;
        }
        return $five;
    }
}

$processed_comment = preg_replace('/\xc2\xa0/', ' ', $comment);
preg_match_all('/@(\w+)/u', $processed_comment, $matches);
$tags = $matches[1];

foreach ($tags as $tag) {
    $tag_escaped = mysqli_real_escape_string($connect, $tag);
    $result = $connect->query("SELECT 1 FROM users WHERE username = '$tag_escaped'");
    if ($result && $result->num_rows > 0) {
        $pattern = '/' . preg_quote('@' . $tag, '/') . '/u';
        $replacement = '<a href="./user/' . htmlspecialchars($tag) . '" class="comment-mention">@' . htmlspecialchars($tag) . '</a>';
        $processed_comment = preg_replace($pattern, $replacement, $processed_comment);
    }
}

$comment_data = [
    'id' => $comment_id,
    'post_id' => $post_id,
    'user_id' => $user_id,
    'username' => $current_user_username,
    'first_name' => $current_user_first_name ?? '',
    'second_name' => $current_user_second_name ?? '',
    'avatar' => $current_user_avatar ?? '',
    'verify_status' => (int)$current_user_verify_status,
    'text' => $processed_comment,
    'date' => $comment_date,
];

$response['success'] = true;
$response['message'] = 'Комментарий успешно добавлен';
$response['comment'] = $comment_data;

ob_end_clean();
echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit();