<?php
require_once('back-files/global.php');
$current_user_placement = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
switch ($current_user_placement) {
    case 'blossom':
        $back_url = 'profile';
        break;
    case 'trophies':
        $back_url = 'profile';
        break;
    case 'edit':
        $back_url = 'profile';
        break;
    case 'requests':
        $back_url = 'friends';
        break;
    case 'friends':
        $arr = explode('/', $_SERVER['REQUEST_URI'],);
        $count = count($arr);
        $back_url = 'user/' . $arr[$count - 2];
        break;
}
?>
<nav class="first-part">
    <ul>
        <li><a id="back" href="<?= $global_url ?>/<?= $back_url ?>">
                <svg width="8" height="13" viewBox="0 0 8 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6.96771 6.03603L1.12165 0.191904C0.865127 -0.0639698 0.449521 -0.0639698 0.192352 0.191904C-0.0641698 0.447777 -0.0641699 0.863383 0.192352 1.11926L5.57471 6.49968L0.192999 11.8801C-0.0635223 12.136 -0.0635224 12.5516 0.192999 12.8081C0.44952 13.064 0.865774 13.064 1.1223 12.8081L6.96836 6.96403C7.22094 6.7108 7.22094 6.28866 6.96771 6.03603Z" />
                </svg>
                Назад</a></li>
    </ul>
</nav>