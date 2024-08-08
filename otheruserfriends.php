<?php
session_start();

require('connect.php');
require('like-or-dislike.php');


$other_username = $_GET['username'];
$result = $connect->query("SELECT * FROM users WHERE username = '$other_username'");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id = $row["id"];
    }
}
$result_friend_2 = $connect->query("SELECT * FROM friends JOIN users ON friends.user_id_1 = users.id WHERE user_id_2 = $id ORDER BY friend_date");
$result_friend_1 = $connect->query("SELECT * FROM friends JOIN users ON friends.user_id_2 = users.id WHERE user_id_1 = $id ORDER BY friend_date");
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="stylesheet" href="../../css/main.css?v=141">
    <link rel="stylesheet" href="../../css/people.css?v=141">
    <title>Друзья пользователя в Rampus (Рампус)</title>
    <link rel="apple-touch-icon" sizes="57x57" href="../../favicons/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="../../favicons/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="../../favicons/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="../../favicons/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="../../favicons/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="../../favicons/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="../../favicons/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="../../favicons/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="../../favicons/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="../../favicons/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../../favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="../../favicons/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../../favicons/favicon-16x16.png">
    <link rel="manifest" href="../../favicons/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="../../favicons/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
</head>

<body>
    <?php require('header-3.php'); ?>
    <main>
        <h1 class="title">Друзья в Rampus (Рампус)</h1>
        <?php if (!isset($_SESSION['user'])) {
            header("Location: auth?request=people");
            exit();
        } else { ?>
            <section class="wrapper main-section">
                <nav class="first-part">
                    <ul>
                        <li><a id="exit" href="../<?= $other_username ?>">Назад</a></li>
                    </ul>
                </nav>
                <div class="second-and-third-parts">
                    <div class="second-part">
                        <div class="friends__users">
                            <p>Друзья <span>@<?= $other_username ?></span></p>
                            <?php if (($result_friend_1->num_rows > 0) || ($result_friend_2->num_rows > 0)) {
                                echo "<ul>";
                                $counter = $result_friend_1->num_rows + $result_friend_2->num_rows;
                                if ($result_friend_1->num_rows > 0) {
                                    while ($row_friend_1 = $result_friend_1->fetch_assoc()) {
                                        $counter -= 1;
                                        $id = $row_friend_1['id'];
                                        $username = $row_friend_1['username'];
                                        $avatar = $row_friend_1['avatar'];
                                        $first_name = $row_friend_1['first_name'];
                                        $second_name = $row_friend_1['second_name'];
                                        echo "<li class='user' onclick='openOtherUserProfileFromOtherProfile(event, `$username`)'>";
                                        echo "<img src='../../uploads/avatar/thin_$avatar'>";
                                        echo "<div class='current-user-info'>";
                                        if ($username == 'rampus') {
                                            echo "<p class='rampus'>$first_name $second_name<img src='../../pics/SuperUserIcon.svg'></p>";
                                        } else {
                                            echo "<p>$first_name $second_name</p>";
                                        }
                                        echo "<p>@$username</p>";
                                        echo "</div>";
                                        echo "</li>";
                                        if ($counter > 0) {
                                            echo "<div class='div-line'></div>";
                                        }
                                    }
                                }
                                if ($result_friend_2->num_rows > 0) {
                                    while ($row_friend_2 = $result_friend_2->fetch_assoc()) {
                                        $counter -= 1;
                                        $id = $row_friend_2['id'];
                                        $username = $row_friend_2['username'];
                                        $avatar = $row_friend_2['avatar'];
                                        $first_name = $row_friend_2['first_name'];
                                        $second_name = $row_friend_2['second_name'];
                                        echo "<li class='user' onclick='openOtherUserProfileFromOtherProfile(event, `$username`)'>";
                                        echo "<img src='../../uploads/avatar/thin_$avatar'>";
                                        echo "<div class='current-user-info'>";
                                        if ($username == 'rampus') {
                                            echo "<p class='rampus'>$first_name $second_name<img src='../../pics/SuperUserIcon.svg'></p>";
                                        } else {
                                            echo "<p>$first_name $second_name</p>";
                                        }
                                        echo "<p>@$username</p>";
                                        echo "</div>";
                                        echo "</li>";
                                        if ($counter > 0) {
                                            echo "<div class='div-line'></div>";
                                        }
                                    }
                                }
                                echo "</ul>";
                            } else { ?>
                                <p>У пользователя нет друзей</p>
                            <?php } ?>
                        </div>
                        <nav class="first-part-mobile">
                            <ul>
                                <li><a href="../../wall"><svg width="28" height="24" viewBox="0 0 28 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M4.56528 18C4.54895 17.8355 4.54059 17.6687 4.54059 17.5V10.5C4.54059 7.18629 7.22688 4.5 10.5406 4.5H21.4865C21.6336 4.5 21.7792 4.50635 21.9231 4.5188C21.681 1.98313 19.545 0 16.9459 0H5.00001C2.23858 0 0 2.23858 0 5V18H4.56528Z" />
                                            <path d="M6.05408 11C6.05408 8.23858 8.29265 6 11.0541 6H23C25.7614 6 28 8.23858 28 11V24H11.0541C8.29266 24 6.05408 21.7614 6.05408 19V11Z" />
                                        </svg>
                                        Стена
                                    </a></li>
                                <li><a href="../../people">
                                        <svg width="16" height="24" viewBox="0 0 16 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M4.97802 21.033C4.97802 21.8681 5.25275 22.5714 5.8022 23.1429C6.35165 23.7143 7.04396 24 7.87912 24C8.73626 24 9.43956 23.7253 9.98901 23.1758C10.5604 22.6044 10.8462 21.9011 10.8462 21.0659C10.8462 20.2088 10.5604 19.4945 9.98901 18.9231C9.41758 18.3516 8.71429 18.0659 7.87912 18.0659C7.04396 18.0659 6.35165 18.3516 5.8022 18.9231C5.25275 19.4725 4.97802 20.1758 4.97802 21.033ZM15.5275 7.51648C15.5275 9.07692 15.0769 10.4505 14.1758 11.6374C13.2967 12.8022 12.0769 13.7473 10.5165 14.4725L7.21978 16.2198L5.73626 12.6593L8.73626 11.0769C10.4066 10.022 11.2418 8.84615 11.2418 7.54945C11.2418 6.58242 10.9231 5.8022 10.2857 5.20879C9.64835 4.59341 8.83517 4.28571 7.84615 4.28571C5.93407 4.28571 4.64835 5.43956 3.98901 7.74725L0 6.2967C0.615385 4.34066 1.59341 2.8022 2.93407 1.68132C4.27473 0.56044 5.9011 0 7.81319 0C10.033 0 11.8681 0.714286 13.3187 2.14286C14.7912 3.54945 15.5275 5.34066 15.5275 7.51648Z" />
                                        </svg>
                                        ****</a></li>
                                <li id="active"><a href="../../people">
                                        <svg width="28" height="24" viewBox="0 0 28 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.66113 15.5149C7.88196 16.2628 7.21631 17.0995 6.67468 18H0C0 15.2153 1.1062 12.5446 3.07532 10.5754C4.3374 9.31335 5.8877 8.40576 7.57141 7.91675C6.60938 7.09143 6 5.86682 6 4.5C6 2.01477 8.01477 0 10.5 0C12.9852 0 15 2.01477 15 4.5C15 4.6897 14.9883 4.87659 14.9655 5.06018C12.9185 6.0155 11.5 8.09216 11.5 10.5001C11.5 11.458 11.7245 12.3635 12.1237 13.1667C10.8506 13.749 9.67737 14.5393 8.66113 15.5149ZM22 10.5001C22 12.9854 19.9852 15.0001 17.5 15.0001C15.0148 15.0001 13 12.9854 13 10.5001C13 8.01489 15.0148 6.00012 17.5 6.00012C19.9852 6.00012 22 8.01489 22 10.5001ZM13.0278 14.5001C11.9414 15.0116 10.9407 15.7102 10.0753 16.5754C8.1062 18.5447 7 21.2153 7 24.0001H17.5H28C28 21.2153 26.8937 18.5447 24.9246 16.5754C24.0593 15.7102 23.0586 15.0116 21.9722 14.5001C20.8735 15.7277 19.277 16.5001 17.5 16.5001C15.723 16.5001 14.1265 15.7277 13.0278 14.5001Z" />
                                        </svg>
                                        Люди</a></li>
                                <li><a href="../../profile"><svg width="28" height="24" viewBox="0 0 28 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.03709 11.3334C6.58858 12.0152 5.25423 12.9468 4.10051 14.1005C1.475 16.726 0 20.287 0 24L14 24H28C28 20.287 26.525 16.726 23.8995 14.1005C22.7458 12.9468 21.4114 12.0152 19.9629 11.3334C18.4981 12.97 16.3693 14 14 14C11.6307 14 9.50195 12.97 8.03709 11.3334Z" />
                                            <circle cx="14" cy="6" r="6" />
                                        </svg>
                                        Профиль
                                    </a></li>
                            </ul>
                        </nav>
                    </div>
                    <div class="third-part">
                    </div>
            </section>
    </main>
<?php require('footer-3.php');
        } ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="../../js/main.js?v=141"></script>
<script src="../../js/people.js?v=141"></script>
</body>

</html>