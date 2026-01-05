<?php
require_once('back-files/global.php');
?>

<header>
    <a class="phone-link" href="<?= $global_url ?>/wall"><img src="<?= $global_url ?>/pics/RampusLogo.svg"><span>Рампус</span></a>
    <div class="third-part-in-header">
        <div>
            <input type="text" name="search-hashtag" id="search-hashtag-in-header" placeholder="Поиск">
            <input type="hidden" name="get-status" id="get-status" value="<?php if (isset($_GET['search'])) {
                                                                                echo $_GET['search'];
                                                                            } else {
                                                                                echo null;
                                                                            } ?>">
            <img id="icon-search-hashtag-in-header" src="<?= $global_url ?>/pics/SearchIcon.svg">
        </div>
        <ul id="success-search-hashtag-in-header">
        </ul>
    </div>
</header>