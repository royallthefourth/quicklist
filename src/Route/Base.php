<?php

namespace RoyallTheFourth\QuickList\Route\Base;

function home(string $webPrefix): string
{
    return "{$webPrefix}/";
}

function login(string $webPrefix): string
{
    return "{$webPrefix}/login";
}

function logout(string $webPrefix): string
{
    return "{$webPrefix}/logout";
}

function unsubscribeGet(string $webPrefix): string
{
    return "{$webPrefix}/unsubscribe/{hash}";
}

function unsubscribePost(string $webPrefix): string
{
    return "{$webPrefix}/unsubscribe";
}

function optinGet(string $webPrefix): string
{
    return "{$webPrefix}/optin/{hash}";
}

function optinPost(string $webPrefix): string
{
    return "{$webPrefix}/optin";
}
