<?php

namespace RoyallTheFourth\QuickList\Console\Delivery;

use function RoyallTheFourth\QuickList\Common\generatorToArray;
use RoyallTheFourth\QuickList\Db\Delivery;
use RoyallTheFourth\SmoothPdo\DataObject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Show extends Command
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
            ->setName('delivery:list')
            ->setDescription('Show all pending deliveries.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        (new SymfonyStyle($input, $output))
            ->table(
                ['id', 'email', 'subject', 'date_scheduled'],
                array_map(function ($row) {
                    return [
                        $row['id'],
                        $row['email'],
                        $row['subject'],
                        (new \DateTimeImmutable($row['date_scheduled'], new \DateTimeZone('UTC')))
                            ->setTimezone($this->timezone)->format('Y-m-d H:i:s')
                    ];
                }, generatorToArray(Delivery\fetchPending($this->db)))
            );
    }
}
