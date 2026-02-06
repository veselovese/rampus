<?php
session_start();
require_once('connect.php');
require('blossoming.php');

$user_id = $_SESSION['user']['id'];

if (isset($_POST['post'])) $text_post = mysqli_real_escape_string($connect, $_POST['post']);
if (isset($_POST['post-mode'])) $post_mode = mysqli_real_escape_string($connect, $_POST['post-mode']);
if (isset($_POST['post-search'])) $post_search = mysqli_real_escape_string($connect, $_POST['post-search']);

$for_friends = $post_mode == 'for-friends' ? 1 : 0;

function postImageSecurity($file_data)
{
    if (!isset($file_data['name']) || !isset($file_data['type'])) {
        return false;
    }

    $name = $file_data['name'];
    $type = $file_data['type'];

    $blacklist = array(".php", ".js", ".html", ".phtml", ".php3", ".php4", ".php5", ".php7", ".phps", ".htaccess");

    foreach ($blacklist as $row) {
        if (preg_match("/" . preg_quote($row, '/') . "\$/i", $name)) {
            return false;
        }
    }

    $allowed_types = array(
        "image/png",
        "image/jpg",
        "image/jpeg",
        "image/tiff",
        "image/heic",
        "image/gif",
        "image/webp",
        "image/bmp"
    );

    if (!in_array($type, $allowed_types)) {
        return false;
    }

    return true;
}

if (isset($_FILES['post-images']) || (isset($_POST['post']) && strlen(trim($text_post)) > 0)) {
    $result_post = mysqli_query($connect, "INSERT INTO posts (user_id, for_friends) VALUES ($user_id, $for_friends);");
    $current_post_id = $connect->query("SELECT @@IDENTITY AS id")->fetch_assoc()['id'];

    $countfiles = count($_FILES['post-images']['name']);

    $totalFileUploaded = 0;
    $dir = '../uploads/post-image/';

    for ($i = 0; $i < $countfiles; $i++) {
        if ($_FILES['post-images']['name'][$i] != '') {
            $single_file = array(
                'name' => $_FILES['post-images']['name'][$i],
                'type' => $_FILES['post-images']['type'][$i],
                'tmp_name' => $_FILES['post-images']['tmp_name'][$i],
                'error' => $_FILES['post-images']['error'][$i],
                'size' => $_FILES['post-images']['size'][$i]
            );

            if (postImageSecurity($single_file)) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $real_mime = finfo_file($finfo, $single_file['tmp_name']);

                $extension = strtolower(pathinfo($single_file['name'], PATHINFO_EXTENSION));
                $name = md5(microtime() . $i . uniqid()) . '.' . $extension;
                $uploadfile = $dir . $name;

                if (move_uploaded_file($single_file['tmp_name'], $uploadfile)) {
                    $totalFileUploaded++;

                    try {
                        $src = imagecreatefromstring(file_get_contents($uploadfile));
                        if (!$src) {
                            throw new Exception('Не удалось создать изображение');
                        }

                        list($old_width, $old_height) = getimagesize($uploadfile);

                        $exif_supported_types = ['image/jpeg', 'image/jpg', 'image/tiff'];
                        if (in_array($real_mime, $exif_supported_types)) {
                            $exif = @exif_read_data($uploadfile);
                            if ($exif && isset($exif['Orientation'])) {
                                $orientation = $exif['Orientation'];
                                switch ($orientation) {
                                    case 3:
                                        $src = imagerotate($src, 180, 0);
                                        break;
                                    case 4:
                                        imageflip($src, IMG_FLIP_HORIZONTAL);
                                        $src = imagerotate($src, 180, 0);
                                        break;
                                    case 5:
                                        imageflip($src, IMG_FLIP_HORIZONTAL);
                                        $src = imagerotate($src, -90, 0);
                                        $temp = $old_width;
                                        $old_width = $old_height;
                                        $old_height = $temp;
                                        break;
                                    case 6:
                                        $src = imagerotate($src, -90, 0);
                                        $i_old_width = $old_width;
                                        $old_width = $old_height;
                                        $old_height = $i_old_width;
                                        break;
                                    case 7:
                                        imageflip($src, IMG_FLIP_HORIZONTAL);
                                        $src = imagerotate($src, 90, 0);
                                        $temp = $old_width;
                                        $old_width = $old_height;
                                        $old_height = $temp;
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

                        if ($real_mime == 'image/png' || $real_mime == 'image/gif') {
                            imagealphablending($tmp1, false);
                            imagesavealpha($tmp1, true);
                            imagealphablending($tmp2, false);
                            imagesavealpha($tmp2, true);
                        }

                        $new_uploadfile1 = $dir . "thin_" . $name;
                        $new_uploadfile2 = $dir . "small_" . $name;

                        imagecopyresampled($tmp1, $src, 0, 0, 0, 0, $new_width1, $new_height1, $old_width, $old_height);
                        imagecopyresampled($tmp2, $src, 0, 0, 0, 0, $new_width2, $new_height2, $old_width, $old_height);

                        switch ($real_mime) {
                            case 'image/png':
                                imagepng($tmp1, $new_uploadfile1, 9);
                                imagepng($tmp2, $new_uploadfile2, 9);
                                break;
                            case 'image/gif':
                                imagegif($tmp1, $new_uploadfile1);
                                imagegif($tmp2, $new_uploadfile2);
                                break;
                            case 'image/webp':
                                imagewebp($tmp1, $new_uploadfile1, 100);
                                imagewebp($tmp2, $new_uploadfile2, 100);
                                break;
                            default:
                                imagejpeg($tmp1, $new_uploadfile1, 100);
                                imagejpeg($tmp2, $new_uploadfile2, 100);
                        }

                        $escaped_name = mysqli_real_escape_string($connect, $name);
                        $sql = "INSERT INTO images_in_posts (post_id, image_url) VALUES ($current_post_id, '$escaped_name')";

                        if (mysqli_query($connect, $sql)) {
                        } else {
                            @unlink($uploadfile);
                            @unlink($new_uploadfile1);
                            @unlink($new_uploadfile2);
                        }
                    } catch (Exception $e) {
                        @unlink($uploadfile);
                    }
                }
            }
        }

        if (isset($_POST['post']) && strlen(trim($text_post)) > 0) {
            preg_match_all('/#\w+/u', $text_post, $matches);
            $hashtags = $matches[0];
            if ($hashtags == null) {
                $hashtags = [null];
            }

            $result_post = mysqli_query($connect, "UPDATE posts SET text = '$text_post' WHERE id = $current_post_id");

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
}

header('Location: ../wall' . $post_search);
exit();
