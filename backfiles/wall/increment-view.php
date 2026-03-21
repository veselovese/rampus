<?php
require_once('../connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;

    if ($postId > 0) {
        $stmt = $connect->prepare("UPDATE posts SET views = views + 1 WHERE id = ?");
        $stmt->execute([$postId]);
        
        echo json_encode(['status' => 'success', 'new_views' => '++']); 
    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error']);
    }
}
?>