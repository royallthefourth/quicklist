<?php

namespace RoyallTheFourth\QuickList\Console\Delivery;

use RoyallTheFourth\QuickList\Db;
use RoyallTheFourth\QuickList\Delivery;
use RoyallTheFourth\SmoothPdo\DataObject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class Schedule extends Command
{
    private $db;
    private $timezone;

    public function __construct(DataObject $db, \DateTimeZone $timezone)
    {
        $this->db = $db;
        $this->timezone;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('delivery:schedule')
            ->setDescription('Schedule a message for delivery.')
            ->addArgument('message-id', InputArgument::REQUIRED, 'The ID of the message.')
            ->addArgument('list-id', InputArgument::REQUIRED, 'The ID of the list.')
            ->addArgument(
                'send-date',
                InputArgument::OPTIONAL,
                'Date to start sending in YYYY-MM-DD HH:MM:SS format. Defaults to now.',
                (new \DateTimeImmutable('now', $this->timezone))->format('Y-m-d H:i:s')
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Db\Delivery\addBulk(
            $this->db,
            Delivery\schedule(
                Db\MailingList\allContactsDeliverable($this->db, $input->getArgument('list-id')),
                $input->getArgument('message-id'),
                new \DateTimeImmutable($input->getArgument('send-date'), $this->timezone))
        );
        $output->writeln('Scheduled delivery.');
    }
}
