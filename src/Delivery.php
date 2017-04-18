<?php

namespace RoyallTheFourth\QuickList\Delivery;

use function RoyallTheFourth\QuickList\Db\Delivery\fetchDue;
use function RoyallTheFourth\QuickList\Db\Delivery\setDelivered;
use RoyallTheFourth\SmoothPdo\DataObject;
use Symfony\Component\Config\Definition\Exception\Exception;

function appendUnsubLink(string $body, string $hash, string $domain, string $prefix = ''): string
{
    return "{$body}\n\nIf you wish to unsubscribe, click here: https://{$domain}/{$prefix}unsubscribe/{$hash}";
}

function messageHash($item): string
{
    return sha1($item . time());
}

/**
 * Sends the next bunch of pending messages
 *
 * @param DataObject $db
 * @param array $config
 * @param \PHPMailer $mailer
 * @return int Number of messages sent this round
 * @throws \Exception
 */
function process(DataObject $db, array $config, \PHPMailer $mailer): int
{
    $count = 0;
    $start = time();
    // gather up the number of unsent emails that can fit within the send limit
    foreach (fetchDue($db, $config['hourly_send_rate']) as $message) {
        if (time() - $start > 55) {
            break;
        }
        $mailer->addAddress($message['email']);
        $mailer->Subject = $message['subject'];
        if (strlen($message['unsub_hash']) > 0) {
            $mailer->Body = appendUnsubLink(
                $message['body'],
                $message['unsub_hash'],
                $config['site_domain'],
                $config['web_prefix']
            );
        } else {
            $mailer->Body = $message['body'];
        }

        try {
            $mailer->send();
            setDelivered($db, $message['id']);
            $count++;
        } catch (Exception $e) {
            throw new \Exception('failed to send message', $e);
        }
        $mailer->clearAddresses();
    }

    return $count;
}

function schedule(iterable $contacts, int $messageId, \DateTimeImmutable $sendDate): iterable
{
    foreach ($contacts as $contact) {
        yield [
            'messageId' => $messageId,
            'listContactId' => $contact['id'],
            'date' => $sendDate->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s'),
            'hash' => messageHash($contact['id'])
        ];
    }
}

function sendsPerMinute(int $hourlySendLimit): int
{
    return max(intdiv($hourlySendLimit, 60), 1);
}
