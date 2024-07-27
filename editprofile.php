<?php
session_start();
require('connect.php');

$userid = $_SESSION['user']['id'];
$avatar = $_FILES['avatar'];
$first_name = $_POST['first-name'];
$second_name = $_POST['second-name'];
$description = $_POST['description'];
$type = $avatar['type'];
$name = md5(microtime()) . '.' . substr($type, strlen("image/"));
$dir = 'uploads/avatar/';
$uploadfile = $dir . $name;

function avatarSecurity($avatar) {
    $name = $avatar['name'];
    $type = $avatar['type'];
    $blacklist = array(".php", ".js", ".html");
    foreach ($blacklist as $row) {
        if (preg_match("/$row\$/i", $name)) return false;
    }

    if (($type != "image/png") && ($type != "image/jpg") && ($type != "image/jpeg")) return false;

    return true;
}

if (avatarSecurity($avatar)) {
    if (move_uploaded_file($avatar['tmp_name'], $uploadfile)) {
        $src = imagecreatefromjpeg($uploadfile);
        if(!$src) $src = imagecreatefrompng($uploadfile);
        if(!$src) $src = imagecreatefromgif($uploadfile);
        list($old_width, $old_height) = getimagesize($uploadfile);
        if ($old_width >= $old_height) {
            $k1 = $old_width / 96;
            $k2 = $old_width / 480;
        } else {
            $k1 = $old_height / 96;
            $k2 = $old_height / 480;
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
        $sql = "UPDATE users SET avatar = '$name' WHERE id = $userid";
        $result = $connect->query($sql);
    }
}

$sql = "UPDATE users SET first_name = '$first_name', second_name = '$second_name', description = '$description' WHERE id = $userid";
    $result = $connect->query($sql);



$check_user = mysqli_query($connect, "SELECT * FROM users WHERE id = $userid");
if (mysqli_num_rows($check_user) > 0) {

    $user = mysqli_fetch_assoc($check_user);

    $_SESSION['user'] = [
        "id" => $user['id'],
        "first_name" => $user['first_name'],
        "second_name" => $user['second_name'],
        "username" => $user['username'],
        "email" => $user['email'],
        "avatar" => $user['avatar'],
        "balance" => $user['balance'],
        "description" => $user['description']
    ];
}

header('Location: ./profile');
