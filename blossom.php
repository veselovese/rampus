<?php
session_start();

if (isset($_SESSION['user'])) {
    require_once('back-files/connect.php');
    require_once('back-files/get-user-friends.php');
    require_once('back-files/find-user-position-in-top.php');

    $current_user_id = $_SESSION['user']['id'];

    $result_current_user_blossom = $connect->query("SELECT blossom_level, blossom_progress FROM users WHERE id = $current_user_id LIMIT 1");
    if ($result_current_user_blossom->num_rows > 0) {
        $row_current_user_blossom = $result_current_user_blossom->fetch_assoc();
        $current_user_blossom_level = $row_current_user_blossom["blossom_level"];
        $current_user_blossom_progress = $row_current_user_blossom["blossom_progress"];

        $current_user_blossom_progress_need = max(20, intval(($current_user_blossom_level - 1) * 1.6 * 20));
    }

    $sql_current_user_posts_and_likes_counter = "SELECT 
        (
            SELECT IFNULL(COUNT(*), 0)
            FROM likes_on_posts l
            JOIN posts p2 ON l.post_id = p2.id
            WHERE p2.user_id = p.user_id
                AND l.user_id != p.user_id
                AND p2.for_friends = 0
        ) AS current_user_likes_counter,
        (
            SELECT IFNULL(COUNT(*), 0)
            FROM reposts r
            JOIN posts p2 ON r.post_id = p2.id
            WHERE p2.user_id = p.user_id
                AND r.user_id != p.user_id
                AND p2.for_friends = 0
        ) AS current_user_reposts_counter,
        COUNT(DISTINCT p.id) AS current_user_posts_counter
    FROM posts p
    JOIN users u ON p.user_id = u.id
    WHERE p.user_id = $current_user_id
        AND p.for_friends = 0";
    $result_current_user_posts_and_likes_counter = $connect->query($sql_current_user_posts_and_likes_counter);
    if ($result_current_user_posts_and_likes_counter->num_rows > 0) {
        $row_current_user_posts_and_likes_counter = $result_current_user_posts_and_likes_counter->fetch_assoc();
        $current_user_posts_counter = $row_current_user_posts_and_likes_counter["current_user_posts_counter"];
        $current_user_likes_counter = $row_current_user_posts_and_likes_counter["current_user_likes_counter"];
        $current_user_reposts_counter = $row_current_user_posts_and_likes_counter["current_user_reposts_counter"];
    }

    $sql_current_user_comments_counter = "SELECT COUNT(*) AS comments_count
    FROM comments c
    JOIN posts p ON c.post_id = p.id
    JOIN users u ON p.user_id = u.id
    WHERE p.user_id = $current_user_id
        AND c.user_id != $current_user_id
        AND p.for_friends = 0";
    $result = $connect->query($sql_current_user_comments_counter);
    $current_user_comments_counter = $result->fetch_assoc()['comments_count'];

    $current_user_friends_counter = $result_friend->num_rows;

    $sql_current_user_commented_counter = "SELECT COUNT(DISTINCT c.id) AS commented_count
    FROM comments c
    JOIN posts p ON c.post_id = p.id
    JOIN users u ON c.user_id = u.id
    WHERE c.user_id = $current_user_id
        AND p.user_id != c.user_id
        AND p.for_friends = 0";
    $result = $connect->query($sql_current_user_commented_counter);
    $current_user_commented_counter = $result->fetch_assoc()['commented_count'];

    $sql_current_user_liked_counter = "SELECT COUNT(DISTINCT l.id) AS liked_count
    FROM likes_on_posts l
    JOIN posts p ON l.post_id = p.id
    JOIN users u ON p.user_id = u.id
    WHERE l.user_id = $current_user_id
        AND p.user_id != $current_user_id
        AND p.for_friends = 0";
    $result = $connect->query($sql_current_user_liked_counter);
    $current_user_liked_counter = $result->fetch_assoc()['liked_count'];

    $sql_current_user_reposted_counter = "SELECT COUNT(DISTINCT r.id) AS reposted_count
    FROM reposts r
    JOIN posts p ON r.post_id = p.id
    JOIN users u ON p.user_id = u.id
    WHERE r.user_id = $current_user_id
        AND p.user_id != $current_user_id
        AND p.for_friends = 0";
    $result = $connect->query($sql_current_user_reposted_counter);
    $current_user_reposted_counter = $result->fetch_assoc()['reposted_count'];

    $sql_current_user_likes_on_comments_counter = "SELECT COUNT(DISTINCT loc.id) AS likes_on_comments_count
    FROM comments c 
    JOIN likes_on_comments loc ON loc.comment_id = c.id 
    JOIN users u ON c.user_id = u.id
    JOIN posts p ON c.post_id = p.id
    WHERE c.user_id = $current_user_id
        AND loc.user_id != $current_user_id 
        AND p.for_friends = 0";
    $result = $connect->query($sql_current_user_likes_on_comments_counter);
    $current_user_likes_on_comments_counter = $result->fetch_assoc()['likes_on_comments_count'];

    $sql_current_user_liked_on_comments_counter = "SELECT COUNT(DISTINCT loc.id) AS liked_on_comments_count
    FROM likes_on_comments loc
    JOIN comments c ON c.id = loc.comment_id
    JOIN users u ON loc.user_id = u.id
    JOIN posts p ON c.post_id = p.id
    WHERE loc.user_id = $current_user_id
        AND c.user_id != $current_user_id
        AND p.for_friends = 0";
    $result = $connect->query($sql_current_user_liked_on_comments_counter);
    $current_user_liked_on_comments_counter = $result->fetch_assoc()['liked_on_comments_count'];

    $current_user_in_top = findUserPositionInTop($current_user_id, $connect);
}

