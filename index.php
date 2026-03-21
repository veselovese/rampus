<?php
require('backfiles/get-base-url.php');
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <title>Рампус — геймифицированная соцсеть. Трофеи, рейтинг и глобальная стена</title>

    <meta property="og:title" content="Рампус — геймифицированная соцсеть. Трофеи, рейтинг и глобальная стена" />

    <meta property="og:site_name" content="Рампус">
    <meta property="og:url" content="<?= $baseUrl ?>">

    <meta name="description" content="Публикуйте, лайкайте, комментируйте и репостите посты. Общайтесь в чатах, коллекционируйте трофеи и соревнуйтесь за место в рейтинге Цветения профиля" />
    <meta property="og:description" content="Публикуйте, лайкайте, комментируйте и репостите посты. Общайтесь в чатах, коллекционируйте трофеи и соревнуйтесь за место в рейтинге Цветения профиля" />

    <meta property="og:image" content="<?= $baseUrl ?>/pics/plugs/RampusMainPlug.png?v=330" />

    <link rel="apple-touch-icon" sizes="180x180" href="favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicons/favicon-16x16.png">
    <link rel="manifest" href="favicons/site.webmanifest">
</head>

<body>
    <main>
        <h1 class="title">Рампус — геймифицированная социальная сеть</h1>
    </main>
</body>

</html>

<?php
header("Location: auth");
exit();
?>