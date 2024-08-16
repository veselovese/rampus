<?php
session_start();
require('connect.php');

$user_id = $_SESSION['user']['id'];
$text_post = $_POST['post'];
$post_source = $_POST['post-source'];
$post_search = $_POST['post-search'];

preg_match_all('/#\w+/u', $text_post, $matches);
$hashtags = $matches[0];
if ($hashtags == null) {
    $hashtags = [null];
}

$text_without_hashtags = preg_replace('/#\w+\s*/u', '', $text_post);

foreach ($hashtags as $hashtag) {
    $hashtag = ltrim($hashtag, '#');  
    if ($hashtag == null) {
        $hashtag_id = 0;
    } else if ($connect->query("SELECT id FROM hashtags WHERE name = '$hashtag'")->num_rows > 0) { 
        $hashtag_id = $connect->query("SELECT id FROM hashtags WHERE name = '$hashtag'")->fetch_assoc()['id'];
    } else {
        $connect->query("INSERT INTO hashtags (name) VALUES ('$hashtag')");
        $hashtag_id = $connect->query("SELECT id FROM hashtags WHERE name = '$hashtag'")->fetch_assoc()['id'];
    }
    
    $result = mysqli_query($connect, "INSERT INTO posts (hashtag_id, text, user_id) VALUES ($hashtag_id, '$text_without_hashtags', $user_id);");
    $current_id = $connect->query("SELECT @@IDENTITY AS id")->fetch_assoc()['id'];
    $blossom_level = $connect->query("SELECT blossom_level FROM users WHERE id = $user_id")->fetch_assoc()['blossom_level'];
    $blossom_progress = $connect->query("SELECT blossom_progress FROM users WHERE id = $user_id")->fetch_assoc()['blossom_progress'];
    $blossom = $blossom_level + $blossom_progress / 100;
    $blossom += 0.1;

    $user_level = intval($blossom);
    $user_progress = ($blossom - $user_level) * 100;

    $connect->query("UPDATE users SET blossom_level = $user_level WHERE id = $user_id");
    $connect->query("UPDATE users SET blossom_progress = $user_progress WHERE id = $user_id");
    if (!$result) { 
        echo "Error: " . mysqli_error($connect);
    } else {
        $_SESSION['message'] = 'Пост добавлен';
    }
}

if (($post_search != '') && ($post_search == ltrim($hashtags[0], '#'))) {
    $post_search = '?search=' . $post_search;
} else {
    $post_search = '';
}

if ($post_source == 'source-profile') {
    header('Location: ./profile' . '#post-' . $current_id);
} else if ($post_source == 'source-wall') {
    header('Location: ./wall' . $post_search . '#post-' . $current_id);
}
