<?php

namespace RoyallTheFourth\QuickList\Action\Contact\Index;

use Psr\Http\Message\ResponseInterface;
use RoyallTheFourth\QuickList\Action\ActionInterface;
use function RoyallTheFourth\QuickList\Db\Contact\paginated;
use function RoyallTheFourth\QuickList\Db\Contact\count;
use function RoyallTheFourth\QuickList\Layout\Partial\pagination;
use RoyallTheFourth\QuickList\Layout\Contact\Index;
use RoyallTheFourth\QuickList\Route\Contact;
use RoyallTheFourth\SmoothPdo\DataObject;
use Zend\Diactoros\Response\HtmlResponse;

final class Get implements ActionInterface
{
    private $db;
    private $page;
    private $perPage;
    private $timezone;
    private $webPrefix;

    public function __construct(DataObject $db, int $page, string $webPrefix, \DateTimeZone $timezone)
    {
        $this->db = $db;
        $this->page = $page;
        $this->perPage = 50;
        $this->timezone = $timezone;
        $this->webPrefix = $webPrefix;
    }

    public function execute(): ResponseInterface
    {
        return new HtmlResponse(
            (new Index(
                paginated($this->db, $this->page, $this->perPage),
                $this->webPrefix,
                $this->timezone,
                pagination($this->page, count($this->db), $this->perPage, Contact\index($this->webPrefix, ''))
            ))->render()
        );
    }
}
