<?php

namespace RoyallTheFourth\QuickList\Console\MailingList;

use RoyallTheFourth\QuickList\Db\MailingList;
use RoyallTheFourth\SmoothPdo\DataObject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Add extends Command
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
            ->setName('list:add')
            ->setDescription('Adds a new mailing list.')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the new mailing list.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        MailingList\add($this->db, $input->getArgument('name'));
    }
}
