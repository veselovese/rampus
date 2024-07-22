<?php
session_start();

require('connect.php');
require('like-or-dislike.php');
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/wall.css">
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
            header("Location: profile");
            exit();
        } else { ?>
            <section class="wrapper main-section">
                <nav class="first-part">
                    <ul>
                        <li><a href="./profile">Профиль</a></li>
                        <li id="active"><a href="./wall">Стена</a></li>
                        <li><a href="./wall">****</a></li>
                        <li><a href="./wall">****</a></li>
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
                                <input type="hidden" required name="post-search" value="<?php if (isset($_GET['search'])) { echo $_GET['search']; }?>">
                                <button disabled class="" type="submit" id="textarea-post_sumbit"><img src="pics/SendIcon.svg"></button>
                            </form>
                        </div>
                        <div class="wall__user-posts">
                            <div>
                                <?php
                                if (!isset($_GET['search'])) {
                                    $search = 'all';
                                } else {
                                    $search = $_GET['search'];
                                }
                                $search_condition = $search !== 'all' ? "AND hashtags.name = '$search'" : '';
                                $sql_post = "SELECT posts.hashtag_id AS hashtag_id, posts.text AS post_text, DATE_FORMAT(posts.post_date, '%d %M в %k:%i') AS post_date, posts.likes AS post_likes, posts.user_id AS user_id, users.first_name AS first_name, users.second_name AS second_name, users.avatar AS avatar, posts.id AS i
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
                                        $avatar = $row_post["avatar"];
                                        $i = $row_post['i'];
                                        echo "<div class='user-post'>";
                                        echo "<div>";
                                        echo "<div class='wall__user-info'>";
                                        echo "<img class='avatar' src='uploads/avatar/" . $avatar . "'>";
                                        echo "<div>";
                                        echo "<p class='first-and-second-names'>" . $first_name . " " . $second_name . "</p>";
                                        echo "<span>" . $post_date . "</span>";
                                        echo "</div>";
                                        echo "</div>";
                                        if ($user_id == $_SESSION['user']['id']) {
                                            echo "<img onclick='showPopup($i)' src='pics/ThreeDotsIcon.svg'>";
                                            echo "<div class='three-dots-popup' id='three-dots-popup_$i'>";
                                            echo "<a class='edit-post' href='./wall'>*************</a>";
                                            echo "<a class='delete-post' href='deletepost?post=$i&source=wall'>Удалить</a>";
                                            echo "</div>";
                                        }
                                        echo "</div>";
                                        if ($hashtag_id != 0) {
                                            $hashtag_name = $connect->query("SELECT name FROM hashtags WHERE id = $hashtag_id")->fetch_assoc()['name'];
                                            echo "<p>" . $post_text . " <a href='?search=$hashtag_name'>#" . $hashtag_name . "</a></p>";
                                        } else {
                                            echo "<p>" . $post_text . "</p>";
                                        }
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
                                        echo "<div class='div-line'></div>";
                                        echo "<div class='wall__comments'>";
                                        $sql_comment = "SELECT comments.text AS comment_text, users.first_name AS first_name, users.second_name AS second_name, users.avatar AS avatar, DATE_FORMAT(comments.comment_date, '%d %M в %k:%i') AS comment_date
                                    FROM comments
                                    JOIN users ON comments.user_id = users.id
                                    JOIN posts ON comments.post_id = posts.id
                                    WHERE comments.post_id = $i
                                    ORDER BY UNIX_TIMESTAMP(comments.comment_date) ASC";
                                        $result_comment = $connect->query($sql_comment);
                                        $rows_num_comment = $result_comment->num_rows;
                                        if ($rows_num_comment > 0) {
                                            echo "<div class='other-users'>";
                                            while ($row_comment = $result_comment->fetch_assoc()) {
                                                $rows_num_comment -= 1;
                                                $first_name = $row_comment['first_name'];
                                                $second_name = $row_comment['second_name'];
                                                $avatar = $row_comment['avatar'];
                                                $comment_text = preg_replace('/\xc2\xa0/', ' ', $row_comment['comment_text']);
                                                $comment_date = $row_comment['comment_date'];
                                                echo "<div class='user-comment'>";
                                                echo "<img src='uploads/avatar/" . $avatar . "'>";
                                                echo "<div>";
                                                echo "<p class='first-and-second-names'>" . $first_name . " " . $second_name . "</p>";
                                                echo "<p class='comment-text'>" . $comment_text . "</p>";
                                                echo "<span class='date'>" . $comment_date . "</span>";
                                                echo "</div>";
                                                echo "</div>";
                                                if ($rows_num_comment > 0) {
                                                    echo "<div class='div-line'></div>";
                                                }
                                            }
                                            echo "</div>";
                                        }
                                        echo "<div class='current-user'>";
                                        echo "<img src='uploads/avatar/" . $_SESSION['user']['avatar'] . "'>";
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
<script src="js/main.js"></script>
<script src="js/wall.js"></script>
</body>

</html>