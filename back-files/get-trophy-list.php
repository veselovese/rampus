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
    DATE_FORMAT(t.get_date, '%e %M') AS get_date,
    COUNT(stfu.user_id) AS users_counter
FROM trophies t
LEFT JOIN sponsored_trophies_from_users stfu ON t.id = stfu.trophy_id
WHERE t.id > 900
GROUP BY t.id;"),
        $connect->query("SELECT 
    stfu.trophy_id,
    u.id AS user_id,
    u.username,
    u.first_name,
    u.second_name,
    u.avatar
FROM sponsored_trophies_from_users stfu
JOIN users u ON stfu.user_id = u.id;")
    ];
}
