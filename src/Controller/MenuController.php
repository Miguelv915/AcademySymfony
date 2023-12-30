<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Controller;

use CarlosChininin\App\Infrastructure\Controller\WebAuthController;
use CarlosChininin\App\Infrastructure\Security\Menu\MenuBuilder;
use CarlosChininin\App\Infrastructure\Security\Permission;
use CarlosChininin\Util\Http\ParamFetcher;
use Pidia\Apps\Demo\Cache\MenuCache;
use Pidia\Apps\Demo\Entity\Menu;
use Pidia\Apps\Demo\Form\MenuType;
use Pidia\Apps\Demo\Manager\MenuManager;
use Pidia\Apps\Demo\Repository\MenuRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/admin/menu')]
class MenuController extends WebAuthController
{
    public const BASE_ROUTE = 'menu_index';

    #[Route(path: '/', name: 'menu_index', defaults: ['page' => '1'], methods: ['GET'])]
    #[Route(path: '/page/{page<[1-9]\d*>}', name: 'menu_index_paginated', methods: ['GET'])]
    public function index(Request $request, int $page, MenuManager $manager): Response
    {
        $this->denyAccess([Permission::LIST]);

        $paginator = $manager->paginate($page, ParamFetcher::fromRequestQuery($request));

        return $this->render(
            'menu/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', name: 'menu_export', methods: ['GET'])]
    public function export(Request $request, MenuManager $manager): Response
    {
        $this->denyAccess([Permission::EXPORT]);

        $headers = [
            'Padre',
            'Nombre',
            'Ruta',
            'Icono',
            'Orden',
            'Activo',
        ];

        /** @var Menu[] $menus */
        $menus = $manager->dataExport(ParamFetcher::fromRequestQuery($request));
        $items = [];
        foreach ($menus as &$menu) {
            $item = [];
            $item[] = $menu->getParent()?->getName();
            $item[] = $menu->getName();
            $item[] = $menu->getRoute();
            $item[] = $menu->getIcon();
            $item[] = $menu->getRanking();
            $item[] = $menu->isActive();

            $items[] = $item;
            unset($item, $menu);
        }

        return $manager->export($items, $headers, 'menu');
    }

    #[Route(path: '/new', name: 'menu_new', methods: ['GET', 'POST'])]
    public function new(Request $request, MenuManager $manager, MenuCache $cache): Response
    {
        $this->denyAccess([Permission::NEW]);

        $menu = new Menu();
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($menu)) {
                $cache->update();
                $this->messageSuccess('Registro creado!!!');

                return $this->redirectToRoute('menu_index', [], Response::HTTP_SEE_OTHER);
            }

            $this->addErrors($manager->errors());
        }

        return $this->render(
            'menu/new.html.twig',
            [
                'menu' => $menu,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{uuid}', name: 'menu_show', methods: ['GET'])]
    public function show(Menu $menu): Response
    {
        $this->denyAccess([Permission::SHOW], $menu);

        return $this->render('menu/show.html.twig', ['menu' => $menu]);
    }

    #[Route(path: '/{uuid}/edit', name: 'menu_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Menu $menu, MenuManager $manager, MenuCache $cache): Response
    {
        $this->denyAccess([Permission::EDIT], $menu);

        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($menu)) {
                $cache->update();
                $this->messageSuccess('Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('menu_index', ['id' => $menu->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render(
            'menu/edit.html.twig',
            [
                'menu' => $menu,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{uuid}/state', name: 'menu_change_state', methods: ['POST'])]
    public function state(Request $request, Menu $menu, MenuManager $manager, MenuCache $cache): Response
    {
        $this->denyAccess([Permission::ENABLE, Permission::DISABLE], $menu);

        if ($this->isCsrfTokenValid('change_state'.$menu->getId(), $request->request->get('_token'))) {
            $menu->changeActive();
            if ($manager->save($menu)) {
                $cache->update();
                $this->messageSuccess('Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('menu_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/{uuid}/delete', name: 'menu_delete', methods: ['POST'])]
    public function delete(Request $request, Menu $menu, MenuManager $manager, MenuCache $cache): Response
    {
        $this->denyAccess([Permission::DELETE], $menu);

        if ($this->isCsrfTokenValid('delete_forever'.$menu->getId(), $request->request->get('_token'))) {
            if ($manager->remove($menu)) {
                $cache->update();
                $this->messageWarning('Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('menu_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/menu_build', name: 'menu_build', methods: ['GET'])]
    public function buildMenu(string $menuSelected, MenuRepository $menuRepository, MenuBuilder $menuBuilder, MenuCache $menuCache): Response
    {
        $content = $menuCache->menus($menuSelected, function () use ($menuRepository, $menuBuilder, $menuSelected) {
            $menus = $menuRepository->searchAllActiveWithOrder();

            return $this->renderView('@App/theme1/menu/menu.html.twig', [
                'menus' => $menuBuilder->execute($menus, $menuSelected),
                'menuSelected' => $menuSelected,
            ]);
        });

        return new Response($content);
    }
}
