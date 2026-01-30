<?php
session_start();
require_once('connect.php');
require('blossoming.php');

$user_id = $_SESSION['user']['id'];

if (isset($_POST['post'])) $text_post = mysqli_real_escape_string($connect, $_POST['post']);
if (isset($_POST['post-mode'])) $post_mode = mysqli_real_escape_string($connect, $_POST['post-mode']);
if (isset($_POST['post-search'])) $post_search = mysqli_real_escape_string($connect, $_POST['post-search']);
if (isset($_FILES['post-image']) && $_FILES['post-image']['name'] != '') {
    $post_image = $_FILES['post-image'];
    $type = $post_image['type'];
    $name = md5(microtime()) . '.' . substr($type, strlen("image/"));
    $dir = '../uploads/post-image/';
    $uploadfile = $dir . $name;
}

$for_friends = $post_mode == 'for-friends' ? 1 : 0;

function postImageSecurity($post_image)
{
    $name = $post_image['name'];
    $type = $post_image['type'];
    $blacklist = array(".php", ".js", ".html");
    foreach ($blacklist as $row) {
        if (preg_match("/$row\$/i", $name)) return false;
    }

    if (($type != "image/png") && ($type != "image/jpg") && ($type != "image/jpeg") && ($type != "image/tiff") && ($type != "image/heic") && ($type != "image/gif")) return false;

    return true;
}

