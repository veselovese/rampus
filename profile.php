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
        <section class="wrapper main-section">
            <nav>
                <ul>
                    <li><a>Профиль</a></li>
                    <li><a>Стена</a></li>
                    <li><a>Чаты</a></li>
                    <li><a>Люди</a></li>
                    <li><a>Выйти</a></li>
                </ul>
            </nav>
            <div></div>
            <div class="third-part profile__posts-and-likes">
                <div class="profile__posts"></div>
                <div class="profile__likes"></div>
            </div>
        </section>
    </main>
    <?php require('footer.php'); ?>
</body>

</html>