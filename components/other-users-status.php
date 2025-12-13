<?php
$current_user_placement = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
echo $current_user_placement;
require('back-files/global.php');
require('../back-files/global.php');

if ($other_user_username == 'rampus' || $other_user_username == 'help') {
    echo "<img class='status' src='$global_url/pics/SuperUserIcon.svg'>";
} else {
    switch ($other_user_in_top) {
        case 1:
            echo "<img class='status' src='$global_url/pics/BlossomFirstIcon.svg'>";
            break;
        case 2:
            echo "<img class='status' src='$global_url/pics/BlossomSecondIcon.svg'>";
            break;
        case 3:
            echo "<img class='status' src='$global_url/pics/BlossomThirdIcon.svg'>";
            break;
    }
}
