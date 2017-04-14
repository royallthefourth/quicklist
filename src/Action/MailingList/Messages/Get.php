<?php

namespace RoyallTheFourth\QuickList\Action\MailingList\Messages;

use Psr\Http\Message\ResponseInterface;
use RoyallTheFourth\QuickList\Action\ActionInterface;
use function RoyallTheFourth\QuickList\Db\MailingList\countMessages;
use function RoyallTheFourth\QuickList\Db\MailingList\paginatedMessagesSentToList;
use function RoyallTheFourth\QuickList\Db\MailingList\getName;
use RoyallTheFourth\QuickList\Layout\MailingList\Messages;
use function RoyallTheFourth\QuickList\Layout\Partial\pagination;
use function RoyallTheFourth\QuickList\Route\MailingList\messages;
use RoyallTheFourth\SmoothPdo\DataObject;
use Zend\Diactoros\Response\HtmlResponse;

final class Get implements ActionInterface
{
    private $db;
    private $listId;
    private $page;
    private $perPage;
    private $webPrefix;

    public function __construct(DataObject $db, int $page, int $listId, string $webPrefix)
    {
        $this->db = $db;
        $this->listId = $listId;
        $this->page = $page;
        $this->perPage = 50;
        $this->webPrefix = $webPrefix;
    }

    public function execute(): ResponseInterface
    {
        return new HtmlResponse(
            (new Messages(
                paginatedMessagesSentToList($this->db, $this->listId, $this->page, $this->perPage),
                getName($this->db, $this->listId),
                $this->webPrefix,
                pagination(
                    $this->page,
                    countMessages($this->db, $this->listId),
                    $this->perPage,
                    messages($this->webPrefix, $this->listId, '')
                )
            ))->render()
        );
    }
}
