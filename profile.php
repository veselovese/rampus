<?php
session_start();
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/profile.css">
    <title>Профиль в Rampus (Рампус)</title>
</head>

<body>
    <?php require('header.php'); ?>
    <main>
        <h1 class="title">Профиль пользователя в Rampus (Рампус)</h1>
        <section class="wrapper main-section">
            <nav class="first-part">
                <ul>
                    <li class="active"><a href="./profile">Профиль</a></li>
                    <li><a href="./wall">Стена</a></li>
                    <li><a href="./chats">Чаты</a></li>
                    <li><a href="./people">Люди</a></li>
                    <li><a id="exit" href="./exit">Выйти</a></li>
                </ul>
            </nav>
            <div class="second-and-third-parts">
                <div class="second-part">
                    <div class="profile__user-info">
                        <img class="avatar" src="uploads/avatar/noavatar.jpg">
                        <div>
                            <p class="first-and-second-names"><span>Матвей</span> <span>Веселов</span></p>
                            <p class="username">@veselovese</p>
                            <p class="description">Дикий и опасный - разнесу всё</p>
                            <div class="balance">
                                <img src="pics/EcoCoinLogo.svg">
                                <p>130.00</p>
                            </div>
                        </div>
                    </div>
                    <div class="profole__user-menu">
                        <span>Имя</span>
                        <span>ID</span>
                        <span>Описание</span>
                        <span>Почта</span>
                        <span>Пароль</span>
                    </div>
                    <div class="profile__new-post">
                        <form action="./add">
                            <input required placeholder="О чём расскажете сегодня?">
                            <button type="submit"><img src="pics/SendIcon.svg"></button>
                        </form>
                    </div>
                    <div class="profile__user-posts">
                        <div>
                            <p>Ваши посты</p>
                            <img src="pics/SearchIcon.svg">
                        </div>
                        <div>
                            <div class="user-post">
                                <img src="pics/ThreeDotsIcon.svg">
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras vehicula semper condimentum. Suspendisse efficitur vulputate est, tempor commodo tellus sollicitudin eleifend. Fusce posuere, massa id vulputate elementum, diam diam pellentesque tortor, vulputate tincidunt tortor est vitae enim. Donec pharetra felis non lorem pulvinar, et blandit magna imperdiet. <span>#точкисбора</span></p>
                                <div>
                                    <button class="like-button"><svg width="23" height="19" viewBox="0 0 23 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M15.3643 17.1232L21.0488 11.4387L21.0494 11.4394C21.6548 10.834 22.135 10.1153 22.4626 9.32432C22.7903 8.53335 22.9589 7.6856 22.9589 6.82947C22.9589 5.97334 22.7903 5.12559 22.4626 4.33463C22.135 3.54366 21.6548 2.82498 21.0494 2.2196C20.4441 1.61423 19.7254 1.13402 18.9344 0.806393C18.1434 0.478767 17.2957 0.310142 16.4396 0.310145C15.5834 0.310148 14.7357 0.478778 13.9447 0.806409C13.1541 1.13391 12.4356 1.61388 11.8304 2.21893L11.8289 2.21742L11.8279 2.21836C11.2229 1.61375 10.5048 1.1341 9.71455 0.806772C8.92359 0.479147 8.07584 0.310521 7.2197 0.310524C6.36357 0.310526 5.51582 0.479157 4.72486 0.806787C3.93389 1.13442 3.2152 1.61463 2.60982 2.22001C2.00444 2.82539 1.52423 3.54408 1.1966 4.33504C0.868969 5.12601 0.700339 5.97376 0.700335 6.82989C0.700332 7.68602 0.868959 8.53377 1.19658 9.32474C1.52421 10.1157 2.00442 10.8344 2.60979 11.4398L2.60985 11.4397L8.29331 17.1232C10.2459 19.0758 13.4117 19.0758 15.3643 17.1232Z" />
                                        </svg>
                                        <span>37</span></button>
                                    <span>25 мая в 13:32</span>
                                </div>
                            </div>
                            <div class="user-post">
                                <img src="pics/ThreeDotsIcon.svg">
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras vehicula semper condimentum. Suspendisse efficitur vulputate est, tempor commodo tellus sollicitudin eleifend. Fusce posuere, massa id vulputate elementum, diam diam pellentesque tortor, vulputate tincidunt tortor est vitae enim. Donec pharetra felis non lorem pulvinar, et blandit magna imperdiet. <span>#точкисбора</span></p>
                                <div>
                                    <button class="like-button" id="liked"><svg width="23" height="19" viewBox="0 0 23 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M15.3643 17.1232L21.0488 11.4387L21.0494 11.4394C21.6548 10.834 22.135 10.1153 22.4626 9.32432C22.7903 8.53335 22.9589 7.6856 22.9589 6.82947C22.9589 5.97334 22.7903 5.12559 22.4626 4.33463C22.135 3.54366 21.6548 2.82498 21.0494 2.2196C20.4441 1.61423 19.7254 1.13402 18.9344 0.806393C18.1434 0.478767 17.2957 0.310142 16.4396 0.310145C15.5834 0.310148 14.7357 0.478778 13.9447 0.806409C13.1541 1.13391 12.4356 1.61388 11.8304 2.21893L11.8289 2.21742L11.8279 2.21836C11.2229 1.61375 10.5048 1.1341 9.71455 0.806772C8.92359 0.479147 8.07584 0.310521 7.2197 0.310524C6.36357 0.310526 5.51582 0.479157 4.72486 0.806787C3.93389 1.13442 3.2152 1.61463 2.60982 2.22001C2.00444 2.82539 1.52423 3.54408 1.1966 4.33504C0.868969 5.12601 0.700339 5.97376 0.700335 6.82989C0.700332 7.68602 0.868959 8.53377 1.19658 9.32474C1.52421 10.1157 2.00442 10.8344 2.60979 11.4398L2.60985 11.4397L8.29331 17.1232C10.2459 19.0758 13.4117 19.0758 15.3643 17.1232Z" />
                                        </svg>
                                        <span>22</span></button>
                                    <span>23 мая в 13:32</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="third-part">
                    <div>
                        <div class="profile__posts">
                            <div>
                                <img src="pics/PostIcon.svg">
                                <span>89</span>
                            </div>
                            <p>посты</p>
                        </div>
                        <div class="profile__likes">
                            <div>
                                <img src="pics/LikeIcon.svg">
                                <span>36</span>
                            </div>
                            <p>лайки</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php require('footer.php'); ?>
</body>

</html>