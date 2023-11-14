<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Security;

use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class PasswordSecurity
{
    public function __construct(
        private PasswordHasherFactoryInterface $encoder,
    ) {
    }

    public function encrypt(UserInterface|string $user, string $password): string
    {
        return $this->encoder->getPasswordHasher($user)->hash($password);
    }

    public function verify($user, string $password, string $hash): bool
    {
        return $this->encoder->getPasswordHasher($user)->verify($hash, $password);
    }

    public function needsRehash(UserInterface|string $user, string $encoded): bool
    {
        return $this->encoder->getPasswordHasher($user)->needsRehash($encoded);
    }
}
