<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Service\Menu;

use CarlosChininin\App\Domain\Model\AuthMenu\MenuServiceInterface;
use Pidia\Apps\Demo\Repository\MenuRepository;

final readonly class MenuService implements MenuServiceInterface
{
    public function __construct(
        private MenuRepository $menuRepository
    ) {
    }

    public function menusToArray(): array
    {
        $menus = $this->menuRepository->allForMenus();
        $data = [];
        foreach ($menus as $menu) {
            $name = mb_strtoupper($menu['parentName'].' - '.$menu['name']);
            $data[$name] = $menu['route'];
        }

        return $data;
    }
}
