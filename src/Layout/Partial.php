<?php

namespace RoyallTheFourth\QuickList\Layout\Partial;

function flash(string $message = ''): string
{
    return <<<flash
    <div id="flash">{$message}</div>
flash;
}

function nav(string $webPrefix): string
{
    return <<<nav
        <ul>
        <li><a href="{$webPrefix}/">dashboard</a></li>
        <li><a href="{$webPrefix}/contact/1">contacts</a></li>
        <li><a href="{$webPrefix}/message/1">messages</a></li>
        <li><a href="{$webPrefix}/logout">logout</a></li>
</ul>
nav;
}

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
            $links .= "<li><a href=\"{$path}/{$i}\">{$i}</a></li>";
        }
    }

    return "<ul class=\"pagination\">{$links}</ul>";
}
