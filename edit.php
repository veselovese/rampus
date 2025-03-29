<?php
session_start();

require_once('back-files/connect.php');

if (isset($_SESSION['user'])) {
    $id = $_SESSION['user']['id'];
    $result = $connect->query("SELECT * FROM users WHERE id = $id");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $username = $row["username"];
            $email = $row["email"];
            $description = $row["description"];
            $first_name = $row["first_name"];
            $second_name = $row["second_name"];
            $current_avatar = $row["avatar"];
            $level = $row["blossom_level"];
        }
    }
} ?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="stylesheet" href="css/main.css?v=250">
    <link rel="stylesheet" href="css/profile.css?v=250">
    <link rel="stylesheet" href="css/edit.css?v=250">
    <title>Редактирование профиля в Rampus (Рампус)</title>
    <link rel="apple-touch-icon" sizes="180x180" href="favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicons/favicon-16x16.png">
    <link rel="manifest" href="favicons/site.webmanifest">
</head>

<body>
    <?php require('header.php'); ?>
    <main>
        <h1 class="title">Редактирование профиля пользователя в Rampus (Рампус)</h1>
        <?php if (!isset($_SESSION['user'])) {
            header("Location: auth");
            exit();
        } else { ?>
            <section class="wrapper main-section">
                <nav class="first-part">
                    <ul>
                        <li><a id="back" href="./profile"><svg width="8" height="13" viewBox="0 0 8 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.96771 6.03603L1.12165 0.191904C0.865127 -0.0639698 0.449521 -0.0639698 0.192352 0.191904C-0.0641698 0.447777 -0.0641699 0.863383 0.192352 1.11926L5.57471 6.49968L0.192999 11.8801C-0.0635223 12.136 -0.0635224 12.5516 0.192999 12.8081C0.44952 13.064 0.865774 13.064 1.1223 12.8081L6.96836 6.96403C7.22094 6.7108 7.22094 6.28866 6.96771 6.03603Z" />
                                </svg>
                                Назад</a></li>
                    </ul>
                </nav>
                <div class="second-and-third-parts">
                    <div class="second-part">
                        <div class="profile__user-info">
                            <form action="./back-files/edit-profile" method="post" enctype="multipart/form-data" class="edit__form" autocomplete='off'>
                                <div class="edit__user-avatar">
                                    <img class="avatar edit" id="current-avatar" src="uploads/avatar/small_<?= $avatar ?>">
                                    <div class="edit__upload-avatar">
                                        <input type="file" name="avatar" id="user-avatar">
                                    </div>
                                    <span onclick="uploadAvatar()">Обновить изображение</span>
                                </div>
                                <div class="edit__user-info">
                                    <div class="user-first-and-second-names">
                                        <label>
                                            Имя
                                            <input type="text" class="" name="first-name" value="<?= $first_name ?>" require>
                                        </label>
                                        <label>
                                            Фамилия
                                            <input type="text" class="" name="second-name" value="<?= $second_name ?>" require>
                                        </label>
                                    </div>
                                    <label>
                                        Описание
                                        <input type="text" class="" name="description" value="<?= $description ?>" require>
                                    </label>
                                    <?php if ($level >= 2) { ?>
                                        <label id="reg__lable_id">
                                            ID (имя пользователя)
                                            <input type="text" class="" name="username" value="<?= $username ?>" id="reg__id" data-username="<?= $username ?>" require>
                                            <span class="at">@</span>
                                            <div><span id="reg__id_on-or-off">Такой ID свободен</span></div>
                                        </label>
                                    <?php } else { ?>
                                        <label id="reg__lable_id" class="blocked">
                                            ID (доступно со 2 уровня)
                                            <input type="text" class="" value="<?= $username ?>" id="reg__id">
                                            <span class="at">@</span>
                                        </label>
                                    <?php } ?>
                                </div>
                                <button type="submit" name="set_avatar">Сохранить</button>
                            </form>
                        </div>
                        <nav class="first-part-mobile">
                            <ul>
                                <li>
                                    <a href="./wall">
                                        <svg width="28" height="24" viewBox="0 0 28 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M20.9219 4C20.4586 1.71776 18.4408 0 16.0219 0H4.99997C2.23855 0 -2.28882e-05 2.23858 -2.28882e-05 5V18H5V11C5 7.13401 8.13401 4 12 4H20.9219Z" />
                                            <path d="M7 11C7 8.23858 9.23858 6 12 6H23C25.7614 6 28 8.23858 28 11V24H12C9.23858 24 7 21.7614 7 19V11Z" />
                                        </svg>
                                        Стена
                                    </a>
                                </li>
                                <li>
                                    <a href="./people">
                                        <svg width='28' height='24' viewBox='0 0 28 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                            <path d='M15 4.49829C12.651 5.47785 11 7.79617 11 10.5001C11 11.397 11.1816 12.2514 11.5102 13.0287C10.4728 13.595 9.51192 14.3105 8.66116 15.1613C7.81316 16.0093 7.09958 16.9666 6.53414 18.0001L0 18C0 15.2153 1.10625 12.5446 3.07538 10.5754C4.33742 9.31339 5.88765 8.4058 7.5714 7.91672C6.60943 7.09142 6 5.86688 6 4.5C6 2.01472 8.01472 0 10.5 0C12.9847 0 14.9991 2.01379 15 4.49829Z' />
                                            <path d='M21.25 10.5001C21.25 12.5712 19.5711 14.2501 17.5 14.2501C15.4289 14.2501 13.75 12.5712 13.75 10.5001C13.75 8.42905 15.4289 6.75012 17.5 6.75012C19.5711 6.75012 21.25 8.42905 21.25 10.5001ZM10.6057 17.1058C11.2822 16.4293 12.0479 15.8625 12.8752 15.4168C14.0826 16.5528 15.7103 17.2501 17.5 17.2501C19.2897 17.2501 20.9174 16.5528 22.1248 15.4168C22.9521 15.8625 23.7177 16.4293 24.3943 17.1058C26.0452 18.7567 27.0429 20.9386 27.2211 23.2501H17.5L7.77887 23.2501C7.95711 20.9386 8.95483 18.7567 10.6057 17.1058Z' stroke-linecap='round' stroke-linejoin='round' />
                                        </svg>
                                        Люди
                                    </a>
                                </li>
                                <li>
                                    <a href="./trophy">
                                        <svg width="28" height="24" viewBox="0 0 28 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.66113 15.5149C7.88196 16.2628 7.21631 17.0995 6.67468 18H0C0 15.2153 1.1062 12.5446 3.07532 10.5754C4.3374 9.31335 5.8877 8.40576 7.57141 7.91675C6.60938 7.09143 6 5.86682 6 4.5C6 2.01477 8.01477 0 10.5 0C12.9852 0 15 2.01477 15 4.5C15 4.6897 14.9883 4.87659 14.9655 5.06018C12.9185 6.0155 11.5 8.09216 11.5 10.5001C11.5 11.458 11.7245 12.3635 12.1237 13.1667C10.8506 13.749 9.67737 14.5393 8.66113 15.5149ZM22 10.5001C22 12.9854 19.9852 15.0001 17.5 15.0001C15.0148 15.0001 13 12.9854 13 10.5001C13 8.01489 15.0148 6.00012 17.5 6.00012C19.9852 6.00012 22 8.01489 22 10.5001ZM13.0278 14.5001C11.9414 15.0116 10.9407 15.7102 10.0753 16.5754C8.1062 18.5447 7 21.2153 7 24.0001H17.5H28C28 21.2153 26.8937 18.5447 24.9246 16.5754C24.0593 15.7102 23.0586 15.0116 21.9722 14.5001C20.8735 15.7277 19.277 16.5001 17.5 16.5001C15.723 16.5001 14.1265 15.7277 13.0278 14.5001Z" />
                                        </svg>
                                        Трофеи
                                    </a>
                                </li>
                                <li>
                                    <a href="./profile">
                                        <!-- <svg width="28" height="24" viewBox="0 0 28 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.03709 11.3334C6.58858 12.0152 5.25423 12.9468 4.10051 14.1005C1.475 16.726 0 20.287 0 24L14 24H28C28 20.287 26.525 16.726 23.8995 14.1005C22.7458 12.9468 21.4114 12.0152 19.9629 11.3334C18.4981 12.97 16.3693 14 14 14C11.6307 14 9.50195 12.97 8.03709 11.3334Z" />
                                            <circle cx="14" cy="6" r="6" />
                                        </svg> -->
                                        <img class="menu-avatar" src="uploads/avatar/thin_<?= $current_avatar ?>">
                                        Профиль
                                    </a>
                                </li>
                            </ul>
                        </nav>
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
<?php require('footer.php');
        } ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="js/main.js?v=250"></script>
<script src="js/edit.js?v=250"></script>
</body>

</html>