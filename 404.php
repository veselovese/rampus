<?php
require('back-files/get-base-url.php');
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="stylesheet" href="css/main.css?v=320">

    <title>Упс, такой страницы в Рампусе нет</title>

    <meta property="og:title" content="Упс, такой страницы в Рампусе нет" />

    <meta property="og:site_name" content="Рампус">
    <meta property="og:url" content="<?= $baseUrl ?>">

    <meta name="description" content="Проверьте ещё раз ссылку. Клянемся, такой страница в Рампусе нет" />
    <meta property="og:description" content="Проверьте ещё раз ссылку. Клянемся, такой страница в Рампусе нет" />

    <meta property="og:image" content="<?= $baseUrl ?>/pics/plugs/RampusMainPlug.png?v=320" />

    <link rel="apple-touch-icon" sizes="180x180" href="favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicons/favicon-16x16.png">
    <link rel="manifest" href="favicons/site.webmanifest">
</head>

<body>
    <h1 class="title">Упс, такой странице в Рампусе нет</h1>
    <?php require_once('components/header.php'); ?>
    <main>
        <section class="wrapper">
            <div class="auth__center">
                <div class="page-error-div">
                    <p class="main-page-error">404 (не six-seven)</p>
                    <p class="page-error">Окак, такой страницы нет. Потерялись?</p>
                    <a href="/wall" class="link-page-error">Вернуться на Стену</a>
                </div>
            </div>
        </section>
    </main>
    <?php require_once('components/footer.php'); ?>
</body>

</html>