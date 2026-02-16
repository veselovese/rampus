<?php
session_start();

if (isset($_SESSION['user'])) {
    require('back-files/like-or-dislike.php');
    require('back-files/find-user-position-in-top.php');
    require('back-files/get-user-friends.php');
    require_once('back-files/connect.php');

    $current_user_id = $_SESSION['user']['id'];
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="stylesheet" href="../css/main.css?v=311">
    <link rel="stylesheet" href="../css/wall.css?v=311">
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
                        <svg id='wall-loading-main' class='loading' width='28' height='28' viewBox='0 0 28 28' fill='none' xmlns='http://www.w3.org/2000/svg'>
                            <path d='M12.9279 0.596388C13.4228 -0.198796 14.5772 -0.198796 15.0721 0.596387L15.2392 0.864906C15.6417 1.51156 16.518 1.65824 17.1079 1.17771L17.3529 0.978176C18.0784 0.387274 19.1702 0.76325 19.3809 1.67654L19.452 1.98495C19.6234 2.72765 20.4047 3.1518 21.1183 2.88947L21.4146 2.78054C22.292 2.45795 23.2029 3.16916 23.1066 4.1016L23.074 4.41647C22.9957 5.17473 23.5974 5.83039 24.3572 5.81468L24.6727 5.80816C25.607 5.78885 26.2384 6.75822 25.8454 7.60875L25.7128 7.89596C25.3932 8.58763 25.7501 9.40374 26.4738 9.63636L26.7743 9.73295C27.6643 10.019 27.9476 11.1415 27.3007 11.818L27.0822 12.0464C26.5561 12.5965 26.6295 13.4846 27.2387 13.9404L27.4916 14.1296C28.2408 14.69 28.1454 15.844 27.3146 16.2731L27.034 16.418C26.3583 16.7669 26.1402 17.6308 26.5689 18.2603L26.7469 18.5217C27.2741 19.2957 26.8104 20.3561 25.8856 20.4913L25.5734 20.537C24.8213 20.6469 24.3354 21.393 24.5371 22.128L24.6209 22.4332C24.8689 23.337 24.0871 24.1889 23.1687 24.0156L22.8585 23.9571C22.1117 23.8161 21.4106 24.3635 21.3635 25.1244L21.3439 25.4403C21.2859 26.3759 20.2707 26.9271 19.4582 26.464L19.1838 26.3076C18.523 25.9311 17.6827 26.2204 17.3919 26.9247L17.2711 27.2172C16.9134 28.0832 15.7748 28.2738 15.1562 27.5712L14.9473 27.3339C14.4442 26.7625 13.5558 26.7625 13.0527 27.3339L12.8438 27.5712C12.2252 28.2738 11.0866 28.0832 10.7289 27.2172L10.6081 26.9247C10.3173 26.2204 9.47699 25.9311 8.81621 26.3076L8.54183 26.464C7.72928 26.9271 6.71407 26.3759 6.65611 25.4403L6.63653 25.1244C6.58939 24.3635 5.88832 23.8161 5.14145 23.9571L4.83132 24.0156C3.91291 24.1889 3.1311 23.337 3.37913 22.4332L3.46288 22.128C3.66458 21.393 3.17867 20.6469 2.42665 20.537L2.11438 20.4913C1.18963 20.3561 0.725939 19.2957 1.25309 18.5217L1.43109 18.2603C1.85978 17.6308 1.64168 16.7669 0.966004 16.418L0.685431 16.2731C-0.145446 15.844 -0.240771 14.69 0.508369 14.1296L0.76134 13.9404C1.37055 13.4846 1.44391 12.5965 0.917793 12.0464L0.699324 11.818C0.0523573 11.1415 0.335728 10.019 1.22568 9.73295L1.5262 9.63636C2.24992 9.40374 2.60679 8.58763 2.28724 7.89596L2.15455 7.60875C1.7616 6.75822 2.39296 5.78885 3.32729 5.80816L3.64279 5.81468C4.4026 5.83039 5.0043 5.17473 4.92595 4.41647L4.89342 4.1016C4.79707 3.16916 5.708 2.45795 6.58545 2.78054L6.88175 2.88947C7.5953 3.1518 8.37663 2.72765 8.54797 1.98495L8.61912 1.67655C8.82982 0.763251 9.9216 0.387274 10.6471 0.978176L10.8921 1.17771C11.482 1.65824 12.3583 1.51156 12.7608 0.864906L12.9279 0.596388Z' />
                        </svg>
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
<script src="../js/main.js?v=311"></script>
<script src="../js/wall.js?v=311"></script>
<script src="../js/comments-actions-current-post.js?v=311"></script>
<script src="../js/currentpost.js?v=311"></script>
</body>

</html>