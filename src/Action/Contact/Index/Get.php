<?php

namespace RoyallTheFourth\QuickList\Action\Contact\Index;

use Psr\Http\Message\ResponseInterface;
use RoyallTheFourth\QuickList\Action\ActionInterface;
use RoyallTheFourth\QuickList\Db;
use function RoyallTheFourth\QuickList\Layout\Partial\pagination;
use RoyallTheFourth\QuickList\Layout;
use RoyallTheFourth\QuickList\Route;
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
            (new Layout\Contact\Index(
                Db\Contact::paginated($this->db, $this->page, $this->perPage),
                $this->webPrefix,
                $this->timezone,
                pagination(
                    $this->page,
                    Db\Contact::count($this->db),
                    $this->perPage,
                    Route\Contact\index($this->webPrefix, '')
                )
            ))->render()
        );
    }
}
