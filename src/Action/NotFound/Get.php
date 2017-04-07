<?php

namespace RoyallTheFourth\QuickList\Action\NotFound;

use Psr\Http\Message\ResponseInterface;
use RoyallTheFourth\QuickList\Action\ActionInterface;
use RoyallTheFourth\QuickList\Layout\Error\NotFound;
use Zend\Diactoros\Response\HtmlResponse;

final class Get implements ActionInterface
{
    public function execute(): ResponseInterface
    {
        return new HtmlResponse((new NotFound())->render(), 404);
    }
}
