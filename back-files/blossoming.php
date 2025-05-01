<?php
function blossoming($user_id, $action_type, $connect)
{
    $blossom_level = $connect->query("SELECT blossom_level FROM users WHERE id = $user_id")->fetch_assoc()['blossom_level'];
    $blossom_progress = $connect->query("SELECT blossom_progress FROM users WHERE id = $user_id")->fetch_assoc()['blossom_progress'];
    $blossom = $blossom_level + $blossom_progress / 100;


    switch ($action_type) {
        case 'request-to-friends':
            $blossom += 0.33;
            break;
        case 'only-add-to-friends':
            $blossom += 0.22;
            break;
        case 'has-deleted-from-friends':
            $blossom -= 0.33;
            break;
        case 'is-deleted-from-friends-by':
            $blossom -= 0.22;
            break;
        case 'add-post':
            $blossom += 0.29;
            break;
        case 'deleted-self-post':
            $blossom -= 0.29;
            break;
        case 'is-commented-by':
            $blossom += 0.14;
            break;
        case 'comment-been-deleted-under-post-by':
            $blossom -= 0.14;
            break;
        case 'has-commented-somebody':
            $blossom += 0.18;
            break;
        case 'deleted-self-comment':
            $blossom -= 0.18;
            break;
        case 'liked-post':
            $blossom += 0.11;
            break;
        case 'disliked-post':
            $blossom -= 0.11;
            break;
        case 'is-liked-by':
            $blossom += 0.08;
            break;
        case 'is-disliked-by':
            $blossom -= 0.08;
            break;
    }

    $user_level = intval($blossom);
    $user_progress = ($blossom - $user_level) * 100;

    $connect->query("UPDATE users SET blossom_level = $user_level WHERE id = $user_id");
    $connect->query("UPDATE users SET blossom_progress = $user_progress WHERE id = $user_id");
}
