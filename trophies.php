<?php
session_start();

if (isset($_SESSION['user'])) {
    require_once('back-files/connect.php');

    $current_user_id = $_SESSION['user']['id'];

    $sql_trophies = "SELECT id, name, short_description, image, stat_number, DATE_FORMAT(get_date, '%e %M') AS get_date FROM trophies WHERE user_id_to = $current_user_id";
    $result_trophies = $connect->query($sql_trophies);
}

?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="stylesheet" href="css/main.css?v=300beta">
    <link rel="stylesheet" href="css/profile.css?v=300beta">
    <title>Трофеи в Рампус</title>
    <link rel="apple-touch-icon" sizes="180x180" href="favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicons/favicon-16x16.png">
    <link rel="manifest" href="favicons/site.webmanifest">
</head>

<body>
    <?php require_once('components/header.php'); ?>
    <main>
        <h1 class="title">Трофеи пользователя в Рампус</h1>
        <?php if (!isset($_SESSION['user'])) {
            header("Location: auth?request=profile");
            exit();
        } else { ?>
            <section class="wrapper main-section">
                <?php require_once('components/back-menu.php'); ?>
                <div class="second-and-third-parts">
                    <div class="second-part">
                        <div class="user__case">
                            <h2>Полка с трофеями</h2>
                            <div>
                                <p class="case-description">Трофеи — это ваши награды за активность. Будьте внимательны, их могут отобрать в любой момент</p>
                            </div>
                            <div class="current-user-case">
                                <?php if ($result_trophies->num_rows > 0) {
                                    while ($row = $result_trophies->fetch_assoc()) {
                                        $trophy_id = $row["id"];
                                        $trophy_name = $row["name"];
                                        $trophy_description = $row["short_description"];
                                        $trophy_stat = $row["stat_number"];
                                        $trophy_image = $row["image"];
                                        $trophy_date = $row["get_date"];
                                        echo "<div class='trophy'>";
                                        echo "<img src='$trophy_image'>";
                                        echo "<p>$trophy_name</p>";
                                        if ($trophy_id == 4) {
                                            if (($trophy_stat[-1] == '1') && (!isset($trophy_stat[-2]) || $trophy_stat[-2] != '1')) {
                                                echo "<p>" . $trophy_stat . " лайк под одним постом — больше ни у кого нет</p>";
                                            } else if (($trophy_stat[-1] == '2' || $trophy_stat[-1] == '3' || $trophy_stat[-1] == '4') && (!isset($trophy_stat[-2]) || $trophy_stat[-2] != '1')) {
                                                echo "<p>" . $trophy_stat . " лайка под одним постом — больше ни у кого нет</p>";
                                            } else {
                                                echo "<p>" . $trophy_stat . " лайков под одним постом — больше ни у кого нет</p>";
                                            }
                                        } else if ($trophy_id == 7) {
                                            if (($trophy_stat[-1] == '1') && (!isset($trophy_stat[-2]) || $trophy_stat[-2] != '1')) {
                                                echo "<p>Вы нравитесь людям — " . $trophy_stat . " комментарий под одним постом</p>";
                                            } else if (($trophy_stat[-1] == '2' || $trophy_stat[-1] == '3' || $trophy_stat[-1] == '4') && (!isset($trophy_stat[-2]) || $trophy_stat[-2] != '1')) {
                                                echo "<p>Вы нравитесь людям — " . $trophy_stat . " комментария под одним постом</p>";
                                            } else {
                                                echo "<p>Вы нравитесь людям — " . $trophy_stat . " комментариев под одним постом</p>";
                                            }
                                        } else if ($trophy_id == 13) {
                                            if (($trophy_stat[-1] == '1') && (!isset($trophy_stat[-2]) || $trophy_stat[-2] != '1')) {
                                                echo "<p>Сколько же ушло времени, чтобы насписать " . $trophy_stat . " пост</p>";
                                            } else if (($trophy_stat[-1] == '2' || $trophy_stat[-1] == '3' || $trophy_stat[-1] == '4') && (!isset($trophy_stat[-2]) || $trophy_stat[-2] != '1')) {
                                                echo "<p>Сколько же ушло времени, чтобы насписать " . $trophy_stat . " поста</p>";
                                            } else {
                                                echo "<p>Сколько же ушло времени, чтобы насписать " . $trophy_stat . " постов</p>";
                                            }
                                        } else if ($trophy_id == 14) {
                                            if (($trophy_stat[-1] == '1') && (!isset($trophy_stat[-2]) || $trophy_stat[-2] != '1')) {
                                                echo "<p>Кот Леопольд найден — у него " . $trophy_stat . " друг</p>";
                                            } else if (($trophy_stat[-1] == '2' || $trophy_stat[-1] == '3' || $trophy_stat[-1] == '4') && (!isset($trophy_stat[-2]) || $trophy_stat[-2] != '1')) {
                                                echo "<p>Кот Леопольд найден — у него " . $trophy_stat . " друга</p>";
                                            } else {
                                                echo "<p>Кот Леопольд найден — у него " . $trophy_stat . " друзей</p>";
                                            }
                                        } else {
                                            echo "<p>$trophy_description</p>";
                                        }
                                        echo "<span>владеете с $trophy_date</span>";
                                        echo "</div>";
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <?php require_once('components/mobile-main-menu.php') ?>
                    </div>
                    <div class="third-part">
                    </div>
            </section>
    </main>
<?php require_once('components/footer.php');
        } ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="js/main.js?v=300beta"></script>
<script src="js/people.js?v=300beta"></script>
</body>

</html>