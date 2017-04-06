<?php

namespace RoyallTheFourth\QuickList\Console\Delivery;

use RoyallTheFourth\QuickList\Delivery;
use RoyallTheFourth\SmoothPdo\DataObject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Schedule extends Command
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
            ->setName('delivery:schedule')
            ->setDescription('Schedule a message for delivery.')
            ->addArgument('message-id', InputArgument::REQUIRED, 'The ID of the message.')
            ->addArgument('list-id', InputArgument::REQUIRED, 'The ID of the list.')
            ->addArgument('send-date', InputArgument::REQUIRED, 'Date to start sending in YYYY-MM-DD HH:MM:SS format.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $recipients = Delivery\schedule(
            $this->db,
            $input->getArgument('message-id'),
            $input->getArgument('list-id'),
            new \DateTimeImmutable($input->getArgument('send-date'))
        );
        $output->writeln('Scheduled delivery to ' . $recipients . ' recipients.');
    }
}
