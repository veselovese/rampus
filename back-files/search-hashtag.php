<?php
require_once('connect.php');
$result_search_hashtag = '';

if (isset($_POST["hashtag"])) {
    $sql_hashtag = "SELECT hashtags.name AS hashtag_name
    FROM hashtags WHERE hashtags.name LIKE '%" . $_POST["hashtag"] . "%'";
} else {
    $sql_hashtag = "SELECT hashtags.name AS hashtag_name
    FROM hashtags";
}

$result_hashtag = $connect->query($sql_hashtag);
if ($result_hashtag->num_rows > 0) {
    $counter = 0;
    while (($row_hashtag = $result_hashtag->fetch_assoc()) && ($counter < 5)) {
        $hashtag_name = $row_hashtag['hashtag_name'];
        if (($_POST['get'] == $hashtag_name)) {
            echo "<li id ='checked'><a href='./wall'>#" . $hashtag_name . "</a></li>";
        } else {
            echo "<li><a href='?search=$hashtag_name'>#" . $hashtag_name . "</a></li>";
        }
        $counter++;
    }
    if ($result_hashtag->num_rows > 5) {
        echo "<li id='all-hashtags'><a href='./wall'>Все хештеги
        <svg class='pointer' width='8' height='13' viewBox='0 0 8 13' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                        <path d='M6.96771 6.03603L1.12165 0.191904C0.865127 -0.0639698 0.449521 -0.0639698 0.192352 0.191904C-0.0641698 0.447777 -0.0641699 0.863383 0.192352 1.11926L5.57471 6.49968L0.192999 11.8801C-0.0635223 12.136 -0.0635224 12.5516 0.192999 12.8081C0.44952 13.064 0.865774 13.064 1.1223 12.8081L6.96836 6.96403C7.22094 6.7108 7.22094 6.28866 6.96771 6.03603Z' />
                                    </svg>
        </a></li>";
    }
} else {
    echo "<p class='no-found'>Хештегов не найдено</p>";
}
