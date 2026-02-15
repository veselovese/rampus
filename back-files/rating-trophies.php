<?php
require_once('connect.php');
require_once('blossoming.php');

# Места в рейтинге
$users_blossom_list = $connect->query("SELECT id FROM users WHERE NOT unrated_status ORDER BY blossom_level DESC, blossom_progress DESC LIMIT 3");
if ($users_blossom_list->num_rows > 0) {
    $level = 0;
    while ($row = $users_blossom_list->fetch_assoc()) {
        $level += 1;
        $user_id_to = $row['id'];
        $trophy_rating = $connect->query("SELECT id FROM trophies WHERE user_id_to = $user_id_to AND id = $level");
        if ($trophy_rating->num_rows == 0) {
            $user_id_from = $connect->query("SELECT user_id_to FROM trophies WHERE id = $level")->fetch_assoc()['user_id_to'];
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, get_date = NOW() WHERE id = $level");
        }
    }
}

# Самый популярный пост по лайкам
$the_likest_post = $connect->query("SELECT p.id AS post_id, 
                                           p.user_id AS user_id, 
                                           COUNT(DISTINCT lop.id) AS likes_counter,
                                           MAX(lop.like_date) AS last_like_date 
                                    FROM likes_on_posts lop 
                                    JOIN posts p ON p.id = lop.post_id 
                                    JOIN users u ON p.user_id = u.id 
                                    WHERE lop.user_id != p.user_id 
                                      AND u.unrated_status = 0 
                                    GROUP BY p.id, p.user_id
                                    ORDER BY likes_counter DESC, 
                                             last_like_date ASC 
                                    LIMIT 1");
if ($the_likest_post->num_rows > 0) {
    $row = $the_likest_post->fetch_assoc();
    $user_id_to = $row['user_id'];
    $the_likest_post_id = $row['post_id'];
    $the_likest_post_likes = $row['likes_counter'];
    $last_like_date = $row['last_like_date'];

    $trophy_the_likest_post = $connect->query("SELECT user_id_to, user_id_from FROM trophies WHERE id = 4");
    if ($trophy_the_likest_post->num_rows == 1) {
        $row = $trophy_the_likest_post->fetch_assoc();
        $check_user_id_to = $row['user_id_to'];
        $check_user_id_from = $row['user_id_from'];

        if ($check_user_id_to != $user_id_to) {
            $user_id_from = $check_user_id_to;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, link = 'post/$the_likest_post_id', stat_number = $the_likest_post_likes, get_date = '$last_like_date' WHERE id = 4");

            blossoming('grab-trophy', $user_id_to, $connect);
            blossoming('lose-trophy', $user_id_from, $connect);
        } else {
            $user_id_from = $check_user_id_from;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, link = 'post/$the_likest_post_id', stat_number = $the_likest_post_likes WHERE id = 4");
        }
    }
}

# Всех больше лайков на постах
$more_likes_on_posts = $connect->query("SELECT p.user_id AS user_id,
                                               COUNT(DISTINCT lop.id) AS likes_counter,
                                               MAX(lop.like_date) AS last_like_date 
                                        FROM posts p 
                                        JOIN likes_on_posts lop ON lop.post_id = p.id 
                                        JOIN users u ON p.user_id = u.id 
                                        WHERE lop.user_id != p.user_id 
                                          AND u.unrated_status = 0
                                        GROUP BY p.user_id 
                                        ORDER BY likes_counter DESC, 
                                                 last_like_date ASC 
                                        LIMIT 1");
if ($more_likes_on_posts->num_rows > 0) {
    $row = $more_likes_on_posts->fetch_assoc();
    $user_id_to = $row['user_id'];
    $max_likes_on_posts = $row['likes_counter'];
    $last_like_date = $row['last_like_date'];

    $trophy_likes_on_one_post = $connect->query("SELECT user_id_to, user_id_from FROM trophies WHERE id = 5");
    if ($trophy_likes_on_one_post->num_rows == 1) {
        $row = $trophy_likes_on_one_post->fetch_assoc();
        $check_user_id_to = $row['user_id_to'];
        $check_user_id_from = $row['user_id_from'];

        if ($check_user_id_to != $user_id_to) {
            $user_id_from = $check_user_id_to;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, stat_number = $max_likes_on_posts, get_date = '$last_like_date' WHERE id = 5");

            blossoming('grab-trophy', $user_id_to, $connect);
            blossoming('lose-trophy', $user_id_from, $connect);
        } else {
            $user_id_from = $check_user_id_from;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, stat_number = $max_likes_on_posts WHERE id = 5");
        }
    }
}

# Всех больше поставил лайков другим на посты
$max_likes_to_other_users = $connect->query("SELECT lop.user_id AS user_id,
                                                   COUNT(DISTINCT lop.id) AS likes_counter,
                                                   MAX(lop.like_date) AS last_like_date 
                                            FROM likes_on_posts lop 
                                            JOIN posts p ON lop.post_id = p.id 
                                            JOIN users u ON lop.user_id = u.id 
                                            WHERE p.user_id != lop.user_id 
                                              AND u.unrated_status = 0
                                            GROUP BY lop.user_id 
                                            ORDER BY likes_counter DESC, 
                                                     last_like_date ASC 
                                            LIMIT 1");
if ($max_likes_to_other_users->num_rows > 0) {
    $row = $max_likes_to_other_users->fetch_assoc();
    $user_id_to = $row['user_id'];
    $max_likes_to_other_posts = $row['likes_counter'];
    $last_like_date = $row['last_like_date'];

    $trophy_likes_to_other_users = $connect->query("SELECT user_id_to, user_id_from FROM trophies WHERE id = 6");
    if ($trophy_likes_to_other_users->num_rows == 1) {
        $row = $trophy_likes_to_other_users->fetch_assoc();
        $check_user_id_to = $row['user_id_to'];
        $check_user_id_from = $row['user_id_from'];

        if ($check_user_id_to != $user_id_to) {
            $user_id_from = $check_user_id_to;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, stat_number = $max_likes_to_other_posts, get_date = '$last_like_date' WHERE id = 6");

            blossoming('grab-trophy', $user_id_to, $connect);
            blossoming('lose-trophy', $user_id_from, $connect);
        } else {
            $user_id_from = $check_user_id_from;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, stat_number = $max_likes_to_other_posts WHERE id = 6");
        }
    }
}

# Самый популярный пост по комментариями
$the_commentest_post = $connect->query("SELECT p.id AS post_id,
                                           p.user_id AS user_id,
                                           COUNT(DISTINCT c.id) AS comments_counter, 
                                           MAX(c.comment_date) AS last_comment_date
                                    FROM comments c 
                                    JOIN posts p ON p.id = c.post_id 
                                    JOIN users u ON p.user_id = u.id 
                                    WHERE c.user_id != p.user_id 
                                      AND u.unrated_status = 0
                                    GROUP BY p.id, p.user_id
                                    ORDER BY comments_counter DESC,
                                             last_comment_date ASC
                                    LIMIT 1");
if ($the_commentest_post->num_rows > 0) {
    $row = $the_commentest_post->fetch_assoc();
    $user_id_to = $row['user_id'];
    $post_id = $row['post_id'];
    $max_comments_under_one_post = $row['comments_counter'];
    $last_comment_date = $row['last_comment_date'];

    $trophy_comments_under_one_post = $connect->query("SELECT user_id_to, user_id_from FROM trophies WHERE id = 7");
    if ($trophy_comments_under_one_post->num_rows == 1) {
        $row = $trophy_comments_under_one_post->fetch_assoc();
        $check_user_id_to = $row['user_id_to'];
        $check_user_id_from = $row['user_id_from'];

        if ($check_user_id_to != $user_id_to) {
            $user_id_from = $check_user_id_to;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, link = 'post/$post_id', stat_number = $max_comments_under_one_post, get_date = '$last_comment_date' WHERE id = 7");

            blossoming('grab-trophy', $user_id_to, $connect);
            blossoming('lose-trophy', $user_id_from, $connect);
        } else {
            $user_id_from = $check_user_id_from;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, link = 'post/$post_id', stat_number = $max_comments_under_one_post WHERE id = 7");
        }
    }
}

# Всех больше комментариев под постами
$more_comments_on_posts = $connect->query("SELECT p.user_id AS user_id,
                               COUNT(DISTINCT c.id) AS comments_counter,
                               MAX(c.comment_date) AS last_comment_date
                        FROM posts p
                        JOIN comments c ON c.post_id = p.id
                        JOIN users u ON p.user_id = u.id 
                        WHERE c.user_id != p.user_id AND u.unrated_status = 0
                        GROUP BY p.user_id
                        ORDER BY comments_counter DESC,
                                 last_comment_date ASC
                        LIMIT 1");
if ($more_comments_on_posts->num_rows > 0) {
    $row = $more_comments_on_posts->fetch_assoc();
    $user_id_to = $row['user_id'];
    $max_comments_under_posts = $row['comments_counter'];
    $last_comment_date = $row['last_comment_date'];

    $trophy_comments_on_posts = $connect->query("SELECT user_id_to, user_id_from FROM trophies WHERE id = 8");
    if ($trophy_comments_on_posts->num_rows == 1) {
        $row = $trophy_comments_on_posts->fetch_assoc();
        $check_user_id_to = $row['user_id_to'];
        $check_user_id_from = $row['user_id_from'];

        if ($check_user_id_to != $user_id_to) {
            $user_id_from = $check_user_id_to;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, stat_number = $max_comments_under_posts, get_date = '$last_comment_date' WHERE id = 8");

            blossoming('grab-trophy', $user_id_to, $connect);
            blossoming('lose-trophy', $user_id_from, $connect);
        } else {
            $user_id_from = $check_user_id_from;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, stat_number = $max_comments_under_posts WHERE id = 8");
        }
    }
}

# Всех больше оставил комментариев другим на посты
$max_comments_to_other_users = $connect->query("SELECT c.user_id AS user_id,
                               COUNT(DISTINCT c.id) AS comments_counter,
                               MAX(c.comment_date) AS last_comment_date
                        FROM comments c
                        JOIN posts p ON c.post_id = p.id
                        JOIN users u ON c.user_id = u.id 
                        WHERE p.user_id != c.user_id
                          AND u.unrated_status = 0
                        GROUP BY c.user_id
                        ORDER BY comments_counter DESC,
                                 last_comment_date ASC
                        LIMIT 1");

if ($max_comments_to_other_users->num_rows > 0) {
    $row = $max_comments_to_other_users->fetch_assoc();
    $user_id_to = $row['user_id'];
    $max_comments_to_other_posts = $row['comments_counter'];
    $last_comment_date = $row['last_comment_date'];

    $trophy_comments_on_posts = $connect->query("SELECT user_id_to, user_id_from FROM trophies WHERE id = 9");
    if ($trophy_comments_on_posts->num_rows == 1) {
        $row = $trophy_comments_on_posts->fetch_assoc();
        $check_user_id_to = $row['user_id_to'];
        $check_user_id_from = $row['user_id_from'];

        if ($check_user_id_to != $user_id_to) {
            $user_id_from = $check_user_id_to;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, stat_number = $max_comments_to_other_posts, get_date = '$last_comment_date' WHERE id = 9");

            blossoming('grab-trophy', $user_id_to, $connect);
            blossoming('lose-trophy', $user_id_from, $connect);
        } else {
            $user_id_from = $check_user_id_from;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, stat_number = $max_comments_to_other_posts WHERE id = 9");
        }
    }
}

# Самый популярный пост по репостам
$the_repostest_post = $connect->query("SELECT p.id AS post_id,
                                           p.user_id AS user_id,
                                           COUNT(DISTINCT r.user_id) AS reposts_counter,
                                           MAX(r.repost_date) AS last_repost_date
                                    FROM reposts r
                                    JOIN posts p ON p.id = r.post_id
                                    JOIN users u ON p.user_id = u.id 
                                    WHERE r.user_id != p.user_id  AND u.unrated_status = 0 
                                    GROUP BY p.id, p.user_id
                                    ORDER BY reposts_counter DESC,
                                             last_repost_date ASC
                                    LIMIT 1");
if ($the_repostest_post->num_rows > 0) {
    $row = $the_repostest_post->fetch_assoc();
    $user_id_to = $row['user_id'];
    $the_repostest_post_id = $row['post_id'];
    $the_repostest_post_reposts = $row['reposts_counter'];
    $last_repost_date = $row['last_repost_date'];

    $trophy_the_repostest_post = $connect->query("SELECT user_id_to, user_id_from FROM trophies WHERE id = 10");
    if ($trophy_the_repostest_post->num_rows == 1) {
        $row = $trophy_the_repostest_post->fetch_assoc();
        $check_user_id_to = $row['user_id_to'];
        $check_user_id_from = $row['user_id_from'];

        if ($check_user_id_to != $user_id_to) {
            $user_id_from = $check_user_id_to;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, link = 'post/$the_repostest_post_id', stat_number = $the_repostest_post_reposts, get_date = '$last_repost_date' WHERE id = 10");

            blossoming('grab-trophy', $user_id_to, $connect);
            blossoming('lose-trophy', $user_id_from, $connect);
        } else {
            $user_id_from = $check_user_id_from;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, link = 'post/$the_repostest_post_id', stat_number = $the_repostest_post_reposts WHERE id = 10");
        }
    }
}

# Всех больше репостов постов
$more_reposts_on_posts = $connect->query("SELECT p.user_id AS user_id,
                               COUNT(DISTINCT r.id) AS reposts_counter,
                               MAX(r.repost_date) AS last_repost_date
                        FROM posts p
                        JOIN reposts r ON r.post_id = p.id
                        JOIN users u ON p.user_id = u.id
                        WHERE r.user_id != p.user_id AND u.unrated_status = 0
                        GROUP BY p.user_id
                        ORDER BY reposts_counter DESC,
                                 last_repost_date ASC
                        LIMIT 1");
if ($more_reposts_on_posts->num_rows > 0) {
    $row = $more_reposts_on_posts->fetch_assoc();
    $user_id_to = $row['user_id'];
    $max_reposts_on_posts = $row['reposts_counter'];
    $last_repost_date = $row['last_repost_date'];

    $trophy_reposts_on_posts = $connect->query("SELECT user_id_to, user_id_from FROM trophies WHERE id = 11");
    if ($trophy_reposts_on_posts->num_rows == 1) {
        $row = $trophy_reposts_on_posts->fetch_assoc();
        $check_user_id_to = $row['user_id_to'];
        $check_user_id_from = $row['user_id_from'];

        if ($check_user_id_to != $user_id_to) {
            $user_id_from = $check_user_id_to;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, stat_number = $max_reposts_on_posts, get_date = '$last_repost_date' WHERE id = 11");

            blossoming('grab-trophy', $user_id_to, $connect);
            blossoming('lose-trophy', $user_id_from, $connect);
        } else {
            $user_id_from = $check_user_id_from;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, stat_number = $max_reposts_on_posts WHERE id = 11");
        }
    }
}

# Всех больше сделал репостов других постов
$max_reposts_to_other_users = $connect->query("SELECT r.user_id AS user_id,
                               COUNT(DISTINCT r.id) AS reposts_counter,
                               MAX(r.repost_date) AS last_repost_date
                        FROM reposts r
                        JOIN posts p ON r.post_id = p.id
                        JOIN users u ON r.user_id = u.id
                        WHERE r.user_id != p.user_id  AND u.unrated_status = 0
                        GROUP BY r.user_id
                        ORDER BY reposts_counter DESC,
                                 last_repost_date ASC
                        LIMIT 1");
if ($max_reposts_to_other_users->num_rows > 0) {
    $row = $max_reposts_to_other_users->fetch_assoc();
    $user_id_to = $row['user_id'];
    $max_reposts_to_other_posts = $row['reposts_counter'];
    $last_repost_date = $row['last_repost_date'];

    $trophy_reposts_to_other_posts = $connect->query("SELECT user_id_to, user_id_from FROM trophies WHERE id = 12");
    if ($trophy_reposts_to_other_posts->num_rows == 1) {
        $row = $trophy_reposts_to_other_posts->fetch_assoc();
        $check_user_id_to = $row['user_id_to'];
        $check_user_id_from = $row['user_id_from'];

        if ($check_user_id_to != $user_id_to) {
            $user_id_from = $check_user_id_to;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, stat_number = $max_reposts_to_other_posts, get_date = '$last_repost_date' WHERE id = 12");

            blossoming('grab-trophy', $user_id_to, $connect);
            blossoming('lose-trophy', $user_id_from, $connect);
        } else {
            $user_id_from = $check_user_id_from;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, stat_number = $max_reposts_to_other_posts WHERE id = 12");
        }
    }
}

# Самое большое количество постов
$more_posts_from_one_user = $connect->query("SELECT u.id AS user_id, COUNT(p.id) AS posts_counter, MAX(p.content_date) AS last_content_date FROM posts p JOIN users u ON p.user_id = u.id WHERE u.unrated_status = 0 GROUP BY u.id ORDER BY posts_counter DESC, last_content_date ASC LIMIT 1");

if ($more_posts_from_one_user->num_rows > 0) {
    $row = $more_posts_from_one_user->fetch_assoc();
    $user_id_to = $row['user_id'];
    $max_posts_from_one_user = $row['posts_counter'];
    $last_content_date = $row['last_content_date'];

    $trophy_posts_from_one_user = $connect->query("SELECT user_id_to, user_id_from FROM trophies WHERE id = 13");
    if ($trophy_posts_from_one_user->num_rows == 1) {
        $row = $trophy_posts_from_one_user->fetch_assoc();
        $check_user_id_to = $row['user_id_to'];
        $check_user_id_from = $row['user_id_from'];

        if ($check_user_id_to != $user_id_to) {
            $user_id_from = $check_user_id_to;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, stat_number = $max_posts_from_one_user, get_date = '$last_content_date' WHERE id = 13");

            blossoming('grab-trophy', $user_id_to, $connect);
            blossoming('lose-trophy', $user_id_from, $connect);
        } else {
            $user_id_from = $check_user_id_from;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, stat_number = $max_posts_from_one_user WHERE id = 13");
        }
    }
}

# Самое большое количество друзей
$more_friends_in_one_user = $connect->query("SELECT u.id AS user_id, COUNT(f.id) AS friends_counter, MAX(f.friend_date) AS last_friend_date FROM friends f JOIN users u ON f.user_id_1 = u.id OR f.user_id_2 = u.id WHERE u.unrated_status = 0 GROUP BY u.id ORDER BY friends_counter DESC, last_friend_date ASC LIMIT 1");
if ($more_friends_in_one_user->num_rows > 0) {
    $row = $more_friends_in_one_user->fetch_assoc();
    $user_id_to = $row['user_id'];
    $max_friends_in_one_user = $row['friends_counter'];
    $last_friend_date = $row['last_friend_date'];

    $trophy_friends_in_one_user = $connect->query("SELECT user_id_to, user_id_from FROM trophies WHERE id = 14");
    if ($trophy_friends_in_one_user->num_rows == 1) {
        $row = $trophy_friends_in_one_user->fetch_assoc();
        $check_user_id_to = $row['user_id_to'];
        $check_user_id_from = $row['user_id_from'];

        if ($check_user_id_to != $user_id_to) {
            $user_id_from = $check_user_id_to;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, stat_number = $max_friends_in_one_user, get_date = '$last_friend_date' WHERE id = 14");

            blossoming('grab-trophy', $user_id_to, $connect);
            blossoming('lose-trophy', $user_id_from, $connect);
        } else {
            $user_id_from = $check_user_id_from;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, stat_number = $max_friends_in_one_user WHERE id = 14");
        }
    }
}
