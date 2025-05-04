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
        <section class="wrapper">
            <div class="reg__center">
                <div class="reg__section">
                    <div class="auth__notify" id="auth__notify">
                        <p><span id="auth__notify-label">Вы авторизировались под пользователем </span><span id="auth__notify-username"></span></p>
                    </div>
                    <div>
                        <img src="pics/RampusLogo.svg">
                        <!-- <a href="./auth" class="desktop reg__link">У меня есть аккаунт</a> -->
                    </div>
                    <form method="POST" class="reg__form" action="./back-files/sign-up" autocomplete="off">
                        <legend class="title">Регистрация Rampus</legend>
                        <div class="reg__input-div">
                            <label id="reg__lable_email">Почта<input type="email" required placeholder="rampus@example.com" name="email" id="reg__email" class="auth-and-reg">
                                <div><span id="reg__email_on-or-off">Такая почта свободна</span></div>
                            </label>
                            <label id="reg__lable_id">Логин<input type="text" required placeholder="rampus" name="username" id="reg__id" class="auth-and-reg">
                                <div><span id="reg__id_on-or-off">Такой ID свободен</span></div>
                            </label>
                            <label id="reg__label_pass-1">Пароль
                                <input type="password" required placeholder="********" name="password_1" id="reg__password_1" minlength="8" class="auth-and-reg">
                                <div class="password"><span id="reg__8-sim">8 символов</span><span id="reg__num">Цифра</span><span id="reg__!?">! или ?</span></div>
                            </label>
                        </div>
                        <div class="reg__buttons">
                            <a href="./auth" class="have-account">
                                <svg width="8" height="13" viewBox="0 0 8 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.96771 6.03603L1.12165 0.191904C0.865127 -0.0639698 0.449521 -0.0639698 0.192352 0.191904C-0.0641698 0.447777 -0.0641699 0.863383 0.192352 1.11926L5.57471 6.49968L0.192999 11.8801C-0.0635223 12.136 -0.0635224 12.5516 0.192999 12.8081C0.44952 13.064 0.865774 13.064 1.1223 12.8081L6.96836 6.96403C7.22094 6.7108 7.22094 6.28866 6.96771 6.03603Z" />
                                </svg>
                            </a>
                            <button type="submit" class="" id="reg__submit-button">Создать</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>
    <?php require('footer.php'); ?>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="js/reg.js?v=250"></script>
</body>

</html>