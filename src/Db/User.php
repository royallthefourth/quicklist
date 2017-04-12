<?php

namespace RoyallTheFourth\QuickList\Db\User;

use RoyallTheFourth\SmoothPdo\DataObject;

function getByName(DataObject $db, string $name): array
{
    $user = $db->prepare('SELECT *
    FROM users
    WHERE name = ?')
        ->execute([$name])
        ->fetch(\PDO::FETCH_ASSOC);

    if ($user) {
        return $user;
    }
    return [];
}

function isValidUserId(DataObject $db, int $id): bool
{
    return is_numeric($db
        ->prepare('SELECT id FROM users WHERE id = ?')
        ->execute([$id])
        ->fetch(\PDO::FETCH_NUM)[0]);
}
