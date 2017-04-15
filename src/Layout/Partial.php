<?php

namespace RoyallTheFourth\QuickList\Layout\Partial;

use RoyallTheFourth\QuickList\Route\Base;
use RoyallTheFourth\QuickList\Route\Delivery;
use RoyallTheFourth\QuickList\Route\MailingList;
use RoyallTheFourth\QuickList\Route\Message;

function flash(string $message = ''): string
{
    return <<<flash
    <div id="flash">{$message}</div>
flash;
}

function nav(string $webPrefix): string
{
    $deliveries = Delivery\index($webPrefix);
    $lists = MailingList\index($webPrefix);
    $messages = Message\index($webPrefix);
    $logout = Base\logout($webPrefix);

    return <<<nav
        <ul>
        <li><a href="{$deliveries}">deliveries</a></li>
        <li><a href="{$lists}">lists</a></li>
        <li><a href="{$messages}">messages</a></li>
        <li><a href="{$logout}">logout</a></li>
</ul>
nav;
}

// TODO find all usages and make sure they use a route function as path
function pagination(int $currentPage, int $total, int $perPage, string $path): string
{
    if ($total <= $perPage) {
        return '';
    }

    $links = '';
    for ($i = 1; ($i - 1) * $perPage < $total; $i++) {
        if ($i === $currentPage) {
            $links .= "<li>{$i}</li>";
        } else {
            $links .= "<li><a href=\"{$path}{$i}\">{$i}</a></li>";
        }
    }

    return "<ul class=\"pagination\">{$links}</ul>";
}
