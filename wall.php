<?php
session_start();

if (isset($_SESSION['user'])) {
    require_once('back-files/connect.php');
    require('back-files/like-or-dislike.php');
    require('back-files/repost-or-unrepost.php');
    require('back-files/find-user-position-in-top.php');
    require('back-files/get-user-friends.php');

    $current_user_id = $_SESSION['user']['id'];

    $user_in_top = findUserPositionInTop($current_user_id, $connect);

    $_SESSION['user']['unread_posts'] = 0;
    $connect->query("UPDATE users SET unread_posts = 0 WHERE id = $current_user_id");
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="stylesheet" href="css/main.css?v=300beta">
    <link rel="stylesheet" href="css/wall.css?v=300beta">
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
                                <label class="wall-filter-popup-li">Все<span>Посты всех пользователей</span><input checked name="wall-filter__mobile" id="wall-filter-all__mobile" type="radio" value=""></label>
                                <label class="wall-filter-popup-li <?php if ($result_friend->num_rows == 0) {
                                                                        echo "no-friends";
                                                                    } ?>">Друзья<span>Посты ваших друзей</span><input name="wall-filter__mobile" id="wall-filter-friends__mobile" type="radio" value=""></label>
                            </div>
                        </div>
                        <!-- <div class="wall__user-posts" id="new-posts"></div> -->
                        <div class="wall__user-posts" id="success-render-posts">
                        </div>
                        <div class="wall__new-post">
                            <!-- <p>О чём расскажете сегодня?</p> -->
                            <form action="./back-files/add" method="post" enctype="multipart/form-data" autocomplete="off">
                                <div class="current-post-image-div">
                                    <img class="post-image" id="current-post-image" src="">
                                    <div class="post-image-delete" onclick="clearPostImage()">
                                        <svg width='10' height='10' viewBox='0 0 10 10' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                            <path d='M0.191016 8.88671C-0.0636719 9.14141 -0.0636719 9.55428 0.191016 9.80898C0.445703 10.0637 0.858643 10.0637 1.11333 9.80898L0.191016 8.88671ZM5.46114 5.46114C5.71584 5.20644 5.71584 4.79357 5.46114 4.53888C5.20644 4.28418 4.79357 4.28418 4.53888 4.53888L5.46114 5.46114ZM4.53888 4.53888C4.28418 4.79357 4.28418 5.20644 4.53888 5.46114C4.79357 5.71584 5.20644 5.71584 5.46114 5.46114L4.53888 4.53888ZM9.80898 1.11333C10.0637 0.858644 10.0637 0.445703 9.80898 0.191016C9.55428 -0.0636719 9.14141 -0.0636719 8.88671 0.191016L9.80898 1.11333ZM5.46114 4.53888C5.20644 4.28418 4.79357 4.28418 4.53888 4.53888C4.28418 4.79357 4.28418 5.20644 4.53888 5.46114L5.46114 4.53888ZM8.88671 9.80898C9.14141 10.0637 9.55428 10.0637 9.80898 9.80898C10.0637 9.55428 10.0637 9.14141 9.80898 8.88671L8.88671 9.80898ZM4.53888 5.46114C4.79357 5.71584 5.20644 5.71584 5.46114 5.46114C5.71584 5.20644 5.71584 4.79357 5.46114 4.53888L4.53888 5.46114ZM1.11333 0.191016C0.858643 -0.0636719 0.445703 -0.0636719 0.191016 0.191016C-0.0636719 0.445703 -0.0636719 0.858644 0.191016 1.11333L1.11333 0.191016ZM1.11333 9.80898L5.46114 5.46114L4.53888 4.53888L0.191016 8.88671L1.11333 9.80898ZM5.46114 5.46114L9.80898 1.11333L8.88671 0.191016L4.53888 4.53888L5.46114 5.46114ZM4.53888 5.46114L8.88671 9.80898L9.80898 8.88671L5.46114 4.53888L4.53888 5.46114ZM5.46114 4.53888L1.11333 0.191016L0.191016 1.11333L4.53888 5.46114L5.46114 4.53888Z' />
                                        </svg>
                                    </div>
                                </div>
                                <div contenteditable="true" id="textarea-post" role="textbox" onkeyup="textareaPost(event)" onkeydown="textareaPostPlaceholder(event)"></div>
                                <label for="textarea-post" id="textarea-post_label">Планирую отпуск на Аляске..</label>
                                <input type="hidden" required name="post" id="textarea-post_input" value="">
                                <input type="hidden" required name="post-source" value="source-wall">
                                <input type="hidden" required name="post-search" value="<?php if (isset($_GET['search'])) {
                                                                                            echo $_GET['search'];
                                                                                        } ?>">
                                <input type="file" name="post-image" id="post-image">
                                <button disabled class="" type="submit" id="textarea-post_sumbit">
                                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M0 14C0 6.26801 6.26801 0 14 0C21.7319 0 28 6.26801 28 14C28 21.7319 21.7319 28 14 28C6.26801 28 0 21.7319 0 14ZM12.6 19.6C12.6 20.3732 13.2268 21 14 21C14.7732 21 15.4 20.3732 15.4 19.6V11.7799L17.2101 13.5899C17.7568 14.1366 18.6432 14.1366 19.1899 13.5899C19.7366 13.0432 19.7366 12.1568 19.1899 11.6101L15.1117 7.5319C15.0907 7.5108 15.0692 7.49043 15.0472 7.47078C14.7907 7.18197 14.4166 7 14 7C13.5834 7 13.2093 7.18197 12.9528 7.47078C12.9308 7.49042 12.9093 7.5108 12.8883 7.5319L8.81005 11.6101C8.26332 12.1568 8.26332 13.0432 8.81005 13.5899C9.35679 14.1366 10.2432 14.1366 10.79 13.5899L12.6 11.7799V19.6Z" />
                                    </svg>
                                </button>
                                <div class="postarea-buttons">
                                    <div onclick='addImageToPost()' class="post-image-icon">
                                        <svg width='18' height='20' viewBox='0 0 18 20' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                            <path fill-rule='evenodd' clip-rule='evenodd' d='M10.0631 2.47923C10.0631 2.47924 10.0631 2.47923 10.0631 2.47923L2.96613 9.8425C1.19829 11.6767 1.19827 14.6607 2.96614 16.4949C4.72012 18.3147 7.55291 18.3147 9.30686 16.4949L16.6998 8.82461C16.9662 8.5482 17.4063 8.54009 17.6827 8.80651C17.9591 9.07293 17.9672 9.51298 17.7008 9.78939L10.3078 17.4597C8.00712 19.8468 4.26589 19.8468 1.96515 17.4597C-0.321693 15.087 -0.321723 11.2504 1.96515 8.87772L9.06213 1.51444C10.6871 -0.171463 13.3326 -0.171475 14.9576 1.51443C16.5687 3.18595 16.5687 5.88581 14.9577 7.55734L7.87205 14.9088C6.92284 15.8936 5.37294 15.8937 4.42377 14.9088C3.48851 13.9384 3.48844 12.3753 4.42377 11.405C4.42378 11.405 4.42376 11.405 4.42377 11.405L10.8834 4.70303C11.1498 4.42662 11.5898 4.41852 11.8663 4.68493C12.1427 4.95135 12.1508 5.3914 11.8844 5.66782L5.42476 12.3698C5.00853 12.8016 5.00845 13.5121 5.42477 13.9441C5.82715 14.3616 6.4686 14.3616 6.87106 13.944C6.87106 13.944 6.87106 13.9441 6.87106 13.944L13.9567 6.59257C15.0487 5.45954 15.0487 3.61228 13.9567 2.47923M10.0631 2.47923C11.1413 1.3606 12.8784 1.36061 13.9567 2.47923Z' />
                                        </svg>
                                        <span>Фото</span>
                                    </div>
                                    <div class="post-mode__div">
                                        <div class="post-mode-div" onclick="showPostModePopup()">
                                            <svg width='28' height='31' viewBox='0 0 28 31' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                                <path d='M25 19.6055C25 18.278 24.9991 17.3577 24.9404 16.6426C24.883 15.9425 24.7759 15.5499 24.626 15.2568V15.2559C24.2972 14.6137 23.7721 14.0909 23.125 13.7627C22.7942 13.595 22.343 13.4837 21.4863 13.4316C20.7964 13.3897 19.9321 13.3887 18.75 13.3887H8.75C7.56785 13.3887 6.70365 13.3897 6.01367 13.4316C5.15689 13.4837 4.70568 13.595 4.375 13.7627C3.72775 14.091 3.2028 14.6144 2.87402 15.2568C2.72409 15.5499 2.61704 15.9425 2.55957 16.6426C2.50088 17.3577 2.5 18.278 2.5 19.6055V21.7832C2.5 23.1107 2.50088 24.0309 2.55957 24.7461C2.61705 25.4465 2.72401 25.8397 2.87402 26.1328C3.20282 26.7751 3.72786 27.2987 4.375 27.627C4.67121 27.7772 5.06793 27.8831 5.77246 27.9404C6.49167 27.9989 7.41684 28 8.75 28H18.75C20.0832 28 21.0083 27.9989 21.7275 27.9404C22.432 27.8831 22.8287 27.7772 23.125 27.627C23.7721 27.2987 24.2972 26.7751 24.626 26.1328C24.776 25.8397 24.8829 25.4465 24.9404 24.7461C24.9991 24.0309 25 23.1107 25 21.7832V19.6055ZM20.3125 9.02734C20.3123 5.4276 17.3794 2.5 13.75 2.5C10.1205 2.5 7.18774 5.4276 7.1875 9.02734V10.8936C7.662 10.888 8.18153 10.8887 8.75 10.8887H18.75C19.3185 10.8887 19.838 10.888 20.3125 10.8936V9.02734ZM22.8125 11.0684C23.3248 11.1602 23.8023 11.3032 24.2559 11.5332C25.3726 12.0996 26.2816 13.0035 26.8516 14.1172C27.2125 14.8224 27.3614 15.5825 27.4316 16.4385C27.5007 17.2796 27.5 18.3195 27.5 19.6055V21.7832C27.5 23.0691 27.5006 24.1091 27.4316 24.9502C27.3614 25.8061 27.2125 26.5663 26.8516 27.2715C26.2816 28.3852 25.3726 29.29 24.2559 29.8564C23.5495 30.2147 22.7881 30.3628 21.9297 30.4326C21.0857 30.5012 20.0419 30.5 18.75 30.5H8.75C7.45813 30.5 6.41432 30.5012 5.57031 30.4326C4.71193 30.3628 3.9505 30.2147 3.24414 29.8564C2.19707 29.3253 1.33243 28.4969 0.758789 27.4775L0.648438 27.2715C0.287543 26.5663 0.138605 25.8061 0.0683596 24.9502C-0.000638187 24.1091 2.29428e-07 23.0691 2.29428e-07 21.7832V19.6055C1.85029e-07 18.3195 -0.000654526 17.2796 0.0683596 16.4385C0.138605 15.5825 0.287543 14.8224 0.648438 14.1172C1.21847 13.0035 2.12749 12.0996 3.24414 11.5332C3.69773 11.3031 4.17518 11.1602 4.6875 11.0684V9.02734C4.68774 4.0364 8.75033 0 13.75 0C18.7496 0 22.8123 4.0364 22.8125 9.02734V11.0684Z' />
                                            </svg>
                                            <span>Приватность</span>
                                        </div>
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
                    <div class="third-part">
                        <div>
                            <div>
                                <p class="third-part-title">Фильтры</p>
                                <div class="wall-filter__choice">
                                    <label class="wall-filter-popup-li">Все<span>Посты всех пользователей</span><input checked name="wall-filter" id="wall-filter-all" type="radio" value=""></label>
                                    <label class="wall-filter-popup-li <?php if ($result_friend->num_rows == 0) {
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
<?php require_once('components/footer.php');
        } ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="js/main.js?v=300beta"></script>
<script src="js/wall.js?v=300beta"></script>
</body>

</html>