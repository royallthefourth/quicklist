<?php

namespace RoyallTheFourth\QuickList\Action\Login;

use EasyCSRF\EasyCSRF;
use EasyCSRF\NativeSessionProvider;
use Psr\Http\Message\ResponseInterface;
use RoyallTheFourth\QuickList\Action\ActionInterface;
use RoyallTheFourth\QuickList\Layout\Login\Form;
use Zend\Diactoros\Response\HtmlResponse;

final class Get implements ActionInterface
{
    private $csrf;
    private $webPrefix;

    public function __construct(string $webPrefix)
    {
        $this->csrf = (new EasyCSRF(new NativeSessionProvider()))->generate('login');
        $this->webPrefix = $webPrefix;
    }

    public function execute(): ResponseInterface
    {
        return new HtmlResponse((new Form($this->csrf, $this->webPrefix))->render());
    }
}
