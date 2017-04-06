<?php

namespace RoyallTheFourth\QuickList\Console\User;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Show extends Command
{
    private $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('user:list')
            ->setDescription('Lists usernames.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $stmt = $this->db->query('SELECT name FROM users');

        while ($user = $stmt->fetch()) {
            $output->writeln($user['name']);
        }
    }
}
