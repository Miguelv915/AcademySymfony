<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Twig;

use CarlosChininin\App\Infrastructure\Security\Security;
use Pidia\Apps\Demo\Repository\ConfigRepository;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\RuntimeExtensionInterface;

final class ConfigMenuRuntime implements RuntimeExtensionInterface
{
    private ?array $menus = null;

    public function __construct(
        private readonly ConfigRepository $configRepository,
        private readonly Security $security,
        private readonly UrlGeneratorInterface $router)
    {
    }

    public function buildMenu(string $section): array
    {
        return $this->build($section);
    }

    private function menus(): array
    {
        if (null === $this->menus) {
            $configId = $this->security->user()?->config()?->getId();
            $this->menus = $this->configRepository->findMenusByConfigId($configId);
        }

        return $this->menus;
    }

    private function build(string $section): array
    {
        if (false === $this->security->isSuperAdmin()) {
            return [];
        }

        $menus = $this->menus();
        $data = [];

        $class = '';
        foreach ($menus as $menu) {
            if (false === $this->isValidRouter($menu['route'])) {
                continue;
            }

            if ($menu['route'] === $section) {
                $class = 'open';
            }
            $data[] = array_merge($menu, ['active' => $menu['route'] === $section]);
        }

        return ['items' => $data, 'class' => $class];
    }

    private function isValidRouter(?string $routeName): bool
    {
        if (null === $routeName) {
            return true;
        }

        try {
            $this->router->generate($routeName);
        } catch (RouteNotFoundException) {
            return false;
        }

        return true;
    }
}
