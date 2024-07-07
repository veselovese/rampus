<?php
session_start();
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/profile.css">
    <title>Профиль в Rampus (Рампус)</title>
</head>

<body>
    <?php require('header.php'); ?>
    <main>
        <h1 class="title">Профиль пользователя в Rampus (Рампус)</h1>
        <section class="wrapper main-section">
            <nav class="first-part">
                <ul>
                    <li class="active"><a href="./profile">Профиль</a></li>
                    <li><a href="./wall">Стена</a></li>
                    <li><a href="./chats">Чаты</a></li>
                    <li><a href="./people">Люди</a></li>
                    <li><a id="exit" href="./exit">Выйти</a></li>
                </ul>
            </nav>
            <div class="second-and-third-parts" >
                <div class="second-part">
                    <div class="profile__user-info">
                        <img class="avatar" src="uploads/avatar/noavatar.jpg">
                        <div>
                            <p class="first-and-second-names"><span>Матвей</span> <span>Веселов</span></p>
                            <p class="username">@veselovese</p>
                            <p class="description">Дикий и опасный - разнесу всё</p>
                            <div class="balance">
                                <img src="pics/EcoCoinLogo.svg">
                                <p>130.00</p>
                            </div>
                        </div>
                    </div>
                    <div class="profole__user-menu">
                        <span>Имя</span>
                        <span>ID</span>
                        <span>Описание</span>
                        <span>Почта</span>
                        <span>Пароль</span>
                    </div>
                </div>
                <div class="third-part">
                    <div class="profile__posts">
                        <div>
                            <img src="pics/PostIcon.svg">
                            <span>89</span>
                        </div>
                        <p>посты</p>
                    </div>
                    <div class="profile__likes">
                        <div>
                            <img src="pics/LikeIcon.svg">
                            <span>36</span>
                        </div>
                        <p>лайки</p>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php require('footer.php'); ?>
</body>

</html>