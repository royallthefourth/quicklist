<?php

namespace RoyallTheFourth\QuickList\Db\Message;

use RoyallTheFourth\SmoothPdo\DataObject;

/**
 * Add a single message
 * @param DataObject $db
 * @param string $subject
 * @param string $body
 * @return int the ID of the message
 */
function add(DataObject $db, string $subject, string $body): int
{
    $db->beginTransaction()
        ->prepare('INSERT INTO messages(subject, body) VALUES(?, ?)')
        ->execute([$subject, $body]);
    $stmt = $db->query('SELECT last_insert_rowid() AS id');
    $db->commit();
    return $stmt->fetch(\PDO::FETCH_NUM)[0];
}

function all(DataObject $db): array
{
    if (!($rs = $db->query('SELECT ROWID AS id, subject FROM messages')->fetchAll(\PDO::FETCH_ASSOC))) {
        $rs = [];
    }

    return $rs;
}
