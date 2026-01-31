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
    }

    $sql_current_user_posts_and_likes_counter = "SELECT IF(SUM(posts.likes), SUM(posts.likes), 0) AS current_user_likes_counter, IF(SUM(posts.reposts), SUM(posts.reposts), 0) AS current_user_reposts_counter, COUNT(*) AS current_user_posts_counter
                    FROM posts
                    JOIN users ON posts.user_id = users.id
                    WHERE posts.user_id = $current_user_id";
    $result_current_user_posts_and_likes_counter = $connect->query($sql_current_user_posts_and_likes_counter);
    if ($result_current_user_posts_and_likes_counter->num_rows > 0) {
        $row_current_user_posts_and_likes_counter = $result_current_user_posts_and_likes_counter->fetch_assoc();
        $current_user_posts_counter = $row_current_user_posts_and_likes_counter["current_user_posts_counter"];
        $current_user_likes_counter = $row_current_user_posts_and_likes_counter["current_user_likes_counter"];
        $current_user_reposts_counter = $row_current_user_posts_and_likes_counter["current_user_reposts_counter"];
    }

    $sql_current_user_comments_counter = "SELECT 1
                    FROM comments 
                    JOIN posts ON comments.post_id = posts.id    
                    WHERE posts.user_id = $current_user_id";
    $current_user_comments_counter = $connect->query($sql_current_user_comments_counter)->num_rows;

    $current_user_friends_counter = $result_friend->num_rows;

    $sql_current_user_commented_counter = "SELECT 1
                    FROM comments
                    WHERE comments.user_id = $current_user_id";
    $current_user_commented_counter = $connect->query($sql_current_user_commented_counter)->num_rows;

    $sql_current_user_liked_counter = "SELECT 1
                    FROM likes_on_posts
                    WHERE likes_on_posts.user_id = $current_user_id";
    $current_user_liked_counter = $connect->query($sql_current_user_liked_counter)->num_rows;

    $sql_current_user_reposted_counter = "SELECT 1
                    FROM reposts
                    WHERE reposts.user_id = $current_user_id";
    $current_user_reposted_counter = $connect->query($sql_current_user_reposted_counter)->num_rows;

    $current_user_in_top = findUserPositionInTop($current_user_id, $connect);
}

?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="stylesheet" href="css/main.css?v=300before">
    <link rel="stylesheet" href="css/profile.css?v=300before">
    <title>Уровень цветения профиля пользователя в Рампус</title>
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
                                    <div class="main-statistic"><span><?= 100 - $current_user_blossom_progress ?>%</span><span>до <?= $current_user_blossom_level + 1 ?> уровня</span></div>
                                </div>
                                <p class="section-title">Последние изменения</p>
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
                                            <img src="pics/CommentsIcon.svg">
                                            <p>Полученные репосты</p>
                                            <span><?= $current_user_reposts_counter ?></span>
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
                                            <img src="pics/CommentedIcon.svg">
                                            <p>Сделанные репосты</p>
                                            <span><?= $current_user_reposted_counter ?></span>
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
<script src="js/main.js?v=300before"></script>
</body>

</html>