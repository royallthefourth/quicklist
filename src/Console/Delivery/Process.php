<?php

namespace RoyallTheFourth\QuickList\Console\Delivery;

use function RoyallTheFourth\QuickList\Db\Delivery\fetchDue;
use function RoyallTheFourth\QuickList\Db\Delivery\setDelivered;
use RoyallTheFourth\QuickList\Delivery;
use RoyallTheFourth\SmoothPdo\DataObject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class Process extends Command
{
    private $config;
    private $db;
    private $lockFilePath;
    private $mailer;

    public function __construct(DataObject $db, array $config, \PHPMailer $mailer)
    {
        $this->config = $config;
        $this->db = $db;
        $this->lockFilePath = __DIR__ . '/../../../config/lock.yml';
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
        // a race condition exists where another process writes the lockfile right after this one check for it.
        // extremely unliklely to happen in the real world.
        if (file_exists($this->lockFilePath)) {
            throw new \Exception('Delivery process already running.');
        }

        file_put_contents(
            $this->lockFilePath,
            Yaml::dump([
                'start_time' => (new \DateTimeImmutable())->format('Y-m-d H:i:s') . ' UTC',
                'pid' => getmypid()
            ])
        );

        $count = 0;
        try {
            foreach (Delivery\process(
                         fetchDue($this->db, $this->config['hourly_send_rate']),
                         $this->config['site_domain'],
                         $this->config['web_prefix'],
                         $this->mailer
                     ) as $deliveryId) {
                setDelivered($this->db, $deliveryId);
                $count++;
            }
        } catch (\Exception $e) {
            throw new \Exception('', 0, $e);
        } finally {
            unlink($this->lockFilePath);
        }
        
        $output->writeln('Sent messages to ' . $count . ' recipients.');
    }
}
