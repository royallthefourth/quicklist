<?php

namespace RoyallTheFourth\QuickList\Action\Unsubscribe;

use EasyCSRF\EasyCSRF;
use EasyCSRF\NativeSessionProvider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RoyallTheFourth\QuickList\Action\ActionInterface;
use function RoyallTheFourth\QuickList\Db\MailingList\getNameFromUnsubHash;
use function RoyallTheFourth\QuickList\Db\MailingList\setUnsub;
use RoyallTheFourth\QuickList\Layout\Unsubscribe\Confirmation;
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
            setUnsub($this->db, $this->hash);
            $output = (new Confirmation(getNameFromUnsubHash($this->db, $this->hash)))->render();
        } catch (Exception $e) {
            $output = $this->hash.$e->getMessage();
        }

        return new HtmlResponse($output);
    }
}
