<?php

namespace RoyallTheFourth\QuickList\Action\MailingList\Index;

use Psr\Http\Message\ResponseInterface;
use RoyallTheFourth\QuickList\Action\ActionInterface;
use function RoyallTheFourth\QuickList\Db\MailingList\summary;
use function RoyallTheFourth\QuickList\Db\MailingList\count;
use function RoyallTheFourth\QuickList\Layout\Partial\pagination;
use RoyallTheFourth\QuickList\Layout\MailingList\Index;
use RoyallTheFourth\SmoothPdo\DataObject;
use Zend\Diactoros\Response\HtmlResponse;

final class Get implements ActionInterface
{
    private $db;
    private $page;
    private $perPage;
    private $webPrefix;

    public function __construct(DataObject $db, int $page, string $webPrefix)
    {
        $this->db = $db;
        $this->page = $page;
        $this->perPage = 50;
        $this->webPrefix = $webPrefix;
    }

    public function execute(): ResponseInterface
    {
        return new HtmlResponse(
            (new Index(
                summary($this->db, $this->page, $this->perPage),
                $this->webPrefix,
                pagination($this->page, count($this->db), $this->perPage, "{$this->webPrefix}/list")
            ))->render()
        );
    }
}
