<?php
session_start();

if (isset($_SESSION['user'])) {
    require_once('back-files/connect.php');
    require_once('back-files/global.php');
    require('back-files/find-user-position-in-top.php');
    require('back-files/get-user-friends.php');

    $current_user_id = $_SESSION['user']['id'];

    $friends_counter = $result_friend->num_rows;
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="stylesheet" href="css/main.css?v=302">
    <link rel="stylesheet" href="css/people.css?v=302">
    <link rel="stylesheet" href="css/chats.css?v=302">
    <title>Чаты в Рампус</title>
    <link rel="apple-touch-icon" sizes="180x180" href="favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicons/favicon-16x16.png">
    <link rel="manifest" href="favicons/site.webmanifest">
</head>

<body>
    <?php require_once('components/header.php'); ?>
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
                            <p>Чаты</p>
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
                                        <a class="important-chat" href="<?= $global_url ?>/chat/rampus">
                                            <img src="<?= $global_url ?>/pics/RabbitRampusAvatar.png" alt="">
                                            <div class="important-chat__user-info">
                                                <p class="main-name">Кролик Рампус</p>
                                                <span class="other-desc">Официальный аккаунт</span>
                                            </div>
                                        </a>
                                    </li>
                                    <div class="div-line"></div>
                                    <li>
                                        <a href="<?= $global_url ?>/chat/help" class="important-chat">
                                            <img src="<?= $global_url ?>/pics/HelpAvatar.jpg" alt="">
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
<?php require_once('components/footer.php');
        } ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="js/main.js?v=302"></script>
<script src="js/chats.js?v=302"></script>
<script src="js/chat.js?v=302"></script>
</body>

</html>