<?php
session_start();

if (isset($_SESSION['user'])) {
    require_once('back-files/connect.php');
    require_once('back-files/get-user-friends.php');
    $id = $_SESSION['user']['id'];
    $result = $connect->query("SELECT * FROM users WHERE id = $id");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $username = $row["username"];
            $first_name = $row["first_name"];
            $second_name = $row["second_name"];
            $current_avatar = $row["avatar"];
        }
    }

    $sql = "SELECT posts.likes AS post_likes
                    FROM posts
                    JOIN users ON posts.user_id = users.id
                    WHERE posts.user_id = $id";
    $sql_comment_counter = "SELECT comments.id
                    FROM comments 
                    JOIN posts ON comments.post_id = posts.id    
                    JOIN users ON users.id = posts.user_id
                    WHERE posts.user_id = $id";
    $sql_commented_counter = "SELECT comments.id
                    FROM comments
                    WHERE comments.user_id = $id";
    $sql_liked_counter = "SELECT likes_on_posts.id
                    FROM likes_on_posts
                    WHERE likes_on_posts.user_id = $id";
    $result = $connect->query($sql);
    $posts_count = $result->num_rows;
    $comment_count = $connect->query($sql_comment_counter)->num_rows;
    $commented_count = $connect->query($sql_commented_counter)->num_rows;
    $liked_count = $connect->query($sql_liked_counter)->num_rows;
    $likes_count = 0;
    if ($posts_count > 0) {
        while ($row = $result->fetch_assoc()) {
            $post_likes = $row["post_likes"];
            $likes_count += $post_likes;
        }
    }

    $blossom_level = $connect->query("SELECT blossom_level FROM users WHERE id = $id")->fetch_assoc()['blossom_level'];
    $blossom_progress = $connect->query("SELECT blossom_progress FROM users WHERE id = $id")->fetch_assoc()['blossom_progress'];
    $blossom = $blossom_level + $blossom_progress / 100;
    $user_level = intval($blossom);
    $user_progress = round($blossom - $user_level, 2) * 100;

    $sql_top = "SELECT id FROM users ORDER BY blossom_level DESC, blossom_progress DESC";
    $result_top = $connect->query($sql_top);
    $top_count = 0;
    if ($result_top->num_rows > 0) {
        while ($row = $result_top->fetch_assoc()) {
            $current_id = $row["id"];
            $top_count += 1;
            if ($current_id == $id) {
                break;
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="stylesheet" href="css/main.css?v=250">
    <link rel="stylesheet" href="css/profile.css?v=250">
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
                                    <div class="main-statistic"><span><?= $user_level ?></span><span>текущий уровень</span></div>
                                    <div class="main-statistic"><span><?= $top_count ?></span><span>место в рейтинге</span></div>
                                    <div class="main-statistic"><span><?= 100 - $user_progress ?>%</span><span>до <?= $user_level + 1 ?> уровня</span></div>
                                </div>
                                <div class="blossom-param">
                                    <div>
                                        <div class="current-param">
                                            <img src="pics/PostsIcon.svg">
                                            <p>Сделанные посты</p>
                                            <span><?= $posts_count ?></span>
                                        </div>
                                        <div class="current-param">
                                            <img src="pics/LikesIcon.svg">
                                            <p>Полученные лайки</p>
                                            <span><?= $likes_count ?></span>
                                        </div>
                                        <div class="current-param">
                                            <img src="pics/CommentsIcon.svg">
                                            <p>Полученные комментарии</p>
                                            <span><?= $comment_count ?></span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="current-param">
                                            <img src="pics/FriendsIcon.svg">
                                            <p>Друзья</p>
                                            <span><?= $result_friend->num_rows ?></span>
                                        </div>
                                        <div class="current-param">
                                            <img src="pics/LikedIcon.svg">
                                            <p>Поставленные лайки</p>
                                            <span><?= $liked_count ?></span>
                                        </div>
                                        <div class="current-param">
                                            <img src="pics/CommentedIcon.svg">
                                            <p>Оставленные комментарии</p>
                                            <span><?= $commented_count ?></span>
                                        </div>
                                    </div>
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
<script src="js/main.js?v=250"></script>
<script src="js/people.js?v=250"></script>
</body>

</html>