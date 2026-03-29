<?php
function getShortPostInfo($connect, $current_post_id)
{
    $sql = "SELECT 
                ANY_VALUE(u.username) AS content_author_username, 
                ANY_VALUE(u.avatar) AS content_author_avatar,
                ANY_VALUE(p.text) AS content_text, 
                ANY_VALUE(p.for_friends) AS content_for_friends, 
                ANY_VALUE(p.likes) AS content_likes, 
                ANY_VALUE(p.reposts) AS content_reposts, 
                ANY_VALUE(p.views) AS content_views,
                ANY_VALUE(iip.image_url) AS content_images,
                COUNT(c.id) AS content_comments
            FROM posts p
            JOIN users u ON p.user_id = u.id
            LEFT JOIN images_in_posts iip ON iip.post_id = p.id AND iip.id = (SELECT MIN(id) FROM images_in_posts WHERE post_id = p.id)
            LEFT JOIN comments c ON c.post_id = p.id
            WHERE p.id = ?
            GROUP BY p.id";

    $stmt = $connect->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $current_post_id);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
    } else {
        return 0;
    }
    return 0;
}
