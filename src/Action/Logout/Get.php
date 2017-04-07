<?php

namespace RoyallTheFourth\QuickList\Action\Logout;

use Psr\Http\Message\ResponseInterface;
use RoyallTheFourth\QuickList\Action\ActionInterface;
use Zend\Diactoros\Response\RedirectResponse;

final class Get implements ActionInterface
{
    private $webPrefix;

    public function __construct(string $webPrefix)
    {
        $this->webPrefix = $webPrefix;
    }

    public function execute(): ResponseInterface
    {
        unset($_SESSION['userId']);
        return new RedirectResponse("{$this->webPrefix}/");
    }
}
