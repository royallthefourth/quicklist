<?php

namespace RoyallTheFourth\QuickList\Db\Delivery;

use function RoyallTheFourth\QuickList\Delivery\sendsPerMinute;
use RoyallTheFourth\SmoothPdo\DataObject;

/**
 * Add a single list contact to be delivered
 * @param DataObject $db
 * @param int $messageId
 * @param int $listId
 * @param string $email
 * @param \DateTimeImmutable $sendDate
 * @param string $hash
 * @return int the ID of the delivery
 */
function add(
    DataObject $db,
    int $messageId,
    int $listId,
    string $email,
    \DateTimeImmutable $sendDate,
    string $hash
): int {
    $db->beginTransaction();
    $db->prepare('INSERT INTO deliveries(message_id, list_contact_id, date_scheduled, unsub_hash)
VALUES(?,
(SELECT LC.id FROM list_contacts LC
INNER JOIN lists L ON L.id = LC.list_id
INNER JOIN contacts C ON C.id = LC.contact_id
WHERE L.id = ?
AND C.email = ?),
?,
?)')->execute([
        $messageId,
        $listId,
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
    $db->beginTransaction();
    foreach ($deliveries as $delivery) {
        $stmt->execute([
            $delivery['messageId'],
            $delivery['listContactId'],
            $delivery['date'],
            $delivery['hash']
        ]);
    }
    $db->commit();
}

function allByContact(DataObject $db, int $contactId): iterable
{
    $stmt = $db->prepare('SELECT M.id AS message_id, subject, date_scheduled, date_sent
    FROM deliveries D
    INNER JOIN messages M ON M.id = D.message_id
    INNER JOIN list_contacts LC ON LC.id = D.list_contact_id
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
        ->prepare('SELECT LC.contact_id, C.email, L.id AS list_id, L.name AS list_name, date_scheduled, date_sent
    FROM deliveries D
    INNER JOIN messages M ON M.id = D.message_id
    INNER JOIN list_contacts LC ON LC.id = D.list_contact_id
    INNER JOIN lists L ON L.id = LC.list_id
    INNER JOIN contacts C ON C.id = LC.contact_id
    WHERE M.id = ?
    ORDER BY date_scheduled DESC')
        ->execute([$messageId]);
    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
        yield $row;
    }
}

function count(DataObject $db): int
{
    return $db->query('SELECT COUNT(id) FROM deliveries')->fetch(\PDO::FETCH_NUM)[0];
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
    $stmt = $db->prepare('SELECT D.id AS id, email, subject, body, unsub_hash
    FROM deliveries D
    INNER JOIN list_contacts LC ON LC.id = D.list_contact_id
    INNER JOIN contacts C ON C.id = LC.contact_id
    INNER JOIN messages M ON M.id = D.message_id
    WHERE LC.date_unsubscribed IS NULL
    AND D.date_scheduled < CURRENT_TIMESTAMP
    AND D.date_sent IS NULL
    AND D.date_canceled IS NULL
    ORDER BY D.date_scheduled ASC
    LIMIT (SELECT MIN(?, MAX(0, ?-(SELECT COUNT(id)
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
    $stmt = $db->prepare('SELECT D.id AS id, email, subject, date_scheduled
    FROM deliveries D
    INNER JOIN list_contacts LC ON LC.id = D.list_contact_id
    INNER JOIN contacts C ON C.id = LC.contact_id
    INNER JOIN messages M ON M.id = D.message_id
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

function paginated(DataObject $db, int $perPage, int $page): iterable
{
    $stmt = $db->prepare('SELECT D.message_id, M.subject, C.id AS contact_id, C.email, date_scheduled, date_sent
FROM deliveries D
  INNER JOIN messages M ON D.message_id = M.id
  INNER JOIN list_contacts LC ON D.list_contact_id = LC.id
  INNER JOIN contacts C ON LC.contact_id = C.id
ORDER BY date_scheduled DESC
LIMIT ? OFFSET ?')
        ->execute([$perPage, ($page - 1) * $perPage]);

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
    WHERE id = ?')->execute([$deliveryId]);
}
