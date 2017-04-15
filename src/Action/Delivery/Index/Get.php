<?php

namespace RoyallTheFourth\QuickList\Action\Delivery\Index;

use Psr\Http\Message\ResponseInterface;
use RoyallTheFourth\QuickList\Action\ActionInterface;
use RoyallTheFourth\QuickList\Db;
use function RoyallTheFourth\QuickList\Layout\Partial\pagination;
use RoyallTheFourth\QuickList\Layout\Delivery\Index;
use RoyallTheFourth\Quicklist\Route;
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
                Db\Delivery\paginated($this->db, $this->perPage, $this->page),
                $this->webPrefix,
                $this->timezone,
                pagination($this->page, Db\Delivery\count($this->db), $this->perPage, Route\Delivery\index($this->webPrefix, $this->page))
            ))->render()
        );
    }
}
