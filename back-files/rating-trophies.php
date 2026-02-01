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
$the_likest_post = $connect->query("SELECT p.id, p.user_id, p.likes FROM posts p JOIN users u ON p.user_id = u.id WHERE NOT u.unrated_status ORDER BY likes DESC LIMIT 1");
if ($the_likest_post->num_rows > 0) {
    $row = $the_likest_post->fetch_assoc();
    $user_id_to = $row['user_id'];
    $the_likest_post_id = $row['id'];
    $the_likest_post_likes = $row['likes'];
    $trophy_the_likest_post = $connect->query("SELECT user_id_to, user_id_from FROM trophies WHERE (NOT user_id_to = $user_id_to OR NOT stat_number = $the_likest_post_likes) AND IF(user_id_to = $user_id_to, TRUE, stat_number < $the_likest_post_likes) AND id = 4");
    if ($trophy_the_likest_post->num_rows == 1) {
        $row = $trophy_the_likest_post->fetch_assoc();
        $check_user_id_to = $row['user_id_to'];
        $check_user_id_from = $row['user_id_from'];

        if ($check_user_id_to != $user_id_to) {
            $user_id_from = $check_user_id_to;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, link = 'post/$the_likest_post_id', stat_number = $the_likest_post_likes, get_date = NOW() WHERE id = 4");

            blossoming('grab-trophy', $user_id_to, $connect);
            blossoming('lose-trophy', $user_id_from, $connect);
        } else {
            $user_id_from = $check_user_id_from;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, link = 'post/$the_likest_post_id', stat_number = $the_likest_post_likes WHERE id = 4");
        }
    }
}

# Всех больше лайков на постах
$users = $connect->query("SELECT id FROM users WHERE NOT unrated_status");
$max_likes_on_posts = 0;

if ($users->num_rows > 0) {
    while ($row_users = $users->fetch_assoc()) {
        $current_user_id = $row_users['id'];
        $posts = $connect->query("SELECT likes FROM posts WHERE user_id = $current_user_id");
        if ($posts->num_rows > 0) {
            $likes_counter = 0;
            while ($row_posts = $posts->fetch_assoc()) {
                $likes = $row_posts['likes'];
                $likes_counter += $likes;
            }
            if ($likes_counter > $max_likes_on_posts) {
                $user_id_to = $current_user_id;
                $max_likes_on_posts = $likes_counter;
            }
        }
    }
    $trophy_likes_on_one_post = $connect->query("SELECT user_id_to, user_id_from FROM trophies WHERE (NOT user_id_to = $user_id_to OR NOT stat_number = $max_likes_on_posts) AND IF(user_id_to = $user_id_to, TRUE, stat_number < $max_likes_on_posts) AND id = 5");
    if ($trophy_likes_on_one_post->num_rows == 1) {
        $row = $trophy_likes_on_one_post->fetch_assoc();
        $check_user_id_to = $row['user_id_to'];
        $check_user_id_from = $row['user_id_from'];

        if ($check_user_id_to != $user_id_to) {
            $user_id_from = $check_user_id_to;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, stat_number = $max_likes_on_posts, get_date = NOW() WHERE id = 5");

            blossoming('grab-trophy', $user_id_to, $connect);
            blossoming('lose-trophy', $user_id_from, $connect);
        } else {
            $user_id_from = $check_user_id_from;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, stat_number = $max_likes_on_posts WHERE id = 5");
        }
    }
}

# Всех больше поставил лайков другим на посты
$users = $connect->query("SELECT id FROM users WHERE NOT unrated_status");
$max_likes_to_other_users = 0;

if ($users->num_rows > 0) {
    while ($row_users = $users->fetch_assoc()) {
        $current_user_id = $row_users['id'];
        $likes = $connect->query("SELECT id FROM likes_on_posts WHERE user_id = $current_user_id");
        $likes_counter = $likes->num_rows;
        if ($likes_counter > $max_likes_to_other_users) {
            $user_id_to = $current_user_id;
            $max_likes_to_other_users = $likes_counter;
        }
    }
    $trophy_likes_to_other_users = $connect->query("SELECT user_id_to, user_id_from FROM trophies WHERE (NOT user_id_to = $user_id_to OR NOT stat_number = $max_likes_to_other_users) AND IF(user_id_to = $user_id_to, TRUE, stat_number < $max_likes_to_other_users) AND id = 6");
    if ($trophy_likes_to_other_users->num_rows == 1) {
        $row = $trophy_likes_to_other_users->fetch_assoc();
        $check_user_id_to = $row['user_id_to'];
        $check_user_id_from = $row['user_id_from'];

        if ($check_user_id_to != $user_id_to) {
            $user_id_from = $check_user_id_to;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, stat_number = $max_likes_to_other_users, get_date = NOW() WHERE id = 6");

            blossoming('grab-trophy', $user_id_to, $connect);
            blossoming('lose-trophy', $user_id_from, $connect);
        } else {
            $user_id_from = $check_user_id_from;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, stat_number = $max_likes_to_other_users WHERE id = 6");
        }
    }
}

