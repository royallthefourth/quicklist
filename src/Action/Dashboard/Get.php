<?php

namespace RoyallTheFourth\QuickList\Action\Dashboard;

use Psr\Http\Message\ResponseInterface;
use RoyallTheFourth\QuickList\Action\ActionInterface;
use RoyallTheFourth\QuickList\Layout\Dashboard;
use Zend\Diactoros\Response\HtmlResponse;

final class Get implements ActionInterface
{
    private $webPrefix;

    public function __construct(string $webPrefix)
    {
        $this->webPrefix = $webPrefix;
    }

    public function execute(): ResponseInterface
    {
        return new HtmlResponse((new Dashboard($this->webPrefix))->render());
    }
}
