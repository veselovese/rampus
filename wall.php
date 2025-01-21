<?php
session_start();

require_once('back-files/connect.php');

if (isset($_SESSION['user'])) {
    require('back-files/like-or-dislike.php');
    require('back-files/rating-trophies.php');
    require('back-files/find-user-position-in-top.php');
    require('back-files/get-user-friends.php');

    $user_id = $_SESSION['user']['id'];
    $result = $connect->query("SELECT * FROM users WHERE id = $user_id");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $username = $row["username"];
            $email = $row["email"];
            $description = $row["description"];
            $first_name = $row["first_name"];
            $second_name = $row["second_name"];
            $avatar = $row["avatar"];
        }
    }
    $user_in_top = findUserPositionInTop($user_id, $connect);

    $_SESSION['user']['unread_posts'] = 0;
    $connect->query("UPDATE users SET unread_posts = 0 WHERE id = $user_id");
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="stylesheet" href="css/main.css?v=200">
    <link rel="stylesheet" href="css/wall.css?v=200">
    <title>Посты на стене в Rampus (Рампус)</title>
    <link rel="apple-touch-icon" sizes="57x57" href="favicons/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="favicons/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="favicons/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="favicons/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="favicons/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="favicons/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="favicons/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="favicons/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="favicons/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="favicons/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="favicons/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicons/favicon-16x16.png">
    <link rel="manifest" href="favicons/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="favicons/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
</head>