?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="stylesheet" href="css/main.css?v=320">
    <link rel="stylesheet" href="css/profile.css?v=320">

    <title>Уровень цветения профиля в Рампусе</title>

    <meta property="og:title" content="Уровень цветения профиля в Рампусе" />

    <meta property="og:site_name" content="Рампус">
    <meta property="og:url" content="https://rampus.ru/blossom">

    <meta name="description" content="Узнайте своё Цветение, место в рейтинге и главные показатели активности" />
    <meta property="og:description" content="Узнайте своё Цветение, место в рейтинге и главные показатели активности" />

    <meta property="og:image" content="https://rampus.ru/pics/plugs/RampusMainPlug.png?v=320" />

    <link rel="apple-touch-icon" sizes="180x180" href="favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicons/favicon-16x16.png">
    <link rel="manifest" href="favicons/site.webmanifest">
</head>

<body>
    <?php require_once('components/header.php'); ?>
    <main>
        <h1 class="title">Уровень цветения профиля пользователя в Рампус</h1>
        <?php if (!isset($_SESSION['user'])) {
            header("Location: auth");
            exit();
        } else { ?>
            <section class="wrapper main-section">
                <?php require_once('components/back-menu.php'); ?>
                <div class="second-and-third-parts">
                    <div class="second-part">
                        <div class="user__blossom">
                            <h2>Уровень цветения</h2>
                            <div>
                                <p class="blossom-description">Уровень цветения профиля — это показатель вашей активности, который складывается из нескольких параметров</p>
                                <div class="main-statistic-div">
                                    <div class="main-statistic"><span><?= $current_user_blossom_level ?></span><span>текущий уровень</span></div>
                                    <div class="main-statistic"><span><?= $current_user_in_top ?></span><span>место в рейтинге</span></div>
                                    <div class="main-statistic"><span><?= $current_user_blossom_progress_need - $current_user_blossom_progress ?></span><span>до <?= $current_user_blossom_level + 1 ?> уровня</span></div>
                                </div>
                                <p class="section-title mobile">Последние изменения</p>
                                <ul class='chats_recent-chats mobile' id="success-blossom-notifications-widget-mobile">
                                </ul>
                                <p class="section-title">Ваши показатели</p>
                                <div class="blossom-param">
                                    <div>
                                        <div class="current-param">
                                            <img src="pics/PostsIcon.svg">
                                            <p>Сделанные посты</p>
                                            <span><?= $current_user_posts_counter ?></span>
                                        </div>
                                        <div class="current-param">
                                            <img src="pics/LikesIcon.svg">
                                            <p>Полученные лайки</p>
                                            <span><?= $current_user_likes_counter ?></span>
                                        </div>
                                        <div class="current-param">
                                            <img src="pics/CommentsIcon.svg">
                                            <p>Полученные комментарии</p>
                                            <span><?= $current_user_comments_counter ?></span>
                                        </div>
                                        <div class="current-param">
                                            <img src="pics/RepostsIconBlossom.svg">
                                            <p>Полученные репосты</p>
                                            <span><?= $current_user_reposts_counter ?></span>
                                        </div>
                                        <div class="current-param">
                                            <!-- <img src="pics/RepostsIconBlossom.svg"> -->
                                            <p>Полученные лайки на комментарии</p>
                                            <span><?= $current_user_likes_on_comments_counter ?></span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="current-param">
                                            <img src="pics/FriendsIcon.svg">
                                            <p>Друзья</p>
                                            <span><?= $current_user_friends_counter ?></span>
                                        </div>
                                        <div class="current-param">
                                            <img src="pics/LikedIcon.svg">
                                            <p>Поставленные лайки</p>
                                            <span><?= $current_user_liked_counter ?></span>
                                        </div>
                                        <div class="current-param">
                                            <img src="pics/CommentedIcon.svg">
                                            <p>Оставленные комментарии</p>
                                            <span><?= $current_user_commented_counter ?></span>
                                        </div>
                                        <div class="current-param">
                                            <img src="pics/RepostedIconBlossom.svg">
                                            <p>Сделанные репосты</p>
                                            <span><?= $current_user_reposted_counter ?></span>
                                        </div>
                                        <div class="current-param">
                                            <!-- <img src="pics/RepostedIconBlossom.svg"> -->
                                            <p>Поставленные лайки на комментарии</p>
                                            <span><?= $current_user_liked_on_comments_counter ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php require_once('components/mobile-main-menu.php') ?>
                    </div>
                    <div class="third-part">
                        <div>
                            <p class="third-part-title">Последние изменения</p>
                            <ul class='chats_recent-chats' id="success-blossom-notifications-widget">
                            </ul>
                        </div>
                    </div>
            </section>
    </main>
<?php require_once('components/footer.php');
        } ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="js/main.js?v=320"></script>
</body>

</html>