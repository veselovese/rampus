<?php
require_once('blossoming.php');

if (isset($_POST['reposted'])) {
    $post_id = $_POST['postId'];
    $user_id = $_SESSION['user']['id'];
    $sql_reposted = "SELECT * FROM posts WHERE id = $post_id";
    $result_reposted = $connect->query($sql_reposted);
    $row_reposted = $result_reposted->fetch_array();
    $reposts = $row_reposted['reposts'];

    $repost_user_id = $row_reposted['user_id'];
    $repost_for_frineds = $row_reposted['for_friends'];
    $repost_text = $row_reposted['text'];
    $repost_img = $row_reposted['img'];
    $repost_hashtag_id = $row_reposted['hashtag_id'];
    $repost_repost_user_id = $row_reposted['repost_user_id'];
    $repost_repost_post_id = $row_reposted['repost_post_id'];

    if (!$repost_for_frineds) {
        # Репост репоста
        if ($repost_repost_user_id) {
            # Такого репоста еще нет
            if ($connect->query("SELECT id FROM reposts WHERE post_id = $repost_repost_post_id AND user_id = $user_id")->num_rows == 0) {
                $connect->query("INSERT INTO reposts (post_id, user_id) VALUES ('$post_id', '$user_id')");
                $connect->query("INSERT INTO reposts (post_id, user_id) VALUES ('$repost_repost_post_id', '$user_id')");

                $sql_repost_post_id = "SELECT id FROM posts WHERE repost_post_id = $repost_repost_post_id AND user_id = $user_id LIMIT 1";
                if ($connect->query($sql_repost_post_id)->num_rows == 1) {
                    $result_repost_post_id = $connect->query($sql_repost_post_id);
                    $row_repost_post_id = $result_repost_post_id->fetch_array();
                    $repost_post_id = $row_repost_post_id['id'];
                    $connect->query("UPDATE posts SET status = 0 WHERE id = $repost_post_id");
                } else {
                    $connect->query("INSERT INTO posts (user_id, hashtag_id, text, img, repost_post_id, repost_user_id) VALUES ('$user_id', '$repost_hashtag_id', '$repost_text', '$repost_img', '$repost_repost_post_id', '$repost_repost_user_id')");
                }

                $connect->query("UPDATE posts SET reposts = reposts + 1 WHERE id = $post_id");
                $connect->query("UPDATE posts SET reposts = reposts + 1 WHERE id = $repost_repost_post_id");

                // $other_id = $connect->query("SELECT user_id FROM posts WHERE id = $post_id")->fetch_assoc()['user_id'];

                // blossoming($user_id, 'liked-post', $connect);
                // blossoming($other_id, 'is-liked-by', $connect);

                echo $reposts + 1;
            } else {
                echo $reposts;
            }
            # Репост поста    
        } else {
            if ($connect->query("SELECT id FROM reposts WHERE post_id = $post_id AND user_id = $user_id")->num_rows == 0) {
                $connect->query("INSERT INTO reposts (post_id, user_id) VALUES ('$post_id', '$user_id')");

                $sql_repost_post_id = "SELECT id FROM posts WHERE repost_post_id = $post_id AND user_id = $user_id LIMIT 1";
                if ($connect->query($sql_repost_post_id)->num_rows == 1) {
                    $result_repost_post_id = $connect->query($sql_repost_post_id);
                    $row_repost_post_id = $result_repost_post_id->fetch_array();
                    $repost_post_id = $row_repost_post_id['id'];
                    $connect->query("UPDATE posts SET status = 0 WHERE id = $repost_post_id");
                } else {
                    $connect->query("INSERT INTO posts (user_id, hashtag_id, text, img, repost_post_id, repost_user_id) VALUES ('$user_id', '$repost_hashtag_id', '$repost_text', '$repost_img', '$post_id', '$repost_user_id')");
                }

                $connect->query("UPDATE posts SET reposts = $reposts + 1 WHERE id = $post_id");

                // $other_id = $connect->query("SELECT user_id FROM posts WHERE id = $post_id")->fetch_assoc()['user_id'];

                // blossoming($user_id, 'liked-post', $connect);
                // blossoming($other_id, 'is-liked-by', $connect);

                echo $reposts + 1;
            } else {
                echo $reposts;
            }
        }
    }

    exit();
}

