<?php

namespace RoyallTheFourth\QuickList\Route\Message;

function add(string $webPrefix): string
{
    return "{$webPrefix}/message/add";
}

function edit(string $webPrefix, $messageId): string
{
    return "{$webPrefix}/message/edit/{$messageId}";
}

function index(string $webPrefix, $page = 1): string
{
    return "{$webPrefix}/message/{$page}";
}

function view(string $webPrefix, $messageId): string
{
    return "{$webPrefix}/message/view/{$messageId}";
}
