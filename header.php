<header>
    <a class="phone-link" href="./"><img src="pics/RampusLogo.svg"><span>Rampus</span></a>
    <div class="third-part-in-header">
        <div>
            <input type="text" name="search-hashtag" id="search-hashtag-in-header" placeholder="Поиск">
            <input type="hidden" name="get-status" id="get-status" value=<?php if (isset($_GET['search'])) {
                                                                                echo $_GET['search'];
                                                                            } else {
                                                                                echo null;
                                                                            } ?>>
            <img id="icon-search-hashtag-in-header" src="pics/SearchIcon.svg">
        </div>
        <ul id="success-search-hashtag-in-header">
        </ul>
    </div>
    <?php if (isset($_SESSION['user'])) { ?>
        <div>
            <a href="./profile"><?= $_SESSION['user']['first_name'] . " " . $_SESSION['user']['second_name'] ?></a>
            <a class="header__avatar" href="./profile"><img src="uploads/avatar/<?= $_SESSION['user']['avatar'] ?>"></a>
        </div>
    <?php } ?>
</header>