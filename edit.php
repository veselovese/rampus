<?php
session_start();

if (isset($_SESSION['user'])) {
    require_once('back-files/connect.php');

    $current_user_id = $_SESSION['user']['id'];
    $current_user_username = $_SESSION['user']['username'];
    $current_user_first_name = $_SESSION['user']['first_name'];
    $current_user_second_name = $_SESSION['user']['second_name'];
    $current_user_description = $_SESSION['user']['description'];
    $current_user_avatar = $_SESSION['user']['avatar'];

    $result_current_user_blossom = $connect->query("SELECT blossom_level, blossom_progress FROM users WHERE id = $current_user_id LIMIT 1");
    if ($result_current_user_blossom->num_rows > 0) {
        $row_current_user_blossom = $result_current_user_blossom->fetch_assoc();
        $current_user_blossom_level = $row_current_user_blossom["blossom_level"];
        $current_user_blossom_progress = $row_current_user_blossom["blossom_progress"];
    }
} ?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="stylesheet" href="css/main.css?v=300before">
    <link rel="stylesheet" href="css/profile.css?v=300before">
    <link rel="stylesheet" href="css/edit.css?v=300before">
    <title>Редактирование профиля в Рампус</title>
    <link rel="apple-touch-icon" sizes="180x180" href="favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicons/favicon-16x16.png">
    <link rel="manifest" href="favicons/site.webmanifest">
</head>

<body>
    <?php require_once('components/header.php'); ?>
    <main>
        <h1 class="title">Редактирование профиля пользователя в Рампус</h1>
        <?php if (!isset($_SESSION['user'])) {
            header("Location: auth");
            exit();
        } else { ?>
            <section class="wrapper main-section">
                <?php require_once('components/back-menu.php'); ?>
                <div class="second-and-third-parts">
                    <div class="second-part">
                        <div class="profile__user-info">
                            <form action="./back-files/edit-profile" method="post" enctype="multipart/form-data" class="edit__form" autocomplete='off'>
                                <div class="edit__user-avatar">
                                    <img class="avatar edit" id="current-avatar" src="uploads/avatar/small_<?= $current_user_avatar ?>">
                                    <div class="edit__upload-avatar">
                                        <input type="file" name="avatar" id="user-avatar">
                                    </div>
                                    <span onclick="uploadAvatar()">Обновить изображение</span>
                                </div>
                                <div class="edit__user-info">
                                    <div class="user-first-and-second-names">
                                        <label>
                                            Имя
                                            <input type="text" class="" name="first-name" value="<?= $current_user_first_name ?>" require>
                                        </label>
                                        <label>
                                            Фамилия
                                            <input type="text" class="" name="second-name" value="<?= $current_user_second_name ?>" require>
                                        </label>
                                    </div>
                                    <label>
                                        Описание
                                        <input type="text" class="" name="description" value="<?= $current_user_description ?>" require>
                                    </label>
                                    <?php if ($current_user_blossom_level >= 2) { ?>
                                        <label id="reg__lable_id">
                                            ID (имя пользователя)
                                            <input type="text" class="" name="username" value="<?= $current_user_username ?>" id="reg__id" data-username="<?= $current_user_username ?>" require>
                                            <span class="at">@</span>
                                            <div><span id="reg__id_on-or-off">Такой ID свободен</span></div>
                                        </label>
                                    <?php } else { ?>
                                        <label id="reg__lable_id" class="blocked">
                                            ID (доступно со 2 уровня)
                                            <input type="text" class="" value="<?= $current_user_username ?>" id="reg__id">
                                            <span class="at">@</span>
                                        </label>
                                    <?php } ?>
                                </div>
                                <button type="submit" name="set_avatar">Сохранить</button>
                            </form>
                        </div>
                        <?php require_once('components/mobile-main-menu.php') ?>
                    </div>
                </div>
                <div class="third-part">
                    <div>
                        <p class="third-part-title">Настройки</p>
                        <div class='edit-filter__choice'>
                            <label class="edit-filter-popup-li" id="edit-filter-top">Персональные<span>Информация о вас</span><input checked name="people-filter" type="radio" value=""></label>
                        </div>
                    </div>
                </div>
            </section>
    </main>
<?php require_once('components/footer.php');
        } ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="js/main.js?v=300before"></script>
<script src="js/edit.js?v=300before"></script>
</body>

</html>