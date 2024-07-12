<?php
session_start();
require('connect.php');

$user_id = $_SESSION['user']['id'];
$text_post = $_POST["post"];

preg_match_all('/#\w+/', $text_post, $matches);
$hashtags = $matches[0];
if ($hashtags == null) {
    $hashtags = [null];
}

$text_without_hashtags = preg_replace('/#\w+\s*/', '', $text_post);

foreach ($hashtags as $hashtag) {
    $hashtag = ltrim($hashtag, '#');  
    if ($hashtag == null) {;
        $hashtag_id = null;
    } else if ($connect->query("SELECT id FROM hashtags WHERE name = '$hashtag'")->num_rows > 0) {
        $hashtag_id = $connect->query("SELECT id FROM hashtags WHERE name = '$hashtag'")->fetch_assoc()['id'];
    } else {
        $connect->query("INSERT INTO hashtags (name) VALUES ('$hashtag')");
        $hashtag_id = $connect->query("SELECT id FROM hashtags WHERE name = '$hashtag'");
    }

    $sql_insert_post = "INSERT INTO posts (hashtag_id, text, user_id) VALUES ($hashtag_id, '$text_without_hashtags', $user_id)";
    if (mysqli_query($connect, $sql_insert_post)) {
        $_SESSION['message'] = 'Пост добавлен';
    } else {
        echo "Error: " . mysqli_error($connect);
    }
}

header('Location: ./wall');
