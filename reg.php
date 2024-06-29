<?php
session_start();
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="stylesheet" href="css/main.css">
    <title>Регистрация в Rampus</title>
</head>

<body>
    <?php require('header.php'); ?>
    <main>
        <h1 class="title">Регистрация в Rampus (Рампус)</h1>
        <section class="wrapper reg__section">
            <form method="POST" class="reg__form" action="./signin">
                <legend>Регистрация Rampus</legend>
                <div>
                    <div class="reg__input-div">
                        <label>Имя<input type="text" required placeholder="Рампус"></label>
                        <label>Фамилия<input type="text" required placeholder="Рампусов"></label>
                        <label>ID (имя пользователя)<input type="text" required placeholder="rampus"></label>
                        <a href="./auth">У меня есть аккаунт</a>
                    </div>
                    <div class="div-line"></div>
                    <div class="reg__input-div">
                        <label>Почта<input type="email" required placeholder="rampus@example.com"></label>
                        <label id="reg__lable_pass-1">Пароль
                            <input type="text" required placeholder="********" minlength="8">
                            <div><span>8 символов</span><span>Цифра</span><span>Символ «!» или «?»</span></div>
                            
                        </label>
                        <label id="reg__lable_pass-2"><input type="text" required placeholder="Тот же пароль ещё раз" minlength="8"></label>
                        <button type="submit">Создать</button>
                    </div>
                </div>
            </form>
        </section>
    </main>
    <?php require('footer.php'); ?>
</body>

</html>