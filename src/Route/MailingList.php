<?php

namespace RoyallTheFourth\QuickList\Route\MailingList;

function add(string $webPrefix): string
{
    return "{$webPrefix}/list/add";
}

function addContacts(string $webPrefix, $listId): string
{
    return "{$webPrefix}/list/{$listId}/contacts/add";
}

function contacts(string $webPrefix, $listId, $page = 1): string
{
    return "{$webPrefix}/list/{$listId}/contacts/{$page}";
}

function index(string $webPrefix, $page = 1): string
{
    return "{$webPrefix}/list/{$page}";
}

function messages(string $webPrefix, $listId, $page = 1): string
{
    return "{$webPrefix}/list/{$listId}/messages/{$page}";
}

function view(string $webPrefix, $listId): string
{
    return "{$webPrefix}/list/view/{$listId}";
}