if (isset($_POST['unreposted'])) {
    $post_id = $_POST['postId'];
    $user_id = $_SESSION['user']['id'];
    $sql_reposted = "SELECT * FROM posts WHERE id = $post_id";
    $result_reposted = $connect->query($sql_reposted);
    $row_reposted = $result_reposted->fetch_array();
    $reposts = $row_reposted['reposts'];

    $repost_user_id = $row_reposted['user_id'];
    $repost_for_frineds = $row_reposted['for_friends'];
    $repost_text = $row_reposted['text'];
    $repost_img = $row_reposted['img'];
    $repost_hashtag_id = $row_reposted['hashtag_id'];
    $repost_repost_user_id = $row_reposted['repost_user_id'];
    $repost_repost_post_id = $row_reposted['repost_post_id'];

    if (!$repost_for_frineds) {
        # Анрепост репоста
        if ($repost_repost_user_id) {
            if ($connect->query("SELECT id FROM reposts WHERE post_id = $post_id AND user_id = $user_id")->num_rows == 1) {
                $connect->query("DELETE FROM reposts WHERE user_id = $user_id AND post_id = $post_id");
                $connect->query("DELETE FROM reposts WHERE user_id = $user_id AND post_id = $repost_repost_post_id");

                $sql_repost_post_id = "SELECT id FROM posts WHERE repost_post_id = $repost_repost_post_id AND user_id = $user_id LIMIT 1";
                $result_repost_post_id = $connect->query($sql_repost_post_id);
                $row_repost_post_id = $result_repost_post_id->fetch_array();
                $repost_post_id = $row_repost_post_id['id'];


                $connect->query("UPDATE posts SET status = 1 WHERE id = $repost_post_id");
                $connect->query("UPDATE posts SET reposts = reposts - 1 WHERE id = $post_id");
                $connect->query("UPDATE posts SET reposts = reposts - 1 WHERE id = $repost_repost_post_id");

                // $other_id = $connect->query("SELECT user_id FROM posts WHERE id = $post_id")->fetch_assoc()['user_id'];

                // blossoming($user_id, 'liked-post', $connect);
                // blossoming($other_id, 'is-liked-by', $connect);

                echo $reposts - 1;
            }
            # Анрепост поста    
        } else {
            if ($connect->query("SELECT id FROM reposts WHERE post_id = $post_id AND user_id = $user_id")->num_rows == 1) {
                $connect->query("DELETE FROM reposts WHERE user_id = $user_id AND post_id = $post_id");

                $sql_repost_post_id = "SELECT id FROM posts WHERE repost_post_id = $post_id AND user_id = $user_id LIMIT 1";
                $result_repost_post_id = $connect->query($sql_repost_post_id);
                $row_repost_post_id = $result_repost_post_id->fetch_array();
                $repost_post_id = $row_repost_post_id['id'];

                $sql_check_another_repost = "SELECT id FROM posts WHERE repost_post_id = $post_id";
                $result_check_another_repost = $connect->query($sql_check_another_repost);
                if ($result_check_another_repost->num_rows > 0) {
                    while ($row_repost_post_id = $result_check_another_repost->fetch_assoc()) {
                        $repost_id = $row_repost_post_id["id"];
                        if ($connect->query("SELECT id FROM reposts WHERE user_id = $user_id AND post_id = $repost_id")->num_rows == 1) {
                            $connect->query("DELETE FROM reposts WHERE user_id = $user_id AND post_id = $repost_id");
                            if ($connect->query("SELECT id FROM reposts WHERE user_id = $user_id AND post_id = $repost_id")->num_rows == 0) {
                                $connect->query("UPDATE posts SET reposts = reposts - 1 WHERE id = $repost_id");
                            }
                        }
                    }
                }

                $connect->query("UPDATE posts SET status = 1 WHERE id = $repost_post_id");
                $connect->query("UPDATE posts SET reposts = reposts - 1 WHERE id = $post_id");

                // $other_id = $connect->query("SELECT user_id FROM posts WHERE id = $post_id")->fetch_assoc()['user_id'];

                // blossoming($user_id, 'liked-post', $connect);
                // blossoming($other_id, 'is-liked-by', $connect);

                echo $reposts - 1;
            }
        }
    }

    exit();
}
