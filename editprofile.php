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

if (move_uploaded_file($avatar['tmp_name'], $uploadfile)) {
    $sql = "UPDATE users SET avatar = '$name' WHERE id = $userid";
    $result = $connect->query($sql);
}

$sql = "UPDATE users SET first_name = '$first_name', second_name = '$second_name', description = '$description' WHERE id = $userid";
    $result = $connect->query($sql);

if (avatarSecurity($avatar)) {
}

function avatarSecurity($avatar)
{
    $name = $avatar['name'];
    $type = $avatar['type'];
    $size = $avatar['size'];
    $blacklist = array(".php", ".js", ".html");
    foreach ($blacklist as $row) {
        if (preg_match("/$row\$/i", $name)) return false;
    }

    if (($type != "image/png") || ($type != "image/jpg") || ($type != "image/jpeg")) return false;
    if ($size > 5 * 1024 * 1024) return false;

    return true;
}

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
