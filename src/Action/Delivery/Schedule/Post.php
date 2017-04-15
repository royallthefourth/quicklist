<?php

namespace RoyallTheFourth\QuickList\Action\Delivery\Schedule;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use RoyallTheFourth\QuickList\Action\ActionInterface;
use RoyallTheFourth\QuickList\Db;
use RoyallTheFourth\QuickList\Delivery;
use RoyallTheFourth\QuickList\Route;
use RoyallTheFourth\SmoothPdo\DataObject;
use Zend\Diactoros\Response\RedirectResponse;

final class Post implements ActionInterface
{
    private $db;
    private $listId;
    private $messageId;
    private $sendDate;
    private $timezone;
    private $webPrefix;

    public function __construct(ServerRequestInterface $request, DataObject $db, string $webPrefix, \DateTimeZone $timezone)
    {
        [
            'listId' => $this->listId,
            'messageId' => $this->messageId,
            'sendDate' => $this->sendDate
        ] = $request->getParsedBody();
        $this->db = $db;
        $this->timezone = $timezone;
        $this->webPrefix = $webPrefix;
    }

    public function execute(): ResponseInterface
    {
        Db\Delivery\addBulk(
            $this->db,
            Delivery\schedule(
                Db\MailingList\allContactsDeliverable($this->db, $this->listId),
                $this->messageId,
                new \DateTimeImmutable($this->sendDate, $this->timezone))
        );
        return new RedirectResponse(Route\Delivery\index($this->webPrefix));
    }
}
