<header>
    <a href="./">Rampus</a>
    <?php if (isset($_SESSION['user'])) { ?>
    <div>
        <a href="./profile"><?= $_SESSION['user']['first_name'] . " " . $_SESSION['user']['second_name'] ?></a>
        <a class="header__avatar" href="./profile"><img src="uploads/avatar/<?= $_SESSION['user']['avatar'] ?>"></a>
    </div>
    <?php } ?>
</header>