<?php

namespace RoyallTheFourth\QuickList\Action\Message\Add;

use Psr\Http\Message\ResponseInterface;
use RoyallTheFourth\QuickList\Action\ActionInterface;
use RoyallTheFourth\QuickList\Layout\Message\Form;
use RoyallTheFourth\SmoothPdo\DataObject;
use Zend\Diactoros\Response\HtmlResponse;

final class Get implements ActionInterface
{
    private $db;
    private $webPrefix;

    public function __construct(DataObject $db, string $webPrefix)
    {
        $this->db = $db;
        $this->webPrefix = $webPrefix;
    }

    public function execute(): ResponseInterface
    {
        return new HtmlResponse(
            (new Form($this->webPrefix))
                ->render()
        );
    }
}
