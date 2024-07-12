<?php
session_start();
require('connect.php');

$user_id = $_SESSION['user']['id'];
$text = $_POST["post"];

preg_match_all('/#\w+/', $text, $matches);
$hashtags = $matches[0];
if ($hashtags == null) {
    $hashtags = [0];
}

$text_without_hashtags = preg_replace('/#\w+\s*/', '', $text);

foreach ($hashtags as $hashtag) {
    $hashtag = ltrim($hashtag, '#');  
    $result = $connect->query("SELECT id FROM hashtags WHERE name = $hashtag");  
    if ($result->num_rows > 0) {;
        $hashtag_id = $result->fetch_assoc()['id'];
    } else if ($hashtag == 0) {
        $hashtag_id = 0;
    } else {
        $connect->query("INSERT INTO hashtags (name) VALUES ($hashtag)");
        $hashtag_id = $connect->query("SELECT id FROM hashtags WHERE name = $hashtag");
    }

    $sql_insert_posts = "INSERT INTO posts (hashtag_id, text, user_id) VALUES ($hashtag_id, $text_without_hashtags, $user_id)";
    if (mysqli_query($connect, $sql_insert_posts)) {
        $_SESSION['message'] = 'Пост успешно добавлен';
    } else {
        echo "Error: " . $sql_insert_posts . "<br>" . mysqli_error($connect);
    }
}

header('Location: ./wall');
