<?php
session_start();

require_once('back-files/connect.php');

if (isset($_SESSION['user'])) {
    require('back-files/rating-trophies.php');
    require('back-files/find-user-position-in-top.php');
    require('back-files/get-user-friends.php');

    $user_id = $_SESSION['user']['id'];
    $result = $connect->query("SELECT * FROM users WHERE id = $user_id");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $username = $row["username"];
            $first_name = $row["first_name"];
            $second_name = $row["second_name"];
            $current_avatar = $row["avatar"];
        }
    }

    $user_in_top = findUserPositionInTop($user_id, $connect);
    $friends_counter = $result_friend_1->num_rows + $result_friend_2->num_rows;

    $unread_posts = $_SESSION['user']['unread_posts'];
    $result_request_to_counter = $result_request_to->num_rows;
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="stylesheet" href="css/main.css?v=250">
    <link rel="stylesheet" href="css/people.css?v=250">
    <title>Друзья пользователя в Rampus (Рампус)</title>
    <link rel="apple-touch-icon" sizes="180x180" href="favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicons/favicon-16x16.png">
    <link rel="manifest" href="favicons/site.webmanifest">
</head>

<body>
    <?php require('header.php'); ?>
    <main>
        <h1 class="title">Друзья в Rampus (Рампус)</h1>
        <?php if (!isset($_SESSION['user'])) {
            header("Location: auth?request=people");
            exit();
        } else { ?>
            <section class="wrapper main-section">
                <?php require_once('components/main-menu.php'); ?>
                <div class="second-and-third-parts">
                    <div class="second-part">
                        <div class="friends__users">
                            <p>Друзья<span><?= $friends_counter ?></span></p>
                            <?php if ($result_request_to->num_rows > 0) { ?>
                                <div class="request_to">
                                    <div class="request_to-div">
                                        <a href="./requests" class="friends-title link">Заявки в друзья<span style="margin-left: 0.625rem;"><?= $result_request_to->num_rows ?></span></a>
                                        <a href="./requests.php" class="open-requests-page">
                                            Все
                                            <svg class='pointer' width='8' height='13' viewBox='0 0 8 13' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                                <path d='M6.96771 6.03603L1.12165 0.191904C0.865127 -0.0639698 0.449521 -0.0639698 0.192352 0.191904C-0.0641698 0.447777 -0.0641699 0.863383 0.192352 1.11926L5.57471 6.49968L0.192999 11.8801C-0.0635223 12.136 -0.0635224 12.5516 0.192999 12.8081C0.44952 13.064 0.865774 13.064 1.1223 12.8081L6.96836 6.96403C7.22094 6.7108 7.22094 6.28866 6.96771 6.03603Z' />
                                            </svg>
                                        </a>
                                    </div>
                                    <div class="users-requests">
                                        <?php
                                        $requests_counter = 0;
                                        while ($row_request = $result_request_to->fetch_assoc()) {
                                            $other_id = $row_request['id'];
                                            $username = $row_request['username'];
                                            $avatar = $row_request['avatar'];
                                            $first_name = $row_request['first_name'];
                                            $second_name = $row_request['second_name'];
                                            if ($requests_counter < 3) {
                                                echo "<div class='request-from-user' onclick='openOtherUserProfile(event, `$username`)'>";
                                                echo "<div class='only-user-info'>";
                                                echo "<img src='uploads/avatar/thin_$avatar'>";
                                                echo "<div class='user-info'>";
                                                echo "<p>$first_name</p>";
                                                echo "<p>@$username</p>";
                                                echo "</div>";
                                                echo "</div>";
                                                echo "<div class='answer-to-requests' id='popup_answer-to-request_$other_id'>";
                                                echo "<span class='answer-to-requests-li request' id='add-to-friends_$other_id' onclick='addToFriendsRequestPage($other_id, $id)'><svg width='17' height='12' viewBox='0 0 17 12' fill='none' xmlns='http://www.w3.org/2000/svg'>
<path d='M1 6.45458L5.62964 11L15.8148 1' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/>
</svg>
Принять</span>";
                                                echo "<span class='answer-to-requests-li unrequest' id='unrequest-from-friends_$other_id' onclick='unrequestToFriendsRequestPage($other_id, $id)'><svg width='10' height='10' viewBox='0 0 10 10' fill='none' xmlns='http://www.w3.org/2000/svg'>
<path d='M0.191016 8.88671C-0.0636719 9.14141 -0.0636719 9.55428 0.191016 9.80898C0.445703 10.0637 0.858643 10.0637 1.11333 9.80898L0.191016 8.88671ZM5.46114 5.46114C5.71584 5.20644 5.71584 4.79357 5.46114 4.53888C5.20644 4.28418 4.79357 4.28418 4.53888 4.53888L5.46114 5.46114ZM4.53888 4.53888C4.28418 4.79357 4.28418 5.20644 4.53888 5.46114C4.79357 5.71584 5.20644 5.71584 5.46114 5.46114L4.53888 4.53888ZM9.80898 1.11333C10.0637 0.858644 10.0637 0.445703 9.80898 0.191016C9.55428 -0.0636719 9.14141 -0.0636719 8.88671 0.191016L9.80898 1.11333ZM5.46114 4.53888C5.20644 4.28418 4.79357 4.28418 4.53888 4.53888C4.28418 4.79357 4.28418 5.20644 4.53888 5.46114L5.46114 4.53888ZM8.88671 9.80898C9.14141 10.0637 9.55428 10.0637 9.80898 9.80898C10.0637 9.55428 10.0637 9.14141 9.80898 8.88671L8.88671 9.80898ZM4.53888 5.46114C4.79357 5.71584 5.20644 5.71584 5.46114 5.46114C5.71584 5.20644 5.71584 4.79357 5.46114 4.53888L4.53888 5.46114ZM1.11333 0.191016C0.858643 -0.0636719 0.445703 -0.0636719 0.191016 0.191016C-0.0636719 0.445703 -0.0636719 0.858644 0.191016 1.11333L1.11333 0.191016ZM1.11333 9.80898L5.46114 5.46114L4.53888 4.53888L0.191016 8.88671L1.11333 9.80898ZM5.46114 5.46114L9.80898 1.11333L8.88671 0.191016L4.53888 4.53888L5.46114 5.46114ZM4.53888 5.46114L8.88671 9.80898L9.80898 8.88671L5.46114 4.53888L4.53888 5.46114ZM5.46114 4.53888L1.11333 0.191016L0.191016 1.11333L4.53888 5.46114L5.46114 4.53888Z' />
</svg>
</span>";
                                                echo "</div>";
                                                echo "</div>";
                                            } else {
                                                $requests_counter++;
                                                break;
                                            }
                                            $requests_counter++;
                                        }
                                        if ($requests_counter > 3) {
                                            // echo "<a href='./requests' class='see-all-requests'>";
                                            // echo "<div>";
                                            // echo "<p>Все завки</p>";
                                            // echo "<span>Раздел заявок<br>в друзья</span>";
                                            // echo "</div>";
                                            // echo "<svg class='pointer' width='8' height='13' viewBox='0 0 8 13' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                            //         <path d='M6.96771 6.03603L1.12165 0.191904C0.865127 -0.0639698 0.449521 -0.0639698 0.192352 0.191904C-0.0641698 0.447777 -0.0641699 0.863383 0.192352 1.11926L5.57471 6.49968L0.192999 11.8801C-0.0635223 12.136 -0.0635224 12.5516 0.192999 12.8081C0.44952 13.064 0.865774 13.064 1.1223 12.8081L6.96836 6.96403C7.22094 6.7108 7.22094 6.28866 6.96771 6.03603Z' />
                                            //         </svg>";
                                            // echo "</a>";
                                            // echo "<a href='./requests' class='see-all-requests-icon-div'>";
                                            // $result_request_to_counter -= 3;
                                            // echo "Еще $result_request_to_counter";
                                            // echo "</a>";
                                        } ?>
                                    </div>

                                </div>
                            <?php } ?>
                            <p class="friends-title">Ваши друзья</p>
                            <?php if (($result_friend_1->num_rows > 0) || ($result_friend_2->num_rows > 0)) {
                                echo "<ul>";
                                if ($result_friend_1->num_rows > 0) {
                                    while ($row_friend_1 = $result_friend_1->fetch_assoc()) {
                                        $friends_counter -= 1;
                                        $id = $row_friend_1['id'];
                                        $username = $row_friend_1['username'];
                                        $avatar = $row_friend_1['avatar'];
                                        $first_name = $row_friend_1['first_name'];
                                        $second_name = $row_friend_1['second_name'];
                                        echo "<li class='user' onclick='openOtherUserProfile(event, `$username`)'>";
                                        echo "<img src='uploads/avatar/thin_$avatar'>";
                                        echo "<div class='current-user-info'>";
                                        if ($username == 'rampus') {
                                            echo "<p class='rampus'>$first_name $second_name<img src='pics/SuperUserIcon.svg'></p>";
                                        } else {
                                            echo "<p>$first_name $second_name</p>";
                                        }
                                        echo "<p>@$username</p>";
                                        echo "</div>";
                                        echo "</li>";
                                        if ($friends_counter > 0) {
                                            echo "<div class='div-line'></div>";
                                        }
                                    }
                                }
                                if ($result_friend_2->num_rows > 0) {
                                    while ($row_friend_2 = $result_friend_2->fetch_assoc()) {
                                        $friends_counter -= 1;
                                        $id = $row_friend_2['id'];
                                        $username = $row_friend_2['username'];
                                        $avatar = $row_friend_2['avatar'];
                                        $first_name = $row_friend_2['first_name'];
                                        $second_name = $row_friend_2['second_name'];
                                        echo "<li class='user' onclick='openOtherUserProfile(event, `$username`)'>";
                                        echo "<img src='uploads/avatar/thin_$avatar'>";
                                        echo "<div class='current-user-info'>";
                                        if ($username == 'rampus') {
                                            echo "<p class='rampus'>$first_name $second_name<img src='pics/SuperUserIcon.svg'></p>";
                                        } else {
                                            echo "<p>$first_name $second_name</p>";
                                        }
                                        echo "<p>@$username</p>";
                                        echo "</div>";
                                        echo "</li>";
                                        if ($friends_counter > 0) {
                                            echo "<div class='div-line'></div>";
                                        }
                                    }
                                }
                                echo "</ul>";
                            } else { ?>
                                <p>Не расстраивайся, но у тебя нет друзей</p>
                            <?php } ?>
                        </div>
                        <nav class="first-part-mobile">
                            <ul>
                                <li>
                                    <a href="./wall">
                                        <svg width="28" height="24" viewBox="0 0 28 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M20.9219 4C20.4586 1.71776 18.4408 0 16.0219 0H4.99997C2.23855 0 -2.28882e-05 2.23858 -2.28882e-05 5V18H5V11C5 7.13401 8.13401 4 12 4H20.9219Z" />
                                            <path d="M7 11C7 8.23858 9.23858 6 12 6H23C25.7614 6 28 8.23858 28 11V24H12C9.23858 24 7 21.7614 7 19V11Z" />
                                        </svg>
                                        Стена
                                    </a>
                                </li>
                                <li>
                                    <a href="./users">
                                        <svg width='28' height='24' viewBox='0 0 28 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                            <path d='M15 4.49829C12.651 5.47785 11 7.79617 11 10.5001C11 11.397 11.1816 12.2514 11.5102 13.0287C10.4728 13.595 9.51192 14.3105 8.66116 15.1613C7.81316 16.0093 7.09958 16.9666 6.53414 18.0001L0 18C0 15.2153 1.10625 12.5446 3.07538 10.5754C4.33742 9.31339 5.88765 8.4058 7.5714 7.91672C6.60943 7.09142 6 5.86688 6 4.5C6 2.01472 8.01472 0 10.5 0C12.9847 0 14.9991 2.01379 15 4.49829Z' />
                                            <path d='M21.25 10.5001C21.25 12.5712 19.5711 14.2501 17.5 14.2501C15.4289 14.2501 13.75 12.5712 13.75 10.5001C13.75 8.42905 15.4289 6.75012 17.5 6.75012C19.5711 6.75012 21.25 8.42905 21.25 10.5001ZM10.6057 17.1058C11.2822 16.4293 12.0479 15.8625 12.8752 15.4168C14.0826 16.5528 15.7103 17.2501 17.5 17.2501C19.2897 17.2501 20.9174 16.5528 22.1248 15.4168C22.9521 15.8625 23.7177 16.4293 24.3943 17.1058C26.0452 18.7567 27.0429 20.9386 27.2211 23.2501H17.5L7.77887 23.2501C7.95711 20.9386 8.95483 18.7567 10.6057 17.1058Z' stroke-linecap='round' stroke-linejoin='round' />
                                        </svg>
                                        Люди
                                    </a>
                                </li>
                                <li>
                                    <a href="./trophies">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M1.9268 10.8618C2.52053 11.4533 3.26562 11.9723 4.12276 12.3C5.41329 15.0644 8.12607 17.0333 11.3277 17.2769V18.9767H8.80676C7.11016 18.9767 5.72553 20.3562 5.72553 22.0465V24H18.6106V22.0465C18.6106 20.3562 17.226 18.9767 15.5294 18.9767H13.0084V17.2448C16.0631 16.8937 18.6321 14.9671 19.8773 12.3C20.7344 11.9723 21.4795 11.4533 22.0732 10.8618C22.0836 10.8514 22.0938 10.8407 22.1036 10.8298C23.2118 9.60312 24 8.07245 24 6.25114C24 4.22598 22.3913 2.62324 20.3585 2.62324H19.9556C19.0317 1.05355 17.3205 0 15.3614 0H8.63867C6.6795 0 4.96828 1.05355 4.04441 2.62324H3.64145C1.60872 2.62324 0 4.22598 0 6.25114C0 8.07245 0.788245 9.60312 1.89638 10.8298C1.90624 10.8407 1.91638 10.8514 1.9268 10.8618ZM3.2532 4.33392C2.3426 4.50732 1.68067 5.28363 1.68067 6.25114C1.68067 7.54825 2.23086 8.69119 3.13068 9.69311C3.18633 9.74808 3.24349 9.80196 3.30209 9.85462C3.23071 9.40931 3.19327 8.95182 3.19327 8.4837V5.13486C3.19327 4.86518 3.21305 4.59734 3.2532 4.33392ZM20.8067 5.13486C20.8067 4.86518 20.7869 4.59734 20.7468 4.33392C21.6574 4.50732 22.3193 5.28363 22.3193 6.25114C22.3193 7.54825 21.7691 8.69119 20.8693 9.69311C20.8137 9.74808 20.7565 9.80196 20.6979 9.85461C20.7693 9.40931 20.8067 8.95182 20.8067 8.4837V5.13486Z" />
                                        </svg>
                                        Трофеи
                                    </a>
                                </li>
                                <li>
                                    <a href="./profile">
                                        <!-- <svg width="28" height="24" viewBox="0 0 28 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.03709 11.3334C6.58858 12.0152 5.25423 12.9468 4.10051 14.1005C1.475 16.726 0 20.287 0 24L14 24H28C28 20.287 26.525 16.726 23.8995 14.1005C22.7458 12.9468 21.4114 12.0152 19.9629 11.3334C18.4981 12.97 16.3693 14 14 14C11.6307 14 9.50195 12.97 8.03709 11.3334Z" />
                                            <circle cx="14" cy="6" r="6" />
                                        </svg> -->
                                        <img class="menu-avatar" src="uploads/avatar/thin_<?= $current_avatar ?>">
                                        Профиль
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    <div class="third-part">
                    </div>
            </section>
    </main>
<?php require('footer.php');
        } ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="js/main.js?v=250"></script>
<script src="js/people.js?v=250"></script>
</body>

</html>