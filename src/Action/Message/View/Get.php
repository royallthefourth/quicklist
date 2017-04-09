<?php

namespace RoyallTheFourth\QuickList\Action\Message\View;

use Psr\Http\Message\ResponseInterface;
use RoyallTheFourth\QuickList\Action\ActionInterface;
use function RoyallTheFourth\QuickList\Db\MailingList\all as lists;
use function RoyallTheFourth\QuickList\Db\Message\oneById;
use function RoyallTheFourth\QuickList\Db\Delivery\allByMessage as deliveries;
use RoyallTheFourth\QuickList\Layout\Message\View;
use RoyallTheFourth\SmoothPdo\DataObject;
use Zend\Diactoros\Response\HtmlResponse;

final class Get implements ActionInterface
{
    private $messageId;
    private $db;
    private $timezone;
    private $webPrefix;

    public function __construct(DataObject $db, int $messageId, string $webPrefix, \DateTimeZone $timezone)
    {
        $this->messageId = $messageId;
        $this->db = $db;
        $this->timezone = $timezone;
        $this->webPrefix = $webPrefix;
    }

    public function execute(): ResponseInterface
    {
        return new HtmlResponse(
            (new View(
                oneById($this->db, $this->messageId),
                deliveries($this->db, $this->messageId),
                lists($this->db),
                $this->webPrefix,
                $this->timezone
            ))->render()
        );
    }
}
