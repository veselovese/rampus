<?php

function getTrophyList()
{
    require('connect.php');
    return $connect->query("SELECT trophies.id, trophies.name, trophies.image, DATE_FORMAT(trophies.get_date, '%e %M') AS get_date, trophies.stat_number, trophies.description, trophies.short_description, trophies.link,
                                        users.first_name, users.avatar, users.username, users.second_name, users.id AS user_id, users.blossom_level
                                        FROM trophies
                                        JOIN users ON trophies.user_id_to = users.id");
}
