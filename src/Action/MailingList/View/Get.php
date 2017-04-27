<?php

namespace RoyallTheFourth\QuickList\Action\MailingList\View;

use Psr\Http\Message\ResponseInterface;
use RoyallTheFourth\QuickList\Action\ActionInterface;
use function RoyallTheFourth\QuickList\Db\MailingList\countActiveContacts;
use function RoyallTheFourth\QuickList\Db\MailingList\countMessages;
use function RoyallTheFourth\QuickList\Db\MailingList\getName;
use RoyallTheFourth\QuickList\Layout\MailingList\View;
use RoyallTheFourth\SmoothPdo\DataObject;
use Zend\Diactoros\Response\HtmlResponse;

final class Get implements ActionInterface
{
    private $db;
    private $listId;
    private $webPrefix;

    public function __construct(DataObject $db, int $listId, string $webPrefix)
    {
        $this->db = $db;
        $this->listId = $listId;
        $this->webPrefix = $webPrefix;
    }

    public function execute(): ResponseInterface
    {
        return new HtmlResponse(
            (new View(
                $this->listId,
                getName($this->db, $this->listId),
                countMessages($this->db, $this->listId),
                countActiveContacts($this->db, $this->listId),
                $this->webPrefix
            ))->render()
        );
    }
}
