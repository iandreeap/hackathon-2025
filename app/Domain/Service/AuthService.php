<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepositoryInterface;

class AuthService
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
    ) {}

    public function register(string $username, string $password): User
    {
        // TODO: check that a user with same username does not exist, create new user and persist
        $userexistent = $this->users->findByUsername($username);
        if($userexistent !== null)
        {
            throw new \InvalidArgumentException('User deja existent');
        }
        // TODO: make sure password is not stored in plain, and proper PHP functions are used for that
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        // TODO: here is a sample code to start with
        $user = new User(null, $username, $hashedPassword, new \DateTimeImmutable());
        $this->users->save($user);

        return $user;
    }

    public function attempt(string $username, string $password): bool
    {
        // TODO: implement this for authenticating the user
        // TODO: make sur ethe user exists and the password matches
        $user = $this->users->findByUsername($username);
        if(!$user)
        {
            return false;
        }

        if(!password_verify($password, $user->passwordHash))
        {
            return false;
        }

        // TODO: don't forget to store in session user data needed afterwards
        $_SESSION['user'] = [
            'id' => $user->id,
            'username' => $user->username,
        ];

        return true;
    }
}
