<?php
require_once('back-files/connect.php');
// mysqli_query($connect, "DELETE FROM images_in_posts WHERE image_url = ''");

// $result_post = mysqli_query($connect, "SELECT id, img FROM posts WHERE img IS NOT NULL AND img != ''");
// if ($result_post->num_rows > 0) {
//     while ($row_post = $result_post->fetch_assoc()) {
//         $post_id = $row_post['id'];
//         $image_url = $row_post['img'];
//         mysqli_query($connect, "INSERT INTO images_in_posts (post_id, image_url) VALUES ($post_id, '$image_url')");
//     }
// }

// mysqli_query($connect, "DELETE images_in_posts from images_in_posts inner join 
//     (select  min(id) minid, image_url, post_id
//      from images_in_posts 
//      group by post_id, image_url
//      having count(1) > 1) as duplicates
//    on (duplicates.post_id = images_in_posts.post_id
//    and duplicates.image_url = images_in_posts.image_url
//    and duplicates.minid <> images_in_posts.id)");
