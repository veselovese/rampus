<?php
session_start();
require_once('connect.php');

$user_id = $_SESSION['user']['id'];
if (isset($_POST['edit-post'])) $text_post = $_POST['edit-post'];
if (isset($_POST['post-source'])) $post_source = $_POST['post-source'];
if (isset($_FILES['edit-post-image']) && $_FILES['edit-post-image']['name'] != '') {
    $post_image = $_FILES['edit-post-image'];
    $type = $post_image['type'];
    $name = md5(microtime()) . '.' . substr($type, strlen("image/"));
    $dir = '../uploads/post-image/';
    $uploadfile = $dir . $name;
}

function postImageSecurity($post_image)
{
    $name = $post_image['name'];
    $type = $post_image['type'];
    $blacklist = array(".php", ".js", ".html");
    foreach ($blacklist as $row) {
        if (preg_match("/$row\$/i", $name)) return false;
    }

    if (($type != "image/png") && ($type != "image/jpg") && ($type != "image/jpeg")) return false;

    return true;
}

if (isset($_FILES['edit-post-image']) && $_FILES['edit-post-image']['name'] != '' && isset($_POST['post'])) {
    if (postImageSecurity($post_image)) {
        preg_match_all('/#\w+/u', $text_post, $matches);
        $hashtags = $matches[0];
        if ($hashtags == null) {
            $hashtags = [null];
        }

        $text_without_hashtags = preg_replace('/#\w+\s*/u', '', $text_post);

        foreach ($hashtags as $hashtag) {
            $hashtag = ltrim($hashtag, '#');
            if ($hashtag == null) {
                $hashtag_id = 0;
            } else if ($connect->query("SELECT id FROM hashtags WHERE name = '$hashtag'")->num_rows > 0) {
                $hashtag_id = $connect->query("SELECT id FROM hashtags WHERE name = '$hashtag'")->fetch_assoc()['id'];
            } else {
                $connect->query("INSERT INTO hashtags (name) VALUES ('$hashtag')");
                $hashtag_id = $connect->query("SELECT id FROM hashtags WHERE name = '$hashtag'")->fetch_assoc()['id'];
            }

            $result = mysqli_query($connect, "INSERT INTO posts (hashtag_id, text, user_id, img) VALUES ($hashtag_id, '$text_without_hashtags', $user_id, '$name');");
            $current_id = $connect->query("SELECT @@IDENTITY AS id")->fetch_assoc()['id'];
            $blossom_level = $connect->query("SELECT blossom_level FROM users WHERE id = $user_id")->fetch_assoc()['blossom_level'];
            $blossom_progress = $connect->query("SELECT blossom_progress FROM users WHERE id = $user_id")->fetch_assoc()['blossom_progress'];
            $blossom = $blossom_level + $blossom_progress / 100;
            $blossom += 0.1;

            $user_level = intval($blossom);
            $user_progress = ($blossom - $user_level) * 100;

            $connect->query("UPDATE users SET blossom_level = $user_level WHERE id = $user_id");
            $connect->query("UPDATE users SET blossom_progress = $user_progress WHERE id = $user_id");
            if (!$result) {
                echo "Error: " . mysqli_error($connect);
            } else {
                $_SESSION['message'] = 'Пост добавлен';
            }
        }

        if (move_uploaded_file($post_image['tmp_name'], $uploadfile)) {
            $uploadfile2 = '../' . $uploadfile;
            $src = imagecreatefromjpeg($uploadfile);
            if (!$src) $src = imagecreatefrompng($uploadfile);
            if (!$src) $src = imagecreatefromgif($uploadfile);
            list($old_width, $old_height) = getimagesize($uploadfile);
            if ($old_width >= $old_height) {
                $k1 = $old_height / 96;
                $k2 = $old_height / 480;
            } else {
                $k1 = $old_width / 96;
                $k2 = $old_width / 480;
            }
            $new_width1 = $old_width / $k1;
            $new_width2 = $old_width / $k2;
            $new_height1 = $old_height / $k1;
            $new_height2 = $old_height / $k2;
            $tmp1 = imagecreatetruecolor($new_width1, $new_height1);
            $tmp2 = imagecreatetruecolor($new_width2, $new_height2);
            $new_uploadfile1 =  $dir . "thin_" . $name;
            $new_uploadfile2 =  $dir . "small_" . $name;
            imagecopyresampled($tmp1, $src, 0, 0, 0, 0, $new_width1, $new_height1, $old_width, $old_height);
            imagecopyresampled($tmp2, $src, 0, 0, 0, 0, $new_width2, $new_height2, $old_width, $old_height);
            imagejpeg($tmp1, $new_uploadfile1, 100);
            imagejpeg($tmp2, $new_uploadfile2, 100);
        }

        if (($post_search != '') && ($post_search == ltrim($hashtags[0], '#'))) {
            $post_search = '?search=' . $post_search;
        } else {
            $post_search = '';
        }

        if ($post_source == 'source-profile') {
            header('Location: ../profile' . '#post-' . $current_id);
            exit();
        } else if ($post_source == 'source-wall') {
            header('Location: ../wall' . $post_search . '#post-' . $current_id);
            exit();
        }
    }
}

