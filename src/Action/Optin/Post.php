<?php

namespace RoyallTheFourth\QuickList\Action\Optin;

use EasyCSRF\EasyCSRF;
use EasyCSRF\NativeSessionProvider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RoyallTheFourth\QuickList\Action\ActionInterface;
use function RoyallTheFourth\QuickList\Db\MailingList\getNameFromHash;
use function RoyallTheFourth\QuickList\Db\MailingList\setOptin;
use RoyallTheFourth\QuickList\Layout\Optin\Confirmation;
use RoyallTheFourth\SmoothPdo\DataObject;
use Symfony\Component\Config\Definition\Exception\Exception;
use Zend\Diactoros\Response\HtmlResponse;

final class Post implements ActionInterface
{
    private $csrf;
    private $db;
    private $hash;

    public function __construct(DataObject $db, ServerRequestInterface $request)
    {
        $params = $request->getParsedBody();
        $this->csrf = $params['csrf'];
        $this->db = $db;
        $this->hash = $params['hash'];
    }

    public function execute(): ResponseInterface
    {
        try {
            (new EasyCSRF(new NativeSessionProvider()))->check($this->hash, $this->csrf);
            setOptin($this->db, $this->hash);
            $output = (new Confirmation(getNameFromHash($this->db, $this->hash)))->render();
        } catch (Exception $e) {
            $output = $this->hash.$e->getMessage();
        }

        return new HtmlResponse($output);
    }
}