# Самый популярный пост по комментариями
$the_commentest_post = $connect->query("SELECT COUNT(*) AS comment_counter, u.id AS user_id, p.id AS post_id FROM comments c JOIN posts p on p.id = c.post_id JOIN users u ON u.id = p.user_id WHERE NOT u.unrated_status GROUP BY c.post_id ORDER BY comment_counter DESC LIMIT 1");
if ($the_commentest_post->num_rows > 0) {
    $row = $the_commentest_post->fetch_assoc();
    $user_id_to = $row['user_id'];
    $post_id = $row['post_id'];
    $max_comments_under_one_post = $row['comment_counter'];
}

$trophy_comments_under_one_post = $connect->query("SELECT user_id_to, user_id_from FROM trophies WHERE (NOT user_id_to = $user_id_to OR NOT stat_number = $max_comments_under_one_post) AND IF(user_id_to = $user_id_to, TRUE, stat_number < $max_comments_under_one_post) AND id = 7");
if ($trophy_comments_under_one_post->num_rows == 1) {
    $row = $trophy_comments_under_one_post->fetch_assoc();
    $check_user_id_to = $row['user_id_to'];
    $check_user_id_from = $row['user_id_from'];

    if ($check_user_id_to != $user_id_to) {
        $user_id_from = $check_user_id_to;
        $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, link = 'post/$post_id', stat_number = $max_comments_under_one_post, get_date = NOW() WHERE id = 7");

        blossoming('grab-trophy', $user_id_to, $connect);
        blossoming('lose-trophy', $user_id_from, $connect);
    } else {
        $user_id_from = $check_user_id_from;
        $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, link = 'post/$post_id', stat_number = $max_comments_under_one_post WHERE id = 7");
    }
}

# Всех больше комментариев под постами
$users = $connect->query("SELECT id FROM users WHERE NOT unrated_status");
$max_comments_on_posts = 0;

if ($users->num_rows > 0) {
    while ($row_users = $users->fetch_assoc()) {
        $current_user_id = $row_users['id'];
        $comments = $connect->query("SELECT COUNT(*) as comment_count FROM comments JOIN posts ON comments.post_id = posts.id JOIN users ON users.id = posts.user_id WHERE posts.user_id = $current_user_id");
        if ($comments->num_rows > 0) {
            $row = $comments->fetch_assoc();
            $comments_counter = $row['comment_count'];
            if ($comments_counter > $max_comments_on_posts) {
                $user_id_to = $current_user_id;
                $max_comments_on_posts = $comments_counter;
            }
        }
    }
    $trophy_comments_on_posts = $connect->query("SELECT user_id_to, user_id_from FROM trophies WHERE (NOT user_id_to = $user_id_to OR NOT stat_number = $max_comments_on_posts) AND IF(user_id_to = $user_id_to, TRUE, stat_number < $max_comments_on_posts) AND id = 8");
    if ($trophy_comments_on_posts->num_rows == 1) {
        $row = $trophy_comments_on_posts->fetch_assoc();
        $check_user_id_to = $row['user_id_to'];
        $check_user_id_from = $row['user_id_from'];

        if ($check_user_id_to != $user_id_to) {
            $user_id_from = $check_user_id_to;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, stat_number = $max_comments_on_posts, get_date = NOW() WHERE id = 8");

            blossoming('grab-trophy', $user_id_to, $connect);
            blossoming('lose-trophy', $user_id_from, $connect);
        } else {
            $user_id_from = $check_user_id_from;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, stat_number = $max_comments_on_posts WHERE id = 8");
        }
    }
}

# Всех больше оставил комментариев другим на посты
$users = $connect->query("SELECT id FROM users WHERE NOT unrated_status");
$max_comments_to_other_posts = 0;

