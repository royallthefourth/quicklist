<?php

namespace RoyallTheFourth\QuickList\Console\Message;

use RoyallTheFourth\QuickList\Db\Message;
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
            ->setName('message:add')
            ->setDescription('Adds a message and outputs the message ID.')
            ->addArgument('subject', InputArgument::REQUIRED, 'The subject line of the message.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Enter your message:');

        $lines = [];
        while ($line = readline()) {
            $lines[] = $line;
        }

        $message = implode("\n", $lines);
        $output->writeln(Message\add($this->db, $input->getArgument('subject'), $message));
    }
}
