<?php
session_start();
$current_user_id = $_SESSION['user']['id'];
date_default_timezone_set('Europe/Moscow');
$today = date('Y-m-d', time());
$yesterday = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));
$beforeyesterday = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d") - 2, date("Y")));
$month_list = array(1 => 'января', 2 => 'февраля', 3 => 'марта', 4 => 'апреля', 5 => 'мая', 6 => 'июня', 7 => 'июля', 8 => 'августа', 9 => 'сентября', 10 => 'октября', 11 => 'ноября', 12 => 'декабря');

require_once('connect.php');
require_once('reposts/check-repost-status.php');
require('get-user-friends-id.php');

$user_friends_id = implode(',', getUserFriendsId($current_user_id, $connect));
$filter = $_POST['filter'] === 'reposts' ? "AND posts.repost_user_id" : "AND posts.repost_user_id IS NULL";

$sql_post = "SELECT 
    posts.id AS content_id,
    posts.hashtag_id AS hashtag_id,
    posts.text AS content_text,
    posts.content_date AS content_date,
    posts.likes AS content_likes,
    posts.reposts AS content_reposts,
    posts.user_id AS author_id,
    users.first_name AS author_first_name,
    users.second_name AS author_second_name,
    users.avatar AS author_avatar,
    users.username AS author_username,
    posts.img AS content_image,
    posts.for_friends AS for_friends,

    posts.repost_user_id AS repost_author_id,
    posts.repost_post_id AS repost_post_id,
    repost_users.first_name AS repost_author_first_name,
    repost_users.second_name AS repost_author_second_name,
    repost_users.username AS repost_author_username,
    repost_users.avatar AS repost_author_avatar

FROM posts
JOIN users ON posts.user_id = users.id
LEFT JOIN users AS repost_users ON posts.repost_user_id = repost_users.id
LEFT JOIN hashtags ON posts.hashtag_id = hashtags.id
WHERE posts.status = 0 AND posts.user_id = $current_user_id $filter

ORDER BY content_date DESC";

