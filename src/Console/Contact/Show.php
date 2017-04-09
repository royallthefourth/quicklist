<?php

namespace RoyallTheFourth\QuickList\Console\Contact;

use function RoyallTheFourth\QuickList\Common\iterableToArray;
use RoyallTheFourth\QuickList\Db\Contact;
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
            ->setName('contact:list')
            ->setDescription('Lists all contacts.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        (new SymfonyStyle($input, $output))
            ->table(
                ['id', 'name', 'date added'],
                array_map(
                    function ($row) {
                        return [
                            $row['id'],
                            $row['email'],
                            (new \DateTimeImmutable($row['date_added'], new \DateTimeZone('UTC')))
                                ->setTimezone($this->timezone)->format('Y-m-d H:i:s')
                        ];
                    },
                    iterableToArray(Contact\all($this->db))
                )
            );
    }
}
