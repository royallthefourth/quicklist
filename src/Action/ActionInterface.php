<?php

namespace RoyallTheFourth\QuickList\Action;

use Psr\Http\Message\ResponseInterface;

interface ActionInterface
{
    public function execute(): ResponseInterface;
}
