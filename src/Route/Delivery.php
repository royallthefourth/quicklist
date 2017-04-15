<?php

namespace RoyallTheFourth\QuickList\Route\Delivery;

function index(string $webPrefix, $page = 1): string
{
    return "{$webPrefix}/delivery/{$page}";
}

function schedule(string $webPrefix): string
{
    return "{$webPrefix}/delivery/schedule";
}
