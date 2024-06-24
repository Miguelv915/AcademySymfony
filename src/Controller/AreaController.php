<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Controller;

use CarlosChininin\App\Infrastructure\Controller\WebAuthController;
use CarlosChininin\App\Infrastructure\Security\Permission;
use CarlosChininin\Util\Http\ParamFetcher;
use Pidia\Apps\Demo\Entity\Area;
use Pidia\Apps\Demo\Form\AreaType;
use Pidia\Apps\Demo\Manager\AreaManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/admin/area')]
class AreaController extends WebAuthController
{
    public const BASE_ROUTE = 'area_index';

    #[Route(path: '/', name: 'area_index', defaults: ['page' => '1'], methods: ['GET'])]
    #[Route(path: '/page/{page<[1-9]\d*>}', name: 'area_index_paginated', methods: ['GET'])]
    public function index(Request $request, int $page, AreaManager $manager): Response
    {
        $this->denyAccess([Permission::LIST]);

        $paginator = $manager->paginate($page, ParamFetcher::fromRequestQuery($request));

        return $this->render(
            'area/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', name: 'area_export', methods: ['GET'])]
    public function export(Request $request, AreaManager $manager): Response
    {
        $this->denyAccess([Permission::EXPORT]);

        $headers = [
            'Nombre',
        ];

        /** @var Area[] $areas */
        $areas = $manager->dataExport(ParamFetcher::fromRequestQuery($request));
        $items = [];
        foreach ($areas as &$area) {
            $item = [];
            $item[] = $area->getNombre();

            $items[] = $item;
            unset($item, $area);
        }

        return $manager->export($items, $headers, 'area');
    }

    #[Route(path: '/new', name: 'area_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AreaManager $manager): Response
    {
        $this->denyAccess([Permission::NEW]);

        $area = new Area();
        $form = $this->createForm(AreaType::class, $area);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($area)) {
                $this->addFlash('success', 'Registro creado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('area_index');
        }

        return $this->render(
            'area/new.html.twig',
            [
                'area' => $area,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{uuid}', name: 'area_show', methods: ['GET'])]
    public function show(Area $area): Response
    {
        $this->denyAccess([Permission::SHOW], $area);

        return $this->render('area/show.html.twig', ['area' => $area]);
    }

    #[Route(path: '/{uuid}/edit', name: 'area_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Area $area, AreaManager $manager): Response
    {
        $this->denyAccess([Permission::EDIT], $area);

        $form = $this->createForm(AreaType::class, $area);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($area)) {
                $this->addFlash('success', 'Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('area_index', ['id' => $area->getId()]);
        }

        return $this->render(
            'area/edit.html.twig',
            [
                'area' => $area,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{uuid}/state', name: 'area_change_state', methods: ['POST'])]
    public function state(Request $request, Area $area, AreaManager $manager): Response
    {
        $this->denyAccess([Permission::ENABLE, Permission::DISABLE], $area);

        if ($this->isCsrfTokenValid('change_state'.$area->getId(), $request->request->get('_token'))) {
            $area->changeActive();
            if ($manager->save($area)) {
                $this->addFlash('success', 'Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('area_index');
    }

    #[Route(path: '/{uuid}/delete', name: 'area_delete', methods: ['POST'])]
    public function delete(Request $request, Area $area, AreaManager $manager): Response
    {
        $this->denyAccess([Permission::DELETE], $area);

        if ($this->isCsrfTokenValid('delete_forever'.$area->getId(), $request->request->get('_token'))) {
            if ($manager->remove($area)) {
                $this->addFlash('warning', 'Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('area_index');
    }
}
