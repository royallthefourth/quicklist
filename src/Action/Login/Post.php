<?php

namespace RoyallTheFourth\QuickList\Action\Login;

use EasyCSRF\EasyCSRF;
use EasyCSRF\NativeSessionProvider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RoyallTheFourth\QuickList\Action\ActionInterface;
use function RoyallTheFourth\QuickList\Db\User\getByName;
use RoyallTheFourth\SmoothPdo\DataObject;
use Zend\Diactoros\Response\RedirectResponse;

final class Post implements ActionInterface
{
    private $csrf;
    private $db;
    private $password;
    private $username;
    private $webPrefix;

    public function __construct(DataObject $db, ServerRequestInterface $request, string $webPrefix)
    {
        $params = $request->getParsedBody();
        $this->csrf = $params['csrf'];
        $this->db = $db;
        $this->password = $params['password'];
        $this->username = $params['username'];
        $this->webPrefix = $webPrefix;
    }

    public function execute(): ResponseInterface
    {
        try {
            (new EasyCSRF(new NativeSessionProvider()))->check('login', $this->csrf, 3600);
            $user = getByName($this->db, $this->username);

            if (password_verify($this->password, $user['password'])) {
                $_SESSION['userId'] = $user['id'];
            } else {
                throw new \Exception('Invalid username or password.');
            }
        } catch (\Exception $e) {
            unset($_SESSION['userId']);
            $_SESSION['flash'] = $e->getMessage();
        }

        return new RedirectResponse("{$this->webPrefix}/");
    }
}
