<?php

function getTrophyList()
{
    require('connect.php');
    return $connect->query("SELECT trophies.id, trophies.name, trophies.image, DATE_FORMAT(trophies.get_date, '%e %M') AS get_date, trophies.stat_number, trophies.description, trophies.short_description, trophies.link,
                                        users.first_name, users.avatar, users.username, users.second_name, users.id AS user_id, users.blossom_level
                                        FROM trophies
                                        JOIN users ON trophies.user_id_to = users.id");
}

function getPersonalTrophyList()
{
    require('connect.php');
    return [
        $connect->query("SELECT 
    t.id,
    t.name,
    t.image,
    t.description,
    t.short_description,
    DATE_FORMAT(ptfu.get_date, '%e %M') AS get_date,
    COUNT(ptfu.user_id) AS users_counter
FROM trophies t
JOIN personal_trophies_from_users ptfu ON t.id = ptfu.trophy_id
WHERE t.id > 900;"),
        $connect->query("SELECT 
    ptfu.trophy_id,
    u.id AS user_id,
    u.username,
    u.first_name,
    u.second_name,
    u.avatar
FROM personal_trophies_from_users ptfu
JOIN users u ON ptfu.user_id = u.id LIMIT 5;")
    ];
}
