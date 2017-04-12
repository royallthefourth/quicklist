<?php

namespace RoyallTheFourth\QuickList\Console\MailingList;

use function RoyallTheFourth\QuickList\Common\readEmailsFromConsole;
use function RoyallTheFourth\QuickList\Db\MailingList\bulkOptIn;
use function RoyallTheFourth\QuickList\Db\MailingList\getId;
use function RoyallTheFourth\QuickList\Db\MailingList\addContactBulkSkipOptIn;
use RoyallTheFourth\SmoothPdo\DataObject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AddContactBulk extends Command
{
    private $db;
    private $domain;

    public function __construct(DataObject $db, string $domain)
    {
        $this->db = $db;
        $this->domain = $domain;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('list:contact:add-bulk')
            ->setDescription('Adds multiple new or existing contacts to a list.')
            ->addArgument('list-name', InputArgument::REQUIRED, 'The name of the mailing list.')
            ->addOption(
                'optin',
                null,
                InputOption::VALUE_OPTIONAL,
                'Send an optin message to the added contacts?',
                'yes'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Enter emails, one per line:');
        $emails = readEmailsFromConsole();
        $listId = getId($this->db, $input->getArgument('list-name'));

        if ($input->getOption('optin') === 'no') {
            addContactBulkSkipOptIn(
                $this->db,
                $listId,
                $emails
            );
        } else {
            bulkOptIn($this->db, $emails, $listId, $this->domain);
        }
    }
}
