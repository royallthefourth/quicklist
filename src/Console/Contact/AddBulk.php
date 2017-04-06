<?php

namespace RoyallTheFourth\QuickList\Console\Contact;

use RoyallTheFourth\QuickList\Db\Contact;
use RoyallTheFourth\SmoothPdo\DataObject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddBulk extends Command
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
            ->setName('contact:add-bulk')
            ->setDescription('Adds multiple contacts, line-by-line.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $emails = [];
        while (strlen($email = readline()) > 0) {
            $emails[] = $email;
        }

        Contact\addBulk($this->db, $emails);
    }
}
