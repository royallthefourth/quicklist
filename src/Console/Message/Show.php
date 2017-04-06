<?php

namespace RoyallTheFourth\QuickList\Console\Message;

use RoyallTheFourth\QuickList\Db\Message;
use RoyallTheFourth\SmoothPdo\DataObject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Show extends Command
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
            ->setName('message:list')
            ->setDescription('Lists all messages.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        (new SymfonyStyle($input, $output))
            ->table(['id', 'name', 'date added'], Message\all($this->db));
    }
}