<body>
    <?php require('header.php'); ?>
    <main>
        <h1 class="title">Посты на стене в Rampus (Рампус)</h1>
        <?php if (!isset($_SESSION['user'])) {
            header("Location: auth?request=wall");
            exit();
        } else { ?>
            <section class="wrapper main-section">
                <nav class="first-part">
                    <ul>
                        <li><a href="./profile" class="menu-profile">
                                <?php if ($username == 'rampus') { ?>
                                    <img class='menu-status' src="pics/SuperUserIcon.svg">
                                <?php } else {
                                    switch ($user_in_top) {
                                        case 1:
                                            echo "<img class='menu-status' src='pics/BlossomFirstIcon.svg'>";
                                            break;
                                        case 2:
                                            echo "<img class='menu-status' src='pics/BlossomSecondIcon.svg'>";
                                            break;
                                        case 3:
                                            echo "<img class='menu-status' src='pics/BlossomThirdIcon.svg'>";
                                            break;
                                    }
                                } ?>
                                <img class="menu-avatar" src="uploads/avatar/thin_<?= $avatar ?>">
                                <div>
                                    <p><?= $first_name ?></p>
                                    <p>@<?= $username ?></p>
                                </div>
                            </a></li>
                        <p class="menu-title">Общение</p>
                        <li id="active">
                            <div class='wall-filter-div'>
                                <div class='wall-filter' id='wall-filter'>
                                    <svg width="28" height="24" viewBox="0 0 28 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M20.9219 4C20.4586 1.71776 18.4408 0 16.0219 0H4.99997C2.23855 0 -2.28882e-05 2.23858 -2.28882e-05 5V18H5V11C5 7.13401 8.13401 4 12 4H20.9219Z" />
                                        <path d="M7 11C7 8.23858 9.23858 6 12 6H23C25.7614 6 28 8.23858 28 11V24H12C9.23858 24 7 21.7614 7 19V11Z" />
                                    </svg>
                                    Стена
                                </div>
                            </div>
                        </li>
                        <li><a href="./people"><svg width='28' height='24' viewBox='0 0 28 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                    <path d='M15 4.49829C12.651 5.47785 11 7.79617 11 10.5001C11 11.397 11.1816 12.2514 11.5102 13.0287C10.4728 13.595 9.51192 14.3105 8.66116 15.1613C7.81316 16.0093 7.09958 16.9666 6.53414 18.0001L0 18C0 15.2153 1.10625 12.5446 3.07538 10.5754C4.33742 9.31339 5.88765 8.4058 7.5714 7.91672C6.60943 7.09142 6 5.86688 6 4.5C6 2.01472 8.01472 0 10.5 0C12.9847 0 14.9991 2.01379 15 4.49829Z' />
                                    <path d='M21.25 10.5001C21.25 12.5712 19.5711 14.2501 17.5 14.2501C15.4289 14.2501 13.75 12.5712 13.75 10.5001C13.75 8.42905 15.4289 6.75012 17.5 6.75012C19.5711 6.75012 21.25 8.42905 21.25 10.5001ZM10.6057 17.1058C11.2822 16.4293 12.0479 15.8625 12.8752 15.4168C14.0826 16.5528 15.7103 17.2501 17.5 17.2501C19.2897 17.2501 20.9174 16.5528 22.1248 15.4168C22.9521 15.8625 23.7177 16.4293 24.3943 17.1058C26.0452 18.7567 27.0429 20.9386 27.2211 23.2501H17.5L7.77887 23.2501C7.95711 20.9386 8.95483 18.7567 10.6057 17.1058Z' stroke-linecap='round' stroke-linejoin='round' />
                                </svg>Люди</a></li>
                        <li><a href="./friends"><svg width='28' height='24' viewBox='0 0 28 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                    <path d='M20.8526 4.93034C20.7754 4.57556 20.6682 4.22744 20.5318 3.89002C20.2227 3.12549 19.7696 2.43082 19.1985 1.84567C18.6273 1.26053 17.9493 0.796361 17.203 0.47968C16.4568 0.162998 15.6569 3.10145e-06 14.8492 0C14.0415 -3.0333e-06 13.2417 0.162987 12.4954 0.479662C11.7496 0.796168 11.0719 1.26 10.5009 1.8447L10.4995 1.84326L10.4985 1.84432C9.92762 1.25991 9.25011 0.7963 8.50454 0.479903C7.75829 0.163222 6.95847 0.000225794 6.15074 0.000222625C5.34301 0.000219592 4.54319 0.163209 3.79695 0.479885C3.0507 0.79656 2.37265 1.26072 1.8015 1.84586C1.23035 2.43101 0.777293 3.12567 0.468191 3.8902C0.159089 4.65473 -2.36435e-06 5.47415 0 6.30167C3.3586e-06 7.12919 0.1591 7.94861 0.468208 8.71314C0.777315 9.47767 1.23038 10.1723 1.80153 10.7575L7.45977 16.554C7.82333 16.0637 8.22442 15.598 8.66116 15.1613C9.51192 14.3105 10.4728 13.595 11.5102 13.0287C11.1816 12.2514 11 11.397 11 10.5001C11 6.91027 13.9101 4.00012 17.5 4.00012C18.7265 4.00012 19.8737 4.33985 20.8526 4.93034Z' />
                                    <path d='M21.25 10.5001C21.25 12.5712 19.5711 14.2501 17.5 14.2501C15.4289 14.2501 13.75 12.5712 13.75 10.5001C13.75 8.42905 15.4289 6.75012 17.5 6.75012C19.5711 6.75012 21.25 8.42905 21.25 10.5001ZM10.6057 17.1058C11.2822 16.4293 12.0479 15.8625 12.8752 15.4168C14.0826 16.5528 15.7103 17.2501 17.5 17.2501C19.2897 17.2501 20.9174 16.5528 22.1248 15.4168C22.9521 15.8625 23.7177 16.4293 24.3943 17.1058C26.0452 18.7567 27.0429 20.9386 27.2211 23.2501H17.5L7.77887 23.2501C7.95711 20.9386 8.95483 18.7567 10.6057 17.1058Z' stroke-linejoin='round' />
                                </svg>
                                Друзья
                                <?php if ($result_request_to->num_rows > 0) { ?>
                                    <span class="notification-in-menu"><?= $result_request_to->num_rows ?></span>
                                <?php } ?>
                            </a></li>
                            <li><a href="./trophy"><svg width='28' height='24' viewBox='0 0 28 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                    <path d='M20.8526 4.93034C20.7754 4.57556 20.6682 4.22744 20.5318 3.89002C20.2227 3.12549 19.7696 2.43082 19.1985 1.84567C18.6273 1.26053 17.9493 0.796361 17.203 0.47968C16.4568 0.162998 15.6569 3.10145e-06 14.8492 0C14.0415 -3.0333e-06 13.2417 0.162987 12.4954 0.479662C11.7496 0.796168 11.0719 1.26 10.5009 1.8447L10.4995 1.84326L10.4985 1.84432C9.92762 1.25991 9.25011 0.7963 8.50454 0.479903C7.75829 0.163222 6.95847 0.000225794 6.15074 0.000222625C5.34301 0.000219592 4.54319 0.163209 3.79695 0.479885C3.0507 0.79656 2.37265 1.26072 1.8015 1.84586C1.23035 2.43101 0.777293 3.12567 0.468191 3.8902C0.159089 4.65473 -2.36435e-06 5.47415 0 6.30167C3.3586e-06 7.12919 0.1591 7.94861 0.468208 8.71314C0.777315 9.47767 1.23038 10.1723 1.80153 10.7575L7.45977 16.554C7.82333 16.0637 8.22442 15.598 8.66116 15.1613C9.51192 14.3105 10.4728 13.595 11.5102 13.0287C11.1816 12.2514 11 11.397 11 10.5001C11 6.91027 13.9101 4.00012 17.5 4.00012C18.7265 4.00012 19.8737 4.33985 20.8526 4.93034Z' />
                                    <path d='M21.25 10.5001C21.25 12.5712 19.5711 14.2501 17.5 14.2501C15.4289 14.2501 13.75 12.5712 13.75 10.5001C13.75 8.42905 15.4289 6.75012 17.5 6.75012C19.5711 6.75012 21.25 8.42905 21.25 10.5001ZM10.6057 17.1058C11.2822 16.4293 12.0479 15.8625 12.8752 15.4168C14.0826 16.5528 15.7103 17.2501 17.5 17.2501C19.2897 17.2501 20.9174 16.5528 22.1248 15.4168C22.9521 15.8625 23.7177 16.4293 24.3943 17.1058C26.0452 18.7567 27.0429 20.9386 27.2211 23.2501H17.5L7.77887 23.2501C7.95711 20.9386 8.95483 18.7567 10.6057 17.1058Z' stroke-linejoin='round' />
                                </svg>
                                Трофеи
                            </a></li>
                        <p class="menu-title">Возможности</p>
                        <li><a href="./edit">
                                <svg width='28' height='24' viewBox='0 0 28 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                    <path fill-rule='evenodd' clip-rule='evenodd' d='M10.6852 0.137016C10.3343 0 9.88958 0 9.00001 0C8.11043 0 7.66569 0 7.31479 0.137016C6.84701 0.319707 6.47535 0.670122 6.28158 1.11117C6.19313 1.31251 6.1585 1.54665 6.14496 1.88819C6.12506 2.39011 5.85204 2.8547 5.39068 3.10584C4.92933 3.35696 4.36608 3.34759 3.89509 3.11288C3.57459 2.95317 3.34221 2.86436 3.11304 2.83592C2.61103 2.7736 2.10333 2.90186 1.70162 3.19248C1.40034 3.41044 1.17795 3.7736 0.733186 4.49991L0.733167 4.49994L0.733124 4.50001C0.288375 5.2263 0.0659982 5.58944 0.0164284 5.94442C-0.0496573 6.41772 0.08638 6.89639 0.394624 7.27515C0.53531 7.44804 0.733043 7.5933 1.03993 7.7751C1.49108 8.0424 1.78135 8.49771 1.78133 9C1.7813 9.50229 1.49103 9.95751 1.03992 10.2247C0.732995 10.4066 0.535234 10.552 0.394529 10.7248C0.0862847 11.1036 -0.0497527 11.5822 0.0163426 12.0555C0.0659044 12.4105 0.288293 12.7737 0.733071 13.5L0.73355 13.5008C1.17802 14.2266 1.40036 14.5896 1.70152 14.8074C2.10323 15.098 2.61093 15.2263 3.11295 15.164C3.3421 15.1356 3.57447 15.0467 3.89494 14.8871C4.36596 14.6524 4.92925 14.643 5.39064 14.8941C5.85203 15.1453 6.12506 15.6099 6.14496 16.1119C6.15851 16.4534 6.19313 16.6875 6.28158 16.8889C6.39517 17.1474 6.56988 17.3748 6.78926 17.5555C7.30922 16.6913 7.93605 15.8862 8.66116 15.1611C9.51192 14.3104 10.4728 13.5949 11.5102 13.0285C11.1816 12.2513 11 11.3969 11 10.5C11 7.08819 13.6286 4.29036 16.9711 4.02119C16.7064 3.60165 16.5246 3.35618 16.2985 3.19254C15.8968 2.90192 15.389 2.77366 14.887 2.83598C14.6579 2.86442 14.4256 2.95322 14.105 3.11292C13.634 3.34763 13.0707 3.35702 12.6094 3.10586C12.1479 2.85472 11.8749 2.39009 11.8551 1.88815C11.8415 1.54663 11.8069 1.3125 11.7184 1.11117C11.5247 0.670122 11.153 0.319707 10.6852 0.137016ZM14.1455 13.4996C14.109 13.4588 14.0732 13.4173 14.0382 13.3752Z' />
                                    <path d='M21.25 10.5C21.25 12.5711 19.5711 14.25 17.5 14.25C15.4289 14.25 13.75 12.5711 13.75 10.5C13.75 8.42893 15.4289 6.75 17.5 6.75C19.5711 6.75 21.25 8.42893 21.25 10.5ZM10.6057 17.1057C11.2822 16.4292 12.0479 15.8623 12.8752 15.4166C14.0826 16.5527 15.7103 17.25 17.5 17.25C19.2897 17.25 20.9174 16.5527 22.1248 15.4166C22.9521 15.8623 23.7177 16.4292 24.3943 17.1057C26.0452 18.7566 27.0429 20.9385 27.2211 23.25H17.5L7.77887 23.25C7.95711 20.9385 8.95483 18.7566 10.6057 17.1057Z' stroke-linecap='round' stroke-linejoin='round' />
                                </svg>
                                Настройки
                                <svg class="pointer" width="8" height="13" viewBox="0 0 8 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.96771 6.03603L1.12165 0.191904C0.865127 -0.0639698 0.449521 -0.0639698 0.192352 0.191904C-0.0641698 0.447777 -0.0641699 0.863383 0.192352 1.11926L5.57471 6.49968L0.192999 11.8801C-0.0635223 12.136 -0.0635224 12.5516 0.192999 12.8081C0.44952 13.064 0.865774 13.064 1.1223 12.8081L6.96836 6.96403C7.22094 6.7108 7.22094 6.28866 6.96771 6.03603Z" />
                                </svg></a></li>
                    </ul>
                </nav>
                <div class="second-and-third-parts">
                    <div class="second-part">
                        <div class="wall__new-post">
                            <form action="./back-files/add" method="post" enctype="multipart/form-data" autocomplete="off">
                                <div contenteditable="true" id="textarea-post" role="textbox" onkeyup="textareaPost(event)" onkeydown="textareaPostPlaceholder(event)"></div>
                                <label for="textarea-post" id="textarea-post_label">О чём расскажете сегодня?</label>
                                <input type="hidden" required name="post" id="textarea-post_input" value="">
                                <input type="hidden" required name="post-source" value="source-wall">
                                <input type="hidden" required name="post-search" value="<?php if (isset($_GET['search'])) {
                                                                                            echo $_GET['search'];
                                                                                        } ?>">
                                <div class="current-post-image-div">
                                    <img class="post-image" id="current-post-image" src="">
                                    <div class="post-image-delete" onclick="clearPostImage()">
                                        <svg width='10' height='10' viewBox='0 0 10 10' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                            <path d='M0.191016 8.88671C-0.0636719 9.14141 -0.0636719 9.55428 0.191016 9.80898C0.445703 10.0637 0.858643 10.0637 1.11333 9.80898L0.191016 8.88671ZM5.46114 5.46114C5.71584 5.20644 5.71584 4.79357 5.46114 4.53888C5.20644 4.28418 4.79357 4.28418 4.53888 4.53888L5.46114 5.46114ZM4.53888 4.53888C4.28418 4.79357 4.28418 5.20644 4.53888 5.46114C4.79357 5.71584 5.20644 5.71584 5.46114 5.46114L4.53888 4.53888ZM9.80898 1.11333C10.0637 0.858644 10.0637 0.445703 9.80898 0.191016C9.55428 -0.0636719 9.14141 -0.0636719 8.88671 0.191016L9.80898 1.11333ZM5.46114 4.53888C5.20644 4.28418 4.79357 4.28418 4.53888 4.53888C4.28418 4.79357 4.28418 5.20644 4.53888 5.46114L5.46114 4.53888ZM8.88671 9.80898C9.14141 10.0637 9.55428 10.0637 9.80898 9.80898C10.0637 9.55428 10.0637 9.14141 9.80898 8.88671L8.88671 9.80898ZM4.53888 5.46114C4.79357 5.71584 5.20644 5.71584 5.46114 5.46114C5.71584 5.20644 5.71584 4.79357 5.46114 4.53888L4.53888 5.46114ZM1.11333 0.191016C0.858643 -0.0636719 0.445703 -0.0636719 0.191016 0.191016C-0.0636719 0.445703 -0.0636719 0.858644 0.191016 1.11333L1.11333 0.191016ZM1.11333 9.80898L5.46114 5.46114L4.53888 4.53888L0.191016 8.88671L1.11333 9.80898ZM5.46114 5.46114L9.80898 1.11333L8.88671 0.191016L4.53888 4.53888L5.46114 5.46114ZM4.53888 5.46114L8.88671 9.80898L9.80898 8.88671L5.46114 4.53888L4.53888 5.46114ZM5.46114 4.53888L1.11333 0.191016L0.191016 1.11333L4.53888 5.46114L5.46114 4.53888Z' />
                                        </svg>
                                    </div>
                                </div>
                                <input type="file" name="post-image" id="post-image">
                                <div onclick='addImageToPost()' class="post-image-icon">
                                    <svg width='18' height='20' viewBox='0 0 18 20' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                        <path fill-rule='evenodd' clip-rule='evenodd' d='M10.0631 2.47923C10.0631 2.47924 10.0631 2.47923 10.0631 2.47923L2.96613 9.8425C1.19829 11.6767 1.19827 14.6607 2.96614 16.4949C4.72012 18.3147 7.55291 18.3147 9.30686 16.4949L16.6998 8.82461C16.9662 8.5482 17.4063 8.54009 17.6827 8.80651C17.9591 9.07293 17.9672 9.51298 17.7008 9.78939L10.3078 17.4597C8.00712 19.8468 4.26589 19.8468 1.96515 17.4597C-0.321693 15.087 -0.321723 11.2504 1.96515 8.87772L9.06213 1.51444C10.6871 -0.171463 13.3326 -0.171475 14.9576 1.51443C16.5687 3.18595 16.5687 5.88581 14.9577 7.55734L7.87205 14.9088C6.92284 15.8936 5.37294 15.8937 4.42377 14.9088C3.48851 13.9384 3.48844 12.3753 4.42377 11.405C4.42378 11.405 4.42376 11.405 4.42377 11.405L10.8834 4.70303C11.1498 4.42662 11.5898 4.41852 11.8663 4.68493C12.1427 4.95135 12.1508 5.3914 11.8844 5.66782L5.42476 12.3698C5.00853 12.8016 5.00845 13.5121 5.42477 13.9441C5.82715 14.3616 6.4686 14.3616 6.87106 13.944C6.87106 13.944 6.87106 13.9441 6.87106 13.944L13.9567 6.59257C15.0487 5.45954 15.0487 3.61228 13.9567 2.47923M10.0631 2.47923C11.1413 1.3606 12.8784 1.36061 13.9567 2.47923Z' />
                                    </svg>
                                </div>
                                <button disabled class="" type="submit" id="textarea-post_sumbit"><img src="pics/SendIcon.svg"></button>
                            </form>
                        </div>
                        <div class="wall-filter-mobile" id="wall-filter-mobile">
                            <div class="wall-filter-mobile-info" id='wall-filter-mobile-info' onclick='showPopupWallFilterMobile()'>
                                <span>
                                    Все
                                </span>
                                <svg width='8' height='13' viewBox='0 0 8 13' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                    <path d='M6.96771 6.03603L1.12165 0.191904C0.865127 -0.0639698 0.449521 -0.0639698 0.192352 0.191904C-0.0641698 0.447777 -0.0641699 0.863383 0.192352 1.11926L5.57471 6.49968L0.192999 11.8801C-0.0635223 12.136 -0.0635224 12.5516 0.192999 12.8081C0.44952 13.064 0.865774 13.064 1.1223 12.8081L6.96836 6.96403C7.22094 6.7108 7.22094 6.28866 6.96771 6.03603Z' />
                                </svg>
                            </div>
                            <div class='wall-filter-popup-mobile' id='popup_wall-filter-mobile'>
                                <label class="wall-filter-popup-li-mobile">Все<input checked name="wall-filter-mobile" id="wall-filter-all-mobile" type="radio" value=""></label>
                                <div class='div-line'></div>
                                <label class="wall-filter-popup-li-mobile <?php if (($result_friend_1->num_rows + $result_friend_2->num_rows) == 0) {
                                                                                echo "no-friends";
                                                                            } ?>">Друзья<input name="wall-filter-mobile" id="wall-filter-friends-mobile" type="radio" value=""></label>
                            </div>
                        </div>
                        <div class="wall__user-posts" id="success-render-posts">
                        </div>
                        <nav class="first-part-mobile">
                            <ul>
                                <li id="active"><a href="./wall"><svg width="28" height="24" viewBox="0 0 28 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M4.56528 18C4.54895 17.8355 4.54059 17.6687 4.54059 17.5V10.5C4.54059 7.18629 7.22688 4.5 10.5406 4.5H21.4865C21.6336 4.5 21.7792 4.50635 21.9231 4.5188C21.681 1.98313 19.545 0 16.9459 0H5.00001C2.23858 0 0 2.23858 0 5V18H4.56528Z" />
                                            <path d="M6.05408 11C6.05408 8.23858 8.29265 6 11.0541 6H23C25.7614 6 28 8.23858 28 11V24H11.0541C8.29266 24 6.05408 21.7614 6.05408 19V11Z" />
                                        </svg>
                                        Стена
                                    </a></li>
                                <li><a href="./wall">
                                        <svg width="16" height="24" viewBox="0 0 16 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M4.97802 21.033C4.97802 21.8681 5.25275 22.5714 5.8022 23.1429C6.35165 23.7143 7.04396 24 7.87912 24C8.73626 24 9.43956 23.7253 9.98901 23.1758C10.5604 22.6044 10.8462 21.9011 10.8462 21.0659C10.8462 20.2088 10.5604 19.4945 9.98901 18.9231C9.41758 18.3516 8.71429 18.0659 7.87912 18.0659C7.04396 18.0659 6.35165 18.3516 5.8022 18.9231C5.25275 19.4725 4.97802 20.1758 4.97802 21.033ZM15.5275 7.51648C15.5275 9.07692 15.0769 10.4505 14.1758 11.6374C13.2967 12.8022 12.0769 13.7473 10.5165 14.4725L7.21978 16.2198L5.73626 12.6593L8.73626 11.0769C10.4066 10.022 11.2418 8.84615 11.2418 7.54945C11.2418 6.58242 10.9231 5.8022 10.2857 5.20879C9.64835 4.59341 8.83517 4.28571 7.84615 4.28571C5.93407 4.28571 4.64835 5.43956 3.98901 7.74725L0 6.2967C0.615385 4.34066 1.59341 2.8022 2.93407 1.68132C4.27473 0.56044 5.9011 0 7.81319 0C10.033 0 11.8681 0.714286 13.3187 2.14286C14.7912 3.54945 15.5275 5.34066 15.5275 7.51648Z" />
                                        </svg>
                                        ****</a></li>
                                <li><a href="./people">
                                        <svg width="28" height="24" viewBox="0 0 28 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.66113 15.5149C7.88196 16.2628 7.21631 17.0995 6.67468 18H0C0 15.2153 1.1062 12.5446 3.07532 10.5754C4.3374 9.31335 5.8877 8.40576 7.57141 7.91675C6.60938 7.09143 6 5.86682 6 4.5C6 2.01477 8.01477 0 10.5 0C12.9852 0 15 2.01477 15 4.5C15 4.6897 14.9883 4.87659 14.9655 5.06018C12.9185 6.0155 11.5 8.09216 11.5 10.5001C11.5 11.458 11.7245 12.3635 12.1237 13.1667C10.8506 13.749 9.67737 14.5393 8.66113 15.5149ZM22 10.5001C22 12.9854 19.9852 15.0001 17.5 15.0001C15.0148 15.0001 13 12.9854 13 10.5001C13 8.01489 15.0148 6.00012 17.5 6.00012C19.9852 6.00012 22 8.01489 22 10.5001ZM13.0278 14.5001C11.9414 15.0116 10.9407 15.7102 10.0753 16.5754C8.1062 18.5447 7 21.2153 7 24.0001H17.5H28C28 21.2153 26.8937 18.5447 24.9246 16.5754C24.0593 15.7102 23.0586 15.0116 21.9722 14.5001C20.8735 15.7277 19.277 16.5001 17.5 16.5001C15.723 16.5001 14.1265 15.7277 13.0278 14.5001Z" />
                                        </svg>
                                        Люди</a></li>
                                <li><a href="./profile"><svg width="28" height="24" viewBox="0 0 28 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.03709 11.3334C6.58858 12.0152 5.25423 12.9468 4.10051 14.1005C1.475 16.726 0 20.287 0 24L14 24H28C28 20.287 26.525 16.726 23.8995 14.1005C22.7458 12.9468 21.4114 12.0152 19.9629 11.3334C18.4981 12.97 16.3693 14 14 14C11.6307 14 9.50195 12.97 8.03709 11.3334Z" />
                                            <circle cx="14" cy="6" r="6" />
                                        </svg>
                                        Профиль
                                    </a></li>
                            </ul>
                        </nav>
                    </div>
                    <div class="third-part">
                        <div>
                            <div>
                                <p class="third-part-title">Фильтры</p>
                                <div class="wall-filter__choice">
                                    <label class="wall-filter-popup-li">Все<span>Посты всех пользователей</span><input checked name="wall-filter" id="wall-filter-all" type="radio" value=""></label>
                                    <label class="wall-filter-popup-li <?php if (($result_friend_1->num_rows + $result_friend_2->num_rows) == 0) {
                                                                            echo "no-friends";
                                                                        } ?>">Друзья<span>Посты ваших друзей</span><input name="wall-filter" id="wall-filter-friends" type="radio" value=""></label>
                                </div>
                            </div>
                            <div>
                                <p class="third-part-title">Поиск по хештегам</p>
                                <div>
                                    <svg id="icon-search-hashtag" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M10.2 2.4C5.89217 2.4 2.4 5.89217 2.4 10.2C2.4 14.5078 5.89218 18 10.2 18C14.5078 18 18 14.5078 18 10.2C18 5.89218 14.5078 2.4 10.2 2.4ZM0 10.2C0 4.56669 4.56669 0 10.2 0C15.8333 0 20.4 4.56669 20.4 10.2C20.4 12.5884 19.5791 14.7851 18.204 16.5233L23.6473 21.9502C24.1166 22.4181 24.1177 23.1779 23.6498 23.6473C23.1819 24.1166 22.4221 24.1177 21.9527 23.6498L16.505 18.2184C14.7697 19.5848 12.5801 20.4 10.2 20.4C4.56669 20.4 0 15.8333 0 10.2Z" />
                                    </svg>

                                    <input type="text" name="search-hashtag" id="search-hashtag" placeholder="вайб2024" value=<?php if (isset($_GET['search'])) {
                                                                                                                                    echo $_GET['search'];
                                                                                                                                } else {
                                                                                                                                    echo null;
                                                                                                                                } ?>>
                                    <input type="hidden" name="get-status" id="get-status" value=<?php if (isset($_GET['search'])) {
                                                                                                        echo $_GET['search'];
                                                                                                    } else {
                                                                                                        echo null;
                                                                                                    } ?>>
                                </div>
                                <ul id="success-search-hashtag">
                                </ul>
                            </div>
                        </div>
                    </div>
            </section>
    </main>
<?php require('footer.php');
        } ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="js/main.js?v=200"></script>
<script src="js/wall.js?v=200"></script>
</body>

</html>