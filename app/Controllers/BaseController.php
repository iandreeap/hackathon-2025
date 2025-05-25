<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

abstract class BaseController
{
    public function __construct(
        protected Twig $view,
    ) {}

    protected function render(Response $response, string $template, array $data = []): Response
    {
        if(session_status() === PHP_SESSION_NONE)
        {
            session_start();
        }

        $data['currentUserId'] = $_SESSION['user']['id'] ?? null;
        $data['currentUserName'] = $_SESSION ['user']['username'] ?? null;

        return $this->view->render($response, $template, $data);
    }

    // TODO: add here any common controller logic and use in concrete controllers
}
