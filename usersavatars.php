<?php
require("connect.php");
$result = $connect->query("SELECT * FROM users");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $name = $row["avatar"];
        $dir = 'uploads/avatar/';
        $uploadfile = $dir . $name;
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
}
