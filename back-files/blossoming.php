<?php
function blossoming($action_type, $user_id, $connect)
{
    $blossom_level = $connect->query("SELECT blossom_level FROM users WHERE id = $user_id")->fetch_assoc()['blossom_level'];
    $blossom_progress = $connect->query("SELECT blossom_progress FROM users WHERE id = $user_id")->fetch_assoc()['blossom_progress'];
    $blossom = $blossom_level + $blossom_progress / 100;

    switch ($action_type) {
        case 'request-to-friends':
            $blossom += 0.11;
            break;
        case 'unrequest-to-friends':
            $blossom -= 0.11;
            break;
        case 'add-to-friends':
            $blossom += 0.28;
            break;
        case 'delete-from-friends':
            $blossom -= 0.28;
            break;
        case 'add-post':
            $blossom += 0.36;
            break;
        case 'delete-post':
            $blossom -= 0.36;
            break;
        case 'has-commented':
            $blossom += 0.24;
            break;
        case 'delete-self-comment':
            $blossom -= 0.24;
            break;
        case 'is-commented-by':
            $blossom += 0.19;
            break;
        case 'comment-deleted-under-post-by':
            $blossom -= 0.19;
            break;
        case 'like-post':
            $blossom += 0.15;
            break;
        case 'dislike-post':
            $blossom -= 0.15;
            break;
        case 'is-liked-by':
            $blossom += 0.12;
            break;
        case 'is-disliked-by':
            $blossom -= 0.12;
            break;
        case 'repost-post':
            $blossom += 0.23;
            break;
        case 'unrepost-post':
            $blossom -= 0.23;
            break;
        case 'is-reposted-by':
            $blossom += 0.19;
            break;
        case 'is-unreposted-by':
            $blossom -= 0.19;
            break;
        case 'grab-trophy':
            $blossom += 0.71;
            break;
        case 'lose-trophy':
            $blossom -= 0.71;
            break;
    }

    $user_level = intval($blossom);
    $user_progress = ($blossom - $user_level) * 100;

    $connect->query("UPDATE users SET blossom_level = $user_level WHERE id = $user_id");
    $connect->query("UPDATE users SET blossom_progress = $user_progress WHERE id = $user_id");
}