if ($users->num_rows > 0) {
    while ($row_users = $users->fetch_assoc()) {
        $current_user_id = $row_users['id'];
        $comments_counter = $connect->query("SELECT id FROM comments WHERE comments.user_id = $current_user_id")->num_rows;
        if ($comments_counter > $max_comments_to_other_posts) {
            $user_id_to = $current_user_id;
            $max_comments_to_other_posts = $comments_counter;
        }
    }
    $trophy_comments_on_posts = $connect->query("SELECT user_id_to, user_id_from FROM trophies WHERE (NOT user_id_to = $user_id_to OR NOT stat_number = $max_comments_to_other_posts) AND IF(user_id_to = $user_id_to, TRUE, stat_number < $max_comments_to_other_posts) AND id = 9");
    if ($trophy_comments_on_posts->num_rows == 1) {
        $row = $trophy_comments_on_posts->fetch_assoc();
        $check_user_id_to = $row['user_id_to'];
        $check_user_id_from = $row['user_id_from'];

        if ($check_user_id_to != $user_id_to) {
            $user_id_from = $check_user_id_to;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, stat_number = $max_comments_to_other_posts, get_date = NOW() WHERE id = 9");

            blossoming('grab-trophy', $user_id_to, $connect);
            blossoming('lose-trophy', $user_id_from, $connect);
        } else {
            $user_id_from = $check_user_id_from;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, stat_number = $max_comments_to_other_posts WHERE id = 9");
        }
    }
}

# Самый популярный пост по репостам
$the_repostest_post = $connect->query("SELECT p.id, p.user_id, p.reposts FROM posts p JOIN users u ON p.user_id = u.id WHERE NOT u.unrated_status ORDER BY reposts DESC LIMIT 1");
if ($the_repostest_post->num_rows > 0) {
    $row = $the_repostest_post->fetch_assoc();
    $user_id_to = $row['user_id'];
    $the_repostest_post_id = $row['id'];
    $the_repostest_post_reposts = $row['reposts'];
    $trophy_the_repostest_post = $connect->query("SELECT user_id_to, user_id_from FROM trophies WHERE (NOT user_id_to = $user_id_to OR NOT stat_number = $the_repostest_post_reposts) AND IF(user_id_to = $user_id_to, TRUE, stat_number < $the_repostest_post_reposts) AND id = 10");
    if ($trophy_the_repostest_post->num_rows == 1) {
        $row = $trophy_the_repostest_post->fetch_assoc();
        $check_user_id_to = $row['user_id_to'];
        $check_user_id_from = $row['user_id_from'];

        if ($check_user_id_to != $user_id_to) {
            $user_id_from = $check_user_id_to;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, link = 'post/$the_repostest_post_id', stat_number = $the_repostest_post_reposts, get_date = NOW() WHERE id = 10");

            blossoming('grab-trophy', $user_id_to, $connect);
            blossoming('lose-trophy', $user_id_from, $connect);
        } else {
            $user_id_from = $check_user_id_from;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, link = 'post/$the_repostest_post_id', stat_number = $the_repostest_post_reposts WHERE id = 10");
        }
    }
}

# Всех больше репостов постов
$users = $connect->query("SELECT id FROM users WHERE NOT unrated_status");
$max_reposts_on_posts = 0;

if ($users->num_rows > 0) {
    while ($row_users = $users->fetch_assoc()) {
        $current_user_id = $row_users['id'];
        $reposts = $connect->query("SELECT COUNT(*) as repost_count FROM reposts JOIN posts ON reposts.post_id = posts.id JOIN users ON users.id = posts.user_id WHERE posts.user_id = $current_user_id");
        if ($reposts->num_rows > 0) {
            $row = $reposts->fetch_assoc();
            $reposts_counter = $row['repost_count'];
            if ($reposts_counter > $max_reposts_on_posts) {
                $user_id_to = $current_user_id;
                $max_reposts_on_posts = $reposts_counter;
            }
        }
    }
    $trophy_reposts_on_posts = $connect->query("SELECT user_id_to, user_id_from FROM trophies WHERE (NOT user_id_to = $user_id_to OR NOT stat_number = $max_reposts_on_posts) AND IF(user_id_to = $user_id_to, TRUE, stat_number < $max_reposts_on_posts) AND id = 11");
    if ($trophy_reposts_on_posts->num_rows == 1) {
        $row = $trophy_reposts_on_posts->fetch_assoc();
        $check_user_id_to = $row['user_id_to'];
        $check_user_id_from = $row['user_id_from'];

        if ($check_user_id_to != $user_id_to) {
            $user_id_from = $check_user_id_to;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, stat_number = $max_reposts_on_posts, get_date = NOW() WHERE id = 11");

            blossoming('grab-trophy', $user_id_to, $connect);
            blossoming('lose-trophy', $user_id_from, $connect);
        } else {
            $user_id_from = $check_user_id_from;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, stat_number = $max_reposts_on_posts WHERE id = 11");
        }
    }
}

# Всех больше сделал репостов других постов
$users = $connect->query("SELECT id FROM users WHERE NOT unrated_status");
$max_reposts_to_other_posts = 0;

