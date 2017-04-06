<?php

namespace RoyallTheFourth\QuickList\Console\Contact;

use RoyallTheFourth\QuickList\Db\Contact;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Show extends Command
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
            ->setName('contact:list')
            ->setDescription('Lists all contacts.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        (new SymfonyStyle($input, $output))
            ->table(['id', 'name', 'date added'], Contact\all($this->db));
    }
}
