<?php
function getShortPostInfo($connect, $current_post_id)
{
    $result_short_post_info = $connect->query("SELECT u.username AS content_author_username, u.avatar AS content_author_avatar,
    p.text AS content_text, p.for_friends AS content_for_friends, p.likes AS content_likes, p.reposts AS content_reposts, p.views AS content_views,
    iip.image_url AS content_images,
    COUNT(c.id) AS content_comments
    FROM posts p
    JOIN users u ON p.user_id = u.id
    LEFT JOIN images_in_posts iip ON iip.post_id = p.id
    LEFT JOIN comments c ON c.post_id = p.id
    WHERE p.id = $current_post_id");

    if ($result_short_post_info->num_rows > 0) {
        return $result_short_post_info->fetch_assoc();
    }
    return 0;
}
