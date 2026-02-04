<?php
session_start();
require_once('connect.php');
require('blossoming.php');

$user_id = $_SESSION['user']['id'];
$post_id = mysqli_real_escape_string($connect, $_POST["post_id"]);
$result_post = $connect->query("SELECT hashtag_id, repost_user_id, repost_post_id FROM posts WHERE id = $post_id AND user_id = $user_id")->fetch_assoc();
$hashtag_id = $result_post['hashtag_id'];
$repost_user_id = $result_post['repost_user_id'];
$repost_post_id = $result_post['repost_post_id'];

if ($hashtag_id > 0) {
    if ($connect->query("SELECT id FROM posts WHERE hashtag_id = $hashtag_id")->num_rows == 1) {
        $connect->query("DELETE FROM hashtags WHERE id = $hashtag_id");
    }
}

if ($repost_user_id) {
    if ($connect->query("SELECT id FROM reposts WHERE post_id = $repost_post_id AND user_id = $user_id")->num_rows == 1) {
        $connect->query("DELETE FROM reposts WHERE user_id = $user_id AND post_id = $repost_post_id");

        $sql_check_another_repost = "SELECT id, user_id FROM posts WHERE repost_post_id = $repost_post_id";
        $result_check_another_repost = $connect->query($sql_check_another_repost);
        if ($result_check_another_repost->num_rows > 0) {
            while ($row_repost_post_id = $result_check_another_repost->fetch_assoc()) {
                $repost_id = $row_repost_post_id["id"];
                $repost_other_user_id = $row_repost_post_id["user_id"];
                if ($connect->query("SELECT id FROM reposts WHERE user_id = $user_id AND post_id = $repost_id")->num_rows == 1) {
                    if ($user_id != $repost_other_user_id) {
                        blossoming('unrepost-post', $user_id,  $connect);
                        blossoming('is-unreposted-by', $repost_other_user_id, $connect);
                    }
                    $connect->query("DELETE FROM reposts WHERE user_id = $user_id AND post_id = $repost_id");
                    if ($connect->query("SELECT id FROM reposts WHERE user_id = $user_id AND post_id = $repost_id")->num_rows == 0) {
                        $connect->query("UPDATE posts SET reposts = reposts - 1 WHERE id = $repost_id");
                    }
                }
            }
        }

        $connect->query("UPDATE posts SET reposts = reposts - 1 WHERE id = $repost_post_id");

        if ($user_id != $repost_user_id) {
            blossoming('unrepost-post', $user_id,  $connect);
            blossoming('is-unreposted-by', $repost_user_id, $connect);
        }
    }
} else {
    blossoming('delete-post', $user_id, $connect);
}

$other_users_id_comments = $connect->query("SELECT user_id FROM comments WHERE post_id = $post_id");

if ($other_users_id_comments->num_rows > 0) {
    while ($row = $other_users_id_comments->fetch_assoc()) {
        $other_id = $row['user_id'];

        if ($user_id != $other_id) {
            blossoming('delete-self-comment', $other_id, $connect);
            blossoming('comment-deleted-under-post-by', $user_id, $connect);
        }

        $connect->query("DELETE FROM comments WHERE post_id = $post_id AND user_id = $other_id");
    }
}

$other_users_id_likes = $connect->query("SELECT user_id FROM likes_on_posts WHERE post_id = $post_id");

if ($other_users_id_likes->num_rows > 0) {
    while ($row = $other_users_id_likes->fetch_assoc()) {
        $other_id = $row['user_id'];

        if ($user_id != $other_id) {
            blossoming('dislike-post', $other_id, $connect);
            blossoming('is-disliked-by', $user_id, $connect);
        }

        $connect->query("DELETE FROM likes_on_posts WHERE post_id = $post_id AND user_id = $other_id");
    }
}

$other_users_id_reposts = $connect->query("SELECT user_id FROM reposts WHERE post_id = $post_id");

if ($other_users_id_reposts->num_rows > 0) {
    while ($row = $other_users_id_reposts->fetch_assoc()) {
        $other_id = $row['user_id'];

        if ($user_id != $other_id) {
            blossoming('unrepost-post', $other_id, $connect);
            blossoming('is-unreposted-by', $user_id, $connect);
        }

        $connect->query("DELETE FROM reposts WHERE post_id = $post_id AND user_id = $other_id");
        $connect->query("UPDATE posts SET status = 1 WHERE repost_post_id = $post_id AND user_id = $other_id");
    }
}

$connect->query("UPDATE posts SET status = 1 WHERE id = $post_id AND user_id = $user_id");
