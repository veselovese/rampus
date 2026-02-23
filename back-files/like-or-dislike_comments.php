<?php
require_once('blossoming.php');

if (isset($_POST['liked-comment'])) {
    $comment_id = mysqli_real_escape_string($connect, $_POST['comment-id']);
    $user_id = $_SESSION['user']['id'];
    $sql_liked = "SELECT likes FROM comments WHERE id = $comment_id";
    $result_liked = $connect->query($sql_liked);
    $row_liked = $result_liked->fetch_array();
    $likes = $row_liked['likes'];

    if ($connect->query("SELECT 1 FROM likes_on_comments WHERE comment_id = $comment_id AND user_id = $user_id")->num_rows == 0) {
        $connect->query("INSERT INTO likes_on_comments (comment_id, user_id) VALUES ('$comment_id', '$user_id')");
        $connect->query("UPDATE comments SET likes = $likes + 1 WHERE id = $comment_id");

        $other_id = $connect->query("SELECT user_id FROM comments WHERE id = $comment_id")->fetch_assoc()['user_id'];

        if ($user_id != $other_id) {
            blossoming('like-comment', $user_id, $connect);
            blossoming('comment-is-liked-by', $other_id,  $connect);
        }

        echo $likes + 1;
    }

    exit();
}

if (isset($_POST['unliked-comment'])) {
    $comment_id = mysqli_real_escape_string($connect, $_POST['comment-id']);
    $user_id = $_SESSION['user']['id'];
    $sql_liked = "SELECT likes FROM comments WHERE id = $comment_id";
    $result_liked = $connect->query($sql_liked);
    $row_liked = $result_liked->fetch_array();
    $likes = $row_liked['likes'];

    if ($connect->query("SELECT 1 FROM likes_on_comments WHERE comment_id = $comment_id AND user_id = $user_id")->num_rows == 1) {
        $connect->query("DELETE FROM likes_on_comments WHERE comment_id = $comment_id AND user_id = $user_id");
        $connect->query("UPDATE comments SET likes = $likes - 1 WHERE id = $comment_id");

        $other_id = $connect->query("SELECT user_id FROM comments WHERE id = $comment_id")->fetch_assoc()['user_id'];

        if ($user_id != $other_id) {
            blossoming('dislike-comments', $user_id,  $connect);
            blossoming('comment-is-disliked-by', $other_id, $connect);
        }

        echo $likes - 1;
    }

    exit();
}
