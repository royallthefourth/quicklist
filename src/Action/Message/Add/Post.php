<?php

namespace RoyallTheFourth\QuickList\Action\Message\Add;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RoyallTheFourth\QuickList\Action\ActionInterface;
use function RoyallTheFourth\QuickList\Db\Message\add;
use RoyallTheFourth\SmoothPdo\DataObject;
use Zend\Diactoros\Response\RedirectResponse;

final class Post implements ActionInterface
{
    private $body;
    private $db;
    private $subject;
    private $webPrefix;

    public function __construct(DataObject $db, ServerRequestInterface $request, string $webPrefix)
    {
        ['subject' => $this->subject, 'body' => $this->body] = $request->getParsedBody();
        $this->db = $db;
        $this->webPrefix = $webPrefix;
    }

    public function execute(): ResponseInterface
    {
        $messageId = add($this->db, $this->subject, $this->body);
        return new RedirectResponse("{$this->webPrefix}/message/view/{$messageId}");
    }
}
