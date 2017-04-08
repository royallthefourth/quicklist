<?php

namespace RoyallTheFourth\QuickList\Console\MailingList;

use RoyallTheFourth\QuickList\MailingList;
use RoyallTheFourth\SmoothPdo\DataObject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class OptInContact extends Command
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
            ->setName('list:contact:optin')
            ->setDescription('Adds an existing contact to a mailing list and sends an opt-in message.')
            ->addArgument('list-name', InputArgument::REQUIRED, 'The name of the mailing list.')
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the recipient.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        MailingList\optIn(
            $this->db,
            $input->getArgument('list-name'),
            $input->getArgument('email'),
            $this->domain
        );
    }
}
