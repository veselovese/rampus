<?php
session_start();

if (isset($_SESSION['user'])) {
    require_once('back-files/connect.php');
    require('back-files/like-or-dislike.php');
    require('back-files/repost-or-unrepost.php');
    require('back-files/find-user-position-in-top.php');
    require('back-files/get-user-friends.php');
    require_once('back-files/get-user-friends-id.php');


    $current_user_id = $_SESSION['user']['id'];
    $unread_main_posts = $_SESSION['user']['unread_main_posts'];
    $unread_thirty_seventh_posts = $_SESSION['user']['unread_thirty_seventh_posts'];
    $unread_all_posts = $_SESSION['user']['unread_all_posts'];

    $user_in_top = findUserPositionInTop($current_user_id, $connect);
    $friends_counter = $result_friend->num_rows;

    $user_with_friends_id_array = getUserFriendsId($current_user_id, $connect);
    $user_with_friends_id_array[] = $current_user_id;
    $user_friends_id = implode(',', $user_with_friends_id_array);

    $limit = 12;

    $sth_main = $connect->query("SELECT COUNT(posts.id) AS posts_counter FROM posts JOIN users ON posts.user_id = users.id WHERE posts.user_id IN ($user_friends_id) AND users.username != 'Thirty_seventh'");
    $row_main = $sth_main->fetch_assoc();
    $total_main = $row_main["posts_counter"];
    $amt_main = ceil($total_main / $limit);

    $sth_ts = $connect->query("SELECT COUNT(posts.id) AS posts_counter FROM posts JOIN users ON posts.user_id = users.id WHERE users.username = 'Thirty_seventh'");
    $row_ts = $sth_ts->fetch_assoc();
    $total_ts = $row_ts["posts_counter"];
    $amt_ts = ceil($total_ts / $limit);

    $sth_all = $connect->query("SELECT COUNT(posts.id) AS posts_counter FROM posts JOIN users ON posts.user_id = users.id WHERE users.username != 'Thirty_seventh' AND posts.repost_user_id IS NULL");
    $row_all = $sth_all->fetch_assoc();
    $total_all = $row_all["posts_counter"];
    $amt_all = ceil($total_all / $limit);
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="stylesheet" href="css/main.css?v=311">
    <link rel="stylesheet" href="css/wall.css?v=311">
    <title>Посты и репосты на стене в Рампус</title>
    <link rel="apple-touch-icon" sizes="180x180" href="favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicons/favicon-16x16.png">
    <link rel="manifest" href="favicons/site.webmanifest">
</head>

<body>
    <?php require_once('components/header.php'); ?> <main>
        <h1 class="title">Посты и репосты на стене в Рампус</h1>
        <?php if (!isset($_SESSION['user'])) {
            header("Location: auth?request=wall");
            exit();
        } else { ?>
            <section class="wrapper main-section">
                <?php require_once('components/main-menu.php'); ?>
                <div class="second-and-third-parts">
                    <div class="second-part">
                        <div class="wall-filter-mobile" id="wall-filter-mobile">
                            <div class="wall-filter__choice">
                                <label class="wall-filter-popup-li">
                                    <div>Основная<span>Вы и ваши друзья</span></div><?= $unread_main_posts > 0 ? '<span class="notification-in-filter" id="notification-in-filter__unread-main-posts-mobile">' . $unread_main_posts . '</span>' : '' ?><input checked name="wall-filter__mobile" id="wall-filter-main__mobile" type="radio" value="">
                                </label>
                                <label class="wall-filter-popup-li">
                                    <div>Расписание<span>Для Тридцать седьмой</span></div><?= $unread_thirty_seventh_posts > 0 ? '<span class="notification-in-filter" id="notification-in-filter__unread-thirty-seventh-posts-mobile">' . $unread_thirty_seventh_posts . '</span>' : '' ?><input name="wall-filter__mobile" id="wall-filter-timetable__mobile" type="radio" value="">
                                </label>
                                <label class="wall-filter-popup-li">
                                    <div>Глобал<span>Посты всех пользователей</span></div><?= $unread_all_posts > 0 ? '<span class="notification-in-filter" id="notification-in-filter__unread-all-posts-mobile">' . $unread_all_posts . '</span>' : '' ?><input name="wall-filter__mobile" id="wall-filter-all__mobile" type="radio" value="">
                                </label>
                            </div>
                        </div>
                        <div class="wall__user-posts" id="success-render-posts">
                        </div>
                        <svg id="wall-loading-main" class="loading" width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.9279 0.596388C13.4228 -0.198796 14.5772 -0.198796 15.0721 0.596387L15.2392 0.864906C15.6417 1.51156 16.518 1.65824 17.1079 1.17771L17.3529 0.978176C18.0784 0.387274 19.1702 0.76325 19.3809 1.67654L19.452 1.98495C19.6234 2.72765 20.4047 3.1518 21.1183 2.88947L21.4146 2.78054C22.292 2.45795 23.2029 3.16916 23.1066 4.1016L23.074 4.41647C22.9957 5.17473 23.5974 5.83039 24.3572 5.81468L24.6727 5.80816C25.607 5.78885 26.2384 6.75822 25.8454 7.60875L25.7128 7.89596C25.3932 8.58763 25.7501 9.40374 26.4738 9.63636L26.7743 9.73295C27.6643 10.019 27.9476 11.1415 27.3007 11.818L27.0822 12.0464C26.5561 12.5965 26.6295 13.4846 27.2387 13.9404L27.4916 14.1296C28.2408 14.69 28.1454 15.844 27.3146 16.2731L27.034 16.418C26.3583 16.7669 26.1402 17.6308 26.5689 18.2603L26.7469 18.5217C27.2741 19.2957 26.8104 20.3561 25.8856 20.4913L25.5734 20.537C24.8213 20.6469 24.3354 21.393 24.5371 22.128L24.6209 22.4332C24.8689 23.337 24.0871 24.1889 23.1687 24.0156L22.8585 23.9571C22.1117 23.8161 21.4106 24.3635 21.3635 25.1244L21.3439 25.4403C21.2859 26.3759 20.2707 26.9271 19.4582 26.464L19.1838 26.3076C18.523 25.9311 17.6827 26.2204 17.3919 26.9247L17.2711 27.2172C16.9134 28.0832 15.7748 28.2738 15.1562 27.5712L14.9473 27.3339C14.4442 26.7625 13.5558 26.7625 13.0527 27.3339L12.8438 27.5712C12.2252 28.2738 11.0866 28.0832 10.7289 27.2172L10.6081 26.9247C10.3173 26.2204 9.47699 25.9311 8.81621 26.3076L8.54183 26.464C7.72928 26.9271 6.71407 26.3759 6.65611 25.4403L6.63653 25.1244C6.58939 24.3635 5.88832 23.8161 5.14145 23.9571L4.83132 24.0156C3.91291 24.1889 3.1311 23.337 3.37913 22.4332L3.46288 22.128C3.66458 21.393 3.17867 20.6469 2.42665 20.537L2.11438 20.4913C1.18963 20.3561 0.725939 19.2957 1.25309 18.5217L1.43109 18.2603C1.85978 17.6308 1.64168 16.7669 0.966004 16.418L0.685431 16.2731C-0.145446 15.844 -0.240771 14.69 0.508369 14.1296L0.76134 13.9404C1.37055 13.4846 1.44391 12.5965 0.917793 12.0464L0.699324 11.818C0.0523573 11.1415 0.335728 10.019 1.22568 9.73295L1.5262 9.63636C2.24992 9.40374 2.60679 8.58763 2.28724 7.89596L2.15455 7.60875C1.7616 6.75822 2.39296 5.78885 3.32729 5.80816L3.64279 5.81468C4.4026 5.83039 5.0043 5.17473 4.92595 4.41647L4.89342 4.1016C4.79707 3.16916 5.708 2.45795 6.58545 2.78054L6.88175 2.88947C7.5953 3.1518 8.37663 2.72765 8.54797 1.98495L8.61912 1.67655C8.82982 0.763251 9.9216 0.387274 10.6471 0.978176L10.8921 1.17771C11.482 1.65824 12.3583 1.51156 12.7608 0.864906L12.9279 0.596388Z" />
                        </svg>
                        <svg id="wall-loading-posts" data-page="1" data-max-main="<?php echo $amt_main; ?>" data-max-ts="<?php echo $amt_ts; ?>" data-max-all="<?php echo $amt_all; ?>" class="" width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.9279 0.596388C13.4228 -0.198796 14.5772 -0.198796 15.0721 0.596387L15.2392 0.864906C15.6417 1.51156 16.518 1.65824 17.1079 1.17771L17.3529 0.978176C18.0784 0.387274 19.1702 0.76325 19.3809 1.67654L19.452 1.98495C19.6234 2.72765 20.4047 3.1518 21.1183 2.88947L21.4146 2.78054C22.292 2.45795 23.2029 3.16916 23.1066 4.1016L23.074 4.41647C22.9957 5.17473 23.5974 5.83039 24.3572 5.81468L24.6727 5.80816C25.607 5.78885 26.2384 6.75822 25.8454 7.60875L25.7128 7.89596C25.3932 8.58763 25.7501 9.40374 26.4738 9.63636L26.7743 9.73295C27.6643 10.019 27.9476 11.1415 27.3007 11.818L27.0822 12.0464C26.5561 12.5965 26.6295 13.4846 27.2387 13.9404L27.4916 14.1296C28.2408 14.69 28.1454 15.844 27.3146 16.2731L27.034 16.418C26.3583 16.7669 26.1402 17.6308 26.5689 18.2603L26.7469 18.5217C27.2741 19.2957 26.8104 20.3561 25.8856 20.4913L25.5734 20.537C24.8213 20.6469 24.3354 21.393 24.5371 22.128L24.6209 22.4332C24.8689 23.337 24.0871 24.1889 23.1687 24.0156L22.8585 23.9571C22.1117 23.8161 21.4106 24.3635 21.3635 25.1244L21.3439 25.4403C21.2859 26.3759 20.2707 26.9271 19.4582 26.464L19.1838 26.3076C18.523 25.9311 17.6827 26.2204 17.3919 26.9247L17.2711 27.2172C16.9134 28.0832 15.7748 28.2738 15.1562 27.5712L14.9473 27.3339C14.4442 26.7625 13.5558 26.7625 13.0527 27.3339L12.8438 27.5712C12.2252 28.2738 11.0866 28.0832 10.7289 27.2172L10.6081 26.9247C10.3173 26.2204 9.47699 25.9311 8.81621 26.3076L8.54183 26.464C7.72928 26.9271 6.71407 26.3759 6.65611 25.4403L6.63653 25.1244C6.58939 24.3635 5.88832 23.8161 5.14145 23.9571L4.83132 24.0156C3.91291 24.1889 3.1311 23.337 3.37913 22.4332L3.46288 22.128C3.66458 21.393 3.17867 20.6469 2.42665 20.537L2.11438 20.4913C1.18963 20.3561 0.725939 19.2957 1.25309 18.5217L1.43109 18.2603C1.85978 17.6308 1.64168 16.7669 0.966004 16.418L0.685431 16.2731C-0.145446 15.844 -0.240771 14.69 0.508369 14.1296L0.76134 13.9404C1.37055 13.4846 1.44391 12.5965 0.917793 12.0464L0.699324 11.818C0.0523573 11.1415 0.335728 10.019 1.22568 9.73295L1.5262 9.63636C2.24992 9.40374 2.60679 8.58763 2.28724 7.89596L2.15455 7.60875C1.7616 6.75822 2.39296 5.78885 3.32729 5.80816L3.64279 5.81468C4.4026 5.83039 5.0043 5.17473 4.92595 4.41647L4.89342 4.1016C4.79707 3.16916 5.708 2.45795 6.58545 2.78054L6.88175 2.88947C7.5953 3.1518 8.37663 2.72765 8.54797 1.98495L8.61912 1.67655C8.82982 0.763251 9.9216 0.387274 10.6471 0.978176L10.8921 1.17771C11.482 1.65824 12.3583 1.51156 12.7608 0.864906L12.9279 0.596388Z" />
                        </svg>
                        <div class="fixed-div-for-bottom">
                            <div class="wall__new-post">
                                <!-- <p>О чём расскажете сегодня?</p> -->
                                <form action="" id="new-post-form" method="post" enctype="multipart/form-data" autocomplete="off">
                                    <div class="current-post-images-div">
                                    </div>
                                    <div contenteditable="true" id="textarea-post" role="textbox" onkeyup="textareaPost(event)" onkeydown="textareaPostPlaceholder(event)"></div>
                                    <label for="textarea-post" id="textarea-post_label">Это просто жесть..</label>
                                    <input type="hidden" required name="post" id="textarea-post_input" value="">
                                    <input type="hidden" required name="post-source" value="source-wall">
                                    <input type="hidden" required name="post-search" value="<?php if (isset($_GET['search'])) {
                                                                                                echo $_GET['search'];
                                                                                            } ?>">
                                    <input type="file" name="post-images[]" id="post-image" multiple>
                                    <button disabled class="" type="submit" id="textarea-post_sumbit">
                                        <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M0 14C0 6.26801 6.26801 0 14 0C21.7319 0 28 6.26801 28 14C28 21.7319 21.7319 28 14 28C6.26801 28 0 21.7319 0 14ZM12.6 19.6C12.6 20.3732 13.2268 21 14 21C14.7732 21 15.4 20.3732 15.4 19.6V11.7799L17.2101 13.5899C17.7568 14.1366 18.6432 14.1366 19.1899 13.5899C19.7366 13.0432 19.7366 12.1568 19.1899 11.6101L15.1117 7.5319C15.0907 7.5108 15.0692 7.49043 15.0472 7.47078C14.7907 7.18197 14.4166 7 14 7C13.5834 7 13.2093 7.18197 12.9528 7.47078C12.9308 7.49042 12.9093 7.5108 12.8883 7.5319L8.81005 11.6101C8.26332 12.1568 8.26332 13.0432 8.81005 13.5899C9.35679 14.1366 10.2432 14.1366 10.79 13.5899L12.6 11.7799V19.6Z" />
                                        </svg>
                                    </button>
                                    <svg id="textarea-post_sumbit_loading" width="34" height="34" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M28.0636 27.7144C31.145 28.3018 33.8622 25.9433 33.4775 22.8301C32.7853 17.2269 31.5089 9.88242 30.2899 7.89607C28.4968 4.97435 25.9482 2.69714 22.9664 1.35242C19.9846 0.00769804 16.7035 -0.344143 13.5381 0.341391C10.3726 1.02692 7.46498 2.71904 5.18281 5.20376C2.90064 7.68849 1.34646 10.8542 0.716816 14.3006C0.0871677 17.747 -0.518248 20.8251 0.716853 24.0715C1.95195 27.318 6.83881 29.9574 9.52235 31.9097C11.6005 33.4215 13.1297 33.8454 15.2128 33.9597C16.0535 34.0058 16.5509 33.0553 16.0733 32.3619C15.8783 32.0788 15.5549 31.9173 15.213 31.8813C13.118 31.661 11.7778 30.1961 9.92214 28.8461C7.90948 27.3819 6.34081 25.3008 5.41448 22.866C4.48816 20.4312 4.24579 17.752 4.71802 15.1672C5.19026 12.5823 4.4511 11.3036 6.16272 9.44011C7.87435 7.57657 11.9599 5.21188 14.334 4.69773C16.7081 4.18358 18.8049 6.88753 21.0412 7.89607C23.2776 8.90461 26.4158 9.33902 27.7606 11.5303C28.7116 13.0798 25.6778 18.6803 23.8192 22.7598C22.824 24.9441 24.1868 26.9753 26.5447 27.4248L28.0636 27.7144Z" />
                                    </svg>
                                    <div class="postarea-buttons">
                                        <button type="button" class="post-image-icon" id="add-image-button">
                                            <svg width='18' height='20' viewBox='0 0 18 20' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                                <path fill-rule='evenodd' clip-rule='evenodd' d='M10.0631 2.47923C10.0631 2.47924 10.0631 2.47923 10.0631 2.47923L2.96613 9.8425C1.19829 11.6767 1.19827 14.6607 2.96614 16.4949C4.72012 18.3147 7.55291 18.3147 9.30686 16.4949L16.6998 8.82461C16.9662 8.5482 17.4063 8.54009 17.6827 8.80651C17.9591 9.07293 17.9672 9.51298 17.7008 9.78939L10.3078 17.4597C8.00712 19.8468 4.26589 19.8468 1.96515 17.4597C-0.321693 15.087 -0.321723 11.2504 1.96515 8.87772L9.06213 1.51444C10.6871 -0.171463 13.3326 -0.171475 14.9576 1.51443C16.5687 3.18595 16.5687 5.88581 14.9577 7.55734L7.87205 14.9088C6.92284 15.8936 5.37294 15.8937 4.42377 14.9088C3.48851 13.9384 3.48844 12.3753 4.42377 11.405C4.42378 11.405 4.42376 11.405 4.42377 11.405L10.8834 4.70303C11.1498 4.42662 11.5898 4.41852 11.8663 4.68493C12.1427 4.95135 12.1508 5.3914 11.8844 5.66782L5.42476 12.3698C5.00853 12.8016 5.00845 13.5121 5.42477 13.9441C5.82715 14.3616 6.4686 14.3616 6.87106 13.944C6.87106 13.944 6.87106 13.9441 6.87106 13.944L13.9567 6.59257C15.0487 5.45954 15.0487 3.61228 13.9567 2.47923M10.0631 2.47923C11.1413 1.3606 12.8784 1.36061 13.9567 2.47923Z' />
                                            </svg>
                                            <span>Фото</span>
                                            <span class="button-notification" id="foto-counter-notification"></span>
                                        </button>
                                        <div class="post-mode__div">
                                            <button type="button" id="post-mode-button" class="post-mode-div" onclick="showPostModePopup()">
                                                <svg width='28' height='31' viewBox='0 0 28 31' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                                    <path d='M25 19.6055C25 18.278 24.9991 17.3577 24.9404 16.6426C24.883 15.9425 24.7759 15.5499 24.626 15.2568V15.2559C24.2972 14.6137 23.7721 14.0909 23.125 13.7627C22.7942 13.595 22.343 13.4837 21.4863 13.4316C20.7964 13.3897 19.9321 13.3887 18.75 13.3887H8.75C7.56785 13.3887 6.70365 13.3897 6.01367 13.4316C5.15689 13.4837 4.70568 13.595 4.375 13.7627C3.72775 14.091 3.2028 14.6144 2.87402 15.2568C2.72409 15.5499 2.61704 15.9425 2.55957 16.6426C2.50088 17.3577 2.5 18.278 2.5 19.6055V21.7832C2.5 23.1107 2.50088 24.0309 2.55957 24.7461C2.61705 25.4465 2.72401 25.8397 2.87402 26.1328C3.20282 26.7751 3.72786 27.2987 4.375 27.627C4.67121 27.7772 5.06793 27.8831 5.77246 27.9404C6.49167 27.9989 7.41684 28 8.75 28H18.75C20.0832 28 21.0083 27.9989 21.7275 27.9404C22.432 27.8831 22.8287 27.7772 23.125 27.627C23.7721 27.2987 24.2972 26.7751 24.626 26.1328C24.776 25.8397 24.8829 25.4465 24.9404 24.7461C24.9991 24.0309 25 23.1107 25 21.7832V19.6055ZM20.3125 9.02734C20.3123 5.4276 17.3794 2.5 13.75 2.5C10.1205 2.5 7.18774 5.4276 7.1875 9.02734V10.8936C7.662 10.888 8.18153 10.8887 8.75 10.8887H18.75C19.3185 10.8887 19.838 10.888 20.3125 10.8936V9.02734ZM22.8125 11.0684C23.3248 11.1602 23.8023 11.3032 24.2559 11.5332C25.3726 12.0996 26.2816 13.0035 26.8516 14.1172C27.2125 14.8224 27.3614 15.5825 27.4316 16.4385C27.5007 17.2796 27.5 18.3195 27.5 19.6055V21.7832C27.5 23.0691 27.5006 24.1091 27.4316 24.9502C27.3614 25.8061 27.2125 26.5663 26.8516 27.2715C26.2816 28.3852 25.3726 29.29 24.2559 29.8564C23.5495 30.2147 22.7881 30.3628 21.9297 30.4326C21.0857 30.5012 20.0419 30.5 18.75 30.5H8.75C7.45813 30.5 6.41432 30.5012 5.57031 30.4326C4.71193 30.3628 3.9505 30.2147 3.24414 29.8564C2.19707 29.3253 1.33243 28.4969 0.758789 27.4775L0.648438 27.2715C0.287543 26.5663 0.138605 25.8061 0.0683596 24.9502C-0.000638187 24.1091 2.29428e-07 23.0691 2.29428e-07 21.7832V19.6055C1.85029e-07 18.3195 -0.000654526 17.2796 0.0683596 16.4385C0.138605 15.5825 0.287543 14.8224 0.648438 14.1172C1.21847 13.0035 2.12749 12.0996 3.24414 11.5332C3.69773 11.3031 4.17518 11.1602 4.6875 11.0684V9.02734C4.68774 4.0364 8.75033 0 13.75 0C18.7496 0 22.8123 4.0364 22.8125 9.02734V11.0684Z' />
                                                </svg>
                                                <span>Приватность</span>
                                            </button>
                                            <fieldset class="post-mode-fieldset" id="post-mode-fieldset">
                                                <legend>Режим публикации поста</legend>
                                                <div>
                                                    <input type="radio" id="for-all" name="post-mode" value="for-all" checked />
                                                    <label for="for-all" id="mode__for-all">Для всех<span>Увидят все пользователи</span></label>
                                                </div>
                                                <div>
                                                    <input type="radio" id="for-friends" name="post-mode" value="for-friends" />
                                                    <label for="for-friends" id="mode__for-friends">Для друзей<span>Увидят только друзья</span></label>
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <?php require_once('components/mobile-main-menu.php') ?>
                        </div>
                    </div>
                    <div class="third-part">
                        <div>
                            <div>
                                <p class="third-part-title">Стена</p>
                                <div class="wall-filter__choice">
                                    <label class="wall-filter-popup-li">
                                        <div>Основная<span>Вы и ваши друзья</span></div><?= $unread_main_posts > 0 ? '<span class="notification-in-filter" id="notification-in-filter__unread-main-posts">' . $unread_main_posts . '</span>' : '' ?><input checked name="wall-filter" id="wall-filter-main" type="radio" value="">
                                    </label>
                                    <label class="wall-filter-popup-li">
                                        <div>Расписание<span>Для Тридцать седьмой</span></div><?= $unread_thirty_seventh_posts > 0 ? '<span class="notification-in-filter" id="notification-in-filter__unread-thirty-seventh-posts">' . $unread_thirty_seventh_posts . '</span>' : '' ?><input name="wall-filter" id="wall-filter-timetable" type="radio" value="">
                                    </label>
                                    <label class="wall-filter-popup-li">
                                        <div>Глобал<span>Посты всех пользователей</span></div><?= $unread_all_posts > 0 ? '<span class="notification-in-filter" id="notification-in-filter__unread-all-posts">' . $unread_all_posts . '</span>' : '' ?><input name="wall-filter" id="wall-filter-all" type="radio" value="">
                                    </label>
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
                                <svg id="wall-loading-hashtags" class="loading" width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12.9279 0.596388C13.4228 -0.198796 14.5772 -0.198796 15.0721 0.596387L15.2392 0.864906C15.6417 1.51156 16.518 1.65824 17.1079 1.17771L17.3529 0.978176C18.0784 0.387274 19.1702 0.76325 19.3809 1.67654L19.452 1.98495C19.6234 2.72765 20.4047 3.1518 21.1183 2.88947L21.4146 2.78054C22.292 2.45795 23.2029 3.16916 23.1066 4.1016L23.074 4.41647C22.9957 5.17473 23.5974 5.83039 24.3572 5.81468L24.6727 5.80816C25.607 5.78885 26.2384 6.75822 25.8454 7.60875L25.7128 7.89596C25.3932 8.58763 25.7501 9.40374 26.4738 9.63636L26.7743 9.73295C27.6643 10.019 27.9476 11.1415 27.3007 11.818L27.0822 12.0464C26.5561 12.5965 26.6295 13.4846 27.2387 13.9404L27.4916 14.1296C28.2408 14.69 28.1454 15.844 27.3146 16.2731L27.034 16.418C26.3583 16.7669 26.1402 17.6308 26.5689 18.2603L26.7469 18.5217C27.2741 19.2957 26.8104 20.3561 25.8856 20.4913L25.5734 20.537C24.8213 20.6469 24.3354 21.393 24.5371 22.128L24.6209 22.4332C24.8689 23.337 24.0871 24.1889 23.1687 24.0156L22.8585 23.9571C22.1117 23.8161 21.4106 24.3635 21.3635 25.1244L21.3439 25.4403C21.2859 26.3759 20.2707 26.9271 19.4582 26.464L19.1838 26.3076C18.523 25.9311 17.6827 26.2204 17.3919 26.9247L17.2711 27.2172C16.9134 28.0832 15.7748 28.2738 15.1562 27.5712L14.9473 27.3339C14.4442 26.7625 13.5558 26.7625 13.0527 27.3339L12.8438 27.5712C12.2252 28.2738 11.0866 28.0832 10.7289 27.2172L10.6081 26.9247C10.3173 26.2204 9.47699 25.9311 8.81621 26.3076L8.54183 26.464C7.72928 26.9271 6.71407 26.3759 6.65611 25.4403L6.63653 25.1244C6.58939 24.3635 5.88832 23.8161 5.14145 23.9571L4.83132 24.0156C3.91291 24.1889 3.1311 23.337 3.37913 22.4332L3.46288 22.128C3.66458 21.393 3.17867 20.6469 2.42665 20.537L2.11438 20.4913C1.18963 20.3561 0.725939 19.2957 1.25309 18.5217L1.43109 18.2603C1.85978 17.6308 1.64168 16.7669 0.966004 16.418L0.685431 16.2731C-0.145446 15.844 -0.240771 14.69 0.508369 14.1296L0.76134 13.9404C1.37055 13.4846 1.44391 12.5965 0.917793 12.0464L0.699324 11.818C0.0523573 11.1415 0.335728 10.019 1.22568 9.73295L1.5262 9.63636C2.24992 9.40374 2.60679 8.58763 2.28724 7.89596L2.15455 7.60875C1.7616 6.75822 2.39296 5.78885 3.32729 5.80816L3.64279 5.81468C4.4026 5.83039 5.0043 5.17473 4.92595 4.41647L4.89342 4.1016C4.79707 3.16916 5.708 2.45795 6.58545 2.78054L6.88175 2.88947C7.5953 3.1518 8.37663 2.72765 8.54797 1.98495L8.61912 1.67655C8.82982 0.763251 9.9216 0.387274 10.6471 0.978176L10.8921 1.17771C11.482 1.65824 12.3583 1.51156 12.7608 0.864906L12.9279 0.596388Z" />
                                </svg>
                            </div>
                        </div>
                    </div>
            </section>
    </main>
<?php require_once('components/footer.php');
        } ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="js/main.js?v=311"></script>
<script src="js/wall.js?v=311"></script>
<script src="js/posts-actions.js?v=311"></script>
<script src="js/comments-actions.js?v=311"></script>
</body>

</html>

<?php
$connect->query("UPDATE users SET last_activity_date = NOW() WHERE id = $current_user_id");
$_SESSION['user']['unread_main_posts'] = 0;
?>