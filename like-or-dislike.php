<?php
if (isset($_POST['liked'])) {
    $post_id = $_POST['postId'];
    $user_id = $_SESSION['user']['id'];
    $sql_liked = "SELECT * FROM posts WHERE id = $post_id";
    $result_liked = $connect->query($sql_liked);
    $row_liked = $result_liked->fetch_array();
    $likes = $row_liked['likes'];
    
    $connect->query("INSERT INTO likes_on_posts (post_id, user_id) VALUES ($post_id, $user_id)");
    $connect->query("UPDATE posts SET likes = $likes + 1 WHERE id = $post_id");

    echo $likes + 1;
    exit();
}

if (isset($_POST['unliked'])) {
    $post_id = $_POST['postId'];
    $user_id = $_SESSION['user']['id'];
    $sql_liked = "SELECT * FROM posts WHERE id = $post_id";
    $result_liked = $connect->query($sql_liked);
    $row_liked = $result_liked->fetch_array();
    $likes = $row_liked['likes'];
    
    $connect->query("DELETE FROM likes_on_posts WHERE post_id = $post_id AND user_id = $user_id");
    $connect->query("UPDATE posts SET likes = $likes - 1 WHERE id = $post_id");

    echo $likes - 1;
    exit();
}