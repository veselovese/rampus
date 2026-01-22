<?php
session_start();
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="stylesheet" href="css/main.css?v=300beta">
    <title>Вход в Рампус</title>
    <link rel="apple-touch-icon" sizes="180x180" href="favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicons/favicon-16x16.png">
    <link rel="manifest" href="favicons/site.webmanifest">
</head>

<body>
    <h1 class="title">Вход в профиль Рампус по логину или почте</h1>
    <?php if (isset($_SESSION['user'])) {
        header("Location: profile");
        exit();
    } ?>
    <?php require_once('components/header.php'); ?>
    <main>
        <section class="wrapper">
            <div class="auth__center">
                <div class="auth__section">
                    <?php if (isset($_GET['reg'])) { ?>
                        <div class="auth__notify info" id="auth__notify-info">
                            <p><span class="auth__notify-label" id="auth__notify-label-extra">Отлично, теперь зайдите в аккаунт <span class="auth__notify-username" id="auth__notify-username-extra">@<?= $_GET['reg']; ?></span></span></p>
                        </div>
                    <?php } ?>
                    <div class="auth__notify" id="auth__notify">
                        <p><span id="auth__notify-label" class="auth__notify-label">Привет, </span><span id="auth__notify-username" class="auth__notify-username"></span></p>
                    </div>
                    <?php $request = '' ?>
                    <?php if (isset($_GET['request'])) {
                        $request = '?request=' . $_GET['request'];
                    } ?>
                    <form method="" class="auth__form" action="">
                        <legend class="title">Вход Rampus</legend>
                        <div>
                            <label>Логин или почта<input type="text" required placeholder="rampus" name="email_or_username" id="email_or_username" class="auth-and-reg" value="<?php if (isset($_GET['reg'])) { echo $_GET['reg']; } ?>"></label>
                            <label>Пароль<input type="password" required placeholder="********" minlength="8" name="password" id="password" class="auth-and-reg"></label>
                        </div>
                        <div class="auth__buttons">
                            <button type="button" class="" id="auth-button">Войти</button>
                            <a href="./reg" class="create-account">
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.34788 13.1488C6.34789 13.509 6.63983 13.8009 7.00002 13.8009C7.36021 13.8009 7.6522 13.5089 7.65219 13.1488L6.34788 13.1488ZM7.65217 7C7.65217 6.6398 7.36023 6.34786 7.00004 6.34786C6.63984 6.34786 6.3479 6.6398 6.3479 7H7.65217ZM6.3479 7C6.3479 7.3602 6.63984 7.65214 7.00004 7.65214C7.36023 7.65214 7.65217 7.3602 7.65217 7H6.3479ZM7.65219 0.85125C7.6522 0.491061 7.36021 0.199068 7.00002 0.199074C6.63983 0.19908 6.34789 0.491024 6.34788 0.851213L7.65219 0.85125ZM7.00004 6.34786C6.63984 6.34786 6.3479 6.6398 6.3479 7C6.3479 7.3602 6.63984 7.65214 7.00004 7.65214V6.34786ZM13.1488 7.65214C13.509 7.65214 13.8009 7.36019 13.8009 7C13.8009 6.63981 13.509 6.34786 13.1488 6.34786V7.65214ZM7.00004 7.65214C7.36023 7.65214 7.65217 7.3602 7.65217 7C7.65217 6.6398 7.36023 6.34786 7.00004 6.34786V7.65214ZM0.851267 6.34782C0.491085 6.34782 0.199092 6.63982 0.199092 7C0.199092 7.36018 0.491085 7.65218 0.851268 7.65218L0.851267 6.34782ZM7.65219 13.1488L7.65217 7H6.3479L6.34788 13.1488L7.65219 13.1488ZM7.65217 7L7.65219 0.85125L6.34788 0.851213L6.3479 7H7.65217ZM7.00004 7.65214H13.1488V6.34786H7.00004V7.65214ZM7.00004 6.34786L0.851267 6.34782L0.851268 7.65218L7.00004 7.65214V6.34786Z" />
                                </svg>
                            </a>
                            <span>Создать новый аккаунт</span>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>
    <?php require_once('components/footer.php'); ?>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="js/auth.js?v=300beta"></script>
</body>

</html>