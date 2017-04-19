<?php

namespace RoyallTheFourth\QuickList\Delivery;

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
 * Sends the next bunch of pending deliveries
 *
 * @param iterable $deliveries
 * @param string $siteDomain
 * @param string $webPrefix
 * @param \PHPMailer $mailer
 * @return iterable The deliveryId of each message that succeeded
 * @throws \Exception
 * @internal param array $config
 */
function process(iterable $deliveries, string $siteDomain, string $webPrefix, \PHPMailer $mailer): iterable
{
    $count = 0;
    $start = time();
    // gather up the number of unsent emails that can fit within the send limit
    foreach ($deliveries as $delivery) {
        if (time() - $start > 55) {
            break;
        }
        $mailer->addAddress($delivery['email']);
        $mailer->Subject = $delivery['subject'];
        if (strlen($delivery['unsub_hash']) > 0) {
            $mailer->Body = appendUnsubLink(
                $delivery['body'],
                $delivery['unsub_hash'],
                $siteDomain,
                $webPrefix
            );
        } else {
            $mailer->Body = $delivery['body'];
        }

        try {
            $mailer->send();
            yield $delivery['id'];
        } catch (Exception $e) {
            throw new \Exception('failed to send delivery', $e);
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
