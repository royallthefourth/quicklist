<?php

namespace RoyallTheFourth\QuickList\Console\Contact;

use RoyallTheFourth\QuickList\Db\Contact;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Add extends Command
{
    private $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('contact:add')
            ->setDescription('Adds a new contact.')
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the new contact.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Contact\add($this->db, $input->getArgument('email'));
    }
}
