<?php

namespace RoyallTheFourth\QuickList\Console\MailingList;

use function RoyallTheFourth\QuickList\Common\generatorToArray;
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

    public function __construct(DataObject $db)
    {
        $this->db = $db;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('list:contact:list')
            ->setDescription('Outputs current contacts on a mailing list.')
            ->addArgument('list-name', InputArgument::REQUIRED, 'The name of the mailing list.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        (new SymfonyStyle($input, $output))
            ->table(
                ['id', 'name'],
                generatorToArray(MailingList\allContacts($this->db, $input->getArgument('list-name')))
            );
    }
}
