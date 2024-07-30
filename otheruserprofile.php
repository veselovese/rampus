<?php
session_start();

require('connect.php');
require('like-or-dislike.php');

$username = $_GET['username'];
if (isset($_SESSION['user'])) {
    $id = $_SESSION['user']['id'];
    $result = $connect->query("SELECT * FROM users WHERE id = $id");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $other_username = $row["username"];
        }
    }
    if ($username == $other_username) {
        header("Location: ../profile");
        exit();
    }
}

$result = $connect->query("SELECT * FROM users WHERE username = '$username'");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $other_id = $row["id"];
        $other_username = $row["username"];
        $other_email = $row["email"];
        $other_description = $row["description"];
        $other_first_name = $row["first_name"];
        $other_second_name = $row["second_name"];
        $other_avatar = $row["avatar"];
    }
} else {
    header("Location: ../profile");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="stylesheet" href="../css/main.css?v=132">
    <link rel="stylesheet" href="../css/profile.css?v=132">
    <title>Профиль в Rampus (Рампус)</title>
    <link rel="apple-touch-icon" sizes="57x57" href="../favicons/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="../favicons/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="../favicons/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="../favicons/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="../favicons/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="../favicons/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="../favicons/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="../favicons/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="../favicons/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="../favicons/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="../favicons/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../favicons/favicon-16x16.png">
    <link rel="manifest" href="../favicons/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="favicons/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
</head>

<body>
    <?php require('header-2.php'); ?>
    <main>
        <h1 class="title">Профиль друга в Rampus (Рампус)</h1>
        <?php if (!isset($_SESSION['user'])) {
            header("Location: ../auth");
            exit();
        } else { ?>
            <section class="wrapper main-section">
                <nav class="first-part">
                    <ul>
                        <li><a href="../profile">Профиль</a></li>
                        <li><a href="../wall">Стена</a></li>
                        <li><a href="../profile">****</a></li>
                        <li id="active"><a href="../people">Люди</a></li>

                    </ul>
                </nav>
                <div class="second-and-third-parts">
                    <div class="second-part">
                        <div class="profile__user-info">
                            <img class="avatar" src="../uploads/avatar/small_<?= $other_avatar ?>">
                            <img class="three-dots show-three-dots-popup" onclick='showPopupUserInfo()' src='../pics/ThreeDotsIcon.svg'>
                            <div class='three-dots-popup' id='three-dots-popup_user-info'>
                                <span class='three-dots-popup-li copy-link' onclick='copyLinkToUser("<?= $other_username ?>")'>Копировать ссылку</span>
                            </div>
                            <div>
                                <p class="first-and-second-names"><?= $other_first_name . " " . $other_second_name ?></p>
                                <p class="username">@<?= $other_username ?></p>
                                <p class="description"><?= $other_description ?></p>
                            </div>
                        </div>
                        <?php
                        require('connect.php');
                        $sql = "SELECT posts.likes AS post_likes
                    FROM posts
                    JOIN users ON posts.user_id = users.id
                    WHERE posts.user_id = $other_id";
                        $result = $connect->query($sql);
                        $posts_count = $result->num_rows;
                        $likes_count = 0;
                        if ($posts_count > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $post_likes = $row["post_likes"];
                                $likes_count += $post_likes;
                            }
                        }
                        ?>
                        <div class="user-menu-and-third-past-mobile">
                            <div class="third-part-mobile">
                                <div class="profile__posts">
                                    <div>
                                        <img src="../pics/PostIcon.svg">
                                        <span> <?= $posts_count ?></span>
                                    </div>
                                    <p>посты</p>
                                </div>
                                <div class="profile__likes">
                                    <div>
                                        <img src="../pics/LikeIcon.svg">
                                        <span><?= $likes_count ?></span>
                                    </div>
                                    <p>лайки</p>
                                </div>
                            </div>
                        </div>
                        <div class="profile__user-posts">
                            <div>
                                <p>Посты</p>
                                <img src="../pics/SearchIcon.svg">
                            </div>
                            <div>
                                <?php
                                $sql = "SELECT hashtags.name AS hashtag_name, posts.text AS post_text, DATE_FORMAT(posts.post_date, '%d %M в %k:%i') AS post_date, posts.likes AS post_likes, posts.id AS i
                  FROM posts
                  LEFT JOIN hashtags ON posts.hashtag_id = hashtags.id
                  JOIN users ON posts.user_id = users.id
                  WHERE posts.user_id = $other_id";
                                $result = $connect->query($sql);
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $hashtag_name = $row["hashtag_name"];
                                        $post_text = preg_replace('/\xc2\xa0/', ' ', $row["post_text"]);
                                        $post_date = $row["post_date"];
                                        $post_likes = $row["post_likes"];
                                        $i = $row["i"];
                                        echo "<div class='user-post' id='post-$i'>";
                                        echo "<img onclick='showPopup($i)' src='../pics/ThreeDotsIcon.svg' class='show-three-dots-popup'>";
                                        echo "<div class='three-dots-popup' id='three-dots-popup_$i'>";
                                        echo "<a class='three-dots-popup-li open-post' href='../wall#post-$i'>Открыть на стене</a>";
                                        echo "<span class='three-dots-popup-li copy-link' onclick='copyLinkToPost($i)'>Копировать ссылку</span>";
                                        echo "</div>";
                                        if ($hashtag_name != 0) {
                                            echo "<p>" . $post_text . " <a href='../wall?search=$hashtag_name'>#" . $hashtag_name . "</a></p>";
                                        } else {
                                            echo "<p>" . $post_text . "</p>";
                                        }
                                        echo "<div>";
                                        echo "<div class='post-buttons'>";
                                        $sql_comment = "SELECT comments.text AS comment_text
                                        FROM comments
                                        JOIN posts ON comments.post_id = posts.id
                                        WHERE comments.post_id = $i
                                        ORDER BY UNIX_TIMESTAMP(comments.comment_date) ASC";
                                        $result_comment = $connect->query($sql_comment);
                                        $rows_num_comment = $result_comment->num_rows;
                                        $sql_like = "SELECT * FROM likes_on_posts WHERE post_id = $i AND user_id = " . $other_id;
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
                                        echo "<a href='../wall#post-$i' class='comment-button comment'><svg width='28' height='24' viewBox='0 0 28 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                            <path d='M0 5C0 2.23858 2.23858 0 5 0L23 0C25.7614 0 28 2.23858 28 5L28 24L5 24C2.23858 24 0 21.7614 0 19L0 5Z' />
                                            </svg>";
                                        echo "<span class='comment-counter'>" . $rows_num_comment . "</span></a>";
                                        echo "</div>";
                                        echo "<span>" . $post_date . "</span>";
                                        echo "</div>";
                                        echo "</div>";
                                    }
                                } else {
                                    echo "<p>Вы ещё не сделали постов</p>";
                                }
                                ?>
                            </div>
                        </div>
                        <nav class="first-part-mobile">
                            <ul>
                                <li><a href="../wall"><svg width="28" height="24" viewBox="0 0 28 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M4.56528 18C4.54895 17.8355 4.54059 17.6687 4.54059 17.5V10.5C4.54059 7.18629 7.22688 4.5 10.5406 4.5H21.4865C21.6336 4.5 21.7792 4.50635 21.9231 4.5188C21.681 1.98313 19.545 0 16.9459 0H5.00001C2.23858 0 0 2.23858 0 5V18H4.56528Z" />
                                            <path d="M6.05408 11C6.05408 8.23858 8.29265 6 11.0541 6H23C25.7614 6 28 8.23858 28 11V24H11.0541C8.29266 24 6.05408 21.7614 6.05408 19V11Z" />
                                        </svg>
                                        Стена
                                    </a></li>
                                <li><a href="../profile">
                                        <svg width="16" height="24" viewBox="0 0 16 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M4.97802 21.033C4.97802 21.8681 5.25275 22.5714 5.8022 23.1429C6.35165 23.7143 7.04396 24 7.87912 24C8.73626 24 9.43956 23.7253 9.98901 23.1758C10.5604 22.6044 10.8462 21.9011 10.8462 21.0659C10.8462 20.2088 10.5604 19.4945 9.98901 18.9231C9.41758 18.3516 8.71429 18.0659 7.87912 18.0659C7.04396 18.0659 6.35165 18.3516 5.8022 18.9231C5.25275 19.4725 4.97802 20.1758 4.97802 21.033ZM15.5275 7.51648C15.5275 9.07692 15.0769 10.4505 14.1758 11.6374C13.2967 12.8022 12.0769 13.7473 10.5165 14.4725L7.21978 16.2198L5.73626 12.6593L8.73626 11.0769C10.4066 10.022 11.2418 8.84615 11.2418 7.54945C11.2418 6.58242 10.9231 5.8022 10.2857 5.20879C9.64835 4.59341 8.83517 4.28571 7.84615 4.28571C5.93407 4.28571 4.64835 5.43956 3.98901 7.74725L0 6.2967C0.615385 4.34066 1.59341 2.8022 2.93407 1.68132C4.27473 0.56044 5.9011 0 7.81319 0C10.033 0 11.8681 0.714286 13.3187 2.14286C14.7912 3.54945 15.5275 5.34066 15.5275 7.51648Z" />
                                        </svg>
                                        ****</a></li>
                                <li id="active"><a href="../profile">
                                        <svg width="16" height="24" viewBox="0 0 16 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M4.97802 21.033C4.97802 21.8681 5.25275 22.5714 5.8022 23.1429C6.35165 23.7143 7.04396 24 7.87912 24C8.73626 24 9.43956 23.7253 9.98901 23.1758C10.5604 22.6044 10.8462 21.9011 10.8462 21.0659C10.8462 20.2088 10.5604 19.4945 9.98901 18.9231C9.41758 18.3516 8.71429 18.0659 7.87912 18.0659C7.04396 18.0659 6.35165 18.3516 5.8022 18.9231C5.25275 19.4725 4.97802 20.1758 4.97802 21.033ZM15.5275 7.51648C15.5275 9.07692 15.0769 10.4505 14.1758 11.6374C13.2967 12.8022 12.0769 13.7473 10.5165 14.4725L7.21978 16.2198L5.73626 12.6593L8.73626 11.0769C10.4066 10.022 11.2418 8.84615 11.2418 7.54945C11.2418 6.58242 10.9231 5.8022 10.2857 5.20879C9.64835 4.59341 8.83517 4.28571 7.84615 4.28571C5.93407 4.28571 4.64835 5.43956 3.98901 7.74725L0 6.2967C0.615385 4.34066 1.59341 2.8022 2.93407 1.68132C4.27473 0.56044 5.9011 0 7.81319 0C10.033 0 11.8681 0.714286 13.3187 2.14286C14.7912 3.54945 15.5275 5.34066 15.5275 7.51648Z" />
                                        </svg>
                                        Люди</a></li>
                                <li><a href="../profile"><svg width="28" height="24" viewBox="0 0 28 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.03709 11.3334C6.58858 12.0152 5.25423 12.9468 4.10051 14.1005C1.475 16.726 0 20.287 0 24L14 24H28C28 20.287 26.525 16.726 23.8995 14.1005C22.7458 12.9468 21.4114 12.0152 19.9629 11.3334C18.4981 12.97 16.3693 14 14 14C11.6307 14 9.50195 12.97 8.03709 11.3334Z" />
                                            <circle cx="14" cy="6" r="6" />
                                        </svg>
                                        Профиль
                                    </a></li>
                            </ul>
                        </nav>
                    </div>
                    <div class="third-part">
                        <?php
                        require('connect.php');
                        $sql = "SELECT posts.likes AS post_likes
                    FROM posts
                    JOIN users ON posts.user_id = users.id
                    WHERE posts.user_id = $other_id";
                        $result = $connect->query($sql);
                        $posts_count = $result->num_rows;
                        $likes_count = 0;
                        if ($posts_count > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $post_likes = $row["post_likes"];
                                $likes_count += $post_likes;
                            }
                        }
                        ?>
                        <div>
                            <div class="profile__posts">
                                <div>
                                    <img src="../pics/PostIcon.svg">
                                    <span> <?= $posts_count ?></span>
                                </div>
                                <p>посты</p>
                            </div>
                            <div class="profile__likes">
                                <div>
                                    <img src="../pics/LikeIcon.svg">
                                    <span><?= $likes_count ?></span>
                                </div>
                                <p>лайки</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
    </main>
<?php require('footer-2.php');
        } ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="../js/main.js"></script>
<script src="../js/profile.js"></script>
<script src="../js/otheruserprofile.js"></script>
</body>

</html>