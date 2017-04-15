<?php

namespace RoyallTheFourth\QuickList\Action\Delivery\Schedule;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use RoyallTheFourth\QuickList\Action\ActionInterface;
use RoyallTheFourth\QuickList\Db;
use RoyallTheFourth\QuickList\Layout\Delivery\Schedule;
use RoyallTheFourth\SmoothPdo\DataObject;
use Zend\Diactoros\Response\HtmlResponse;

final class Get implements ActionInterface
{
    private $db;
    private $listId;
    private $messageId;
    private $timezone;
    private $webPrefix;

    public function __construct(ServerRequestInterface $request, DataObject $db, string $webPrefix, \DateTimeZone $timezone)
    {
        [
            'listId' => $this->listId,
            'messageId' => $this->messageId
        ] = $request->getQueryParams();
        $this->db = $db;
        $this->timezone = $timezone;
        $this->webPrefix = $webPrefix;
    }

    public function execute(): ResponseInterface
    {

        return new HtmlResponse(
            (new Schedule(
                $this->listId,
                Db\MailingList\getName($this->db, $this->listId),
                $this->messageId,
                Db\Message\getName($this->db, $this->messageId),
                $this->timezone,
                $this->webPrefix
                )
            )->render()
        );
    }
}
