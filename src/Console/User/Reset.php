<?php

namespace RoyallTheFourth\QuickList\Console\User;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class Reset extends Command
{
    private $db;
    private $helper;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
        $this->helper = new QuestionHelper();
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('user:reset')
            ->setDescription('Resets a user\'s password.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $this->helper->ask($input, $output, new Question('Username: '));
        $password = $this->helper->ask($input, $output, (new Question('Password: '))->setHidden(true));

        $this->db
            ->prepare('UPDATE users SET password = ? WHERE name = ?')
            ->execute([password_hash($password, PASSWORD_BCRYPT), $username]);
    }
}
