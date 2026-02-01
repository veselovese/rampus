<?php
session_start();

if (isset($_SESSION['user'])) {
    require_once('back-files/connect.php');
    require('back-files/find-user-position-in-top.php');
    require('back-files/get-user-friends.php');
    require('back-files/get-trophy-list.php');

    $current_user_id = $_SESSION['user']['id'];
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="stylesheet" href="css/main.css?v=301">
    <link rel="stylesheet" href="css/trophy.css?v=301">
    <title>Полка с трофеяими и ачивками в Рампус</title>
    <link rel="apple-touch-icon" sizes="180x180" href="favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicons/favicon-16x16.png">
    <link rel="manifest" href="favicons/site.webmanifest">
</head>

<body>
    <?php require_once('components/header.php'); ?>
    <main>
        <h1 class="title">Полка с трофеяими и ачивками в Рампус</h1>
        <?php if (!isset($_SESSION['user'])) {
            header("Location: auth?request=case");
            exit();
        } else { ?>
            <section class="wrapper main-section">
                <?php require_once('components/main-menu.php'); ?>
                <div class="second-and-third-parts">
                    <div class="second-part">
                        <div class="">
                            <p class="main-title">Трофеи</p>
                            <p class='section-title rating'>Рейтинг пользователей</p>
                            <div class='rating-trophies-div'>
                                <div class='trophy-list'>
                                    <?php
                                    $list = getTrophyList();
                                    if ($list->num_rows > 0) {
                                        while ($row = $list->fetch_assoc()) {
                                            $trophy_id = $row['id'];
                                            $trophy_name = $row['name'];
                                            $trophy_short_desc = $row['short_description'];
                                            $trophy_desc = $row['description'];
                                            $trophy_image = $row['image'];
                                            $trophy_date = $row['get_date'];
                                            $user_first_name = $row['first_name'];
                                            $user_second_name = $row['second_name'];
                                            $current_user_id = $row['user_id'];
                                            $user_username = $row['username'];
                                            $user_avatar = $row['avatar'];
                                            $user_level = $row['blossom_level'];
                                            if ($trophy_id < 4) {
                                                $result = $connect->query("SELECT posts.likes AS post_likes, posts.reposts AS post_repost FROM posts JOIN users ON posts.user_id = users.id WHERE posts.user_id = $current_user_id");
                                                $comment_count = $connect->query("SELECT comments.id FROM comments JOIN posts ON comments.post_id = posts.id JOIN users ON users.id = posts.user_id WHERE posts.user_id = $current_user_id")->num_rows;
                                                $posts_count = $result->num_rows;
                                                $likes_count = 0;
                                                $reposts_count = 0;
                                                if ($posts_count > 0) {
                                                    while ($row = $result->fetch_assoc()) {
                                                        $post_likes = $row["post_likes"];
                                                        $post_repost = $row["post_repost"];
                                                        $likes_count += $post_likes;
                                                        $reposts_count += $post_repost;
                                                    }
                                                }
                                                $likes_count = (string)$likes_count;
                                                $reposts_count = (string)$reposts_count;
                                                $posts_count = (string)$posts_count;
                                                $comment_count = (string)$comment_count;
                                                echo "<div class='current-trophy'>
                                                <div class='trophy-info'>
                                                <img class='icon' src='$trophy_image'>
                                                <div class='current-trophy-info'>
                                                <p class='name'>$trophy_name</p>
                                                <p class='desc'>$trophy_short_desc</p>
                                                </div>
                                                </div>
                                                <div class='user-statistic'>Это";
                                                echo "<a href='./users' class='current-static blossom-level'><img src='pics/BlossomIcon.svg'>" . $user_level . " уровень</a>:";
                                                if (($posts_count[-1] == '1') && (!isset($posts_count[-2]) || $posts_count[-2] != '1')) {
                                                    echo "<span class='current-static post'>" . $posts_count . " пост</span>,";
                                                } else if (($posts_count[-1] == '2' || $posts_count[-1] == '3' || $posts_count[-1] == '4') && (!isset($posts_count[-2]) || $posts_count[-2] != '1')) {
                                                    echo "<span class='current-static post'>" . $posts_count . " поста</span>,";
                                                } else {
                                                    echo "<span class='current-static post'>" . $posts_count . " постов</span>,";
                                                }
                                                if (($likes_count[-1] == '1') && (!isset($likes_count[-2]) || $likes_count[-2] != '1')) {
                                                    echo "<span class='current-static like'>" . $likes_count . " лайк</span>и";
                                                } else if (($likes_count[-1] == '2' || $likes_count[-1] == '3' || $likes_count[-1] == '4') && (!isset($likes_count[-2]) || $likes_count[-2] != '1')) {
                                                    echo "<span class='current-static like'>" . $likes_count . " лайка</span>,";
                                                } else {
                                                    echo "<span class='current-static like'>" . $likes_count . " лайков</span>,";
                                                }
                                                if (($comment_count[-1] == '1') && (!isset($comment_count[-2]) || $comment_count[-2] != '1')) {
                                                    echo "<span class='current-static comment'>" . $comment_count . " комментарий</span>";
                                                } else if (($comment_count[-1] == '2' || $comment_count[-1] == '3' || $comment_count[-1] == '4') && (!isset($comment_count[-2]) || $comment_count[-2] != '1')) {
                                                    echo "<span class='current-static comment'>" . $comment_count . " комментария</span>и";
                                                } else {
                                                    echo "<span class='current-static comment'>" . $comment_count . " комментариев</span>и";
                                                }
                                                if (($reposts_count[-1] == '1') && (!isset($reposts_count[-2]) || $reposts_count[-2] != '1')) {
                                                    echo "<span class='current-static repost'>" . $reposts_count . " репост</span>";
                                                } else if (($reposts_count[-1] == '2' || $reposts_count[-1] == '3' || $reposts_count[-1] == '4') && (!isset($reposts_count[-2]) || $reposts_count[-2] != '1')) {
                                                    echo "<span class='current-static repost'>" . $reposts_count . " репоста</span>";
                                                } else {
                                                    echo "<span class='current-static repost'>" . $reposts_count . " репостов</span>";
                                                }
                                                echo "</div>
                                                <div class='user-trophy-info'>
                                                    <a href='./user/$user_username'>
                                                    <img src='uploads/avatar/small_$user_avatar'>
                                                    </a>
                                                    <div class='more-user-trophy-info'>";
                                                if ($user_first_name || $user_second_name) {
                                                    echo "<a href='./user/$user_username'>$user_first_name $user_second_name</a>";
                                                } else {
                                                    echo "<a href='./user/$user_username'>@$user_username</a>";
                                                }
                                                echo "<span class='date'>владеет <br class='br-mobile'>с $trophy_date</span>
                                                    </div>
                                                </div>
                                                </div>";
                                            } ?>
                                    <?php }
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class=''>
                                <p class='section-title'>Основные показатели</p>
                                <div class='trophy-list'>
                                    <?php
                                    $list = getTrophyList();
                                    if ($list->num_rows > 0) {
                                        while ($row = $list->fetch_assoc()) {
                                            $trophy_id = $row['id'];
                                            $trophy_name = $row['name'];
                                            $trophy_short_desc = $row['short_description'];
                                            $trophy_desc = $row['description'];
                                            $trophy_desc = explode("*", $trophy_desc);
                                            $trophy_stat = $row['stat_number'];
                                            $trophy_image = $row['image'];
                                            $trophy_date = $row['get_date'];
                                            $trophy_link = $row['link'];
                                            $user_first_name = $row['first_name'];
                                            $user_second_name = $row['second_name'];
                                            $current_user_id = $row['user_id'];
                                            $user_username = $row['username'];
                                            $user_avatar = $row['avatar'];
                                            if ($trophy_id > 3) {
                                                echo "<div class='current-trophy'>
                                                <div class='trophy-info'>
                                                <img class='icon' src='$trophy_image'>
                                                <div class='current-trophy-info'>
                                                <p class='name'>$trophy_name</p>
                                                <p class='desc'>$trophy_short_desc</p>
                                                </div>
                                                </div>
                                                <div class='user-statistic'>";
                                                if (($trophy_stat[-1] == '1') && (!isset($trophy_stat[-2]) || $trophy_stat[-2] != '1')) {
                                                    $trophy_i = 1;
                                                } else if (($trophy_stat[-1] == '2' || $trophy_stat[-1] == '3' || $trophy_stat[-1] == '4') && (!isset($trophy_stat[-2]) || $trophy_stat[-2] != '1')) {
                                                    $trophy_i = 2;
                                                } else {
                                                    $trophy_i = 3;
                                                }
                                                if ($trophy_link) {
                                                    $trophy_last_peace = "<a href='$trophy_link'>$trophy_desc[4]</a>";
                                                } else {
                                                    $trophy_last_peace = "$trophy_desc[4]";
                                                }
                                                echo "<p class='current-desc'>$trophy_desc[0] <span>$trophy_stat $trophy_desc[$trophy_i]</span> $trophy_last_peace</p>
                                                </div>
                                                <div class='user-trophy-info'>
                                                    <a href='./user/$user_username'>
                                                    <img src='uploads/avatar/small_$user_avatar'>
                                                    </a>
                                                    <div class='more-user-trophy-info'>";
                                                if ($user_first_name || $user_second_name) {
                                                    echo "<a href='./user/$user_username'>$user_first_name $user_second_name</a>";
                                                } else {
                                                    echo "<a href='./user/$user_username'>@$user_username</a>";
                                                }
                                                echo "<span class='date'>владеет <br class='br-mobile'>с $trophy_date</span>
                                                    </div>
                                                </div>
                                                </div>";
                                            } ?>
                                    <?php }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php require_once('components/mobile-main-menu.php') ?> 
                    </div>
                    <div class="third-part">
                    </div>
            </section>
    </main>
<?php require_once('components/footer.php');
        } ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="js/main.js?v=301"></script>
<script src="js/people.js?v=301"></script>
</body>

</html>