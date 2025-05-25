<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Domain\Service\AuthService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;

class AuthController extends BaseController
{
    public function __construct(
        Twig $view,
        private AuthService $authService,
        private LoggerInterface $logger,
    ) {
        parent::__construct($view);
    }

    public function showRegister(Request $request, Response $response): Response
    {
        // TODO: you also have a logger service that you can inject and use anywhere; file is var/app.log
        $this->logger->info('Register page requested');

        return $this->render($response, 'auth/register.twig');
    }

    public function register(Request $request, Response $response): Response
    {
        // TODO: call corresponding service to perform user registration
        $data = $request->getParsedBody();
        $username=$data['username'] ?? '';
        $password=$data['password'] ?? '';
        $passwordMatch = $data['password_match'] ?? '';
        $errors =[];


        //username (>=4 chars)
        if(strlen($username)<4)
        {
            $errors['username'] = 'Username-ul trebuie să aibă minim 4 caractere';
        }

        //password (>=8 chars si 1 numar)
        $continenumar=false; 
        for($i=0; $i<strlen($password); $i++)
        {
            $caracter = $password[$i];
            if($caracter >= '0' && $caracter <='9')
            {
                $continenumar = true;
                break;
            }
        }

        if(strlen($password)<8 || $continenumar == false)
        {
            $errors['password'] = 'Parola trebuie sa aiba 8 caractere si sa contina un numar';
        }

        //confirmarea potrivirii parolelor
        $passwordMatch = $data['password_match'] ?? '';
        if($password!==$passwordMatch)
        {
            $errors['password_match'] = 'Parolele nu se potrivesc.';
        }

        if (!empty($errors))
        {
            return $this->render($response, 'auth/register.twig', [
                'username' => $username,
                'errors' => $errors

            ]);
    
        }
        $this->AuthService->register($username, $password);

        return $response->withHeader('Location', '/login')->withStatus(302);
    }

    public function showLogin(Request $request, Response $response): Response
    {
        return $this->render($response, 'auth/login.twig');
    }

    public function login(Request $request, Response $response): Response
    {
        // TODO: call corresponding service to perform user login, handle login failures

        $data = $request->getParsedBody();
        $username=$data['username'] ?? '';
        $password=$data['password'] ?? '';
        $errors = [];

        if($username === '' || $password === '')
        {
            $errors['necompletat'] = 'Nu uita sa completezi user si parola';
        }


        if(empty($errors) && !$this->AuthService->attempt($username, $password)) 
        {
            $errors['incorect'] = 'Username sau parola incorecte';
        }

        if(!empty($errors))
        {
            return $this->render($response, 'auth/login.twig', [
            'username' => $username, 
            'errors' => $errors
            ]);
        }

        return $response->withHeader('Location', '/')->withStatus(302);
    }

    public function logout(Request $request, Response $response): Response
    {
        // TODO: handle logout by clearing session data and destroying session
        $_SESSION = [];
        session_destroy();

        return $response->withHeader('Location', '/login')->withStatus(302);
    }
}
