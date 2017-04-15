<?php

namespace RoyallTheFourth\QuickList\Action\MailingList\AddContacts;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RoyallTheFourth\QuickList\Action\ActionInterface;
use function RoyallTheFourth\QuickList\Common\onlyValidEmails;
use function RoyallTheFourth\QuickList\Db\MailingList\addContactBulkSkipOptIn;
use function RoyallTheFourth\QuickList\Db\MailingList\bulkOptIn;
use function RoyallTheFourth\QuickList\Route\MailingList\view;
use RoyallTheFourth\SmoothPdo\DataObject;
use Zend\Diactoros\Response\RedirectResponse;

final class Post implements ActionInterface
{
    private $db;
    private $emails;
    private $listId;
    private $optin;
    private $siteDomain;
    private $webPrefix;

    public function __construct(
        DataObject $db,
        ServerRequestInterface $request,
        string $webPrefix,
        int $listId,
        string $siteDomain
    ) {
        $post = $request->getParsedBody();
        $this->emails = $post['emails'];
        $this->optin = $post['optin'] ?? 'no';
        $this->db = $db;
        $this->listId = $listId;
        $this->siteDomain = $siteDomain;
        $this->webPrefix = $webPrefix;
    }

    public function execute(): ResponseInterface
    {
        $emails = onlyValidEmails(explode("\n", $this->emails));
        if ($this->optin === 'yes') {
            bulkOptIn($this->db, $emails, $this->listId, $this->siteDomain);
        } else {
            addContactBulkSkipOptIn($this->db, $this->listId, $emails);
        }
        return new RedirectResponse(view($this->webPrefix, $this->listId));
    }
}
