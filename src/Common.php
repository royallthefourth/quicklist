<?php

namespace RoyallTheFourth\QuickList\Common;

use Symfony\Component\Yaml\Yaml;

function generatorToArray(\Generator $gen): array
{
    $out = [];
    foreach ($gen as $item) {
        $out[] = $item;
    }

    return $out;
}

function config(): array
{
    return Yaml::parse(file_get_contents(__DIR__ . '/../config/config.yml'));
}

function mailer(array $config): \PHPMailer
{
    $mailer = new \PHPMailer(true);
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
