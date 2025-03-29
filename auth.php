<?php
session_start();
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="stylesheet" href="css/main.css?v=200">
    <title>Вход в Rampus</title>
    <link rel="apple-touch-icon" sizes="180x180" href="favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicons/favicon-16x16.png">
    <link rel="manifest" href="favicons/site.webmanifest">
</head>

<body>
    <h1 class="title">Вход в Rampus (Рампус)</h1>
    <?php if (isset($_SESSION['user'])) {
        header("Location: profile");
        exit();
    } ?>
    <?php require('header.php'); ?>
    <main>
        <section class="wrapper auth__section">
            <img src="pics/RampusLogo.svg">
            <div class="div-line"></div>
            <?php $request = '' ?>
            <?php if (isset($_GET['request'])) {
                $request = '?request=' . $_GET['request'];
            } ?>
            <form method="POST" class="auth__form" action="./back-files/sign-in<?php echo $request ?>">
                <div>
                    <legend>Вход Rampus</legend>
                    <label>ID или почта<input type="text" required placeholder="rampus" name="email_or_username"></label>
                    <label>Пароль<input type="password" required placeholder="********" minlength="8" name="password"></label>
                </div>
                <div>
                    <button type="submit" class="">Войти</button>
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