<?php
require_once('blossoming.php');

if (isset($_POST['liked'])) {
    $post_id = mysqli_real_escape_string($connect, $_POST['postId']);
    $user_id = $_SESSION['user']['id'];
    $sql_liked = "SELECT likes, repost_post_id FROM posts WHERE id = $post_id";
    $result_liked = $connect->query($sql_liked);
    $row_liked = $result_liked->fetch_array();
    $likes = $row_liked['likes'];
    $repost_post_id = $row_liked['repost_post_id'];

    if ($connect->query("SELECT 1 FROM likes_on_posts WHERE post_id = $post_id AND user_id = $user_id")->num_rows == 0) {
        $connect->query("INSERT INTO likes_on_posts (post_id, user_id) VALUES ('$post_id', '$user_id')");
        $connect->query("UPDATE posts SET likes = $likes + 1 WHERE id = $post_id");

        $other_id = $connect->query("SELECT user_id FROM posts WHERE id = $post_id")->fetch_assoc()['user_id'];

        blossoming('like-post', $user_id, $connect);
        blossoming('is-liked-by', $other_id,  $connect);

        if ($repost_post_id && $connect->query("SELECT 1 FROM likes_on_posts WHERE post_id = $repost_post_id AND user_id = $user_id")->num_rows == 0) {
            $connect->query("INSERT INTO likes_on_posts (post_id, user_id) VALUES ('$repost_post_id', '$user_id')");
            $connect->query("UPDATE posts SET likes = $likes + 1 WHERE id = $repost_post_id");

            $other_id = $connect->query("SELECT user_id FROM posts WHERE id = $repost_post_id")->fetch_assoc()['user_id'];

            blossoming('like-post', $user_id, $connect);
            blossoming('is-liked-by', $other_id,  $connect);
        }

        echo $likes + 1;
    }

    exit();
}

if (isset($_POST['unliked'])) {
    $post_id = mysqli_real_escape_string($connect, $_POST['postId']);
    $user_id = $_SESSION['user']['id'];
    $sql_liked = "SELECT likes, repost_post_id FROM posts WHERE id = $post_id";
    $result_liked = $connect->query($sql_liked);
    $row_liked = $result_liked->fetch_array();
    $likes = $row_liked['likes'];
    $repost_post_id = $row_liked['repost_post_id'];

    if ($connect->query("SELECT 1 FROM likes_on_posts WHERE post_id = $post_id AND user_id = $user_id")->num_rows == 1) {
        $connect->query("DELETE FROM likes_on_posts WHERE post_id = $post_id AND user_id = $user_id");
        $connect->query("UPDATE posts SET likes = $likes - 1 WHERE id = $post_id");

        $other_id = $connect->query("SELECT user_id FROM posts WHERE id = $post_id")->fetch_assoc()['user_id'];

        blossoming('dislike-post', $user_id,  $connect);
        blossoming('is-disliked-by', $other_id, $connect);

        if ($repost_post_id && $connect->query("SELECT id FROM likes_on_posts WHERE post_id = $repost_post_id AND user_id = $user_id")->num_rows == 1) {
            $connect->query("DELETE FROM likes_on_posts WHERE post_id = $repost_post_id AND user_id = $user_id");
            $connect->query("UPDATE posts SET likes = $likes - 1 WHERE id = $repost_post_id");

            $other_id = $connect->query("SELECT user_id FROM posts WHERE id = $repost_post_id")->fetch_assoc()['user_id'];

            blossoming('dislike-post', $user_id,  $connect);
            blossoming('is-disliked-by', $other_id,  $connect);
        }

        echo $likes - 1;
    }

    exit();
}
