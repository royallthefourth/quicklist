<?php

namespace RoyallTheFourth\QuickList\Action\Message\Edit;

use Psr\Http\Message\ResponseInterface;
use RoyallTheFourth\QuickList\Action\ActionInterface;
use function RoyallTheFourth\QuickList\Db\Message\oneById as message;
use RoyallTheFourth\QuickList\Layout\Message\Form;
use RoyallTheFourth\SmoothPdo\DataObject;
use Zend\Diactoros\Response\HtmlResponse;

final class Get implements ActionInterface
{
    private $db;
    private $messageId;
    private $webPrefix;

    public function __construct(DataObject $db, int $messageId, string $webPrefix)
    {
        $this->db = $db;
        $this->messageId = $messageId;
        $this->webPrefix = $webPrefix;
    }

    public function execute(): ResponseInterface
    {
        return new HtmlResponse(
            (new Form($this->webPrefix, message($this->db, $this->messageId)))
                ->render()
        );
    }
}
