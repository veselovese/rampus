<?php
if ($other_user_verify_status) {
    echo "<img class='status' src='/pics/SuperUserIcon.svg'>";
} else {
    switch ($other_user_in_top) {
        case 1:
            echo "<img class='status' src='/pics/BlossomFirstIcon.svg'>";
            break;
        case 2:
            echo "<img class='status' src='/pics/BlossomSecondIcon.svg'>";
            break;
        case 3:
            echo "<img class='status' src='/pics/BlossomThirdIcon.svg'>";
            break;
    }
}
