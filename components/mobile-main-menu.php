<?php
require_once('back-files/global.php');
require_once('back-files/get-user-friends.php');

$current_user_id = $_SESSION['user']['id'];
$current_user_avatar = $_SESSION['user']['avatar'];
$current_user_placement = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

$unread_main_posts = $_SESSION['user']['unread_main_posts'];
$unread_thirty_seventh_posts = $_SESSION['user']['unread_thirty_seventh_posts'];
$unread_all_posts = $_SESSION['user']['unread_all_posts'];
$current_user_unread_posts = $unread_all_posts + $unread_main_posts + $unread_thirty_seventh_posts;
$current_user_requests = $result_request_to->num_rows;
$current_user_unread_chats = require('back-files/chats/get-user-unread-chats.php');
?>

<?php echo $current_user_placement != 'wall' ? "<div class='fixed-div-for-bottom'>" : ""; ?>
<nav class="first-part-mobile">
    <ul>
        <li id="<?php echo $current_user_placement == 'wall' ? 'active' : '' ?>">
            <a href="<?= $global_url ?>/wall">
                <svg width="28" height="24" viewBox="0 0 28 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 5C0 2.23858 2.23858 0 5 0H23C25.7614 0 28 2.23858 28 5V24H5C2.23858 24 0 21.7614 0 19V5Z" />
                </svg>
                Стена
                <?php if ($current_user_unread_posts > 0) { ?>
                    <span id="notification__unread-posts-mobile" class="notification-in-menu-mobile active"><?= $current_user_unread_posts  ?></span>
                <?php } ?>
            </a>
        </li>
        <li id="<?php echo $current_user_placement == 'users' ? 'active' : '' ?>">
            <a href="<?= $global_url ?>/users">
                <svg width='28' height='24' viewBox='0 0 28 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                    <path d='M15 4.49829C12.651 5.47785 11 7.79617 11 10.5001C11 11.397 11.1816 12.2514 11.5102 13.0287C10.4728 13.595 9.51192 14.3105 8.66116 15.1613C7.81316 16.0093 7.09958 16.9666 6.53414 18.0001L0 18C0 15.2153 1.10625 12.5446 3.07538 10.5754C4.33742 9.31339 5.88765 8.4058 7.5714 7.91672C6.60943 7.09142 6 5.86688 6 4.5C6 2.01472 8.01472 0 10.5 0C12.9847 0 14.9991 2.01379 15 4.49829Z' />
                    <path d='M21.25 10.5001C21.25 12.5712 19.5711 14.2501 17.5 14.2501C15.4289 14.2501 13.75 12.5712 13.75 10.5001C13.75 8.42905 15.4289 6.75012 17.5 6.75012C19.5711 6.75012 21.25 8.42905 21.25 10.5001ZM10.6057 17.1058C11.2822 16.4293 12.0479 15.8625 12.8752 15.4168C14.0826 16.5528 15.7103 17.2501 17.5 17.2501C19.2897 17.2501 20.9174 16.5528 22.1248 15.4168C22.9521 15.8625 23.7177 16.4293 24.3943 17.1058C26.0452 18.7567 27.0429 20.9386 27.2211 23.2501H17.5L7.77887 23.2501C7.95711 20.9386 8.95483 18.7567 10.6057 17.1058Z' stroke-linecap='round' stroke-linejoin='round' />
                </svg>
                Люди
            </a>
        </li>
        <li id="<?php echo $current_user_placement == 'chats' ? 'active' : '' ?>">
            <a href="<?= $global_url ?>/chats">
                <svg width="28" height="24" viewBox="0 0 28 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M20.9219 4C20.4586 1.71776 18.4408 0 16.0219 0H4.99997C2.23855 0 -2.28882e-05 2.23858 -2.28882e-05 5V18H5V11C5 7.13401 8.13401 4 12 4H20.9219Z" />
                    <path d="M7 11C7 8.23858 9.23858 6 12 6H23C25.7614 6 28 8.23858 28 11V24H12C9.23858 24 7 21.7614 7 19V11Z" />
                </svg>
                Чаты
                <span class="notification-in-menu-mobile <?php echo $current_user_unread_chats > 0 ? 'active' : '' ?>" id="notification__unread-chats-mobile"><?php echo $current_user_unread_chats > 0 ? $current_user_unread_chats : '' ?></span>
            </a>
        </li>
        <li id="<?php echo $current_user_placement == 'case' ? 'active' : '' ?>">
            <a href="<?= $global_url ?>/case">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M1.9268 10.8618C2.52053 11.4533 3.26562 11.9723 4.12276 12.3C5.41329 15.0644 8.12607 17.0333 11.3277 17.2769V18.9767H8.80676C7.11016 18.9767 5.72553 20.3562 5.72553 22.0465V24H18.6106V22.0465C18.6106 20.3562 17.226 18.9767 15.5294 18.9767H13.0084V17.2448C16.0631 16.8937 18.6321 14.9671 19.8773 12.3C20.7344 11.9723 21.4795 11.4533 22.0732 10.8618C22.0836 10.8514 22.0938 10.8407 22.1036 10.8298C23.2118 9.60312 24 8.07245 24 6.25114C24 4.22598 22.3913 2.62324 20.3585 2.62324H19.9556C19.0317 1.05355 17.3205 0 15.3614 0H8.63867C6.6795 0 4.96828 1.05355 4.04441 2.62324H3.64145C1.60872 2.62324 0 4.22598 0 6.25114C0 8.07245 0.788245 9.60312 1.89638 10.8298C1.90624 10.8407 1.91638 10.8514 1.9268 10.8618ZM3.2532 4.33392C2.3426 4.50732 1.68067 5.28363 1.68067 6.25114C1.68067 7.54825 2.23086 8.69119 3.13068 9.69311C3.18633 9.74808 3.24349 9.80196 3.30209 9.85462C3.23071 9.40931 3.19327 8.95182 3.19327 8.4837V5.13486C3.19327 4.86518 3.21305 4.59734 3.2532 4.33392ZM20.8067 5.13486C20.8067 4.86518 20.7869 4.59734 20.7468 4.33392C21.6574 4.50732 22.3193 5.28363 22.3193 6.25114C22.3193 7.54825 21.7691 8.69119 20.8693 9.69311C20.8137 9.74808 20.7565 9.80196 20.6979 9.85461C20.7693 9.40931 20.8067 8.95182 20.8067 8.4837V5.13486Z" />
                </svg>
                Полка
            </a>
        </li>
    </ul>
    <a href="<?= $global_url ?>/profile" class="menu__link-profile" id="<?php echo $current_user_placement == 'profile' ? 'active' : '' ?>">
        <img class="menu-avatar" src="<?= $global_url ?>/uploads/avatar/thin_<?= $current_user_avatar ?>">
    </a>
</nav>
<?php echo $current_user_placement != 'wall' ? "</div>" : ""; ?>