<?php

namespace RoyallTheFourth\QuickList\Db\Message;

use RoyallTheFourth\SmoothPdo\DataObject;

/**
 * Add a single message
 * @param DataObject $db
 * @param string $subject
 * @param string $body
 * @param int $fromSystem Is this message from the system? eg optin
 * @return int the ID of the message
 */
function add(DataObject $db, string $subject, string $body, int $fromSystem = 0): int
{
    $db->beginTransaction()
        ->prepare('INSERT INTO messages(subject, body, from_system) VALUES(?, ?, ?)')
        ->execute([$subject, $body, $fromSystem]);
    $stmt = $db->query('SELECT last_insert_rowid() AS id');
    $db->commit();
    return $stmt->fetch(\PDO::FETCH_NUM)[0];
}

function all(DataObject $db): array
{
    if (!($rs = $db->query('SELECT * FROM messages WHERE from_system = 0')
        ->fetchAll(\PDO::FETCH_ASSOC))
    ) {
        $rs = [];
    }

    return $rs;
}

function count(DataObject $db): int
{
    return $db->query('SELECT COUNT(id) FROM messages WHERE from_system = 0')->fetch(\PDO::FETCH_NUM)[0];
}

function oneById(DataObject $db, int $messageId): array
{
    return $db
        ->prepare('SELECT * FROM messages WHERE id = ?')
        ->execute([$messageId])
        ->fetch(\PDO::FETCH_ASSOC);
}

function paginated(DataObject $db, int $page = 1, int $perPage = 50): iterable
{
    $stmt = $db->prepare('SELECT *
FROM messages
WHERE from_system = 0
ORDER BY date_added DESC
LIMIT ? OFFSET ?')
        ->execute([$perPage, ($page - 1) * $perPage]);

    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
        yield $row;
    }
}

function update(DataObject $db, string $subject, string $body, int $messageId)
{
    $db
        ->prepare('UPDATE messages SET (subject, body) = (?, ?) WHERE id = ?')
        ->execute([$subject, $body, $messageId]);
}
