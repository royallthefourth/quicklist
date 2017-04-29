<?php

namespace RoyallTheFourth\QuickList\Console\Contact;

use function RoyallTheFourth\QuickList\Common\readEmailsFromConsole;
use function RoyallTheFourth\QuickList\Db\Contact\removeBulk;
use RoyallTheFourth\SmoothPdo\DataObject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveBulk extends Command
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
            ->setName('list:contact:remove-bulk')
            ->setDescription('Removes multiple contacts from all lists.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Enter emails, one per line:');
        removeBulk($this->db, readEmailsFromConsole());
    }
}
