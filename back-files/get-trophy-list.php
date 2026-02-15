<?php

function getTrophyList()
{
    require('connect.php');
    return $connect->query("SELECT trophies.id, trophies.name, trophies.image, DATE_FORMAT(trophies.get_date, '%e %M') AS get_date, trophies.stat_number, trophies.description, trophies.short_description, trophies.link,
                                        uto.first_name, uto.avatar, uto.username, uto.second_name, uto.id AS user_id, uto.blossom_level,
                                        ufrom.first_name AS from_user_first_name, ufrom.avatar AS from_user_avatar, ufrom.username AS from_user_username, ufrom.second_name AS from_user_second_name, ufrom.id AS from_user_id
                                        FROM trophies
                                        JOIN users uto ON trophies.user_id_to = uto.id
                                        JOIN users ufrom ON trophies.user_id_from = ufrom.id");
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
    COUNT(stfu.user_id) AS users_counter,
    u.id AS sponsor_user_id,
    u.first_name AS sponsor_user_first_name,
    u.second_name AS sponsor_user_second_name,
    u.username AS sponsor_user_username,
    u.avatar AS sponsor_user_avatar
FROM trophies t
LEFT JOIN users u on t.sponsor_user_id = u.id
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
