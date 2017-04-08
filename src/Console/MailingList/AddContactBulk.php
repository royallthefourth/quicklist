<?php

namespace RoyallTheFourth\QuickList\Console\MailingList;

use function RoyallTheFourth\QuickList\Contact\onlyValidEmails;
use function RoyallTheFourth\QuickList\Db\MailingList\getId;
use function RoyallTheFourth\QuickList\Db\MailingList\addContactBulkSkipOptIn;
use RoyallTheFourth\SmoothPdo\DataObject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddContactBulk extends Command
{
    private $db;
    private $domain;

    public function __construct(DataObject $db, string $domain)
    {
        $this->domain = $domain;
        $this->db = $db;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('list:contact:add-bulk')
            ->setDescription('Adds multiple new or existing contacts without optin.')
            ->addArgument('list-name', InputArgument::REQUIRED, 'The name of the mailing list.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Enter emails, one per line');
        $emails = [];
        while (strlen($email = readline()) > 0) {
            $emails[] = $email;
        }

        addContactBulkSkipOptIn(
            $this->db,
            getId($this->db, $input->getArgument('list-name')),
            onlyValidEmails($emails)
        );
    }
}
