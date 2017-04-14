<?php

namespace RoyallTheFourth\QuickList\Route\Contact;

function index(string $webPrefix, $page = 1): string
{
    return "{$webPrefix}/contact/{$page}";
}

function view(string $webPrefix, $contactId): string
{
    return "{$webPrefix}{$webPrefix}/contact/view/{$contactId}";
}