if ((!isset($_FILES['edit-post-image']) || $_FILES['edit-post-image']['name'] == '') && isset($_POST['post'])) {
    preg_match_all('/#\w+/u', $text_post, $matches);
    $hashtags = $matches[0];
    if ($hashtags == null) {
        $hashtags = [null];
    }

    $text_without_hashtags = preg_replace('/#\w+\s*/u', '', $text_post);

    foreach ($hashtags as $hashtag) {
        $hashtag = ltrim($hashtag, '#');
        if ($hashtag == null) {
            $hashtag_id = 0;
        } else if ($connect->query("SELECT id FROM hashtags WHERE name = '$hashtag'")->num_rows > 0) {
            $hashtag_id = $connect->query("SELECT id FROM hashtags WHERE name = '$hashtag'")->fetch_assoc()['id'];
        } else {
            $connect->query("INSERT INTO hashtags (name) VALUES ('$hashtag')");
            $hashtag_id = $connect->query("SELECT id FROM hashtags WHERE name = '$hashtag'")->fetch_assoc()['id'];
        }

        $result = mysqli_query($connect, "INSERT INTO posts (hashtag_id, text, user_id, img) VALUES ($hashtag_id, '$text_without_hashtags', $user_id, '$name');");
        $current_id = $connect->query("SELECT @@IDENTITY AS id")->fetch_assoc()['id'];
        $blossom_level = $connect->query("SELECT blossom_level FROM users WHERE id = $user_id")->fetch_assoc()['blossom_level'];
        $blossom_progress = $connect->query("SELECT blossom_progress FROM users WHERE id = $user_id")->fetch_assoc()['blossom_progress'];
        $blossom = $blossom_level + $blossom_progress / 100;
        $blossom += 0.1;

        $user_level = intval($blossom);
        $user_progress = ($blossom - $user_level) * 100;

        $connect->query("UPDATE users SET blossom_level = $user_level WHERE id = $user_id");
        $connect->query("UPDATE users SET blossom_progress = $user_progress WHERE id = $user_id");
        if (!$result) {
            echo "Error: " . mysqli_error($connect);
        } else {
            $_SESSION['message'] = 'Пост добавлен';
        }
    }

    if (($post_search != '') && ($post_search == ltrim($hashtags[0], '#'))) {
        $post_search = '?search=' . $post_search;
    } else {
        $post_search = '';
    }

    if ($post_source == 'source-profile') {
        header('Location: ../profile' . '#post-' . $current_id);
        exit();
    } else if ($post_source == 'source-wall') {
        header('Location: ../wall' . $post_search . '#post-' . $current_id);
        exit();
    }
}
