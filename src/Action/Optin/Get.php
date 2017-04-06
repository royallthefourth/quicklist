<?php

namespace RoyallTheFourth\QuickList\Action\Optin;

use EasyCSRF\EasyCSRF;
use EasyCSRF\NativeSessionProvider;
use Psr\Http\Message\ResponseInterface;
use RoyallTheFourth\QuickList\Action\ActionInterface;
use function RoyallTheFourth\QuickList\Db\MailingList\getNameFromOptinHash;
use RoyallTheFourth\QuickList\Layout\Optin\Landing;
use RoyallTheFourth\SmoothPdo\DataObject;
use Zend\Diactoros\Response\HtmlResponse;

final class Get implements ActionInterface
{
    private $csrf;
    private $db;
    private $hash;
    private $webPrefix;

    public function __construct(DataObject $db, string $hash, string $webPrefix)
    {
        $this->csrf = (new EasyCSRF(new NativeSessionProvider()))->generate($hash);
        $this->db = $db;
        $this->hash = $hash;
        $this->webPrefix = $webPrefix;
    }

    public function execute(): ResponseInterface
    {
        return new HtmlResponse(
            (new Landing(
                getNameFromOptinHash($this->db, $this->hash),
                $this->hash,
                $this->csrf,
                $this->webPrefix
            ))->render()
        );
    }
}
