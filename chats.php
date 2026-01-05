<?php
session_start();

require_once('back-files/connect.php');

if (isset($_SESSION['user'])) {
    require('back-files/rating-trophies.php');
    require('back-files/find-user-position-in-top.php');
    require('back-files/get-user-friends.php');

    $user_id = $_SESSION['user']['id'];
    $result = $connect->query("SELECT * FROM users WHERE id = $user_id");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $current_username = $row["username"];
            $current_first_name = $row["first_name"];
            $current_second_name = $row["second_name"];
            $current_avatar = $row["avatar"];
        }
    }

    $user_in_top = findUserPositionInTop($user_id, $connect);
    $unread_posts = $_SESSION['user']['unread_posts'];
    $user_level = $connect->query("SELECT blossom_level FROM users WHERE id = '$user_id'")->fetch_assoc()['blossom_level'];
}

$friends_counter = $result_friend->num_rows;
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="stylesheet" href="css/main.css?v=250">
    <link rel="stylesheet" href="css/people.css?v=250">
    <link rel="stylesheet" href="css/chats.css?v=250">
    <title>Чаты в Рампус</title>
    <link rel="apple-touch-icon" sizes="180x180" href="favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicons/favicon-16x16.png">
    <link rel="manifest" href="favicons/site.webmanifest">
</head>

<body>
    <?php require('header.php'); ?>
    <main>
        <h1 class="title">Чаты с пользователями в Рампус</h1>
        <?php if (!isset($_SESSION['user'])) {
            header("Location: auth?request=people");
            exit();
        } else { ?>
            <section class="wrapper main-section">
                <?php require_once('components/main-menu.php'); ?>
                <div class="second-and-third-parts">
                    <div class="second-part">
                        <div class="chats all-chats">
                            <p>Чаты<span><?= $friends_counter ?></span></p>
                            <div class="search-chats-div">
                                <input type="text" name="search-chats" id="search-chats" placeholder="Рампус Кроликович">
                                <input type="hidden" name="current-user-id" id="currentuserid_input" value="<?= $current_user_id ?>">
                                <img id="icon-search-people" src="pics/SearchIcon.svg">
                            </div>
                            <ul id="success-search-chats">
                            </ul>
                        </div>
                        <?php require_once('components/mobile-main-menu.php') ?>
                    </div>
                    <div class="third-part">
                        <div>
                            <div>
                                <p class="third-part-title">Нужные чаты</p>
                                <ul class='chats_important-chats'>
                                    <li>
                                        <a class="important-chat" href="http://localhost/rampus/chat/rampus">
                                            <img src="http://localhost/rampus/pics/RabbitRampusAvatar.png" alt="">
                                            <div class="important-chat__user-info">
                                                <p class="main-name">Кролик Рампус</p>
                                                <span class="other-desc">Официальный аккаунт</span>
                                            </div>
                                        </a>
                                    </li>
                                    <div class="div-line"></div>
                                    <li>
                                        <a href="http://localhost/rampus/chat/help" class="important-chat">
                                            <img src="http://localhost/rampus/pics/HelpAvatar.jpg" alt="">
                                            <div class="important-chat__user-info">
                                                <p class="main-name">Поддержка</p>
                                                <span class="other-desc">По всем вопросам</span>
                                            </div>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
            </section>
    </main>
<?php require('footer.php');
        } ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="js/main.js?v=250"></script>
<script src="js/chats.js?v=250"></script>
<script src="js/chat.js?v=250"></script>
</body>

</html>