<?php
function blossoming($action_type, $user_id, $connect)
{
    $unrated_status = $connect->query("SELECT unrated_status FROM users WHERE id = $user_id")->fetch_assoc()['unrated_status'];
    if (!$unrated_status) {
        $blossom_level = $connect->query("SELECT blossom_level FROM users WHERE id = $user_id")->fetch_assoc()['blossom_level'];
        $blossom_progress = $connect->query("SELECT blossom_progress FROM users WHERE id = $user_id")->fetch_assoc()['blossom_progress'];
        $blossom = $blossom_level + $blossom_progress / 100;

        switch ($action_type) {
            case 'request-to-friends':
                $blossom += 0.11;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', 11)");
                break;
            case 'unrequest-to-friends':
                $blossom -= 0.11;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', -11)");
                break;
            case 'add-to-friends':
                $blossom += 0.16;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', 16)");
                break;
            case 'delete-from-friends':
                $blossom -= 0.16;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', -16)");
                break;
            case 'add-post':
                $blossom += 0.13;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', 13)");
                break;
            case 'delete-post':
                $blossom -= 0.13;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', -13)");
                break;
            case 'has-commented':
                $blossom += 0.09;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', 9)");
                break;
            case 'delete-self-comment':
                $blossom -= 0.09;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', -9)");
                break;
            case 'is-commented-by':
                $blossom += 0.08;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', 8)");
                break;
            case 'comment-deleted-under-post-by':
                $blossom -= 0.08;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', -8)");
                break;
            case 'like-post':
                $blossom += 0.07;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', 7)");
                break;
            case 'dislike-post':
                $blossom -= 0.07;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', -7)");
                break;
            case 'is-liked-by':
                $blossom += 0.06;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', 6)");
                break;
            case 'is-disliked-by':
                $blossom -= 0.06;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', -6)");
                break;
            case 'repost-post':
                $blossom += 0.09;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', 9)");
                break;
            case 'unrepost-post':
                $blossom -= 0.09;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', -9)");
                break;
            case 'is-reposted-by':
                $blossom += 0.07;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', 7)");
                break;
            case 'is-unreposted-by':
                $blossom -= 0.07;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', -7)");
                break;
            case 'grab-trophy':
                $blossom += 0.37;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', 37)");
                break;
            case 'lose-trophy':
                $blossom -= 0.37;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', -37)");
                break;
        }

        require('rating-trophies.php');

        $user_level = intval($blossom);
        $user_progress = ($blossom - $user_level) * 100;

        $connect->query("UPDATE users SET blossom_level = $user_level, blossom_progress = $user_progress WHERE id = $user_id");
    }
}
