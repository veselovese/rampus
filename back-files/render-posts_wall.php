<?php
session_start();
$current_user_id = $_SESSION['user']['id'];

require_once('connect.php');
require('find-user-position-in-top.php');
require('get-user-friends-id.php');

$user_friends_id = implode(',', getUserFriendsId($current_user_id, $connect));

$filter = $_POST['filter'] === 'friends' ? "AND posts.user_id IN ($user_friends_id)" : "";
$search = $_POST['search'];
$search = $search != null ? "AND hashtags.name = '$search'" : '';

$sql_post = "SELECT posts.hashtag_id AS hashtag_id, posts.text AS post_text, DATE_FORMAT(posts.post_date, '%e %M в %k:%i') AS post_date, posts.likes AS post_likes, posts.user_id AS user_id, users.first_name AS first_name, users.second_name AS second_name, users.avatar AS avatar, posts.id AS i, users.username AS username, posts.img AS post_image
                            FROM posts
                            JOIN users ON posts.user_id = users.id
                            LEFT JOIN hashtags ON posts.hashtag_id = hashtags.id
                            WHERE posts.status = 0 $filter $search";

$result_post = $connect->query($sql_post);
if ($result_post->num_rows > 0) {
    while ($row_post = $result_post->fetch_assoc()) {
        $post_user_id = $row_post["user_id"];
        $hashtag_id = $row_post["hashtag_id"];
        $post_text = preg_replace('/\xc2\xa0/', ' ', $row_post["post_text"]);
        $post_date = $row_post["post_date"];
        $post_likes = $row_post["post_likes"];
        $post_first_name = $row_post["first_name"];
        $post_second_name = $row_post["second_name"];
        $post_username = $row_post["username"];
        $post_avatar = $row_post["avatar"];
        $post_image = $row_post["post_image"];
        $i = $row_post['i'];
        $user_in_top = findUserPositionInTop($post_user_id, $connect);
        echo "<div class='user-post' id='post-$i'>";
        echo "<div>";
        echo "<div class='wall__user-info'>";
        echo "<img class='avatar' src='uploads/avatar/thin_" . $post_avatar . "'>";
        echo "<div>";
        if ($post_username == 'rampus') {
            echo "<a href='./user/$post_username' class='first-and-second-names rampus'>" . $post_first_name . " " . $post_second_name . "</a>";
        } else {
            echo "<a href='./user/$post_username' class='first-and-second-names'>" . $post_first_name . " " . $post_second_name . "</a>";
        }
        echo "<span>" . $post_date . "</span>";
        echo "</div>";
        if ($post_username == 'rampus') {
            echo "<img src='pics/SuperUserIcon.svg'>";
        } else {
            switch ($user_in_top) {
                case 1:
                    echo "<img src='pics/BlossomFirstIcon.svg'>";
                    break;
                case 2:
                    echo "<img src='pics/BlossomSecondIcon.svg'>";
                    break;
                case 3:
                    echo "<img src='pics/BlossomThirdIcon.svg'>";
                    break;
            }
        }
        echo "</div>";
        echo "<div class='div-show-three-dots-popup' onclick='showPopup($i)' id='div-show-three-dots-popup_$i'>";
        echo "<img src='pics/ThreeDotsIcon.svg' class='show-three-dots-popup'>";
        echo "</div>";
        echo "<div class='three-dots-popup' id='three-dots-popup_$i'>";
        if ($post_user_id == $current_user_id) {
            echo "<span class='three-dots-popup-li edit' onclick='editPost($i)'>Редактировать</span>";
        }
        echo "<span class='three-dots-popup-li copy-link' onclick='copyLinkToPost($i)'>Копировать ссылку</span>";
        echo "<a class='three-dots-popup-li open-profile' href='./user/$post_username'>Открыть профиль</a>";
        if ($post_user_id == $current_user_id) {
            echo "<a class='three-dots-popup-li delete-post' href='back-files/delete-post?post=$i&source=wall'>Удалить</a>";
        }
        echo "</div>";
        echo "</div>";
        if ($hashtag_id != 0) {
            $hashtag_name = $connect->query("SELECT name FROM hashtags WHERE id = $hashtag_id")->fetch_assoc()['name'];
            echo "<p class='main-text'>" . $post_text . " <a href='?search=$hashtag_name'>#" . $hashtag_name . "</a></p>";
        } else {
            echo "<p class='main-text'>" . $post_text . "</p>";
        }
        // echo "<form action='./back-files/edit-post' method='POST' autocomplete='off'>
        //                     <input type='text' required name='edit-post' id='edit-post_$i' value='$post_text'>
        //                     <input type='hidden' required name='post-source' value='source-wall'>
        //                     <input type='file' name='edit-post-image' id='post-image_$i' value='$post_image'>
        //                     <button type='submit'>Сохранить</button>
        //                 </form>";
        if ($post_image != null) {
            echo "<div class='image-in-post-div'>";
            echo "<img class='image-in-post-hide' src=./uploads/post-image/small_" . $post_image . ">";
            echo "<img class='image-in-post' src=./uploads/post-image/small_" . $post_image . ">";
            echo "</div>";
        }
        echo "<div class='post-buttons'>";
        $sql_comment = "SELECT comments.text AS comment_text, users.first_name AS first_name, users.second_name AS second_name, users.avatar AS avatar, DATE_FORMAT(comments.comment_date, '%e %M в %k:%i') AS comment_date, users.id AS comment_user_id, users.username AS comment_username, comments.id AS comment_id
                                        FROM comments
                                        JOIN users ON comments.user_id = users.id
                                        JOIN posts ON comments.post_id = posts.id
                                        WHERE comments.post_id = $i
                                        ORDER BY UNIX_TIMESTAMP(comments.comment_date) ASC";
        $result_comment = $connect->query($sql_comment);
        $rows_num_comment = $result_comment->num_rows;
        $sql_like = "SELECT * FROM likes_on_posts WHERE post_id = $i AND user_id = " . $current_user_id;
        $result_like = $connect->query($sql_like);
        if ($result_like->num_rows > 0) {
            echo "<button id='$i' class='like-button liked'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                                    <path d='M21.3345 8.71342C21.0727 9.33749 20.6942 9.9081 20.2183 10.3954L20.148 10.4648L13.6656 16.8654C12.4711 18.0449 10.5278 18.0449 9.33329 16.8654L9.33326 16.8654L2.85134 10.4657C2.34261 9.96338 1.93992 9.36791 1.66547 8.7137L0.7102 9.11444L1.66547 8.71369C1.39104 8.05952 1.25 7.35894 1.25 6.65182C1.25 5.94469 1.39104 5.24411 1.66547 4.58994C1.93992 3.93572 2.34261 3.34025 2.85134 2.83794C3.36011 2.33559 3.96496 1.93628 4.63177 1.66356C5.29861 1.39084 6.01386 1.25027 6.73655 1.25027C7.45924 1.25027 8.17449 1.39084 8.84133 1.66356C9.50754 1.93603 10.1119 2.33487 10.6204 2.83658L11.4989 3.70337L11.4998 3.7025L11.5009 3.7036L12.3791 2.83686C12.8877 2.33489 13.4922 1.93586 14.1587 1.66329C14.8255 1.39057 15.5408 1.25 16.2634 1.25C16.9861 1.25 17.7014 1.39057 18.3682 1.66329C19.035 1.93601 19.6399 2.33532 20.1487 2.83767C20.6574 3.33999 21.0601 3.93546 21.3345 4.58967C21.609 5.24384 21.75 5.94442 21.75 6.65155C21.75 7.35867 21.609 8.05925 21.3345 8.71342Z' />
                                                </svg>";
            echo "<span class='like-counter'>" . $post_likes . "</span></button>";
            if ($post_likes == 1) {
                echo "<button id='$i' class='like-button unliked hide'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                                    <path d='M21.3345 8.71342C21.0727 9.33749 20.6942 9.9081 20.2183 10.3954L20.148 10.4648L13.6656 16.8654C12.4711 18.0449 10.5278 18.0449 9.33329 16.8654L9.33326 16.8654L2.85134 10.4657C2.34261 9.96338 1.93992 9.36791 1.66547 8.7137L0.7102 9.11444L1.66547 8.71369C1.39104 8.05952 1.25 7.35894 1.25 6.65182C1.25 5.94469 1.39104 5.24411 1.66547 4.58994C1.93992 3.93572 2.34261 3.34025 2.85134 2.83794C3.36011 2.33559 3.96496 1.93628 4.63177 1.66356C5.29861 1.39084 6.01386 1.25027 6.73655 1.25027C7.45924 1.25027 8.17449 1.39084 8.84133 1.66356C9.50754 1.93603 10.1119 2.33487 10.6204 2.83658L11.4989 3.70337L11.4998 3.7025L11.5009 3.7036L12.3791 2.83686C12.8877 2.33489 13.4922 1.93586 14.1587 1.66329C14.8255 1.39057 15.5408 1.25 16.2634 1.25C16.9861 1.25 17.7014 1.39057 18.3682 1.66329C19.035 1.93601 19.6399 2.33532 20.1487 2.83767C20.6574 3.33999 21.0601 3.93546 21.3345 4.58967C21.609 5.24384 21.75 5.94442 21.75 6.65155C21.75 7.35867 21.609 8.05925 21.3345 8.71342Z' />
                                                </svg>";
            } else {
                echo "<button id='$i' class='like-button unliked hide'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                                    <path d='M21.3345 8.71342C21.0727 9.33749 20.6942 9.9081 20.2183 10.3954L20.148 10.4648L13.6656 16.8654C12.4711 18.0449 10.5278 18.0449 9.33329 16.8654L9.33326 16.8654L2.85134 10.4657C2.34261 9.96338 1.93992 9.36791 1.66547 8.7137L0.7102 9.11444L1.66547 8.71369C1.39104 8.05952 1.25 7.35894 1.25 6.65182C1.25 5.94469 1.39104 5.24411 1.66547 4.58994C1.93992 3.93572 2.34261 3.34025 2.85134 2.83794C3.36011 2.33559 3.96496 1.93628 4.63177 1.66356C5.29861 1.39084 6.01386 1.25027 6.73655 1.25027C7.45924 1.25027 8.17449 1.39084 8.84133 1.66356C9.50754 1.93603 10.1119 2.33487 10.6204 2.83658L11.4989 3.70337L11.4998 3.7025L11.5009 3.7036L12.3791 2.83686C12.8877 2.33489 13.4922 1.93586 14.1587 1.66329C14.8255 1.39057 15.5408 1.25 16.2634 1.25C16.9861 1.25 17.7014 1.39057 18.3682 1.66329C19.035 1.93601 19.6399 2.33532 20.1487 2.83767C20.6574 3.33999 21.0601 3.93546 21.3345 4.58967C21.609 5.24384 21.75 5.94442 21.75 6.65155C21.75 7.35867 21.609 8.05925 21.3345 8.71342Z' />
                                                </svg>";
                echo "<span class='like-counter'>" . $post_likes . "</span></button>";
            }
        } else {
            if ($post_likes == 0) {
                echo "<button id='$i' class='like-button unliked'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                                    <path d='M21.3345 8.71342C21.0727 9.33749 20.6942 9.9081 20.2183 10.3954L20.148 10.4648L13.6656 16.8654C12.4711 18.0449 10.5278 18.0449 9.33329 16.8654L9.33326 16.8654L2.85134 10.4657C2.34261 9.96338 1.93992 9.36791 1.66547 8.7137L0.7102 9.11444L1.66547 8.71369C1.39104 8.05952 1.25 7.35894 1.25 6.65182C1.25 5.94469 1.39104 5.24411 1.66547 4.58994C1.93992 3.93572 2.34261 3.34025 2.85134 2.83794C3.36011 2.33559 3.96496 1.93628 4.63177 1.66356C5.29861 1.39084 6.01386 1.25027 6.73655 1.25027C7.45924 1.25027 8.17449 1.39084 8.84133 1.66356C9.50754 1.93603 10.1119 2.33487 10.6204 2.83658L11.4989 3.70337L11.4998 3.7025L11.5009 3.7036L12.3791 2.83686C12.8877 2.33489 13.4922 1.93586 14.1587 1.66329C14.8255 1.39057 15.5408 1.25 16.2634 1.25C16.9861 1.25 17.7014 1.39057 18.3682 1.66329C19.035 1.93601 19.6399 2.33532 20.1487 2.83767C20.6574 3.33999 21.0601 3.93546 21.3345 4.58967C21.609 5.24384 21.75 5.94442 21.75 6.65155C21.75 7.35867 21.609 8.05925 21.3345 8.71342Z' />
                                                </svg>";
            } else {
                echo "<button id='$i' class='like-button unliked'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                                    <path d='M21.3345 8.71342C21.0727 9.33749 20.6942 9.9081 20.2183 10.3954L20.148 10.4648L13.6656 16.8654C12.4711 18.0449 10.5278 18.0449 9.33329 16.8654L9.33326 16.8654L2.85134 10.4657C2.34261 9.96338 1.93992 9.36791 1.66547 8.7137L0.7102 9.11444L1.66547 8.71369C1.39104 8.05952 1.25 7.35894 1.25 6.65182C1.25 5.94469 1.39104 5.24411 1.66547 4.58994C1.93992 3.93572 2.34261 3.34025 2.85134 2.83794C3.36011 2.33559 3.96496 1.93628 4.63177 1.66356C5.29861 1.39084 6.01386 1.25027 6.73655 1.25027C7.45924 1.25027 8.17449 1.39084 8.84133 1.66356C9.50754 1.93603 10.1119 2.33487 10.6204 2.83658L11.4989 3.70337L11.4998 3.7025L11.5009 3.7036L12.3791 2.83686C12.8877 2.33489 13.4922 1.93586 14.1587 1.66329C14.8255 1.39057 15.5408 1.25 16.2634 1.25C16.9861 1.25 17.7014 1.39057 18.3682 1.66329C19.035 1.93601 19.6399 2.33532 20.1487 2.83767C20.6574 3.33999 21.0601 3.93546 21.3345 4.58967C21.609 5.24384 21.75 5.94442 21.75 6.65155C21.75 7.35867 21.609 8.05925 21.3345 8.71342Z' />
                                                </svg>";
                echo "<span class='like-counter'>" . $post_likes . "</span></button>";
            }
            echo "<button id='$i' class='like-button liked hide'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                                <path fill-rule='evenodd' clip-rule='evenodd' d='M15.3643 17.1232L21.0488 11.4387L21.0494 11.4394C21.6548 10.834 22.135 10.1153 22.4626 9.32432C22.7903 8.53335 22.9589 7.6856 22.9589 6.82947C22.9589 5.97334 22.7903 5.12559 22.4626 4.33463C22.135 3.54366 21.6548 2.82498 21.0494 2.2196C20.4441 1.61423 19.7254 1.13402 18.9344 0.806393C18.1434 0.478767 17.2957 0.310142 16.4396 0.310145C15.5834 0.310148 14.7357 0.478778 13.9447 0.806409C13.1541 1.13391 12.4356 1.61388 11.8304 2.21893L11.8289 2.21742L11.8279 2.21836C11.2229 1.61375 10.5048 1.1341 9.71455 0.806772C8.92359 0.479147 8.07584 0.310521 7.2197 0.310524C6.36357 0.310526 5.51582 0.479157 4.72486 0.806787C3.93389 1.13442 3.2152 1.61463 2.60982 2.22001C2.00444 2.82539 1.52423 3.54408 1.1966 4.33504C0.868969 5.12601 0.700339 5.97376 0.700335 6.82989C0.700332 7.68602 0.868959 8.53377 1.19658 9.32474C1.52421 10.1157 2.00442 10.8344 2.60979 11.4398L2.60985 11.4397L8.29331 17.1232C10.2459 19.0758 13.4117 19.0758 15.3643 17.1232Z'/>
                                            </svg>";
            echo "<span class='like-counter'>" . $post_likes . "</span></button>";
        }
        if ($rows_num_comment == 0) {
            echo "<button onclick='commentButtonClick($i)' class='comment-button comment'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                                <path d='M4 1.25L19 1.25C20.5188 1.25 21.75 2.48122 21.75 4L21.75 17.75L4 17.75C2.48122 17.75 1.25 16.5188 1.25 15L1.25 4C1.25 2.48122 2.48122 1.25 4 1.25Z' />
                                            </svg>";
        } else {
            echo "<button onclick='commentButtonClick($i)' class='comment-button comment'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                                <path d='M4 1.25L19 1.25C20.5188 1.25 21.75 2.48122 21.75 4L21.75 17.75L4 17.75C2.48122 17.75 1.25 16.5188 1.25 15L1.25 4C1.25 2.48122 2.48122 1.25 4 1.25Z' />
                                            </svg>";
            echo "<span class='comment-counter'>" . $rows_num_comment . "</span></button>";
        }
        echo "</div>";
        echo "<div class='div-line'></div>";
        echo "<div class='wall__comments'>";
        if ($rows_num_comment > 0) {
            echo "<div class='other-users'>";
            $comment_count = 0;
            while ($row_comment = $result_comment->fetch_assoc()) {
                $comment_id = $row_comment['comment_id'];
                $comment_user_id = $row_comment['comment_user_id'];
                $comment_username = $row_comment['comment_username'];
                $comment_first_name = $row_comment['first_name'];
                $comment_second_name = $row_comment['second_name'];
                $comment_avatar = $row_comment['avatar'];
                $comment_text = preg_replace('/\xc2\xa0/', ' ', $row_comment['comment_text']);
                $comment_date = $row_comment['comment_date'];
                if ($comment_count < 2) {
                    if ($rows_num_comment < $result_comment->num_rows) {
                        echo "<div class='div-line'></div>";
                    }
                    echo "<div class='user-comment'>";
                    echo "<img src='uploads/avatar/thin_" . $comment_avatar . "'>";
                    echo "<div class='comment-div'>";
                    if ($comment_username == 'rampus') {
                        echo "<div><a href='./user/$comment_username' class='first-and-second-names rampus'>" . $comment_first_name . " " . $comment_second_name . "</a><span class='date'>" . $comment_date . "</span>";
                        if ($comment_user_id == $current_user_id) {
                            echo "<a class='delete-comment' href='back-files/delete-comment?comment=$comment_id'><svg width='10' height='10' viewBox='0 0 10 10' fill='none' xmlns='http://www.w3.org/2000/svg'>
<path d='M0.191016 8.88671C-0.0636719 9.14141 -0.0636719 9.55428 0.191016 9.80898C0.445703 10.0637 0.858643 10.0637 1.11333 9.80898L0.191016 8.88671ZM5.46114 5.46114C5.71584 5.20644 5.71584 4.79357 5.46114 4.53888C5.20644 4.28418 4.79357 4.28418 4.53888 4.53888L5.46114 5.46114ZM4.53888 4.53888C4.28418 4.79357 4.28418 5.20644 4.53888 5.46114C4.79357 5.71584 5.20644 5.71584 5.46114 5.46114L4.53888 4.53888ZM9.80898 1.11333C10.0637 0.858644 10.0637 0.445703 9.80898 0.191016C9.55428 -0.0636719 9.14141 -0.0636719 8.88671 0.191016L9.80898 1.11333ZM5.46114 4.53888C5.20644 4.28418 4.79357 4.28418 4.53888 4.53888C4.28418 4.79357 4.28418 5.20644 4.53888 5.46114L5.46114 4.53888ZM8.88671 9.80898C9.14141 10.0637 9.55428 10.0637 9.80898 9.80898C10.0637 9.55428 10.0637 9.14141 9.80898 8.88671L8.88671 9.80898ZM4.53888 5.46114C4.79357 5.71584 5.20644 5.71584 5.46114 5.46114C5.71584 5.20644 5.71584 4.79357 5.46114 4.53888L4.53888 5.46114ZM1.11333 0.191016C0.858643 -0.0636719 0.445703 -0.0636719 0.191016 0.191016C-0.0636719 0.445703 -0.0636719 0.858644 0.191016 1.11333L1.11333 0.191016ZM1.11333 9.80898L5.46114 5.46114L4.53888 4.53888L0.191016 8.88671L1.11333 9.80898ZM5.46114 5.46114L9.80898 1.11333L8.88671 0.191016L4.53888 4.53888L5.46114 5.46114ZM4.53888 5.46114L8.88671 9.80898L9.80898 8.88671L5.46114 4.53888L4.53888 5.46114ZM5.46114 4.53888L1.11333 0.191016L0.191016 1.11333L4.53888 5.46114L5.46114 4.53888Z' />
</svg></a>";
                        }
                        echo "</div>";
                    } else {
                        echo "<div><a href='./user/$comment_username' class='first-and-second-names'>" . $comment_first_name . " " . $comment_second_name . "</a><span class='date'>" . $comment_date . "</span>";
                        if ($comment_user_id == $current_user_id) {
                            echo "<a class='delete-comment' href='back-files/delete-comment?comment=$comment_id'><svg width='10' height='10' viewBox='0 0 10 10' fill='none' xmlns='http://www.w3.org/2000/svg'>
<path d='M0.191016 8.88671C-0.0636719 9.14141 -0.0636719 9.55428 0.191016 9.80898C0.445703 10.0637 0.858643 10.0637 1.11333 9.80898L0.191016 8.88671ZM5.46114 5.46114C5.71584 5.20644 5.71584 4.79357 5.46114 4.53888C5.20644 4.28418 4.79357 4.28418 4.53888 4.53888L5.46114 5.46114ZM4.53888 4.53888C4.28418 4.79357 4.28418 5.20644 4.53888 5.46114C4.79357 5.71584 5.20644 5.71584 5.46114 5.46114L4.53888 4.53888ZM9.80898 1.11333C10.0637 0.858644 10.0637 0.445703 9.80898 0.191016C9.55428 -0.0636719 9.14141 -0.0636719 8.88671 0.191016L9.80898 1.11333ZM5.46114 4.53888C5.20644 4.28418 4.79357 4.28418 4.53888 4.53888C4.28418 4.79357 4.28418 5.20644 4.53888 5.46114L5.46114 4.53888ZM8.88671 9.80898C9.14141 10.0637 9.55428 10.0637 9.80898 9.80898C10.0637 9.55428 10.0637 9.14141 9.80898 8.88671L8.88671 9.80898ZM4.53888 5.46114C4.79357 5.71584 5.20644 5.71584 5.46114 5.46114C5.71584 5.20644 5.71584 4.79357 5.46114 4.53888L4.53888 5.46114ZM1.11333 0.191016C0.858643 -0.0636719 0.445703 -0.0636719 0.191016 0.191016C-0.0636719 0.445703 -0.0636719 0.858644 0.191016 1.11333L1.11333 0.191016ZM1.11333 9.80898L5.46114 5.46114L4.53888 4.53888L0.191016 8.88671L1.11333 9.80898ZM5.46114 5.46114L9.80898 1.11333L8.88671 0.191016L4.53888 4.53888L5.46114 5.46114ZM4.53888 5.46114L8.88671 9.80898L9.80898 8.88671L5.46114 4.53888L4.53888 5.46114ZM5.46114 4.53888L1.11333 0.191016L0.191016 1.11333L4.53888 5.46114L5.46114 4.53888Z' />
</svg></a>";
                        }
                        echo "</div>";
                    }
                    echo "<p class='comment-text main-text'>" . $comment_text . "</p>";
                    echo "<span class='date mobile'>" . $comment_date . "</span>";
                    echo "</div>";
                    echo "</div>";
                } else {
                    if ($rows_num_comment < $result_comment->num_rows) {
                        echo "<div class='div-line hide comment_div-line_$i'></div>";
                    }
                    echo "<div class='user-comment hide comment_user-comment_$i'>";
                    echo "<img src='uploads/avatar/thin_" . $comment_avatar . "'>";
                    echo "<div class='comment-div'>";
                    if ($comment_username == 'rampus') {
                        echo "<div><a href='./user/$comment_username' class='first-and-second-names rampus'>" . $comment_first_name . " " . $comment_second_name . "</a><span class='date'>" . $comment_date . "</span>";
                        if ($comment_user_id == $current_user_id) {
                            echo "<a class='delete-comment' href='back-files/delete-comment?comment=$comment_id'><svg width='10' height='10' viewBox='0 0 10 10' fill='none' xmlns='http://www.w3.org/2000/svg'>
<path d='M0.191016 8.88671C-0.0636719 9.14141 -0.0636719 9.55428 0.191016 9.80898C0.445703 10.0637 0.858643 10.0637 1.11333 9.80898L0.191016 8.88671ZM5.46114 5.46114C5.71584 5.20644 5.71584 4.79357 5.46114 4.53888C5.20644 4.28418 4.79357 4.28418 4.53888 4.53888L5.46114 5.46114ZM4.53888 4.53888C4.28418 4.79357 4.28418 5.20644 4.53888 5.46114C4.79357 5.71584 5.20644 5.71584 5.46114 5.46114L4.53888 4.53888ZM9.80898 1.11333C10.0637 0.858644 10.0637 0.445703 9.80898 0.191016C9.55428 -0.0636719 9.14141 -0.0636719 8.88671 0.191016L9.80898 1.11333ZM5.46114 4.53888C5.20644 4.28418 4.79357 4.28418 4.53888 4.53888C4.28418 4.79357 4.28418 5.20644 4.53888 5.46114L5.46114 4.53888ZM8.88671 9.80898C9.14141 10.0637 9.55428 10.0637 9.80898 9.80898C10.0637 9.55428 10.0637 9.14141 9.80898 8.88671L8.88671 9.80898ZM4.53888 5.46114C4.79357 5.71584 5.20644 5.71584 5.46114 5.46114C5.71584 5.20644 5.71584 4.79357 5.46114 4.53888L4.53888 5.46114ZM1.11333 0.191016C0.858643 -0.0636719 0.445703 -0.0636719 0.191016 0.191016C-0.0636719 0.445703 -0.0636719 0.858644 0.191016 1.11333L1.11333 0.191016ZM1.11333 9.80898L5.46114 5.46114L4.53888 4.53888L0.191016 8.88671L1.11333 9.80898ZM5.46114 5.46114L9.80898 1.11333L8.88671 0.191016L4.53888 4.53888L5.46114 5.46114ZM4.53888 5.46114L8.88671 9.80898L9.80898 8.88671L5.46114 4.53888L4.53888 5.46114ZM5.46114 4.53888L1.11333 0.191016L0.191016 1.11333L4.53888 5.46114L5.46114 4.53888Z' />
</svg></a>";
                        }
                        echo "</div>";
                    } else {
                        echo "<div><a href='./user/$comment_username' class='first-and-second-names'>" . $comment_first_name . " " . $comment_second_name . "</a><span class='date'>" . $comment_date . "</span>";
                        if ($comment_user_id == $current_user_id) {
                            echo "<a class='delete-comment' href='back-files/delete-comment?comment=$comment_id'><svg width='10' height='10' viewBox='0 0 10 10' fill='none' xmlns='http://www.w3.org/2000/svg'>
<path d='M0.191016 8.88671C-0.0636719 9.14141 -0.0636719 9.55428 0.191016 9.80898C0.445703 10.0637 0.858643 10.0637 1.11333 9.80898L0.191016 8.88671ZM5.46114 5.46114C5.71584 5.20644 5.71584 4.79357 5.46114 4.53888C5.20644 4.28418 4.79357 4.28418 4.53888 4.53888L5.46114 5.46114ZM4.53888 4.53888C4.28418 4.79357 4.28418 5.20644 4.53888 5.46114C4.79357 5.71584 5.20644 5.71584 5.46114 5.46114L4.53888 4.53888ZM9.80898 1.11333C10.0637 0.858644 10.0637 0.445703 9.80898 0.191016C9.55428 -0.0636719 9.14141 -0.0636719 8.88671 0.191016L9.80898 1.11333ZM5.46114 4.53888C5.20644 4.28418 4.79357 4.28418 4.53888 4.53888C4.28418 4.79357 4.28418 5.20644 4.53888 5.46114L5.46114 4.53888ZM8.88671 9.80898C9.14141 10.0637 9.55428 10.0637 9.80898 9.80898C10.0637 9.55428 10.0637 9.14141 9.80898 8.88671L8.88671 9.80898ZM4.53888 5.46114C4.79357 5.71584 5.20644 5.71584 5.46114 5.46114C5.71584 5.20644 5.71584 4.79357 5.46114 4.53888L4.53888 5.46114ZM1.11333 0.191016C0.858643 -0.0636719 0.445703 -0.0636719 0.191016 0.191016C-0.0636719 0.445703 -0.0636719 0.858644 0.191016 1.11333L1.11333 0.191016ZM1.11333 9.80898L5.46114 5.46114L4.53888 4.53888L0.191016 8.88671L1.11333 9.80898ZM5.46114 5.46114L9.80898 1.11333L8.88671 0.191016L4.53888 4.53888L5.46114 5.46114ZM4.53888 5.46114L8.88671 9.80898L9.80898 8.88671L5.46114 4.53888L4.53888 5.46114ZM5.46114 4.53888L1.11333 0.191016L0.191016 1.11333L4.53888 5.46114L5.46114 4.53888Z' />
</svg></a>";
                        }
                        echo "</div>";
                    }
                    echo "<p class='comment-text main-text'>" . $comment_text . "</p>";
                    echo "<span class='date mobile'>" . $comment_date . "</span>";
                    echo "</div>";
                    echo "</div>";
                }
                $comment_count++;
                $rows_num_comment--;
            }
            if ($result_comment->num_rows > 2) {
                echo "<p class='see-all-comments' onclick='seeAllComments($i)' id='see-all-comments_$i'>Показать все комментарии</p>";
            }
            echo "</div>";
        }
        echo "<div class='current-user'>";
        echo "<form action='./back-files/comment' method='post' autocomplete='off'>
                                        <div contenteditable='true' class='textarea-comment' id='textarea-comment_$i' role='textbox' onkeyup='textareaComment(event, $i)' onkeydown='textareaCommentPlaceholder(event, $i)'></div>
                                        <label for='textarea-comment' class='textarea-comment_label' id='textarea-comment_label_$i'>Ответить..</label>
                                        <input type='hidden' required name='comment' class='textarea-comment_input' id='textarea-comment_input_$i' value=''>
                                        <input type='hidden' name='comment_id' value='$i'>
                                        <button type='submit' id='textarea-comment_submit_$i' class='' disabled>
                                        <svg width='28' height='28' viewBox='0 0 28 28' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                            <path fill-rule='evenodd' clip-rule='evenodd' d='M0 14C0 6.26801 6.26801 0 14 0C21.7319 0 28 6.26801 28 14C28 21.7319 21.7319 28 14 28C6.26801 28 0 21.7319 0 14ZM12.6 19.6C12.6 20.3732 13.2268 21 14 21C14.7732 21 15.4 20.3732 15.4 19.6V11.7799L17.2101 13.5899C17.7568 14.1366 18.6432 14.1366 19.1899 13.5899C19.7366 13.0432 19.7366 12.1568 19.1899 11.6101L15.1117 7.5319C15.0907 7.5108 15.0692 7.49043 15.0472 7.47078C14.7907 7.18197 14.4166 7 14 7C13.5834 7 13.2093 7.18197 12.9528 7.47078C12.9308 7.49042 12.9093 7.5108 12.8883 7.5319L8.81005 11.6101C8.26332 12.1568 8.26332 13.0432 8.81005 13.5899C9.35679 14.1366 10.2432 14.1366 10.79 13.5899L12.6 11.7799V19.6Z' />
                                        </svg>
                                        </button>
                                    </form>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }
} else {
    echo "<p class='no-found'>Постов не найдено</p>";
}
