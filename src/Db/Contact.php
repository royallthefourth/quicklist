<?php

namespace RoyallTheFourth\QuickList\Db;

use RoyallTheFourth\SmoothPdo\DataObject;

class Contact{
    public static function count(DataObject $db): int
    {
        return $db->query('SELECT COUNT(id) FROM contacts')->fetch(\PDO::FETCH_NUM)[0];
    }

    public static function oneById(DataObject $db, int $contactId): array
    {
        return $db
            ->prepare('SELECT * FROM contacts WHERE id = ?')
            ->execute([$contactId])
            ->fetch(\PDO::FETCH_ASSOC);
    }

    public static function paginated(DataObject $db, int $page = 1, int $perPage = 50): iterable
    {
        $stmt = $db->prepare('SELECT *
FROM contacts
ORDER BY date_added DESC
LIMIT ? OFFSET ?')
              ->execute([$perPage, ($page - 1) * $perPage]);

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            yield $row;
        }
    }

    public static function removeBulk(DataObject $db, iterable $emails): void
    {
        $stmt = $db->prepare('UPDATE list_contacts
    SET date_removed = CURRENT_TIMESTAMP
    WHERE contact_id = (SELECT id FROM contacts WHERE email LIKE ?)
    AND date_removed IS NULL');

        foreach ($emails as $email) {
            $stmt->execute([$email]);
        }
    }
}