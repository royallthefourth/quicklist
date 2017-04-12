<?php

namespace RoyallTheFourth\QuickList\Action\MailingList\Contacts;

use Psr\Http\Message\ResponseInterface;
use RoyallTheFourth\QuickList\Action\ActionInterface;
use function RoyallTheFourth\QuickList\Db\MailingList\paginatedContactsRaw;
use function RoyallTheFourth\QuickList\Db\MailingList\getName;
use function RoyallTheFourth\QuickList\Db\MailingList\count;
use function RoyallTheFourth\QuickList\Layout\Partial\pagination;
use RoyallTheFourth\QuickList\Layout\MailingList\Contacts;
use RoyallTheFourth\SmoothPdo\DataObject;
use Zend\Diactoros\Response\HtmlResponse;

final class Get implements ActionInterface
{
    private $db;
    private $listId;
    private $page;
    private $perPage;
    private $timezone;
    private $webPrefix;

    public function __construct(DataObject $db, int $page, int $listId, string $webPrefix, \DateTimeZone $timezone)
    {
        $this->db = $db;
        $this->listId = $listId;
        $this->page = $page;
        $this->perPage = 50;
        $this->timezone = $timezone;
        $this->webPrefix = $webPrefix;
    }

    public function execute(): ResponseInterface
    {
        return new HtmlResponse(
            (new Contacts(
                paginatedContactsRaw($this->db, $this->listId, $this->page, $this->perPage),
                getName($this->db, $this->listId),
                $this->webPrefix,
                pagination($this->page, count($this->db), $this->perPage, "{$this->webPrefix}/list"),
                $this->timezone
            ))->render()
        );
    }
}
