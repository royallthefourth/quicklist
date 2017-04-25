<?php

namespace RoyallTheFourth\QuickList\Common;

use Respect\Validation\Validator;
use Symfony\Component\Yaml\Yaml;

function iterableToArray(iterable $iter): array
{
    $out = [];
    foreach ($iter as $el) {
        $out[] = $el;
    }

    return $out;
}

function config(): array
{
    return Yaml::parse(file_get_contents(__DIR__ . '/../config/config.yml'));
}

function currentVersion(): string
{
    return '1.0';
}

function mailer(array $config): \PHPMailer
{
    $mailer = new \PHPMailer();
    $mailer->isSMTP();
    $mailer->Host = implode(';', $config['smtp']['hosts']);
    $mailer->Username = $config['smtp']['user'];
    $mailer->Password = $config['smtp']['pass'];
    $mailer->SMTPSecure = $config['smtp']['security'];
    $mailer->SMTPAuth = true;
    $mailer->SMTPKeepAlive = true;
    $mailer->Port = $config['smtp']['port'];
    $mailer->setFrom($config['from']);
    return $mailer;
}

/**
 * Converts database times to human-readable times
 *
 * @param string $dateTime The dateTime in UTC
 * @param \DateTimeZone $timezone
 * @return string
 */
function localDate(?string $dateTime, \DateTimeZone $timezone): string
{
    if ($dateTime === null) {
        return '';
    }
    return (new \DateTimeImmutable($dateTime, new \DateTimeZone('UTC')))
        ->setTimezone($timezone)
        ->format('Y-m-d H:i:s');
}

function onlyValidEmails(iterable $emails): iterable
{
    foreach ($emails as $email) {
        $email = trim($email);
        if (Validator::email()->validate($email)) {
            yield $email;
        }
    }
}

function readEmailsFromConsole(): iterable
{
    while ($email = trim(fgets(STDIN))) {
        if (Validator::email()->validate($email)) {
            yield $email;
        }
    }
}

function readMessageFromConsole(): string
{
    $lines = [];
    while ($line = trim(fgets(STDIN))) {
        $lines[] = $line;
    }

    return implode("\n", $lines);
}
