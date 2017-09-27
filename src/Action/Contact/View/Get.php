<?php

namespace RoyallTheFourth\QuickList\Action\Contact\View;

use Psr\Http\Message\ResponseInterface;
use RoyallTheFourth\QuickList\Action\ActionInterface;
use RoyallTheFourth\QuickList\Db\Contact;
use function RoyallTheFourth\QuickList\Db\Delivery\allByContact as deliveries;
use function RoyallTheFourth\QuickList\Db\MailingList\allByContact as lists;
use RoyallTheFourth\QuickList\Layout\Contact\View;
use RoyallTheFourth\SmoothPdo\DataObject;
use Zend\Diactoros\Response\HtmlResponse;

final class Get implements ActionInterface
{
    private $contactId;
    private $db;
    private $timezone;
    private $webPrefix;

    public function __construct(DataObject $db, int $contactId, string $webPrefix, \DateTimeZone $timezone)
    {
        $this->contactId = $contactId;
        $this->db = $db;
        $this->timezone = $timezone;
        $this->webPrefix = $webPrefix;
    }

    public function execute(): ResponseInterface
    {
        return new HtmlResponse(
            (new View(
                Contact::oneById($this->db, $this->contactId),
                lists($this->db, $this->contactId),
                deliveries($this->db, $this->contactId),
                $this->webPrefix,
                $this->timezone
            ))->render()
        );
    }
}