if ($users->num_rows > 0) {
    while ($row_users = $users->fetch_assoc()) {
        $current_user_id = $row_users['id'];
        $reposts_counter = $connect->query("SELECT id FROM reposts WHERE reposts.user_id = $current_user_id")->num_rows;
        if ($reposts_counter > $max_reposts_to_other_posts) {
            $user_id_to = $current_user_id;
            $max_reposts_to_other_posts = $reposts_counter;
        }
    }
    $trophy_reposts_to_other_posts = $connect->query("SELECT user_id_to, user_id_from FROM trophies WHERE (NOT user_id_to = $user_id_to OR NOT stat_number = $max_reposts_to_other_posts) AND IF(user_id_to = $user_id_to, TRUE, stat_number < $max_reposts_to_other_posts) AND id = 12");
    if ($trophy_reposts_to_other_posts->num_rows == 1) {
        $row = $trophy_reposts_to_other_posts->fetch_assoc();
        $check_user_id_to = $row['user_id_to'];
        $check_user_id_from = $row['user_id_from'];

        if ($check_user_id_to != $user_id_to) {
            $user_id_from = $check_user_id_to;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, stat_number = $max_reposts_to_other_posts, get_date = NOW() WHERE id = 12");

            blossoming('grab-trophy', $user_id_to, $connect);
            blossoming('lose-trophy', $user_id_from, $connect);
        } else {
            $user_id_from = $check_user_id_from;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, stat_number = $max_reposts_to_other_posts WHERE id = 12");
        }
    }
}

# Самое большое количество постов
$users = $connect->query("SELECT id FROM users WHERE NOT unrated_status");
$max_posts_from_one_user = 0;

if ($users->num_rows > 0) {
    while ($row = $users->fetch_assoc()) {
        $current_user_id = $row['id'];
        $post_counter = $connect->query("SELECT id FROM posts WHERE user_id = $current_user_id")->num_rows;
        if ($post_counter > $max_posts_from_one_user) {
            $user_id_to = $current_user_id;
            $max_posts_from_one_user = $post_counter;
        }
    }
    $trophy_posts_from_one_user = $connect->query("SELECT user_id_to, user_id_from FROM trophies WHERE (NOT user_id_to = $user_id_to OR NOT stat_number = $max_posts_from_one_user) AND IF(user_id_to = $user_id_to, TRUE, stat_number < $max_posts_from_one_user) AND id = 13");
    if ($trophy_posts_from_one_user->num_rows == 1) {
        $row = $trophy_posts_from_one_user->fetch_assoc();
        $check_user_id_to = $row['user_id_to'];
        $check_user_id_from = $row['user_id_from'];

        if ($check_user_id_to != $user_id_to) {
            $user_id_from = $check_user_id_to;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, stat_number = $max_posts_from_one_user, get_date = NOW() WHERE id = 13");

            blossoming('grab-trophy', $user_id_to, $connect);
            blossoming('lose-trophy', $user_id_from, $connect);
        } else {
            $user_id_from = $check_user_id_from;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, stat_number = $max_posts_from_one_user WHERE id = 13");
        }
    }
}

# Самое большое количество друзей
$users = $connect->query("SELECT id FROM users WHERE NOT unrated_status");
$max_friends_in_one_user = 0;

if ($users->num_rows > 0) {
    while ($row = $users->fetch_assoc()) {
        $current_user_id = $row['id'];
        $friend_counter = $connect->query("SELECT id FROM friends WHERE user_id_1 = $current_user_id OR user_id_2 = $current_user_id")->num_rows;
        if ($friend_counter > $max_friends_in_one_user) {
            $user_id_to = $current_user_id;
            $max_friends_in_one_user = $friend_counter;
        }
    }
    $trophy_friends_in_one_user = $connect->query("SELECT user_id_to, user_id_from FROM trophies WHERE (NOT user_id_to = $user_id_to OR NOT stat_number = $max_friends_in_one_user) AND IF(user_id_to = $user_id_to, TRUE, stat_number < $max_friends_in_one_user) AND id = 14");
    if ($trophy_friends_in_one_user->num_rows == 1) {
        $row = $trophy_friends_in_one_user->fetch_assoc();
        $check_user_id_to = $row['user_id_to'];
        $check_user_id_from = $row['user_id_from'];

        if ($check_user_id_to != $user_id_to) {
            $user_id_from = $check_user_id_to;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, stat_number = $max_friends_in_one_user, get_date = NOW() WHERE id = 14");

            blossoming('grab-trophy', $user_id_to, $connect);
            blossoming('lose-trophy', $user_id_from, $connect);
        } else {
            $user_id_from = $check_user_id_from;
            $connect->query("UPDATE trophies SET user_id_to = $user_id_to, user_id_from = $user_id_from, stat_number = $max_friends_in_one_user WHERE id = 14");
        }
    }
}
