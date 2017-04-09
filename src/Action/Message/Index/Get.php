<?php

namespace RoyallTheFourth\QuickList\Action\Message\Index;

use Psr\Http\Message\ResponseInterface;
use RoyallTheFourth\QuickList\Action\ActionInterface;
use function RoyallTheFourth\QuickList\Db\Message\paginated;
use function RoyallTheFourth\QuickList\Db\Message\count;
use function RoyallTheFourth\QuickList\Layout\Partial\pagination;
use RoyallTheFourth\QuickList\Layout\Message\Index;
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
                $this->page,
                $this->timezone,
                pagination($this->page, count($this->db), $this->perPage, "{$this->webPrefix}/contact")
            ))->render()
        );
    }
}
