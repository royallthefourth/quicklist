<?php

namespace RoyallTheFourth\QuickList\Action\MailingList\Add;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RoyallTheFourth\QuickList\Action\ActionInterface;
use function RoyallTheFourth\QuickList\Db\MailingList\add;
use function RoyallTheFourth\QuickList\Route\MailingList\view;
use RoyallTheFourth\SmoothPdo\DataObject;
use Zend\Diactoros\Response\RedirectResponse;

final class Post implements ActionInterface
{
    private $db;
    private $name;
    private $webPrefix;

    public function __construct(DataObject $db, ServerRequestInterface $request, string $webPrefix)
    {
        ['name' => $this->name] = $request->getParsedBody();
        $this->db = $db;
        $this->webPrefix = $webPrefix;
    }

    public function execute(): ResponseInterface
    {
        $listId = add($this->db, $this->name);
        return new RedirectResponse(view($this->webPrefix, $listId));
    }
}
