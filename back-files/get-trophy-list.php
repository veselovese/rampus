<?php

function getTrophyList()
{
    require('connect.php');
    return $connect->query("SELECT trophies.id AS id, trophies.name AS name, trophies.image as image, DATE_FORMAT(trophies.get_date, '%e %M') AS get_date, trophies.stat_number,
                                        users.first_name as first_name, users.avatar as avatar, users.username as username, trophies.description, users.second_name, users.id AS user_id, users.blossom_level
                                        FROM trophies
                                        JOIN users ON trophies.user_id_to = users.id");
}
