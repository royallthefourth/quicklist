<?php

namespace RoyallTheFourth\QuickList\Action\Message\Edit;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RoyallTheFourth\QuickList\Action\ActionInterface;
use function RoyallTheFourth\QuickList\Db\Message\update;
use RoyallTheFourth\SmoothPdo\DataObject;
use Zend\Diactoros\Response\RedirectResponse;

final class Post implements ActionInterface
{
    private $body;
    private $db;
    private $messageId;
    private $subject;
    private $webPrefix;

    public function __construct(DataObject $db, ServerRequestInterface $request, int $messageId, string $webPrefix)
    {

        ['subject' => $this->subject, 'body' => $this->body] = $request->getParsedBody();
        $this->db = $db;
        $this->messageId = $messageId;
        $this->webPrefix = $webPrefix;
    }

    public function execute(): ResponseInterface
    {
        update($this->db, $this->subject, $this->body, $this->messageId);
        return new RedirectResponse("{$this->webPrefix}/message/view/{$this->messageId}");
    }
}
