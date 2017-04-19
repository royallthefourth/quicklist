<?php

namespace RoyallTheFourth\QuickList\Console\Delivery;

use function RoyallTheFourth\QuickList\Db\Delivery\fetchDue;
use function RoyallTheFourth\QuickList\Db\Delivery\setDelivered;
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
        $count = 0;
        foreach (Delivery\process(
            fetchDue($this->db, $this->config['hourly_send_rate']),
            $this->config['site_domain'],
            $this->config['web_prefix'],
            $this->mailer
        ) as $deliveryId) {
            setDelivered($this->db, $deliveryId);
            $count++;
        }
        $output->writeln('Sent messages to ' . $count . ' recipients.');
    }
}
