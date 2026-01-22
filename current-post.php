<?php
session_start();

if (isset($_SESSION['user'])) {
    require('back-files/like-or-dislike.php');
    require('back-files/find-user-position-in-top.php');
    require('back-files/get-user-friends.php');
    require_once('back-files/connect.php');

    $current_user_id = $_SESSION['user']['id'];}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="stylesheet" href="../css/main.css?v=300beta">
    <link rel="stylesheet" href="../css/wall.css?v=300beta">
    <title>Пост на стене в Рампус</title>
    <link rel="apple-touch-icon" sizes="180x180" href="../favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../favicons/favicon-16x16.png">
    <link rel="manifest" href="../favicons/site.webmanifest">
</head>

<body>
    <?php require_once('components/header.php'); ?>
    <main>
        <h1 class="title">Пост на стене в Рампус</h1>
        <?php if (!isset($_SESSION['user'])) {
            header("Location: ../auth?request=wall");
            exit();
        } else { ?>
            <section class="wrapper main-section">
                <?php require_once('components/main-menu.php'); ?>
                <div class="second-and-third-parts">
                    <div class="second-part">
                        <div class="wall__user-posts current-post" id="success-render-posts">
                        </div>
                        <?php require_once('components/mobile-main-menu.php') ?>
                    </div>
                    <div class="third-part">
                        <div>
                        </div>
                    </div>
            </section>
    </main>
<?php require_once('components/footer.php');
        } ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="../js/main.js?v=300beta"></script>
<script src="../js/wall.js?v=300beta"></script>
<script src="../js/currentpost.js?v=300beta"></script>
</body>

</html>