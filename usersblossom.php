<?php
require('connect.php');

$users = $connect->query("SELECT * FROM users");
while ($row = $users->fetch_assoc()) {
    $id = $row['id'];

    $result_friend_1 = $connect->query("SELECT users.avatar AS friend_avatar, users.first_name AS friend_first_name, users.username AS friend_username FROM friends JOIN users ON friends.user_id_2 = users.id WHERE user_id_1 = $id ORDER BY friend_date");
    $result_friend_2 = $connect->query("SELECT users.avatar AS friend_avatar, users.first_name AS friend_first_name, users.username AS friend_username FROM friends JOIN users ON friends.user_id_1 = users.id WHERE user_id_2 = $id ORDER BY friend_date");

    $sql = "SELECT posts.likes AS post_likes
                    FROM posts
                    JOIN users ON posts.user_id = users.id
                    WHERE posts.user_id = $id";
    $sql_comment_counter = "SELECT comments.id
                    FROM comments 
                    JOIN posts ON comments.post_id = posts.id    
                    JOIN users ON users.id = posts.user_id
                    WHERE posts.user_id = $id";
    $sql_commented_counter = "SELECT comments.id
                    FROM comments
                    WHERE comments.user_id = $id";
    $sql_liked_counter = "SELECT likes_on_posts.id
                    FROM likes_on_posts
                    WHERE likes_on_posts.user_id = $id";
    $result = $connect->query($sql);
    $posts_count = $result->num_rows;
    $comment_count = $connect->query($sql_comment_counter)->num_rows;
    $commented_count = $connect->query($sql_commented_counter)->num_rows;
    $liked_count = $connect->query($sql_liked_counter)->num_rows;
    $likes_count = 0;
    if ($posts_count > 0) {
        while ($row = $result->fetch_assoc()) {
            $post_likes = $row["post_likes"];
            $likes_count += $post_likes;
        }
    }

    $blossom = ($posts_count + $likes_count * 0.3 + $comment_count * 0.4 + $liked_count * 0.2 + $commented_count * 0.3 + ($result_friend_1->num_rows + $result_friend_2->num_rows) * 0.7) / 10;
    $user_level = intval($blossom);
    $user_progress = round($blossom - $user_level, 2) * 100;
    $user_level += 1;
    $connect->query("UPDATE users SET blossom_level = $user_level WHERE id = $id");
    $connect->query("UPDATE users SET blossom_progress = $user_progress WHERE id = $id");
}
