<?php
session_start();

require('connect.php');
require('like-or-dislike.php');

$id = $_SESSION['user']['id'];
$result_friend_1 = $connect->query("SELECT user_id_1 FROM friends JOIN users ON friends.user_id_1 = users.id WHERE user_id_2 = $id");
$result_friend_2 = $connect->query("SELECT user_id_2 FROM friends JOIN users ON friends.user_id_2 = users.id WHERE user_id_1 = $id");
$friends_id = array();
if ($result_friend_1->num_rows > 0) {
    while ($row_friend = $result_friend_1->fetch_assoc()) {
        $friends_id[] = $row_friend['user_id_1'];
    }
}
if ($result_friend_2->num_rows > 0) {
    while ($row_friend = $result_friend_2->fetch_assoc()) {
        $friends_id[] = $row_friend['user_id_2'];
    }
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="stylesheet" href="css/main.css?v=141">
    <link rel="stylesheet" href="css/wall2.css?v=141">
    <title>Посты на стене в Rampus (Рампус)</title>
    <link rel="apple-touch-icon" sizes="57x57" href="favicons/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="favicons/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="favicons/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="favicons/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="favicons/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="favicons/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="favicons/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="favicons/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="favicons/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="favicons/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="favicons/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicons/favicon-16x16.png">
    <link rel="manifest" href="favicons/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="favicons/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
</head>

<body>
    <?php require('header.php'); ?>
    <main>
        <h1 class="title">Посты на стене в Rampus (Рампус)</h1>
        <?php if (!isset($_SESSION['user'])) {
            header("Location: auth?request=wall");
            exit();
        } else { ?>
            <section class="wrapper main-section">
                <nav class="first-part">
                    <ul>
                        <li><a href="./profile">Профиль</a></li>
                        <li id="active">
                            <div class='wall-filter-div'>
                                <div class='wall-filter' id='wall-filter' onclick='showPopupWallFilter()'>
                                    Стена
                                    <div>
                                        <span>
                                            все
                                        </span>
                                        <svg width='8' height='13' viewBox='0 0 8 13' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                            <path d='M6.96771 6.03603L1.12165 0.191904C0.865127 -0.0639698 0.449521 -0.0639698 0.192352 0.191904C-0.0641698 0.447777 -0.0641699 0.863383 0.192352 1.11926L5.57471 6.49968L0.192999 11.8801C-0.0635223 12.136 -0.0635224 12.5516 0.192999 12.8081C0.44952 13.064 0.865774 13.064 1.1223 12.8081L6.96836 6.96403C7.22094 6.7108 7.22094 6.28866 6.96771 6.03603Z' />
                                        </svg>
                                    </div>
                                </div>
                                <div class='wall-filter-popup' id='popup_wall-filter'>
                                    <label class="wall-filter-popup-li">Все<input checked name="wall-filter" id="wall-filter-all" type="radio" value=""></label>
                                    <div class='div-line'></div>
                                    <label class="wall-filter-popup-li">Друзья<input name="wall-filter" id="wall-filter-friends" type="radio" value=""></label>
                                </div>
                            </div>
                        </li>
                        <li><a href="./wall">****</a></li>
                        <li><a href="./people">Люди</a></li>
                    </ul>
                </nav>
                <div class="second-and-third-parts">
                    <div class="second-part">
                        <div class="wall__new-post">
                            <form action="./add" method="post" autocomplete="off">
                                <div contenteditable="true" id="textarea-post" role="textbox" onkeyup="textareaPost(event)" onkeydown="textareaPostPlaceholder(event)"></div>
                                <label for="textarea-post" id="textarea-post_label">О чём расскажете сегодня?</label>
                                <input type="hidden" required name="post" id="textarea-post_input" value="">
                                <input type="hidden" required name="post-source" value="source-wall">
                                <input type="hidden" required name="post-search" value="<?php if (isset($_GET['search'])) {
                                                                                            echo $_GET['search'];
                                                                                        } ?>">
                                <button disabled class="" type="submit" id="textarea-post_sumbit"><img src="pics/SendIcon.svg"></button>
                            </form>
                        </div>
                        <div class="wall__user-posts" id="posts-filter-all">
                            <div>
                                <?php
                                if (!isset($_GET['search'])) {
                                    $search = 'all';
                                } else {
                                    $search = $_GET['search'];
                                }
                                $search_condition = $search !== 'all' ? "AND hashtags.name = '$search'" : '';
                                $sql_post = "SELECT posts.hashtag_id AS hashtag_id, posts.text AS post_text, DATE_FORMAT(posts.post_date, '%d %M в %k:%i') AS post_date, posts.likes AS post_likes, posts.user_id AS user_id, users.first_name AS first_name, users.second_name AS second_name, users.avatar AS avatar, posts.id AS i, users.username AS username
                            FROM posts
                            JOIN users ON posts.user_id = users.id
                            LEFT JOIN hashtags ON posts.hashtag_id = hashtags.id
                            WHERE posts.status = 0 $search_condition";

                                $result_post = $connect->query($sql_post);
                                if ($result_post->num_rows > 0) {
                                    while ($row_post = $result_post->fetch_assoc()) {
                                        $hashtag_id = $row_post["hashtag_id"];
                                        $post_text = preg_replace('/\xc2\xa0/', ' ', $row_post["post_text"]);
                                        $post_date = $row_post["post_date"];
                                        $post_likes = $row_post["post_likes"];
                                        $user_id = $row_post["user_id"];
                                        $first_name = $row_post["first_name"];
                                        $second_name = $row_post["second_name"];
                                        $username = $row_post["username"];
                                        $avatar = $row_post["avatar"];
                                        $i = $row_post['i'];
                                        echo "<div class='user-post' id='post-$i'>";
                                        echo "<div>";
                                        echo "<div class='wall__user-info'>";
                                        echo "<img class='avatar' src='uploads/avatar/thin_" . $avatar . "'>";
                                        echo "<div>";
                                        if ($user_id == $_SESSION['user']['id']) {
                                            if ($username == 'rampus') {
                                                echo "<a href='./profile' class='first-and-second-names rampus'>" . $first_name . " " . $second_name . "<img src='pics/SuperUserIcon.svg'></a>";
                                            } else {
                                                echo "<a href='./profile' class='first-and-second-names'>" . $first_name . " " . $second_name . "</a>";
                                            }
                                        } else {
                                            if ($username == 'rampus') {
                                                echo "<a href='./user/$username' class='first-and-second-names rampus'>" . $first_name . " " . $second_name . "<img src='pics/SuperUserIcon.svg'></a>";
                                            } else {
                                                echo "<a href='./user/$username' class='first-and-second-names'>" . $first_name . " " . $second_name . "</a>";
                                            }
                                        }
                                        echo "<span>" . $post_date . "</span>";
                                        echo "</div>";
                                        echo "</div>";
                                        echo "<img onclick='showPopup($i)' src='pics/ThreeDotsIcon.svg' class='show-three-dots-popup'>";
                                        echo "<div class='three-dots-popup' id='three-dots-popup_$i'>";
                                        echo "<span class='three-dots-popup-li copy-link' onclick='copyLinkToPost($i)'>Копировать ссылку</span>";
                                        echo "<a class='three-dots-popup-li open-profile' href='./user/$username'>Открыть профиль</a>";
                                        if ($user_id == $_SESSION['user']['id']) {
                                            // echo "<a class='three-dots-popup-li edit-post' href='./wall'>*************</a>";
                                            echo "<a class='three-dots-popup-li delete-post' href='deletepost?post=$i&source=wall'>Удалить</a>";
                                        }
                                        echo "</div>";
                                        echo "</div>";
                                        if ($hashtag_id != 0) {
                                            $hashtag_name = $connect->query("SELECT name FROM hashtags WHERE id = $hashtag_id")->fetch_assoc()['name'];
                                            echo "<p class='main-text'>" . $post_text . " <a href='?search=$hashtag_name'>#" . $hashtag_name . "</a></p>";
                                        } else {
                                            echo "<p class='main-text'>" . $post_text . "</p>";
                                        }
                                        echo "<div class='post-buttons'>";
                                        $sql_comment = "SELECT comments.text AS comment_text, users.first_name AS first_name, users.second_name AS second_name, users.avatar AS avatar, DATE_FORMAT(comments.comment_date, '%d %M в %k:%i') AS comment_date, users.id AS comment_user_id, users.username AS comment_username
                                        FROM comments
                                        JOIN users ON comments.user_id = users.id
                                        JOIN posts ON comments.post_id = posts.id
                                        WHERE comments.post_id = $i
                                        ORDER BY UNIX_TIMESTAMP(comments.comment_date) ASC";
                                        $result_comment = $connect->query($sql_comment);
                                        $rows_num_comment = $result_comment->num_rows;
                                        $sql_like = "SELECT * FROM likes_on_posts WHERE post_id = $i AND user_id = " . $_SESSION['user']['id'];
                                        $result_like = $connect->query($sql_like);
                                        if ($result_like->num_rows > 0) {
                                            echo "<button id='$i' class='like-button liked'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                                <path fill-rule='evenodd' clip-rule='evenodd' d='M15.3643 17.1232L21.0488 11.4387L21.0494 11.4394C21.6548 10.834 22.135 10.1153 22.4626 9.32432C22.7903 8.53335 22.9589 7.6856 22.9589 6.82947C22.9589 5.97334 22.7903 5.12559 22.4626 4.33463C22.135 3.54366 21.6548 2.82498 21.0494 2.2196C20.4441 1.61423 19.7254 1.13402 18.9344 0.806393C18.1434 0.478767 17.2957 0.310142 16.4396 0.310145C15.5834 0.310148 14.7357 0.478778 13.9447 0.806409C13.1541 1.13391 12.4356 1.61388 11.8304 2.21893L11.8289 2.21742L11.8279 2.21836C11.2229 1.61375 10.5048 1.1341 9.71455 0.806772C8.92359 0.479147 8.07584 0.310521 7.2197 0.310524C6.36357 0.310526 5.51582 0.479157 4.72486 0.806787C3.93389 1.13442 3.2152 1.61463 2.60982 2.22001C2.00444 2.82539 1.52423 3.54408 1.1966 4.33504C0.868969 5.12601 0.700339 5.97376 0.700335 6.82989C0.700332 7.68602 0.868959 8.53377 1.19658 9.32474C1.52421 10.1157 2.00442 10.8344 2.60979 11.4398L2.60985 11.4397L8.29331 17.1232C10.2459 19.0758 13.4117 19.0758 15.3643 17.1232Z'/>
                                            </svg>";
                                            echo "<span class='like-counter'>" . $post_likes . "</span></button>";
                                            echo "<button id='$i' class='like-button unliked hide'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                                <path fill-rule='evenodd' clip-rule='evenodd' d='M15.3643 17.1232L21.0488 11.4387L21.0494 11.4394C21.6548 10.834 22.135 10.1153 22.4626 9.32432C22.7903 8.53335 22.9589 7.6856 22.9589 6.82947C22.9589 5.97334 22.7903 5.12559 22.4626 4.33463C22.135 3.54366 21.6548 2.82498 21.0494 2.2196C20.4441 1.61423 19.7254 1.13402 18.9344 0.806393C18.1434 0.478767 17.2957 0.310142 16.4396 0.310145C15.5834 0.310148 14.7357 0.478778 13.9447 0.806409C13.1541 1.13391 12.4356 1.61388 11.8304 2.21893L11.8289 2.21742L11.8279 2.21836C11.2229 1.61375 10.5048 1.1341 9.71455 0.806772C8.92359 0.479147 8.07584 0.310521 7.2197 0.310524C6.36357 0.310526 5.51582 0.479157 4.72486 0.806787C3.93389 1.13442 3.2152 1.61463 2.60982 2.22001C2.00444 2.82539 1.52423 3.54408 1.1966 4.33504C0.868969 5.12601 0.700339 5.97376 0.700335 6.82989C0.700332 7.68602 0.868959 8.53377 1.19658 9.32474C1.52421 10.1157 2.00442 10.8344 2.60979 11.4398L2.60985 11.4397L8.29331 17.1232C10.2459 19.0758 13.4117 19.0758 15.3643 17.1232Z'/>
                                            </svg>";
                                            echo "<span class='like-counter'>" . $post_likes . "</span></button>";
                                        } else {
                                            echo "<button id='$i' class='like-button unliked'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                                <path fill-rule='evenodd' clip-rule='evenodd' d='M15.3643 17.1232L21.0488 11.4387L21.0494 11.4394C21.6548 10.834 22.135 10.1153 22.4626 9.32432C22.7903 8.53335 22.9589 7.6856 22.9589 6.82947C22.9589 5.97334 22.7903 5.12559 22.4626 4.33463C22.135 3.54366 21.6548 2.82498 21.0494 2.2196C20.4441 1.61423 19.7254 1.13402 18.9344 0.806393C18.1434 0.478767 17.2957 0.310142 16.4396 0.310145C15.5834 0.310148 14.7357 0.478778 13.9447 0.806409C13.1541 1.13391 12.4356 1.61388 11.8304 2.21893L11.8289 2.21742L11.8279 2.21836C11.2229 1.61375 10.5048 1.1341 9.71455 0.806772C8.92359 0.479147 8.07584 0.310521 7.2197 0.310524C6.36357 0.310526 5.51582 0.479157 4.72486 0.806787C3.93389 1.13442 3.2152 1.61463 2.60982 2.22001C2.00444 2.82539 1.52423 3.54408 1.1966 4.33504C0.868969 5.12601 0.700339 5.97376 0.700335 6.82989C0.700332 7.68602 0.868959 8.53377 1.19658 9.32474C1.52421 10.1157 2.00442 10.8344 2.60979 11.4398L2.60985 11.4397L8.29331 17.1232C10.2459 19.0758 13.4117 19.0758 15.3643 17.1232Z'/>
                                            </svg>";
                                            echo "<span class='like-counter'>" . $post_likes . "</span></button>";
                                            echo "<button id='$i' class='like-button liked hide'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                                <path fill-rule='evenodd' clip-rule='evenodd' d='M15.3643 17.1232L21.0488 11.4387L21.0494 11.4394C21.6548 10.834 22.135 10.1153 22.4626 9.32432C22.7903 8.53335 22.9589 7.6856 22.9589 6.82947C22.9589 5.97334 22.7903 5.12559 22.4626 4.33463C22.135 3.54366 21.6548 2.82498 21.0494 2.2196C20.4441 1.61423 19.7254 1.13402 18.9344 0.806393C18.1434 0.478767 17.2957 0.310142 16.4396 0.310145C15.5834 0.310148 14.7357 0.478778 13.9447 0.806409C13.1541 1.13391 12.4356 1.61388 11.8304 2.21893L11.8289 2.21742L11.8279 2.21836C11.2229 1.61375 10.5048 1.1341 9.71455 0.806772C8.92359 0.479147 8.07584 0.310521 7.2197 0.310524C6.36357 0.310526 5.51582 0.479157 4.72486 0.806787C3.93389 1.13442 3.2152 1.61463 2.60982 2.22001C2.00444 2.82539 1.52423 3.54408 1.1966 4.33504C0.868969 5.12601 0.700339 5.97376 0.700335 6.82989C0.700332 7.68602 0.868959 8.53377 1.19658 9.32474C1.52421 10.1157 2.00442 10.8344 2.60979 11.4398L2.60985 11.4397L8.29331 17.1232C10.2459 19.0758 13.4117 19.0758 15.3643 17.1232Z'/>
                                            </svg>";
                                            echo "<span class='like-counter'>" . $post_likes . "</span></button>";
                                        }
                                        echo "<button onclick='commentButtonClick($i)' class='comment-button comment'><svg width='28' height='24' viewBox='0 0 28 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                            <path d='M0 5C0 2.23858 2.23858 0 5 0L23 0C25.7614 0 28 2.23858 28 5L28 24L5 24C2.23858 24 0 21.7614 0 19L0 5Z' />
                                            </svg>";
                                        echo "<span class='comment-counter'>" . $rows_num_comment . "</span></button>";
                                        echo "</div>";
                                        echo "<div class='div-line'></div>";
                                        echo "<div class='wall__comments'>";
                                        if ($rows_num_comment > 0) {
                                            echo "<div class='other-users'>";
                                            $comment_count = 2;
                                            $comment_count_current = $result_comment->num_rows;
                                            while ($row_comment = $result_comment->fetch_assoc()) {
                                                $comment_user_id = $row_comment['comment_user_id'];
                                                $comment_username = $row_comment['comment_username'];
                                                $first_name = $row_comment['first_name'];
                                                $second_name = $row_comment['second_name'];
                                                $avatar = $row_comment['avatar'];
                                                $comment_text = preg_replace('/\xc2\xa0/', ' ', $row_comment['comment_text']);
                                                $comment_date = $row_comment['comment_date'];
                                                if ($comment_count_current > 2) {
                                                    if ($comment_count > 0) {
                                                        if ($rows_num_comment < $result_comment->num_rows) {
                                                            echo "<div class='div-line'></div>";
                                                        }
                                                        echo "<div class='user-comment'>";
                                                        echo "<img src='uploads/avatar/thin_" . $avatar . "'>";
                                                        echo "<div>";
                                                        if ($comment_user_id == $_SESSION['user']['id']) {
                                                            if ($comment_username == 'rampus') {
                                                                echo "<a href='./profile' class='first-and-second-names rampus'>" . $first_name . " " . $second_name . "<img src='pics/SuperUserIcon.svg'></a>";
                                                            } else {
                                                                echo "<a href='./profile' class='first-and-second-names'>" . $first_name . " " . $second_name . "</a>";
                                                            }
                                                        } else {
                                                            if ($comment_username == 'rampus') {
                                                                echo "<a href='./user/$comment_username' class='first-and-second-names rampus'>" . $first_name . " " . $second_name . "<img src='pics/SuperUserIcon.svg'></a>";
                                                            } else {
                                                                echo "<a href='./user/$comment_username' class='first-and-second-names'>" . $first_name . " " . $second_name . "</a>";
                                                            }
                                                        }
                                                        echo "<p class='comment-text main-text'>" . $comment_text . "</p>";
                                                        echo "<span class='date'>" . $comment_date . "</span>";
                                                        echo "</div>";
                                                        echo "</div>";
                                                        $comment_count -= 1;
                                                    } else {
                                                        if ($rows_num_comment < $result_comment->num_rows) {
                                                            echo "<div class='div-line hide comment_div-line_$i'></div>";
                                                        }
                                                        echo "<div class='user-comment hide comment_user-comment_$i'>";
                                                        echo "<img src='uploads/avatar/thin_" . $avatar . "'>";
                                                        echo "<div>";
                                                        if ($comment_user_id == $_SESSION['user']['id']) {
                                                            if ($comment_username == 'rampus') {
                                                                echo "<a href='./profile' class='first-and-second-names rampus'>" . $first_name . " " . $second_name . "<img src='pics/SuperUserIcon.svg'></a>";
                                                            } else {
                                                                echo "<a href='./profile' class='first-and-second-names'>" . $first_name . " " . $second_name . "</a>";
                                                            }
                                                        } else {
                                                            if ($comment_username == 'rampus') {
                                                                echo "<a href='./user/$comment_username' class='first-and-second-names rampus'>" . $first_name . " " . $second_name . "<img src='pics/SuperUserIcon.svg'></a>";
                                                            } else {
                                                                echo "<a href='./user/$comment_username' class='first-and-second-names'>" . $first_name . " " . $second_name . "</a>";
                                                            }
                                                        }
                                                        echo "<p class='comment-text main-text'>" . $comment_text . "</p>";
                                                        echo "<span class='date'>" . $comment_date . "</span>";
                                                        echo "</div>";
                                                        echo "</div>";
                                                    }
                                                } else {
                                                    if ($rows_num_comment < $result_comment->num_rows) {
                                                        echo "<div class='div-line'></div>";
                                                    }
                                                    echo "<div class='user-comment'>";
                                                    echo "<img src='uploads/avatar/thin_" . $avatar . "'>";
                                                    echo "<div>";
                                                    if ($comment_user_id == $_SESSION['user']['id']) {
                                                        if ($comment_username == 'rampus') {
                                                            echo "<a href='./profile' class='first-and-second-names rampus'>" . $first_name . " " . $second_name . "<img src='pics/SuperUserIcon.svg'></a>";
                                                        } else {
                                                            echo "<a href='./profile' class='first-and-second-names'>" . $first_name . " " . $second_name . "</a>";
                                                        }
                                                    } else {
                                                        if ($comment_username == 'rampus') {
                                                            echo "<a href='./user/$comment_username' class='first-and-second-names rampus'>" . $first_name . " " . $second_name . "<img src='pics/SuperUserIcon.svg'></a>";
                                                        } else {
                                                            echo "<a href='./user/$comment_username' class='first-and-second-names'>" . $first_name . " " . $second_name . "</a>";
                                                        }
                                                    }
                                                    echo "<p class='comment-text main-text'>" . $comment_text . "</p>";
                                                    echo "<span class='date'>" . $comment_date . "</span>";
                                                    echo "</div>";
                                                    echo "</div>";
                                                }
                                                $rows_num_comment -= 1;
                                            }
                                            if ($comment_count_current > 2) {
                                                echo "<p class='see-all-comments' onclick='seeAllComments($i)' id='see-all-comments_$i'>Показать все комментарии</p>";
                                            }
                                            echo "</div>";
                                        }
                                        echo "<div class='current-user'>";
                                        echo "<form action='./comment' method='post' autocomplete='off'>
                                        <div contenteditable='true' class='textarea-comment' id='textarea-comment_$i' role='textbox' onkeyup='textareaComment(event, $i)' onkeydown='textareaCommentPlaceholder(event, $i)'></div>
                                        <label for='textarea-comment' class='textarea-comment_label' id='textarea-comment_label_$i'>Ответить..</label>
                                        <input type='hidden' required name='comment' class='textarea-comment_input' id='textarea-comment_input_$i' value=''>
                                        <input type='hidden' name='comment_id' value='$i'>
                                        <button type='submit' id='textarea-comment_submit_$i' class='' disabled><img src='pics/SendIcon.svg'></button>
                                    </form>";
                                        echo "</div>";
                                        echo "</div>";
                                        echo "</div>";
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <div class="wall__user-posts" id="posts-filter-friends">
                            <div>
                                <?php
                                if (!isset($_GET['search'])) {
                                    $search = 'all';
                                } else {
                                    $search = $_GET['search'];
                                }
                                $search_condition = $search !== 'all' ? "AND hashtags.name = '$search'" : '';
                                $sql_post = "SELECT posts.hashtag_id AS hashtag_id, posts.text AS post_text, DATE_FORMAT(posts.post_date, '%d %M в %k:%i') AS post_date, posts.likes AS post_likes, posts.user_id AS user_id, users.first_name AS first_name, users.second_name AS second_name, users.avatar AS avatar, posts.id AS i, users.username AS username
                            FROM posts
                            JOIN users ON posts.user_id = users.id
                            LEFT JOIN hashtags ON posts.hashtag_id = hashtags.id
                            WHERE posts.status = 0 $search_condition";
                                $result_post = $connect->query($sql_post);
                                if ($result_post->num_rows > 0) {
                                    while ($row_post = $result_post->fetch_assoc()) {
                                        $user_id = $row_post["user_id"];
                                        if (in_array($user_id, $friends_id)) {

                                            $hashtag_id = $row_post["hashtag_id"];
                                            $post_text = preg_replace('/\xc2\xa0/', ' ', $row_post["post_text"]);
                                            $post_date = $row_post["post_date"];
                                            $post_likes = $row_post["post_likes"];
                                            $first_name = $row_post["first_name"];
                                            $second_name = $row_post["second_name"];
                                            $username = $row_post["username"];
                                            $avatar = $row_post["avatar"];
                                            $i = $row_post['i'];
                                            echo "<div class='user-post' id='post-$i'>";
                                            echo "<div>";
                                            echo "<div class='wall__user-info'>";
                                            echo "<img class='avatar' src='uploads/avatar/thin_" . $avatar . "'>";
                                            echo "<div>";
                                            if ($user_id == $_SESSION['user']['id']) {
                                                if ($username == 'rampus') {
                                                    echo "<a href='./profile' class='first-and-second-names rampus'>" . $first_name . " " . $second_name . "<img src='pics/SuperUserIcon.svg'></a>";
                                                } else {
                                                    echo "<a href='./profile' class='first-and-second-names'>" . $first_name . " " . $second_name . "</a>";
                                                }
                                            } else {
                                                if ($username == 'rampus') {
                                                    echo "<a href='./user/$username' class='first-and-second-names rampus'>" . $first_name . " " . $second_name . "<img src='pics/SuperUserIcon.svg'></a>";
                                                } else {
                                                    echo "<a href='./user/$username' class='first-and-second-names'>" . $first_name . " " . $second_name . "</a>";
                                                }
                                            }
                                            echo "<span>" . $post_date . "</span>";
                                            echo "</div>";
                                            echo "</div>";
                                            echo "<img onclick='showPopup($i)' src='pics/ThreeDotsIcon.svg' class='show-three-dots-popup'>";
                                            echo "<div class='three-dots-popup' id='three-dots-popup_$i'>";
                                            echo "<span class='three-dots-popup-li copy-link' onclick='copyLinkToPost($i)'>Копировать ссылку</span>";
                                            echo "<a class='three-dots-popup-li open-profile' href='./user/$username'>Открыть профиль</a>";
                                            if ($user_id == $_SESSION['user']['id']) {
                                                // echo "<a class='three-dots-popup-li edit-post' href='./wall'>*************</a>";
                                                echo "<a class='three-dots-popup-li delete-post' href='deletepost?post=$i&source=wall'>Удалить</a>";
                                            }
                                            echo "</div>";
                                            echo "</div>";
                                            if ($hashtag_id != 0) {
                                                $hashtag_name = $connect->query("SELECT name FROM hashtags WHERE id = $hashtag_id")->fetch_assoc()['name'];
                                                echo "<p class='main-text'>" . $post_text . " <a href='?search=$hashtag_name'>#" . $hashtag_name . "</a></p>";
                                            } else {
                                                echo "<p class='main-text'>" . $post_text . "</p>";
                                            }
                                            echo "<div class='post-buttons'>";
                                            $sql_comment = "SELECT comments.text AS comment_text, users.first_name AS first_name, users.second_name AS second_name, users.avatar AS avatar, DATE_FORMAT(comments.comment_date, '%d %M в %k:%i') AS comment_date, users.id AS comment_user_id, users.username AS comment_username
                                        FROM comments
                                        JOIN users ON comments.user_id = users.id
                                        JOIN posts ON comments.post_id = posts.id
                                        WHERE comments.post_id = $i
                                        ORDER BY UNIX_TIMESTAMP(comments.comment_date) ASC";
                                            $result_comment = $connect->query($sql_comment);
                                            $rows_num_comment = $result_comment->num_rows;
                                            $sql_like = "SELECT * FROM likes_on_posts WHERE post_id = $i AND user_id = " . $_SESSION['user']['id'];
                                            $result_like = $connect->query($sql_like);
                                            if ($result_like->num_rows > 0) {
                                                echo "<button id='$i' class='like-button liked'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                            <path fill-rule='evenodd' clip-rule='evenodd' d='M15.3643 17.1232L21.0488 11.4387L21.0494 11.4394C21.6548 10.834 22.135 10.1153 22.4626 9.32432C22.7903 8.53335 22.9589 7.6856 22.9589 6.82947C22.9589 5.97334 22.7903 5.12559 22.4626 4.33463C22.135 3.54366 21.6548 2.82498 21.0494 2.2196C20.4441 1.61423 19.7254 1.13402 18.9344 0.806393C18.1434 0.478767 17.2957 0.310142 16.4396 0.310145C15.5834 0.310148 14.7357 0.478778 13.9447 0.806409C13.1541 1.13391 12.4356 1.61388 11.8304 2.21893L11.8289 2.21742L11.8279 2.21836C11.2229 1.61375 10.5048 1.1341 9.71455 0.806772C8.92359 0.479147 8.07584 0.310521 7.2197 0.310524C6.36357 0.310526 5.51582 0.479157 4.72486 0.806787C3.93389 1.13442 3.2152 1.61463 2.60982 2.22001C2.00444 2.82539 1.52423 3.54408 1.1966 4.33504C0.868969 5.12601 0.700339 5.97376 0.700335 6.82989C0.700332 7.68602 0.868959 8.53377 1.19658 9.32474C1.52421 10.1157 2.00442 10.8344 2.60979 11.4398L2.60985 11.4397L8.29331 17.1232C10.2459 19.0758 13.4117 19.0758 15.3643 17.1232Z'/>
                                            </svg>";
                                                echo "<span class='like-counter'>" . $post_likes . "</span></button>";
                                                echo "<button id='$i' class='like-button unliked hide'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                            <path fill-rule='evenodd' clip-rule='evenodd' d='M15.3643 17.1232L21.0488 11.4387L21.0494 11.4394C21.6548 10.834 22.135 10.1153 22.4626 9.32432C22.7903 8.53335 22.9589 7.6856 22.9589 6.82947C22.9589 5.97334 22.7903 5.12559 22.4626 4.33463C22.135 3.54366 21.6548 2.82498 21.0494 2.2196C20.4441 1.61423 19.7254 1.13402 18.9344 0.806393C18.1434 0.478767 17.2957 0.310142 16.4396 0.310145C15.5834 0.310148 14.7357 0.478778 13.9447 0.806409C13.1541 1.13391 12.4356 1.61388 11.8304 2.21893L11.8289 2.21742L11.8279 2.21836C11.2229 1.61375 10.5048 1.1341 9.71455 0.806772C8.92359 0.479147 8.07584 0.310521 7.2197 0.310524C6.36357 0.310526 5.51582 0.479157 4.72486 0.806787C3.93389 1.13442 3.2152 1.61463 2.60982 2.22001C2.00444 2.82539 1.52423 3.54408 1.1966 4.33504C0.868969 5.12601 0.700339 5.97376 0.700335 6.82989C0.700332 7.68602 0.868959 8.53377 1.19658 9.32474C1.52421 10.1157 2.00442 10.8344 2.60979 11.4398L2.60985 11.4397L8.29331 17.1232C10.2459 19.0758 13.4117 19.0758 15.3643 17.1232Z'/>
                                            </svg>";
                                                echo "<span class='like-counter'>" . $post_likes . "</span></button>";
                                            } else {
                                                echo "<button id='$i' class='like-button unliked'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                            <path fill-rule='evenodd' clip-rule='evenodd' d='M15.3643 17.1232L21.0488 11.4387L21.0494 11.4394C21.6548 10.834 22.135 10.1153 22.4626 9.32432C22.7903 8.53335 22.9589 7.6856 22.9589 6.82947C22.9589 5.97334 22.7903 5.12559 22.4626 4.33463C22.135 3.54366 21.6548 2.82498 21.0494 2.2196C20.4441 1.61423 19.7254 1.13402 18.9344 0.806393C18.1434 0.478767 17.2957 0.310142 16.4396 0.310145C15.5834 0.310148 14.7357 0.478778 13.9447 0.806409C13.1541 1.13391 12.4356 1.61388 11.8304 2.21893L11.8289 2.21742L11.8279 2.21836C11.2229 1.61375 10.5048 1.1341 9.71455 0.806772C8.92359 0.479147 8.07584 0.310521 7.2197 0.310524C6.36357 0.310526 5.51582 0.479157 4.72486 0.806787C3.93389 1.13442 3.2152 1.61463 2.60982 2.22001C2.00444 2.82539 1.52423 3.54408 1.1966 4.33504C0.868969 5.12601 0.700339 5.97376 0.700335 6.82989C0.700332 7.68602 0.868959 8.53377 1.19658 9.32474C1.52421 10.1157 2.00442 10.8344 2.60979 11.4398L2.60985 11.4397L8.29331 17.1232C10.2459 19.0758 13.4117 19.0758 15.3643 17.1232Z'/>
                                            </svg>";
                                                echo "<span class='like-counter'>" . $post_likes . "</span></button>";
                                                echo "<button id='$i' class='like-button liked hide'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                            <path fill-rule='evenodd' clip-rule='evenodd' d='M15.3643 17.1232L21.0488 11.4387L21.0494 11.4394C21.6548 10.834 22.135 10.1153 22.4626 9.32432C22.7903 8.53335 22.9589 7.6856 22.9589 6.82947C22.9589 5.97334 22.7903 5.12559 22.4626 4.33463C22.135 3.54366 21.6548 2.82498 21.0494 2.2196C20.4441 1.61423 19.7254 1.13402 18.9344 0.806393C18.1434 0.478767 17.2957 0.310142 16.4396 0.310145C15.5834 0.310148 14.7357 0.478778 13.9447 0.806409C13.1541 1.13391 12.4356 1.61388 11.8304 2.21893L11.8289 2.21742L11.8279 2.21836C11.2229 1.61375 10.5048 1.1341 9.71455 0.806772C8.92359 0.479147 8.07584 0.310521 7.2197 0.310524C6.36357 0.310526 5.51582 0.479157 4.72486 0.806787C3.93389 1.13442 3.2152 1.61463 2.60982 2.22001C2.00444 2.82539 1.52423 3.54408 1.1966 4.33504C0.868969 5.12601 0.700339 5.97376 0.700335 6.82989C0.700332 7.68602 0.868959 8.53377 1.19658 9.32474C1.52421 10.1157 2.00442 10.8344 2.60979 11.4398L2.60985 11.4397L8.29331 17.1232C10.2459 19.0758 13.4117 19.0758 15.3643 17.1232Z'/>
                                            </svg>";
                                                echo "<span class='like-counter'>" . $post_likes . "</span></button>";
                                            }
                                            echo "<button onclick='commentButtonClick($i)' class='comment-button comment'><svg width='28' height='24' viewBox='0 0 28 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                        <path d='M0 5C0 2.23858 2.23858 0 5 0L23 0C25.7614 0 28 2.23858 28 5L28 24L5 24C2.23858 24 0 21.7614 0 19L0 5Z' />
                                        </svg>";
                                            echo "<span class='comment-counter'>" . $rows_num_comment . "</span></button>";
                                            echo "</div>";
                                            echo "<div class='div-line'></div>";
                                            echo "<div class='wall__comments'>";
                                            if ($rows_num_comment > 0) {
                                                echo "<div class='other-users'>";
                                                $comment_count = 2;
                                                $comment_count_current = $result_comment->num_rows;
                                                while ($row_comment = $result_comment->fetch_assoc()) {
                                                    $comment_user_id = $row_comment['comment_user_id'];
                                                    $comment_username = $row_comment['comment_username'];
                                                    $first_name = $row_comment['first_name'];
                                                    $second_name = $row_comment['second_name'];
                                                    $avatar = $row_comment['avatar'];
                                                    $comment_text = preg_replace('/\xc2\xa0/', ' ', $row_comment['comment_text']);
                                                    $comment_date = $row_comment['comment_date'];
                                                    if ($comment_count_current > 2) {
                                                        if ($comment_count > 0) {
                                                            if ($rows_num_comment < $result_comment->num_rows) {
                                                                echo "<div class='div-line'></div>";
                                                            }
                                                            echo "<div class='user-comment'>";
                                                            echo "<img src='uploads/avatar/thin_" . $avatar . "'>";
                                                            echo "<div>";
                                                            if ($comment_user_id == $_SESSION['user']['id']) {
                                                                if ($comment_username == 'rampus') {
                                                                    echo "<a href='./profile' class='first-and-second-names rampus'>" . $first_name . " " . $second_name . "<img src='pics/SuperUserIcon.svg'></a>";
                                                                } else {
                                                                    echo "<a href='./profile' class='first-and-second-names'>" . $first_name . " " . $second_name . "</a>";
                                                                }
                                                            } else {
                                                                if ($comment_username == 'rampus') {
                                                                    echo "<a href='./user/$comment_username' class='first-and-second-names rampus'>" . $first_name . " " . $second_name . "<img src='pics/SuperUserIcon.svg'></a>";
                                                                } else {
                                                                    echo "<a href='./user/$comment_username' class='first-and-second-names'>" . $first_name . " " . $second_name . "</a>";
                                                                }
                                                            }
                                                            echo "<p class='comment-text main-text'>" . $comment_text . "</p>";
                                                            echo "<span class='date'>" . $comment_date . "</span>";
                                                            echo "</div>";
                                                            echo "</div>";
                                                            $comment_count -= 1;
                                                        } else {
                                                            if ($rows_num_comment < $result_comment->num_rows) {
                                                                echo "<div class='div-line hide comment_div-line_$i'></div>";
                                                            }
                                                            echo "<div class='user-comment hide comment_user-comment_$i'>";
                                                            echo "<img src='uploads/avatar/thin_" . $avatar . "'>";
                                                            echo "<div>";
                                                            if ($comment_user_id == $_SESSION['user']['id']) {
                                                                if ($comment_username == 'rampus') {
                                                                    echo "<a href='./profile' class='first-and-second-names rampus'>" . $first_name . " " . $second_name . "<img src='pics/SuperUserIcon.svg'></a>";
                                                                } else {
                                                                    echo "<a href='./profile' class='first-and-second-names'>" . $first_name . " " . $second_name . "</a>";
                                                                }
                                                            } else {
                                                                if ($comment_username == 'rampus') {
                                                                    echo "<a href='./user/$comment_username' class='first-and-second-names rampus'>" . $first_name . " " . $second_name . "<img src='pics/SuperUserIcon.svg'></a>";
                                                                } else {
                                                                    echo "<a href='./user/$comment_username' class='first-and-second-names'>" . $first_name . " " . $second_name . "</a>";
                                                                }
                                                            }
                                                            echo "<p class='comment-text main-text'>" . $comment_text . "</p>";
                                                            echo "<span class='date'>" . $comment_date . "</span>";
                                                            echo "</div>";
                                                            echo "</div>";
                                                        }
                                                    } else {
                                                        if ($rows_num_comment < $result_comment->num_rows) {
                                                            echo "<div class='div-line'></div>";
                                                        }
                                                        echo "<div class='user-comment'>";
                                                        echo "<img src='uploads/avatar/thin_" . $avatar . "'>";
                                                        echo "<div>";
                                                        if ($comment_user_id == $_SESSION['user']['id']) {
                                                            if ($comment_username == 'rampus') {
                                                                echo "<a href='./profile' class='first-and-second-names rampus'>" . $first_name . " " . $second_name . "<img src='pics/SuperUserIcon.svg'></a>";
                                                            } else {
                                                                echo "<a href='./profile' class='first-and-second-names'>" . $first_name . " " . $second_name . "</a>";
                                                            }
                                                        } else {
                                                            if ($comment_username == 'rampus') {
                                                                echo "<a href='./user/$comment_username' class='first-and-second-names rampus'>" . $first_name . " " . $second_name . "<img src='pics/SuperUserIcon.svg'></a>";
                                                            } else {
                                                                echo "<a href='./user/$comment_username' class='first-and-second-names'>" . $first_name . " " . $second_name . "</a>";
                                                            }
                                                        }
                                                        echo "<p class='comment-text main-text'>" . $comment_text . "</p>";
                                                        echo "<span class='date'>" . $comment_date . "</span>";
                                                        echo "</div>";
                                                        echo "</div>";
                                                    }
                                                    $rows_num_comment -= 1;
                                                }
                                                if ($comment_count_current > 2) {
                                                    echo "<p class='see-all-comments' onclick='seeAllComments($i)' id='see-all-comments_$i'>Показать все комментарии</p>";
                                                }
                                                echo "</div>";
                                            }
                                            echo "<div class='current-user'>";
                                            echo "<form action='./comment' method='post' autocomplete='off'>
                                        <div contenteditable='true' class='textarea-comment' id='textarea-comment_$i' role='textbox' onkeyup='textareaComment(event, $i)' onkeydown='textareaCommentPlaceholder(event, $i)'></div>
                                        <label for='textarea-comment' class='textarea-comment_label' id='textarea-comment_label_$i'>Ответить..</label>
                                        <input type='hidden' required name='comment' class='textarea-comment_input' id='textarea-comment_input_$i' value=''>
                                        <input type='hidden' name='comment_id' value='$i'>
                                        <button type='submit' id='textarea-comment_submit_$i' class='' disabled><img src='pics/SendIcon.svg'></button>
                                        </form>";
                                            echo "</div>";
                                            echo "</div>";
                                            echo "</div>";
                                        }
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <nav class="first-part-mobile">
                            <ul>
                                <li id="active"><a href="./wall"><svg width="28" height="24" viewBox="0 0 28 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M4.56528 18C4.54895 17.8355 4.54059 17.6687 4.54059 17.5V10.5C4.54059 7.18629 7.22688 4.5 10.5406 4.5H21.4865C21.6336 4.5 21.7792 4.50635 21.9231 4.5188C21.681 1.98313 19.545 0 16.9459 0H5.00001C2.23858 0 0 2.23858 0 5V18H4.56528Z" />
                                            <path d="M6.05408 11C6.05408 8.23858 8.29265 6 11.0541 6H23C25.7614 6 28 8.23858 28 11V24H11.0541C8.29266 24 6.05408 21.7614 6.05408 19V11Z" />
                                        </svg>
                                        Стена
                                    </a></li>
                                <li><a href="./wall">
                                        <svg width="16" height="24" viewBox="0 0 16 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M4.97802 21.033C4.97802 21.8681 5.25275 22.5714 5.8022 23.1429C6.35165 23.7143 7.04396 24 7.87912 24C8.73626 24 9.43956 23.7253 9.98901 23.1758C10.5604 22.6044 10.8462 21.9011 10.8462 21.0659C10.8462 20.2088 10.5604 19.4945 9.98901 18.9231C9.41758 18.3516 8.71429 18.0659 7.87912 18.0659C7.04396 18.0659 6.35165 18.3516 5.8022 18.9231C5.25275 19.4725 4.97802 20.1758 4.97802 21.033ZM15.5275 7.51648C15.5275 9.07692 15.0769 10.4505 14.1758 11.6374C13.2967 12.8022 12.0769 13.7473 10.5165 14.4725L7.21978 16.2198L5.73626 12.6593L8.73626 11.0769C10.4066 10.022 11.2418 8.84615 11.2418 7.54945C11.2418 6.58242 10.9231 5.8022 10.2857 5.20879C9.64835 4.59341 8.83517 4.28571 7.84615 4.28571C5.93407 4.28571 4.64835 5.43956 3.98901 7.74725L0 6.2967C0.615385 4.34066 1.59341 2.8022 2.93407 1.68132C4.27473 0.56044 5.9011 0 7.81319 0C10.033 0 11.8681 0.714286 13.3187 2.14286C14.7912 3.54945 15.5275 5.34066 15.5275 7.51648Z" />
                                        </svg>
                                        ****</a></li>
                                <li><a href="./people">
                                        <svg width="28" height="24" viewBox="0 0 28 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.66113 15.5149C7.88196 16.2628 7.21631 17.0995 6.67468 18H0C0 15.2153 1.1062 12.5446 3.07532 10.5754C4.3374 9.31335 5.8877 8.40576 7.57141 7.91675C6.60938 7.09143 6 5.86682 6 4.5C6 2.01477 8.01477 0 10.5 0C12.9852 0 15 2.01477 15 4.5C15 4.6897 14.9883 4.87659 14.9655 5.06018C12.9185 6.0155 11.5 8.09216 11.5 10.5001C11.5 11.458 11.7245 12.3635 12.1237 13.1667C10.8506 13.749 9.67737 14.5393 8.66113 15.5149ZM22 10.5001C22 12.9854 19.9852 15.0001 17.5 15.0001C15.0148 15.0001 13 12.9854 13 10.5001C13 8.01489 15.0148 6.00012 17.5 6.00012C19.9852 6.00012 22 8.01489 22 10.5001ZM13.0278 14.5001C11.9414 15.0116 10.9407 15.7102 10.0753 16.5754C8.1062 18.5447 7 21.2153 7 24.0001H17.5H28C28 21.2153 26.8937 18.5447 24.9246 16.5754C24.0593 15.7102 23.0586 15.0116 21.9722 14.5001C20.8735 15.7277 19.277 16.5001 17.5 16.5001C15.723 16.5001 14.1265 15.7277 13.0278 14.5001Z" />
                                        </svg>
                                        Люди</a></li>
                                <li><a href="./profile"><svg width="28" height="24" viewBox="0 0 28 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.03709 11.3334C6.58858 12.0152 5.25423 12.9468 4.10051 14.1005C1.475 16.726 0 20.287 0 24L14 24H28C28 20.287 26.525 16.726 23.8995 14.1005C22.7458 12.9468 21.4114 12.0152 19.9629 11.3334C18.4981 12.97 16.3693 14 14 14C11.6307 14 9.50195 12.97 8.03709 11.3334Z" />
                                            <circle cx="14" cy="6" r="6" />
                                        </svg>
                                        Профиль
                                    </a></li>
                            </ul>
                        </nav>
                    </div>
                    <div class="third-part">
                        <div>
                            <div>
                                <input type="text" name="search-hashtag" id="search-hashtag" placeholder="Поиск">
                                <input type="hidden" name="get-status" id="get-status" value=<?php if (isset($_GET['search'])) {
                                                                                                    echo $_GET['search'];
                                                                                                } else {
                                                                                                    echo null;
                                                                                                } ?>>
                                <img id="icon-search-hashtag" src="pics/SearchIcon.svg">
                            </div>
                            <ul id="success-search-hashtag">
                            </ul>
                        </div>
                    </div>
            </section>
    </main>
<?php require('footer.php');
        } ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="js/main.js?v=141"></script>
<script src="js/wall.js?v=141"></script>
</body>

</html>