<?php
function blossoming($action_type, $user_id, $connect)
{
    $unrated_status = $connect->query("SELECT unrated_status FROM users WHERE id = $user_id")->fetch_assoc()['unrated_status'];
    if (!$unrated_status) {
        $blossom_level = $connect->query("SELECT blossom_level FROM users WHERE id = $user_id")->fetch_assoc()['blossom_level'];
        $blossom_progress = $connect->query("SELECT blossom_progress FROM users WHERE id = $user_id")->fetch_assoc()['blossom_progress'];
        // $blossom = $blossom_level + $blossom_progress / 100;

        switch ($action_type) {
            case 'add-to-friends':
                $blossom_progress += 16;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', 16)");
                break;
            case 'delete-from-friends':
                $blossom_progress -= 16;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', -16)");
                break;
            case 'add-post':
                $blossom_progress += 13;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', 13)");
                break;
            case 'delete-post':
                $blossom_progress -= 13;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', -13)");
                break;
            case 'has-commented':
                $blossom_progress += 9;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', 9)");
                break;
            case 'delete-self-comment':
                $blossom_progress -= 9;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', -9)");
                break;
            case 'is-commented-by':
                $blossom_progress += 8;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', 8)");
                break;
            case 'comment-deleted-under-post-by':
                $blossom_progress -= 8;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', -8)");
                break;
            case 'like-post':
                $blossom_progress += 7;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', 7)");
                break;
            case 'dislike-post':
                $blossom_progress -= 7;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', -7)");
                break;
            case 'is-liked-by':
                $blossom_progress += 6;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', 6)");
                break;
            case 'is-disliked-by':
                $blossom_progress -= 6;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', -6)");
                break;
            case 'repost-post':
                $blossom_progress += 9;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', 9)");
                break;
            case 'unrepost-post':
                $blossom_progress -= 9;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', -9)");
                break;
            case 'is-reposted-by':
                $blossom_progress += 7;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', 7)");
                break;
            case 'is-unreposted-by':
                $blossom_progress -= 7;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', -7)");
                break;
            case 'like-comment':
                $blossom_progress += 3;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', 3)");
                break;
            case 'dislike-comment':
                $blossom_progress -= 3;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', -3)");
                break;
            case 'comment-is-liked-by':
                $blossom_progress += 2;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', 2)");
                break;
            case 'comment-is-disliked-by':
                $blossom_progress -= 2;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', -2)");
                break;
            case 'grab-trophy':
                $blossom_progress += 37;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', 37)");
                break;
            case 'lose-trophy':
                $blossom_progress -= 37;
                $connect->query("INSERT INTO blossom_notifications (action_type, user_id, blossom_change) VALUES ('$action_type', '$user_id', -37)");
                break;
        }


        if ($blossom_progress < 0) {
            $blossom_level -= 1;
            $user_progress_need = max(20, intval(($blossom_level - 1) * 1.6 * 20));
            $user_level = $blossom_level;
            $user_progress = $user_progress_need + $blossom_progress;
        } else {
            $user_progress_need = max(20, intval(($blossom_level - 1) * 1.6 * 20));
            if ($blossom_progress >= $user_progress_need) {
                $user_level = $blossom_level + 1;
                $user_progress = $blossom_progress - $user_progress_need;
            } else {
                $user_level = $blossom_level;
                $user_progress = $blossom_progress;
            }
        }


        $connect->query("UPDATE users SET blossom_level = $user_level, blossom_progress = $user_progress WHERE id = $user_id");

        require('rating-trophies.php');
    }
}
