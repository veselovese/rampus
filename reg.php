<?php
session_start();
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="stylesheet" href="css/main.css?v=250">
    <title>Регистрация в Rampus</title>
    <link rel="apple-touch-icon" sizes="180x180" href="favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicons/favicon-16x16.png">
    <link rel="manifest" href="favicons/site.webmanifest">
</head>

<body>
    <h1 class="title">Регистрация в Rampus (Рампус)</h1>
    <?php if (isset($_SESSION['user'])) {
        header("Location: profile");
        exit();
    } ?>
    <?php require('header.php'); ?>
    <main>
        <section class="wrapper reg__section">
            <form method="POST" class="reg__form" action="./back-files/sign-up" autocomplete="off">
                <legend>Регистрация Rampus</legend>
                <div>
                    <div class="reg__input-div">
                        <label>Имя<input type="text" required placeholder="Рампус" name="first_name" id="reg__first_name"></label>
                        <label>Фамилия<input type="text" required placeholder="Рампусов" name="second_name" id="reg__second_name"></label>
                        <label id="reg__lable_id">ID (имя пользователя)<input type="text" required placeholder="rampus" name="username" id="reg__id">
                            <div><span id="reg__id_on-or-off">Такой ID свободен</span></div>
                        </label>
                        <a href="./auth" class="desktop">У меня есть аккаунт</a>
                    </div>
                    <div class="div-line"></div>
                    <div class="reg__input-div">
                        <label id="reg__lable_email">Почта<input type="email" required placeholder="rampus@example.com" name="email" id="reg__email">
                            <div><span id="reg__email_on-or-off">Такая почта свободна</span></div>
                        </label>
                        <label id="reg__label_pass-1">Пароль
                            <input type="password" required placeholder="********" name="password_1" id="reg__password_1" minlength="8">
                            <div><span id="reg__8-sim">8 символов</span><span id="reg__num">Цифра</span><span id="reg__!?">! или ?</span></div>
                        </label>
                        <label id="reg__label_pass-2"><input type="password" required placeholder="Тот же пароль ещё раз" minlength="8" name="password_2" id="reg__password_2"></label>
                        <button type="submit" class="" id="reg__submit-button">Создать</button>
                        <a href="./auth" class="mobile reg__link">У меня есть аккаунт</a>
                    </div>
                </div>
            </form>
        </section>
    </main>
    <?php require('footer.php'); ?>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="js/reg.js?v=250"></script>
</body>

</html>