$result_post = $connect->query($sql_post);
$posts_counter = 0;
if ($result_post->num_rows > 0) {
    while ($row_post = $result_post->fetch_assoc()) {
        $content_type = $row_post["repost_author_id"] ? 'repost' : 'post';
        $content_author_id = $row_post["author_id"];
        $content_repost_author_id = $row_post["repost_author_id"];
        $for_friends = $row_post["for_friends"];
        $hashtag_id = $row_post["hashtag_id"];
        $content_text = preg_replace('/\xc2\xa0/', ' ', $row_post["content_text"]);
        $content_text = preg_replace('/#(\w+)\s*/u', '<a href="./wall?search=$1">#$1</a> ', $content_text);
        preg_match_all('/@(\w+)/u', $content_text, $matches);
        $tags = $matches[1];
        foreach ($tags as $tag) {
            $tag = $connect->real_escape_string($tag);
            $result = $connect->query("SELECT 1 FROM users WHERE username = '$tag'");
            if ($result && $result->num_rows > 0) {
                $pattern = '/' . preg_quote('@' . $tag, '/') . '/u';
                $replacement = '<a href="./user/' . htmlspecialchars($tag) . '">@' . htmlspecialchars($tag) . '</a>';
                $content_text = preg_replace($pattern, $replacement, $content_text);
            }
        }
        $content_date = $row_post["content_date"];
        $content_date_db = date_format(date_create($content_date), 'Y-m-d');
        switch ($content_date_db) {
            case $today:
                $content_date = date_format(date_create($content_date), 'сегодня в G:i');
                break;
            case $yesterday:
                $content_date = date_format(date_create($content_date), 'вчера в G:i');
                break;
            case $beforeyesterday:
                $content_date = date_format(date_create($content_date), 'позавчера в G:i');
                break;
            default:
                $content_date = date_format(date_create($content_date), 'j ') . $month_list[date_format(date_create($content_date), 'n')] . date_format(date_create($content_date), ' в G:i');
                break;
        }
        $content_likes = $row_post["content_likes"];
        $content_reposts = $row_post["content_reposts"];
        $content_username = $row_post["author_username"];
        $content_first_name = $row_post["author_first_name"];
        $content_second_name = $row_post["author_second_name"];
        $content_avatar = $row_post["author_avatar"];
        $content_repost_first_name = $row_post["repost_author_first_name"];
        $content_repost_second_name = $row_post["repost_author_second_name"];
        $content_repost_username = $row_post["repost_author_username"];
        $content_repost_avatar = $row_post["repost_author_avatar"];
        $content_image = $row_post["content_image"];
        $content_id = $row_post['content_id'];
        $content_repost_id = $row_post['repost_post_id'];
        $check_repost_status = $content_type == 'repost' ? haveIMakeRepost($content_repost_id) : false;
        if ($posts_counter < 3) {
            echo "<div class='user-post' id='post-$content_id'>";
            echo "<div class='extra-post-info'>";
            echo $content_type == 'repost' ? "<a href='./post/$content_repost_id' class='repost-link-profile'>Репост  <img src='uploads/avatar/thin_$content_repost_avatar' class='repost_avatar'>@$content_repost_username</a>" : "";
            echo $for_friends ? "<div class='for-friends'><svg width='28' height='31' viewBox='0 0 28 31' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                            <path d='M25 19.6055C25 18.278 24.9991 17.3577 24.9404 16.6426C24.883 15.9425 24.7759 15.5499 24.626 15.2568V15.2559C24.2972 14.6137 23.7721 14.0909 23.125 13.7627C22.7942 13.595 22.343 13.4837 21.4863 13.4316C20.7964 13.3897 19.9321 13.3887 18.75 13.3887H8.75C7.56785 13.3887 6.70365 13.3897 6.01367 13.4316C5.15689 13.4837 4.70568 13.595 4.375 13.7627C3.72775 14.091 3.2028 14.6144 2.87402 15.2568C2.72409 15.5499 2.61704 15.9425 2.55957 16.6426C2.50088 17.3577 2.5 18.278 2.5 19.6055V21.7832C2.5 23.1107 2.50088 24.0309 2.55957 24.7461C2.61705 25.4465 2.72401 25.8397 2.87402 26.1328C3.20282 26.7751 3.72786 27.2987 4.375 27.627C4.67121 27.7772 5.06793 27.8831 5.77246 27.9404C6.49167 27.9989 7.41684 28 8.75 28H18.75C20.0832 28 21.0083 27.9989 21.7275 27.9404C22.432 27.8831 22.8287 27.7772 23.125 27.627C23.7721 27.2987 24.2972 26.7751 24.626 26.1328C24.776 25.8397 24.8829 25.4465 24.9404 24.7461C24.9991 24.0309 25 23.1107 25 21.7832V19.6055ZM20.3125 9.02734C20.3123 5.4276 17.3794 2.5 13.75 2.5C10.1205 2.5 7.18774 5.4276 7.1875 9.02734V10.8936C7.662 10.888 8.18153 10.8887 8.75 10.8887H18.75C19.3185 10.8887 19.838 10.888 20.3125 10.8936V9.02734ZM22.8125 11.0684C23.3248 11.1602 23.8023 11.3032 24.2559 11.5332C25.3726 12.0996 26.2816 13.0035 26.8516 14.1172C27.2125 14.8224 27.3614 15.5825 27.4316 16.4385C27.5007 17.2796 27.5 18.3195 27.5 19.6055V21.7832C27.5 23.0691 27.5006 24.1091 27.4316 24.9502C27.3614 25.8061 27.2125 26.5663 26.8516 27.2715C26.2816 28.3852 25.3726 29.29 24.2559 29.8564C23.5495 30.2147 22.7881 30.3628 21.9297 30.4326C21.0857 30.5012 20.0419 30.5 18.75 30.5H8.75C7.45813 30.5 6.41432 30.5012 5.57031 30.4326C4.71193 30.3628 3.9505 30.2147 3.24414 29.8564C2.19707 29.3253 1.33243 28.4969 0.758789 27.4775L0.648438 27.2715C0.287543 26.5663 0.138605 25.8061 0.0683596 24.9502C-0.000638187 24.1091 2.29428e-07 23.0691 2.29428e-07 21.7832V19.6055C1.85029e-07 18.3195 -0.000654526 17.2796 0.0683596 16.4385C0.138605 15.5825 0.287543 14.8224 0.648438 14.1172C1.21847 13.0035 2.12749 12.0996 3.24414 11.5332C3.69773 11.3031 4.17518 11.1602 4.6875 11.0684V9.02734C4.68774 4.0364 8.75033 0 13.75 0C18.7496 0 22.8123 4.0364 22.8125 9.02734V11.0684Z' />
                                        </svg>
                <span class='for-friends'>Для друзей</span></div>" : "";
            echo "<div class='div-show-three-dots-popup in-profile' onclick='showPopup($content_id)' id='div-show-three-dots-popup_$content_id'>";
            echo "<img src='pics/ThreeDotsIcon.svg' class='show-three-dots-popup'>";
            echo "</div>";
            echo "</div>";
            if ($hashtag_id != 0) {
                $hashtag_name = $connect->query("SELECT name FROM hashtags WHERE id = $hashtag_id")->fetch_assoc()['name'];
                echo "<p>" . $content_text . " <a href='./wall?search=$hashtag_name'>#" . $hashtag_name . "</a></p>";
            } else {
                echo "<p>" . $content_text . "</p>";
            }
            if ($content_image != null) {
                echo "<div class='image-in-post-div'>";
                echo "<img class='image-in-post-hide' src=./uploads/post-image/small_" . $content_image . ">";
                echo "<img class='image-in-post' src=./uploads/post-image/small_" . $content_image . ">";
                echo "</div>";
            }
            echo "<div class='buttons-and-date'>";
            echo "<div class='post-buttons'>";
            $sql_comment = "SELECT comments.text AS comment_text
                                        FROM comments
                                        JOIN posts ON comments.post_id = posts.id
                                        WHERE comments.post_id = $content_id
                                        ORDER BY UNIX_TIMESTAMP(comments.comment_date) ASC";
            $result_comment = $connect->query($sql_comment);
            $rows_num_comment = $result_comment->num_rows;
            $sql_like = "SELECT * FROM likes_on_posts WHERE post_id = $content_id AND user_id = " . $current_user_id;
            $sql_repost = "SELECT * FROM reposts WHERE post_id = $content_id AND user_id = " . $current_user_id;
            $result_like = $connect->query($sql_like);
            $result_repost = $connect->query($sql_repost);
            if ($result_like->num_rows > 0) {
                echo "<button id='$content_id' class='like-button liked'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
            <path d='M21.3345 8.71342C21.0727 9.33749 20.6942 9.9081 20.2183 10.3954L20.148 10.4648L13.6656 16.8654C12.4711 18.0449 10.5278 18.0449 9.33329 16.8654L9.33326 16.8654L2.85134 10.4657C2.34261 9.96338 1.93992 9.36791 1.66547 8.7137L0.7102 9.11444L1.66547 8.71369C1.39104 8.05952 1.25 7.35894 1.25 6.65182C1.25 5.94469 1.39104 5.24411 1.66547 4.58994C1.93992 3.93572 2.34261 3.34025 2.85134 2.83794C3.36011 2.33559 3.96496 1.93628 4.63177 1.66356C5.29861 1.39084 6.01386 1.25027 6.73655 1.25027C7.45924 1.25027 8.17449 1.39084 8.84133 1.66356C9.50754 1.93603 10.1119 2.33487 10.6204 2.83658L11.4989 3.70337L11.4998 3.7025L11.5009 3.7036L12.3791 2.83686C12.8877 2.33489 13.4922 1.93586 14.1587 1.66329C14.8255 1.39057 15.5408 1.25 16.2634 1.25C16.9861 1.25 17.7014 1.39057 18.3682 1.66329C19.035 1.93601 19.6399 2.33532 20.1487 2.83767C20.6574 3.33999 21.0601 3.93546 21.3345 4.58967C21.609 5.24384 21.75 5.94442 21.75 6.65155C21.75 7.35867 21.609 8.05925 21.3345 8.71342Z' />
            </svg>";
                echo "<span class='like-counter'>" . $content_likes . "</span></button>";
                if ($content_likes == 1) {
                    echo "<button id='$content_id' class='like-button unliked hide'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
                <path d='M21.3345 8.71342C21.0727 9.33749 20.6942 9.9081 20.2183 10.3954L20.148 10.4648L13.6656 16.8654C12.4711 18.0449 10.5278 18.0449 9.33329 16.8654L9.33326 16.8654L2.85134 10.4657C2.34261 9.96338 1.93992 9.36791 1.66547 8.7137L0.7102 9.11444L1.66547 8.71369C1.39104 8.05952 1.25 7.35894 1.25 6.65182C1.25 5.94469 1.39104 5.24411 1.66547 4.58994C1.93992 3.93572 2.34261 3.34025 2.85134 2.83794C3.36011 2.33559 3.96496 1.93628 4.63177 1.66356C5.29861 1.39084 6.01386 1.25027 6.73655 1.25027C7.45924 1.25027 8.17449 1.39084 8.84133 1.66356C9.50754 1.93603 10.1119 2.33487 10.6204 2.83658L11.4989 3.70337L11.4998 3.7025L11.5009 3.7036L12.3791 2.83686C12.8877 2.33489 13.4922 1.93586 14.1587 1.66329C14.8255 1.39057 15.5408 1.25 16.2634 1.25C16.9861 1.25 17.7014 1.39057 18.3682 1.66329C19.035 1.93601 19.6399 2.33532 20.1487 2.83767C20.6574 3.33999 21.0601 3.93546 21.3345 4.58967C21.609 5.24384 21.75 5.94442 21.75 6.65155C21.75 7.35867 21.609 8.05925 21.3345 8.71342Z' />
                </svg>";
                } else {
                    echo "<button id='$content_id' class='like-button unliked hide'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
                <path d='M21.3345 8.71342C21.0727 9.33749 20.6942 9.9081 20.2183 10.3954L20.148 10.4648L13.6656 16.8654C12.4711 18.0449 10.5278 18.0449 9.33329 16.8654L9.33326 16.8654L2.85134 10.4657C2.34261 9.96338 1.93992 9.36791 1.66547 8.7137L0.7102 9.11444L1.66547 8.71369C1.39104 8.05952 1.25 7.35894 1.25 6.65182C1.25 5.94469 1.39104 5.24411 1.66547 4.58994C1.93992 3.93572 2.34261 3.34025 2.85134 2.83794C3.36011 2.33559 3.96496 1.93628 4.63177 1.66356C5.29861 1.39084 6.01386 1.25027 6.73655 1.25027C7.45924 1.25027 8.17449 1.39084 8.84133 1.66356C9.50754 1.93603 10.1119 2.33487 10.6204 2.83658L11.4989 3.70337L11.4998 3.7025L11.5009 3.7036L12.3791 2.83686C12.8877 2.33489 13.4922 1.93586 14.1587 1.66329C14.8255 1.39057 15.5408 1.25 16.2634 1.25C16.9861 1.25 17.7014 1.39057 18.3682 1.66329C19.035 1.93601 19.6399 2.33532 20.1487 2.83767C20.6574 3.33999 21.0601 3.93546 21.3345 4.58967C21.609 5.24384 21.75 5.94442 21.75 6.65155C21.75 7.35867 21.609 8.05925 21.3345 8.71342Z' />
                </svg>";
                    echo "<span class='like-counter'>" . $content_likes . "</span></button>";
                }
            } else {
                if ($content_likes == 0) {
                    echo "<button id='$content_id' class='like-button unliked'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
                <path d='M21.3345 8.71342C21.0727 9.33749 20.6942 9.9081 20.2183 10.3954L20.148 10.4648L13.6656 16.8654C12.4711 18.0449 10.5278 18.0449 9.33329 16.8654L9.33326 16.8654L2.85134 10.4657C2.34261 9.96338 1.93992 9.36791 1.66547 8.7137L0.7102 9.11444L1.66547 8.71369C1.39104 8.05952 1.25 7.35894 1.25 6.65182C1.25 5.94469 1.39104 5.24411 1.66547 4.58994C1.93992 3.93572 2.34261 3.34025 2.85134 2.83794C3.36011 2.33559 3.96496 1.93628 4.63177 1.66356C5.29861 1.39084 6.01386 1.25027 6.73655 1.25027C7.45924 1.25027 8.17449 1.39084 8.84133 1.66356C9.50754 1.93603 10.1119 2.33487 10.6204 2.83658L11.4989 3.70337L11.4998 3.7025L11.5009 3.7036L12.3791 2.83686C12.8877 2.33489 13.4922 1.93586 14.1587 1.66329C14.8255 1.39057 15.5408 1.25 16.2634 1.25C16.9861 1.25 17.7014 1.39057 18.3682 1.66329C19.035 1.93601 19.6399 2.33532 20.1487 2.83767C20.6574 3.33999 21.0601 3.93546 21.3345 4.58967C21.609 5.24384 21.75 5.94442 21.75 6.65155C21.75 7.35867 21.609 8.05925 21.3345 8.71342Z' />
                </svg>";
                } else {
                    echo "<button id='$content_id' class='like-button unliked'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
                <path d='M21.3345 8.71342C21.0727 9.33749 20.6942 9.9081 20.2183 10.3954L20.148 10.4648L13.6656 16.8654C12.4711 18.0449 10.5278 18.0449 9.33329 16.8654L9.33326 16.8654L2.85134 10.4657C2.34261 9.96338 1.93992 9.36791 1.66547 8.7137L0.7102 9.11444L1.66547 8.71369C1.39104 8.05952 1.25 7.35894 1.25 6.65182C1.25 5.94469 1.39104 5.24411 1.66547 4.58994C1.93992 3.93572 2.34261 3.34025 2.85134 2.83794C3.36011 2.33559 3.96496 1.93628 4.63177 1.66356C5.29861 1.39084 6.01386 1.25027 6.73655 1.25027C7.45924 1.25027 8.17449 1.39084 8.84133 1.66356C9.50754 1.93603 10.1119 2.33487 10.6204 2.83658L11.4989 3.70337L11.4998 3.7025L11.5009 3.7036L12.3791 2.83686C12.8877 2.33489 13.4922 1.93586 14.1587 1.66329C14.8255 1.39057 15.5408 1.25 16.2634 1.25C16.9861 1.25 17.7014 1.39057 18.3682 1.66329C19.035 1.93601 19.6399 2.33532 20.1487 2.83767C20.6574 3.33999 21.0601 3.93546 21.3345 4.58967C21.609 5.24384 21.75 5.94442 21.75 6.65155C21.75 7.35867 21.609 8.05925 21.3345 8.71342Z' />
                </svg>";
                    echo "<span class='like-counter'>" . $content_likes . "</span></button>";
                }
                echo "<button id='$content_id' class='like-button liked hide'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
            <path fill-rule='evenodd' clip-rule='evenodd' d='M15.3643 17.1232L21.0488 11.4387L21.0494 11.4394C21.6548 10.834 22.135 10.1153 22.4626 9.32432C22.7903 8.53335 22.9589 7.6856 22.9589 6.82947C22.9589 5.97334 22.7903 5.12559 22.4626 4.33463C22.135 3.54366 21.6548 2.82498 21.0494 2.2196C20.4441 1.61423 19.7254 1.13402 18.9344 0.806393C18.1434 0.478767 17.2957 0.310142 16.4396 0.310145C15.5834 0.310148 14.7357 0.478778 13.9447 0.806409C13.1541 1.13391 12.4356 1.61388 11.8304 2.21893L11.8289 2.21742L11.8279 2.21836C11.2229 1.61375 10.5048 1.1341 9.71455 0.806772C8.92359 0.479147 8.07584 0.310521 7.2197 0.310524C6.36357 0.310526 5.51582 0.479157 4.72486 0.806787C3.93389 1.13442 3.2152 1.61463 2.60982 2.22001C2.00444 2.82539 1.52423 3.54408 1.1966 4.33504C0.868969 5.12601 0.700339 5.97376 0.700335 6.82989C0.700332 7.68602 0.868959 8.53377 1.19658 9.32474C1.52421 10.1157 2.00442 10.8344 2.60979 11.4398L2.60985 11.4397L8.29331 17.1232C10.2459 19.0758 13.4117 19.0758 15.3643 17.1232Z'/>
            </svg>";
                echo "<span class='like-counter'>" . $content_likes . "</span></button>";
            }
            echo "<a href='./post/$content_id' class='comment-button comment'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
        <path d='M4 1.25L19 1.25C20.5188 1.25 21.75 2.48122 21.75 4L21.75 17.75L4 17.75C2.48122 17.75 1.25 16.5188 1.25 15L1.25 4C1.25 2.48122 2.48122 1.25 4 1.25Z' />
        </svg>";
            echo $rows_num_comment != 0 ? "<span class='comment-counter'>" . $rows_num_comment . "</span>" : "";
            echo "</a>";
            if (!$for_friends && !($content_type == 'repost' && $content_author_id == $current_user_id) && !$check_repost_status) {
                if ($result_repost->num_rows > 0) {
                    echo "<button id='repost-$content_id' class='repost-button reposted'><svg width='27' height='22' viewBox='0 0 27 22' fill='none' xmlns='http://www.w3.org/2000/svg'>
                    <path d='M22.2501 4.41667V2.30556C22.2501 2.0256 22.0921 1.75712 21.8108 1.55917C21.5295 1.36121 21.1479 1.25 20.7501 1.25H5.75013C5.3523 1.25 4.97077 1.36121 4.68947 1.55917C4.40816 1.75712 4.25013 2.0256 4.25013 2.30556V12.8611M4.25013 12.8611L7.25012 10.75M4.25013 12.8611L1.25012 10.75M4.25012 17.0833V19.1944C4.25012 19.4744 4.40816 19.7429 4.68946 19.9408C4.97077 20.1388 5.3523 20.25 5.75012 20.25H20.7501C21.1479 20.25 21.5295 20.1388 21.8108 19.9408C22.0921 19.7429 22.2501 19.4744 22.2501 19.1944V8.63889M22.2501 8.63889L19.2501 10.75M22.2501 8.63889L25.2501 10.75' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'/>
                    </svg>";
                    echo "<span class='repost-counter'>" . $content_reposts . "</span></button>";
                    if ($content_reposts == 1) {
                        echo "<button id='repost-$content_id' class='repost-button unreposted hide'><svg width='27' height='22' viewBox='0 0 27 22' fill='none' xmlns='http://www.w3.org/2000/svg'>
                        <path d='M22.2501 4.41667V2.30556C22.2501 2.0256 22.0921 1.75712 21.8108 1.55917C21.5295 1.36121 21.1479 1.25 20.7501 1.25H5.75013C5.3523 1.25 4.97077 1.36121 4.68947 1.55917C4.40816 1.75712 4.25013 2.0256 4.25013 2.30556V12.8611M4.25013 12.8611L7.25012 10.75M4.25013 12.8611L1.25012 10.75M4.25012 17.0833V19.1944C4.25012 19.4744 4.40816 19.7429 4.68946 19.9408C4.97077 20.1388 5.3523 20.25 5.75012 20.25H20.7501C21.1479 20.25 21.5295 20.1388 21.8108 19.9408C22.0921 19.7429 22.2501 19.4744 22.2501 19.1944V8.63889M22.2501 8.63889L19.2501 10.75M22.2501 8.63889L25.2501 10.75' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'/>
                        </svg>";
                    } else {
                        echo "<button id='repost-$content_id' class='repost-button unreposted hide'><svg width='27' height='22' viewBox='0 0 27 22' fill='none' xmlns='http://www.w3.org/2000/svg'>
                        <path d='M22.2501 4.41667V2.30556C22.2501 2.0256 22.0921 1.75712 21.8108 1.55917C21.5295 1.36121 21.1479 1.25 20.7501 1.25H5.75013C5.3523 1.25 4.97077 1.36121 4.68947 1.55917C4.40816 1.75712 4.25013 2.0256 4.25013 2.30556V12.8611M4.25013 12.8611L7.25012 10.75M4.25013 12.8611L1.25012 10.75M4.25012 17.0833V19.1944C4.25012 19.4744 4.40816 19.7429 4.68946 19.9408C4.97077 20.1388 5.3523 20.25 5.75012 20.25H20.7501C21.1479 20.25 21.5295 20.1388 21.8108 19.9408C22.0921 19.7429 22.2501 19.4744 22.2501 19.1944V8.63889M22.2501 8.63889L19.2501 10.75M22.2501 8.63889L25.2501 10.75' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'/>
                        </svg>";
                        echo "<span class='repost-counter'>" . $content_reposts . "</span></button>";
                    }
                } else {
                    if ($content_reposts == 0) {
                        echo "<button id='repost-$content_id' class='repost-button unreposted'><svg width='27' height='22' viewBox='0 0 27 22' fill='none' xmlns='http://www.w3.org/2000/svg'>
                        <path d='M22.2501 4.41667V2.30556C22.2501 2.0256 22.0921 1.75712 21.8108 1.55917C21.5295 1.36121 21.1479 1.25 20.7501 1.25H5.75013C5.3523 1.25 4.97077 1.36121 4.68947 1.55917C4.40816 1.75712 4.25013 2.0256 4.25013 2.30556V12.8611M4.25013 12.8611L7.25012 10.75M4.25013 12.8611L1.25012 10.75M4.25012 17.0833V19.1944C4.25012 19.4744 4.40816 19.7429 4.68946 19.9408C4.97077 20.1388 5.3523 20.25 5.75012 20.25H20.7501C21.1479 20.25 21.5295 20.1388 21.8108 19.9408C22.0921 19.7429 22.2501 19.4744 22.2501 19.1944V8.63889M22.2501 8.63889L19.2501 10.75M22.2501 8.63889L25.2501 10.75' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'/>
                        </svg>";
                    } else {
                        echo "<button id='repost-$content_id' class='repost-button unreposted'><svg width='27' height='22' viewBox='0 0 27 22' fill='none' xmlns='http://www.w3.org/2000/svg'>
                        <path d='M22.2501 4.41667V2.30556C22.2501 2.0256 22.0921 1.75712 21.8108 1.55917C21.5295 1.36121 21.1479 1.25 20.7501 1.25H5.75013C5.3523 1.25 4.97077 1.36121 4.68947 1.55917C4.40816 1.75712 4.25013 2.0256 4.25013 2.30556V12.8611M4.25013 12.8611L7.25012 10.75M4.25013 12.8611L1.25012 10.75M4.25012 17.0833V19.1944C4.25012 19.4744 4.40816 19.7429 4.68946 19.9408C4.97077 20.1388 5.3523 20.25 5.75012 20.25H20.7501C21.1479 20.25 21.5295 20.1388 21.8108 19.9408C22.0921 19.7429 22.2501 19.4744 22.2501 19.1944V8.63889M22.2501 8.63889L19.2501 10.75M22.2501 8.63889L25.2501 10.75' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'/>
                        </svg>";
                        echo "<span class='repost-counter'>" . $content_reposts . "</span></button>";
                    }
                    echo "<button id='repost-$content_id' class='repost-button reposted hide'><svg width='27' height='22' viewBox='0 0 27 22' fill='none' xmlns='http://www.w3.org/2000/svg'>
                    <path d='M22.2501 4.41667V2.30556C22.2501 2.0256 22.0921 1.75712 21.8108 1.55917C21.5295 1.36121 21.1479 1.25 20.7501 1.25H5.75013C5.3523 1.25 4.97077 1.36121 4.68947 1.55917C4.40816 1.75712 4.25013 2.0256 4.25013 2.30556V12.8611M4.25013 12.8611L7.25012 10.75M4.25013 12.8611L1.25012 10.75M4.25012 17.0833V19.1944C4.25012 19.4744 4.40816 19.7429 4.68946 19.9408C4.97077 20.1388 5.3523 20.25 5.75012 20.25H20.7501C21.1479 20.25 21.5295 20.1388 21.8108 19.9408C22.0921 19.7429 22.2501 19.4744 22.2501 19.1944V8.63889M22.2501 8.63889L19.2501 10.75M22.2501 8.63889L25.2501 10.75' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'/>
                    </svg>";
                    echo "<span class='repost-counter'>" . $content_reposts . "</span></button>";
                }
            }
            echo "</div>";
            echo "<span class='post-date'>" . $content_date . "</span>";
            echo "</div>";
            echo "<div class='three-dots-popup' id='three-dots-popup_$content_id'>";
            echo "<a class='three-dots-popup-li open-post' href='./post/$content_id'>Открыть на стене</a>";
            echo "<span class='three-dots-popup-li copy-link' onclick='copyLinkToPost($content_id)'>Копировать ссылку</span>";
            echo "<span class='three-dots-popup-li delete-post' id='$content_id'>Удалить</span>";
            echo "</div>";
            echo "</div>";
        } else {
            echo "<div class='user-post hide profile_user-post' id='post-$content_id'>";
            echo "<div class='extra-post-info'>";
            echo $content_type == 'repost' ? "<a href='./post/$content_repost_id' class='repost-link-profile'>Репост  <img src='uploads/avatar/thin_$content_repost_avatar' class='repost_avatar'>@$content_repost_username</a>" : "";
            echo $for_friends ? "<div class='for-friends'><svg width='28' height='31' viewBox='0 0 28 31' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                            <path d='M25 19.6055C25 18.278 24.9991 17.3577 24.9404 16.6426C24.883 15.9425 24.7759 15.5499 24.626 15.2568V15.2559C24.2972 14.6137 23.7721 14.0909 23.125 13.7627C22.7942 13.595 22.343 13.4837 21.4863 13.4316C20.7964 13.3897 19.9321 13.3887 18.75 13.3887H8.75C7.56785 13.3887 6.70365 13.3897 6.01367 13.4316C5.15689 13.4837 4.70568 13.595 4.375 13.7627C3.72775 14.091 3.2028 14.6144 2.87402 15.2568C2.72409 15.5499 2.61704 15.9425 2.55957 16.6426C2.50088 17.3577 2.5 18.278 2.5 19.6055V21.7832C2.5 23.1107 2.50088 24.0309 2.55957 24.7461C2.61705 25.4465 2.72401 25.8397 2.87402 26.1328C3.20282 26.7751 3.72786 27.2987 4.375 27.627C4.67121 27.7772 5.06793 27.8831 5.77246 27.9404C6.49167 27.9989 7.41684 28 8.75 28H18.75C20.0832 28 21.0083 27.9989 21.7275 27.9404C22.432 27.8831 22.8287 27.7772 23.125 27.627C23.7721 27.2987 24.2972 26.7751 24.626 26.1328C24.776 25.8397 24.8829 25.4465 24.9404 24.7461C24.9991 24.0309 25 23.1107 25 21.7832V19.6055ZM20.3125 9.02734C20.3123 5.4276 17.3794 2.5 13.75 2.5C10.1205 2.5 7.18774 5.4276 7.1875 9.02734V10.8936C7.662 10.888 8.18153 10.8887 8.75 10.8887H18.75C19.3185 10.8887 19.838 10.888 20.3125 10.8936V9.02734ZM22.8125 11.0684C23.3248 11.1602 23.8023 11.3032 24.2559 11.5332C25.3726 12.0996 26.2816 13.0035 26.8516 14.1172C27.2125 14.8224 27.3614 15.5825 27.4316 16.4385C27.5007 17.2796 27.5 18.3195 27.5 19.6055V21.7832C27.5 23.0691 27.5006 24.1091 27.4316 24.9502C27.3614 25.8061 27.2125 26.5663 26.8516 27.2715C26.2816 28.3852 25.3726 29.29 24.2559 29.8564C23.5495 30.2147 22.7881 30.3628 21.9297 30.4326C21.0857 30.5012 20.0419 30.5 18.75 30.5H8.75C7.45813 30.5 6.41432 30.5012 5.57031 30.4326C4.71193 30.3628 3.9505 30.2147 3.24414 29.8564C2.19707 29.3253 1.33243 28.4969 0.758789 27.4775L0.648438 27.2715C0.287543 26.5663 0.138605 25.8061 0.0683596 24.9502C-0.000638187 24.1091 2.29428e-07 23.0691 2.29428e-07 21.7832V19.6055C1.85029e-07 18.3195 -0.000654526 17.2796 0.0683596 16.4385C0.138605 15.5825 0.287543 14.8224 0.648438 14.1172C1.21847 13.0035 2.12749 12.0996 3.24414 11.5332C3.69773 11.3031 4.17518 11.1602 4.6875 11.0684V9.02734C4.68774 4.0364 8.75033 0 13.75 0C18.7496 0 22.8123 4.0364 22.8125 9.02734V11.0684Z' />
                                        </svg>
                <span class='for-friends'>Для друзей</span></div>" : "";

            echo "<div class='div-show-three-dots-popup in-profile' onclick='showPopup($content_id)' id='div-show-three-dots-popup_$content_id'>";
            echo "<img src='pics/ThreeDotsIcon.svg' class='show-three-dots-popup'>";
            echo "</div>";
            echo "</div>";
            if ($hashtag_id != 0) {
                $hashtag_name = $connect->query("SELECT name FROM hashtags WHERE id = $hashtag_id")->fetch_assoc()['name'];
                echo "<p>" . $content_text . " <a href='./wall?search=$hashtag_name'>#" . $hashtag_name . "</a></p>";
            } else {
                echo "<p>" . $content_text . "</p>";
            }
            if ($content_image != null) {
                echo "<div class='image-in-post-div'>";
                echo "<img class='image-in-post-hide' src=./uploads/post-image/small_" . $content_image . ">";
                echo "<img class='image-in-post' src=./uploads/post-image/small_" . $content_image . ">";
                echo "</div>";
            }
            echo "<div class='buttons-and-date'>";
            echo "<div class='post-buttons'>";
            $sql_comment = "SELECT comments.text AS comment_text
                                        FROM comments
                                        JOIN posts ON comments.post_id = posts.id
                                        WHERE comments.post_id = $content_id
                                        ORDER BY UNIX_TIMESTAMP(comments.comment_date) ASC";
            $result_comment = $connect->query($sql_comment);
            $rows_num_comment = $result_comment->num_rows;
            $sql_like = "SELECT * FROM likes_on_posts WHERE post_id = $content_id AND user_id = " . $current_user_id;
            $sql_repost = "SELECT * FROM reposts WHERE post_id = $content_id AND user_id = " . $current_user_id;
            $result_like = $connect->query($sql_like);
            $result_repost = $connect->query($sql_repost);
            if ($result_like->num_rows > 0) {
                echo "<button id='$content_id' class='like-button liked'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
            <path d='M21.3345 8.71342C21.0727 9.33749 20.6942 9.9081 20.2183 10.3954L20.148 10.4648L13.6656 16.8654C12.4711 18.0449 10.5278 18.0449 9.33329 16.8654L9.33326 16.8654L2.85134 10.4657C2.34261 9.96338 1.93992 9.36791 1.66547 8.7137L0.7102 9.11444L1.66547 8.71369C1.39104 8.05952 1.25 7.35894 1.25 6.65182C1.25 5.94469 1.39104 5.24411 1.66547 4.58994C1.93992 3.93572 2.34261 3.34025 2.85134 2.83794C3.36011 2.33559 3.96496 1.93628 4.63177 1.66356C5.29861 1.39084 6.01386 1.25027 6.73655 1.25027C7.45924 1.25027 8.17449 1.39084 8.84133 1.66356C9.50754 1.93603 10.1119 2.33487 10.6204 2.83658L11.4989 3.70337L11.4998 3.7025L11.5009 3.7036L12.3791 2.83686C12.8877 2.33489 13.4922 1.93586 14.1587 1.66329C14.8255 1.39057 15.5408 1.25 16.2634 1.25C16.9861 1.25 17.7014 1.39057 18.3682 1.66329C19.035 1.93601 19.6399 2.33532 20.1487 2.83767C20.6574 3.33999 21.0601 3.93546 21.3345 4.58967C21.609 5.24384 21.75 5.94442 21.75 6.65155C21.75 7.35867 21.609 8.05925 21.3345 8.71342Z' />
            </svg>";
                echo "<span class='like-counter'>" . $content_likes . "</span></button>";
                if ($content_likes == 1) {
                    echo "<button id='$content_id' class='like-button unliked hide'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
                <path d='M21.3345 8.71342C21.0727 9.33749 20.6942 9.9081 20.2183 10.3954L20.148 10.4648L13.6656 16.8654C12.4711 18.0449 10.5278 18.0449 9.33329 16.8654L9.33326 16.8654L2.85134 10.4657C2.34261 9.96338 1.93992 9.36791 1.66547 8.7137L0.7102 9.11444L1.66547 8.71369C1.39104 8.05952 1.25 7.35894 1.25 6.65182C1.25 5.94469 1.39104 5.24411 1.66547 4.58994C1.93992 3.93572 2.34261 3.34025 2.85134 2.83794C3.36011 2.33559 3.96496 1.93628 4.63177 1.66356C5.29861 1.39084 6.01386 1.25027 6.73655 1.25027C7.45924 1.25027 8.17449 1.39084 8.84133 1.66356C9.50754 1.93603 10.1119 2.33487 10.6204 2.83658L11.4989 3.70337L11.4998 3.7025L11.5009 3.7036L12.3791 2.83686C12.8877 2.33489 13.4922 1.93586 14.1587 1.66329C14.8255 1.39057 15.5408 1.25 16.2634 1.25C16.9861 1.25 17.7014 1.39057 18.3682 1.66329C19.035 1.93601 19.6399 2.33532 20.1487 2.83767C20.6574 3.33999 21.0601 3.93546 21.3345 4.58967C21.609 5.24384 21.75 5.94442 21.75 6.65155C21.75 7.35867 21.609 8.05925 21.3345 8.71342Z' />
                </svg>";
                } else {
                    echo "<button id='$content_id' class='like-button unliked hide'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
                <path d='M21.3345 8.71342C21.0727 9.33749 20.6942 9.9081 20.2183 10.3954L20.148 10.4648L13.6656 16.8654C12.4711 18.0449 10.5278 18.0449 9.33329 16.8654L9.33326 16.8654L2.85134 10.4657C2.34261 9.96338 1.93992 9.36791 1.66547 8.7137L0.7102 9.11444L1.66547 8.71369C1.39104 8.05952 1.25 7.35894 1.25 6.65182C1.25 5.94469 1.39104 5.24411 1.66547 4.58994C1.93992 3.93572 2.34261 3.34025 2.85134 2.83794C3.36011 2.33559 3.96496 1.93628 4.63177 1.66356C5.29861 1.39084 6.01386 1.25027 6.73655 1.25027C7.45924 1.25027 8.17449 1.39084 8.84133 1.66356C9.50754 1.93603 10.1119 2.33487 10.6204 2.83658L11.4989 3.70337L11.4998 3.7025L11.5009 3.7036L12.3791 2.83686C12.8877 2.33489 13.4922 1.93586 14.1587 1.66329C14.8255 1.39057 15.5408 1.25 16.2634 1.25C16.9861 1.25 17.7014 1.39057 18.3682 1.66329C19.035 1.93601 19.6399 2.33532 20.1487 2.83767C20.6574 3.33999 21.0601 3.93546 21.3345 4.58967C21.609 5.24384 21.75 5.94442 21.75 6.65155C21.75 7.35867 21.609 8.05925 21.3345 8.71342Z' />
                </svg>";
                    echo "<span class='like-counter'>" . $content_likes . "</span></button>";
                }
            } else {
                if ($content_likes == 0) {
                    echo "<button id='$content_id' class='like-button unliked'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
                <path d='M21.3345 8.71342C21.0727 9.33749 20.6942 9.9081 20.2183 10.3954L20.148 10.4648L13.6656 16.8654C12.4711 18.0449 10.5278 18.0449 9.33329 16.8654L9.33326 16.8654L2.85134 10.4657C2.34261 9.96338 1.93992 9.36791 1.66547 8.7137L0.7102 9.11444L1.66547 8.71369C1.39104 8.05952 1.25 7.35894 1.25 6.65182C1.25 5.94469 1.39104 5.24411 1.66547 4.58994C1.93992 3.93572 2.34261 3.34025 2.85134 2.83794C3.36011 2.33559 3.96496 1.93628 4.63177 1.66356C5.29861 1.39084 6.01386 1.25027 6.73655 1.25027C7.45924 1.25027 8.17449 1.39084 8.84133 1.66356C9.50754 1.93603 10.1119 2.33487 10.6204 2.83658L11.4989 3.70337L11.4998 3.7025L11.5009 3.7036L12.3791 2.83686C12.8877 2.33489 13.4922 1.93586 14.1587 1.66329C14.8255 1.39057 15.5408 1.25 16.2634 1.25C16.9861 1.25 17.7014 1.39057 18.3682 1.66329C19.035 1.93601 19.6399 2.33532 20.1487 2.83767C20.6574 3.33999 21.0601 3.93546 21.3345 4.58967C21.609 5.24384 21.75 5.94442 21.75 6.65155C21.75 7.35867 21.609 8.05925 21.3345 8.71342Z' />
                </svg>";
                } else {
                    echo "<button id='$content_id' class='like-button unliked'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
                <path d='M21.3345 8.71342C21.0727 9.33749 20.6942 9.9081 20.2183 10.3954L20.148 10.4648L13.6656 16.8654C12.4711 18.0449 10.5278 18.0449 9.33329 16.8654L9.33326 16.8654L2.85134 10.4657C2.34261 9.96338 1.93992 9.36791 1.66547 8.7137L0.7102 9.11444L1.66547 8.71369C1.39104 8.05952 1.25 7.35894 1.25 6.65182C1.25 5.94469 1.39104 5.24411 1.66547 4.58994C1.93992 3.93572 2.34261 3.34025 2.85134 2.83794C3.36011 2.33559 3.96496 1.93628 4.63177 1.66356C5.29861 1.39084 6.01386 1.25027 6.73655 1.25027C7.45924 1.25027 8.17449 1.39084 8.84133 1.66356C9.50754 1.93603 10.1119 2.33487 10.6204 2.83658L11.4989 3.70337L11.4998 3.7025L11.5009 3.7036L12.3791 2.83686C12.8877 2.33489 13.4922 1.93586 14.1587 1.66329C14.8255 1.39057 15.5408 1.25 16.2634 1.25C16.9861 1.25 17.7014 1.39057 18.3682 1.66329C19.035 1.93601 19.6399 2.33532 20.1487 2.83767C20.6574 3.33999 21.0601 3.93546 21.3345 4.58967C21.609 5.24384 21.75 5.94442 21.75 6.65155C21.75 7.35867 21.609 8.05925 21.3345 8.71342Z' />
                </svg>";
                    echo "<span class='like-counter'>" . $content_likes . "</span></button>";
                }
                echo "<button id='$content_id' class='like-button liked hide'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
            <path fill-rule='evenodd' clip-rule='evenodd' d='M15.3643 17.1232L21.0488 11.4387L21.0494 11.4394C21.6548 10.834 22.135 10.1153 22.4626 9.32432C22.7903 8.53335 22.9589 7.6856 22.9589 6.82947C22.9589 5.97334 22.7903 5.12559 22.4626 4.33463C22.135 3.54366 21.6548 2.82498 21.0494 2.2196C20.4441 1.61423 19.7254 1.13402 18.9344 0.806393C18.1434 0.478767 17.2957 0.310142 16.4396 0.310145C15.5834 0.310148 14.7357 0.478778 13.9447 0.806409C13.1541 1.13391 12.4356 1.61388 11.8304 2.21893L11.8289 2.21742L11.8279 2.21836C11.2229 1.61375 10.5048 1.1341 9.71455 0.806772C8.92359 0.479147 8.07584 0.310521 7.2197 0.310524C6.36357 0.310526 5.51582 0.479157 4.72486 0.806787C3.93389 1.13442 3.2152 1.61463 2.60982 2.22001C2.00444 2.82539 1.52423 3.54408 1.1966 4.33504C0.868969 5.12601 0.700339 5.97376 0.700335 6.82989C0.700332 7.68602 0.868959 8.53377 1.19658 9.32474C1.52421 10.1157 2.00442 10.8344 2.60979 11.4398L2.60985 11.4397L8.29331 17.1232C10.2459 19.0758 13.4117 19.0758 15.3643 17.1232Z'/>
            </svg>";
                echo "<span class='like-counter'>" . $content_likes . "</span></button>";
            }
            echo "<a href='./post/$content_id' class='comment-button comment'><svg width='23' height='19' viewBox='0 0 23 19' fill='none' xmlns='http://www.w3.org/2000/svg'>
        <path d='M4 1.25L19 1.25C20.5188 1.25 21.75 2.48122 21.75 4L21.75 17.75L4 17.75C2.48122 17.75 1.25 16.5188 1.25 15L1.25 4C1.25 2.48122 2.48122 1.25 4 1.25Z' />
        </svg>";
            echo $rows_num_comment != 0 ? "<span class='comment-counter'>" . $rows_num_comment . "</span>" : "";
            echo "</a>";
            if (!$for_friends && !($content_type == 'repost' && $content_author_id == $current_user_id) && !$check_repost_status) {
                if ($result_repost->num_rows > 0) {
                    echo "<button id='repost-$content_id' class='repost-button reposted'><svg width='27' height='22' viewBox='0 0 27 22' fill='none' xmlns='http://www.w3.org/2000/svg'>
                    <path d='M22.2501 4.41667V2.30556C22.2501 2.0256 22.0921 1.75712 21.8108 1.55917C21.5295 1.36121 21.1479 1.25 20.7501 1.25H5.75013C5.3523 1.25 4.97077 1.36121 4.68947 1.55917C4.40816 1.75712 4.25013 2.0256 4.25013 2.30556V12.8611M4.25013 12.8611L7.25012 10.75M4.25013 12.8611L1.25012 10.75M4.25012 17.0833V19.1944C4.25012 19.4744 4.40816 19.7429 4.68946 19.9408C4.97077 20.1388 5.3523 20.25 5.75012 20.25H20.7501C21.1479 20.25 21.5295 20.1388 21.8108 19.9408C22.0921 19.7429 22.2501 19.4744 22.2501 19.1944V8.63889M22.2501 8.63889L19.2501 10.75M22.2501 8.63889L25.2501 10.75' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'/>
                    </svg>";
                    echo "<span class='repost-counter'>" . $content_reposts . "</span></button>";
                    if ($content_reposts == 1) {
                        echo "<button id='repost-$content_id' class='repost-button unreposted hide'><svg width='27' height='22' viewBox='0 0 27 22' fill='none' xmlns='http://www.w3.org/2000/svg'>
                        <path d='M22.2501 4.41667V2.30556C22.2501 2.0256 22.0921 1.75712 21.8108 1.55917C21.5295 1.36121 21.1479 1.25 20.7501 1.25H5.75013C5.3523 1.25 4.97077 1.36121 4.68947 1.55917C4.40816 1.75712 4.25013 2.0256 4.25013 2.30556V12.8611M4.25013 12.8611L7.25012 10.75M4.25013 12.8611L1.25012 10.75M4.25012 17.0833V19.1944C4.25012 19.4744 4.40816 19.7429 4.68946 19.9408C4.97077 20.1388 5.3523 20.25 5.75012 20.25H20.7501C21.1479 20.25 21.5295 20.1388 21.8108 19.9408C22.0921 19.7429 22.2501 19.4744 22.2501 19.1944V8.63889M22.2501 8.63889L19.2501 10.75M22.2501 8.63889L25.2501 10.75' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'/>
                        </svg>";
                    } else {
                        echo "<button id='repost-$content_id' class='repost-button unreposted hide'><svg width='27' height='22' viewBox='0 0 27 22' fill='none' xmlns='http://www.w3.org/2000/svg'>
                        <path d='M22.2501 4.41667V2.30556C22.2501 2.0256 22.0921 1.75712 21.8108 1.55917C21.5295 1.36121 21.1479 1.25 20.7501 1.25H5.75013C5.3523 1.25 4.97077 1.36121 4.68947 1.55917C4.40816 1.75712 4.25013 2.0256 4.25013 2.30556V12.8611M4.25013 12.8611L7.25012 10.75M4.25013 12.8611L1.25012 10.75M4.25012 17.0833V19.1944C4.25012 19.4744 4.40816 19.7429 4.68946 19.9408C4.97077 20.1388 5.3523 20.25 5.75012 20.25H20.7501C21.1479 20.25 21.5295 20.1388 21.8108 19.9408C22.0921 19.7429 22.2501 19.4744 22.2501 19.1944V8.63889M22.2501 8.63889L19.2501 10.75M22.2501 8.63889L25.2501 10.75' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'/>
                        </svg>";
                        echo "<span class='repost-counter'>" . $content_reposts . "</span></button>";
                    }
                } else {
                    if ($content_reposts == 0) {
                        echo "<button id='repost-$content_id' class='repost-button unreposted'><svg width='27' height='22' viewBox='0 0 27 22' fill='none' xmlns='http://www.w3.org/2000/svg'>
                        <path d='M22.2501 4.41667V2.30556C22.2501 2.0256 22.0921 1.75712 21.8108 1.55917C21.5295 1.36121 21.1479 1.25 20.7501 1.25H5.75013C5.3523 1.25 4.97077 1.36121 4.68947 1.55917C4.40816 1.75712 4.25013 2.0256 4.25013 2.30556V12.8611M4.25013 12.8611L7.25012 10.75M4.25013 12.8611L1.25012 10.75M4.25012 17.0833V19.1944C4.25012 19.4744 4.40816 19.7429 4.68946 19.9408C4.97077 20.1388 5.3523 20.25 5.75012 20.25H20.7501C21.1479 20.25 21.5295 20.1388 21.8108 19.9408C22.0921 19.7429 22.2501 19.4744 22.2501 19.1944V8.63889M22.2501 8.63889L19.2501 10.75M22.2501 8.63889L25.2501 10.75' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'/>
                        </svg>";
                    } else {
                        echo "<button id='repost-$content_id' class='repost-button unreposted'><svg width='27' height='22' viewBox='0 0 27 22' fill='none' xmlns='http://www.w3.org/2000/svg'>
                        <path d='M22.2501 4.41667V2.30556C22.2501 2.0256 22.0921 1.75712 21.8108 1.55917C21.5295 1.36121 21.1479 1.25 20.7501 1.25H5.75013C5.3523 1.25 4.97077 1.36121 4.68947 1.55917C4.40816 1.75712 4.25013 2.0256 4.25013 2.30556V12.8611M4.25013 12.8611L7.25012 10.75M4.25013 12.8611L1.25012 10.75M4.25012 17.0833V19.1944C4.25012 19.4744 4.40816 19.7429 4.68946 19.9408C4.97077 20.1388 5.3523 20.25 5.75012 20.25H20.7501C21.1479 20.25 21.5295 20.1388 21.8108 19.9408C22.0921 19.7429 22.2501 19.4744 22.2501 19.1944V8.63889M22.2501 8.63889L19.2501 10.75M22.2501 8.63889L25.2501 10.75' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'/>
                        </svg>";
                        echo "<span class='repost-counter'>" . $content_reposts . "</span></button>";
                    }
                    echo "<button id='repost-$content_id' class='repost-button reposted hide'><svg width='27' height='22' viewBox='0 0 27 22' fill='none' xmlns='http://www.w3.org/2000/svg'>
                    <path d='M22.2501 4.41667V2.30556C22.2501 2.0256 22.0921 1.75712 21.8108 1.55917C21.5295 1.36121 21.1479 1.25 20.7501 1.25H5.75013C5.3523 1.25 4.97077 1.36121 4.68947 1.55917C4.40816 1.75712 4.25013 2.0256 4.25013 2.30556V12.8611M4.25013 12.8611L7.25012 10.75M4.25013 12.8611L1.25012 10.75M4.25012 17.0833V19.1944C4.25012 19.4744 4.40816 19.7429 4.68946 19.9408C4.97077 20.1388 5.3523 20.25 5.75012 20.25H20.7501C21.1479 20.25 21.5295 20.1388 21.8108 19.9408C22.0921 19.7429 22.2501 19.4744 22.2501 19.1944V8.63889M22.2501 8.63889L19.2501 10.75M22.2501 8.63889L25.2501 10.75' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'/>
                    </svg>";
                    echo "<span class='repost-counter'>" . $content_reposts . "</span></button>";
                }
            }
            echo "</div>";
            echo "<span class='post-date'>" . $content_date . "</span>";
            echo "</div>";
            echo "<div class='three-dots-popup' id='three-dots-popup_$content_id'>";
            echo "<a class='three-dots-popup-li open-post' href='./wall#post-$content_id'>Открыть на стене</a>";
            echo "<span class='three-dots-popup-li copy-link' onclick='copyLinkToPost($content_id)'>Копировать ссылку</span>";
            echo "<a class='three-dots-popup-li delete-post' href='back-files/delete-post?post=$content_id&source=profile'>Удалить</a>";
            echo "</div>";
            echo "</div>";
        }
        $posts_counter++;
    }
    if ($posts_counter > 3) {
        echo "<button type='button' onclick='seeAllPosts()' id='see-all-posts'>";
        echo "Показать все посты";
        echo "</button>";
    }
} else {
    echo "<p class='no-posts-yet'>Увы, но вы ещё не сделали постов</p>";
}
