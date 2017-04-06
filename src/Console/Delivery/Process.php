<?php

namespace RoyallTheFourth\QuickList\Console\Delivery;

use RoyallTheFourth\QuickList\Delivery;
use RoyallTheFourth\SmoothPdo\DataObject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Process extends Command
{
    private $config;
    private $db;
    private $mailer;

    public function __construct(DataObject $db, array $config, \PHPMailer $mailer)
    {
        $this->config = $config;
        $this->db = $db;
        $this->mailer = $mailer;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('delivery:process')
            ->setDescription('Process the next chunk of scheduled deliveries.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $recipients = Delivery\process($this->db, $this->config, $this->mailer);
        $output->writeln('Sent messages to ' . $recipients . ' recipients.');
    }
}
