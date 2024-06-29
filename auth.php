<?php
session_start();
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="stylesheet" href="css/main.css">
    <title>Вход в Rampus</title>
</head>

<body>
    <?php require('header.php'); ?>
    <main>
        <h1 class="title">Вход в Rampus (Рампус)</h1>
        <section class="wrapper auth__section">
            <img src="pics/RampusLogo.svg">
            <div class="div-line"></div>
            <form method="POST" class="auth__form" action="./signin">
                <div>
                <legend>Вход Rampus</legend>
                <label>ID или почта<input type="text" required placeholder="rampus"></label>
                <label>Пароль<input type="text" required placeholder="********" minlength="8"></label>
                </div>
                <div>
                    <button type="submit">Войти</button>
                    <div class="auth__links">
                        <a href="./">Не помню пароль</a>
                        <a href="./reg">Создать аккаунт</a>
                    </div>
                </div>
            </form>
        </section>
    </main>
    <?php require('footer.php'); ?>
</body>

</html>