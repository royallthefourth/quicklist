<?php

namespace RoyallTheFourth\QuickList\Console\MailingList;

use function RoyallTheFourth\QuickList\Common\iterableToArray;
use RoyallTheFourth\QuickList\Db\MailingList;
use RoyallTheFourth\SmoothPdo\DataObject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ShowContacts extends Command
{
    private $db;
    private $timezone;

    public function __construct(DataObject $db, \DateTimeZone $timezone)
    {
        $this->db = $db;
        $this->timezone = $timezone;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('list:contact:list')
            ->setDescription('Lists current contacts on a mailing list.')
            ->addArgument('list-id', InputArgument::REQUIRED, 'The ID of the mailing list.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        (new SymfonyStyle($input, $output))
            ->table(
                ['id', 'email', 'date_added'],
                array_map(function ($row) {
                    return [
                        $row['id'],
                        $row['email'],
                        (new \DateTimeImmutable($row['date_added'], new \DateTimeZone('UTC')))
                            ->setTimezone($this->timezone)->format('Y-m-d H:i:s')
                    ];
                }, iterableToArray(MailingList\allContacts($this->db, $input->getArgument('list-id'))))
            );
    }
}
