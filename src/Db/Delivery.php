<?php

namespace RoyallTheFourth\QuickList\Db\Delivery;

use function RoyallTheFourth\QuickList\Delivery\sendsPerMinute;
use RoyallTheFourth\SmoothPdo\DataObject;

/**
 * Add a single list contact to be delivered
 * @param DataObject $db
 * @param int $messageId
 * @param string $list
 * @param string $email
 * @param \DateTimeImmutable $sendDate
 * @param string $hash
 * @return int the ID of the delivery
 */
function add(
    DataObject $db,
    int $messageId,
    string $list,
    string $email,
    \DateTimeImmutable $sendDate,
    string $hash
): int {
    $db->beginTransaction();
    $db->prepare('INSERT INTO deliveries(message_id, list_contact_id, date_scheduled, unsub_hash)
VALUES(?,
(SELECT LC.ROWID FROM list_contacts LC
INNER JOIN lists L ON L.ROWID = LC.list_id
INNER JOIN contacts C ON C.ROWID = LC.contact_id
WHERE L.name = ?
AND C.email = ?),
?,
?)')->execute([
        $messageId,
        $list,
        $email,
        $sendDate->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s'),
        $hash
    ]);
    $stmt = $db->query('SELECT last_insert_rowid() AS id');
    $db->commit();
    return $stmt->fetch(\PDO::FETCH_NUM)[0];
}

/**
 * @param DataObject $db
 * @param iterable $deliveries
 */
function addBulk(DataObject $db, iterable $deliveries): void
{
    $stmt = $db->prepare('INSERT INTO deliveries(message_id, list_contact_id, date_scheduled, unsub_hash)
VALUES(?, ?, ?, ?)');
    foreach ($deliveries as $delivery) {
        $stmt->execute([
            $delivery['messageId'],
            $delivery['listContactId'],
            $delivery['date']->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s'),
            $delivery['hash']
        ]);
    }
}

function allByContact(DataObject $db, int $contactId): iterable
{
    $stmt = $db->prepare('SELECT M.ROWID AS message_id, subject, date_scheduled, date_sent
    FROM deliveries D
    INNER JOIN messages M ON M.ROWID = D.message_id
    INNER JOIN list_contacts LC ON LC.ROWID = D.list_contact_id
    WHERE LC.contact_id = ?
    ORDER BY date_scheduled DESC')
        ->execute([$contactId]);
    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
        yield $row;
    }
}

function allByMessage(DataObject $db, int $messageId): iterable
{
    $stmt = $db
        ->prepare('SELECT LC.contact_id, C.email, L.ROWID AS list_id, L.name AS list_name, date_scheduled, date_sent
    FROM deliveries D
    INNER JOIN messages M ON M.ROWID = D.message_id
    INNER JOIN list_contacts LC ON LC.ROWID = D.list_contact_id
    INNER JOIN lists L ON L.ROWID = LC.list_id
    INNER JOIN contacts C ON C.ROWID = LC.contact_id
    WHERE M.ROWID = ?
    ORDER BY date_scheduled DESC')
        ->execute([$messageId]);
    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
        yield $row;
    }
}

/**
 * Fetch the upcoming deliveries that fit within the hourly send limit.
 *
 * @param DataObject $db
 * @param int $hourlySendLimit
 * @return iterable
 */
function fetchDue(DataObject $db, int $hourlySendLimit): iterable
{
    $stmt = $db->prepare('SELECT D.ROWID AS id, email, subject, body, unsub_hash
    FROM deliveries D
    INNER JOIN list_contacts LC ON LC.ROWID = D.list_contact_id
    INNER JOIN contacts C ON C.ROWID = LC.contact_id
    INNER JOIN messages M ON M.ROWID = D.message_id
    WHERE LC.date_unsubscribed IS NULL
    AND D.date_scheduled < CURRENT_TIMESTAMP
    AND D.date_sent IS NULL
    AND D.date_canceled IS NULL
    ORDER BY D.date_scheduled ASC
    LIMIT (SELECT MIN(?, MAX(0, ?-(SELECT COUNT(ROWID)
FROM deliveries
WHERE date_sent >= datetime(\'now\', \'-1 hour\')))))')
        ->execute([sendsPerMinute($hourlySendLimit), $hourlySendLimit]);

    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
        yield $row;
    }
}

/**
 * Fetch all upcoming deliveries.
 *
 * @param DataObject $db
 * @return iterable
 */
function fetchPending(DataObject $db): iterable
{
    $stmt = $db->prepare('SELECT D.ROWID AS id, email, subject, date_scheduled
    FROM deliveries D
    INNER JOIN list_contacts LC ON LC.ROWID = D.list_contact_id
    INNER JOIN contacts C ON C.ROWID = LC.contact_id
    INNER JOIN messages M ON M.ROWID = D.message_id
    WHERE LC.date_unsubscribed IS NULL
    AND D.date_scheduled < CURRENT_TIMESTAMP
    AND D.date_sent IS NULL
    AND D.date_canceled IS NULL
    ORDER BY D.date_scheduled ASC')
        ->execute();

    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
        yield $row;
    }
}

/**
 * Sets the given delivery as having been delivered at the current timestamp
 *
 * @param int $deliveryId
 * @param DataObject $db
 */
function setDelivered(DataObject $db, int $deliveryId): void
{
    $db->prepare('UPDATE deliveries
    SET date_sent = CURRENT_TIMESTAMP
    WHERE ROWID = ?')->execute([$deliveryId]);
}
