<?php

namespace RoyallTheFourth\QuickList\Console\MailingList;

use RoyallTheFourth\QuickList\Db\MailingList;
use RoyallTheFourth\SmoothPdo\DataObject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveContact extends Command
{
    private $db;

    public function __construct(DataObject $db)
    {
        $this->db = $db;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('list:contact:remove')
            ->setDescription('Removes a contact from a mailing list.')
            ->addArgument('list-name', InputArgument::REQUIRED, 'The name of the mailing list.')
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the recipient.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        MailingList\removeContact(
            $this->db,
            $input->getArgument('list-name'),
            $input->getArgument('email')
        );
    }
}