if (isset($_FILES['post-image']) && $_FILES['post-image']['name'] != '' && (isset($_POST['post']) && strlen(trim($text_post)) > 0)) {
    if (postImageSecurity($post_image)) {
        preg_match_all('/#\w+/u', $text_post, $matches);
        $hashtags = $matches[0];
        if ($hashtags == null) {
            $hashtags = [null];
        }

        $result_post = mysqli_query($connect, "INSERT INTO posts (text, user_id, for_friends, img) VALUES ('$text_post', $user_id, $for_friends, '$name');");
        $current_post_id = $connect->query("SELECT @@IDENTITY AS id")->fetch_assoc()['id'];

        foreach ($hashtags as $hashtag) {
            $hashtag = ltrim($hashtag, '#');
            if ($hashtag == null) {
                $hashtag_id = 0;
            } else if ($connect->query("SELECT id FROM hashtags WHERE name = '$hashtag'")->num_rows > 0) {
                $hashtag_id = $connect->query("SELECT id FROM hashtags WHERE name = '$hashtag'")->fetch_assoc()['id'];
                mysqli_query($connect, "INSERT INTO hashtags_in_posts (post_id, hashtag_id) VALUES ($current_post_id, $hashtag_id);");
            } else {
                $connect->query("INSERT INTO hashtags (name) VALUES ('$hashtag')");
                $hashtag_id = $connect->query("SELECT id FROM hashtags WHERE name = '$hashtag'")->fetch_assoc()['id'];
                mysqli_query($connect, "INSERT INTO hashtags_in_posts (post_id, hashtag_id) VALUES ($current_post_id, $hashtag_id);");
            }
        }

        if (!$result_post) {
            echo "Error: " . mysqli_error($connect);
        } else {
            $_SESSION['message'] = 'Пост добавлен';
        }

        if (move_uploaded_file($post_image['tmp_name'], $uploadfile)) {
            $uploadfile2 = '../' . $uploadfile;
            $src = imagecreatefromstring(file_get_contents($uploadfile));
            list($old_width, $old_height) = getimagesize($uploadfile);

            $exif_supported_types = ['image/jpeg', 'image/jpg', 'image/tiff'];
            if (in_array($type, $exif_supported_types)) {
                $exif = exif_read_data($uploadfile);
                if ($exif && isset($exif['Orientation'])) {
                    $orientation = $exif['Orientation'];
                    switch ($orientation) {
                        case 3:
                            $src = imagerotate($src, 180, 0);
                            break;
                        case 4:
                            $src = imagerotate($src, 180, 0);
                            imageflip($src, IMG_FLIP_HORIZONTAL);
                            break;
                        case 6:
                            $src = imagerotate($src, -90, 0);
                            $i_old_width = $old_width;
                            $old_width = $old_height;
                            $old_height = $i_old_width;
                            break;
                        case 8:
                            $src = imagerotate($src, 90, 0);
                            $i_old_width = $old_width;
                            $old_width = $old_height;
                            $old_height = $i_old_width;
                            break;
                    }
                }
            }

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

        blossoming('add-post', $user_id, $connect);
    }
}

if (isset($_FILES['post-image']) && $_FILES['post-image']['name'] != '' && (!isset($_POST['post']) || !strlen(trim($text_post)) > 0)) {
    if (postImageSecurity($post_image)) {
        $result = mysqli_query($connect, "INSERT INTO posts (user_id, for_friends, img) VALUES ($user_id, $for_friends, '$name');");
        $current_id = $connect->query("SELECT @@IDENTITY AS id")->fetch_assoc()['id'];


        if (!$result) {
            echo "Error: " . mysqli_error($connect);
        } else {
            $_SESSION['message'] = 'Пост добавлен';
        }

        if (move_uploaded_file($post_image['tmp_name'], $uploadfile)) {
            $uploadfile2 = '../' . $uploadfile;
            $src = imagecreatefromstring(file_get_contents($uploadfile));
            list($old_width, $old_height) = getimagesize($uploadfile);

            $exif_supported_types = ['image/jpeg', 'image/jpg', 'image/tiff'];
            if (in_array($type, $exif_supported_types)) {
                $exif = exif_read_data($uploadfile);
                if ($exif && isset($exif['Orientation'])) {
                    $orientation = $exif['Orientation'];
                    switch ($orientation) {
                        case 3:
                            $src = imagerotate($src, 180, 0);
                            break;
                        case 4:
                            $src = imagerotate($src, 180, 0);
                            imageflip($src, IMG_FLIP_HORIZONTAL);
                            break;
                        case 6:
                            $src = imagerotate($src, -90, 0);
                            $i_old_width = $old_width;
                            $old_width = $old_height;
                            $old_height = $i_old_width;
                            break;
                        case 8:
                            $src = imagerotate($src, 90, 0);
                            $i_old_width = $old_width;
                            $old_width = $old_height;
                            $old_height = $i_old_width;
                            break;
                    }
                }
            }

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

        blossoming('add-post', $user_id, $connect);
    }
}

if ((!isset($_FILES['post-image']) || $_FILES['post-image']['name'] == '') && (isset($_POST['post']) && strlen(trim($text_post)) > 0)) {
    preg_match_all('/#\w+/u', $text_post, $matches);
    $hashtags = $matches[0];
    if ($hashtags == null) {
        $hashtags = [null];
    }

    $result_post = $connect->query("INSERT INTO posts (text, user_id, for_friends) VALUES ('$text_post', $user_id, $for_friends);");
    $current_post_id = $connect->query("SELECT @@IDENTITY AS id")->fetch_assoc()['id'];

    foreach ($hashtags as $hashtag) {
        $hashtag = ltrim($hashtag, '#');
        if ($hashtag == null) {
            $hashtag_id = 0;
        } else if ($connect->query("SELECT id FROM hashtags WHERE name = '$hashtag'")->num_rows > 0) {
            $hashtag_id = $connect->query("SELECT id FROM hashtags WHERE name = '$hashtag'")->fetch_assoc()['id'];
            mysqli_query($connect, "INSERT INTO hashtags_in_posts (post_id, hashtag_id) VALUES ($current_post_id, $hashtag_id);");
        } else {
            $connect->query("INSERT INTO hashtags (name) VALUES ('$hashtag')");
            $hashtag_id = $connect->query("SELECT id FROM hashtags WHERE name = '$hashtag'")->fetch_assoc()['id'];
            mysqli_query($connect, "INSERT INTO hashtags_in_posts (post_id, hashtag_id) VALUES ($current_post_id, $hashtag_id);");
        }
    }

    if (!$result_post) {
        echo "Error: " . mysqli_error($connect);
    } else {
        $_SESSION['message'] = 'Пост добавлен';
    }

    if (($post_search != '') && ($post_search == ltrim($hashtags[0], '#'))) {
        $post_search = '?search=' . $post_search;
    } else {
        $post_search = '';
    }

    blossoming('add-post', $user_id, $connect);
}

header('Location: ../wall' . $post_search);
exit();
