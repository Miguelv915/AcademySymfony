<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Controller;

use CarlosChininin\App\Infrastructure\Controller\WebAuthController;
use CarlosChininin\App\Infrastructure\Security\Permission;
use CarlosChininin\Util\Http\ParamFetcher;
use Pidia\Apps\Demo\Entity\Parametro;
use Pidia\Apps\Demo\Form\ParametroType;
use Pidia\Apps\Demo\Manager\ParametroManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/admin/parametro')]
class ParametroController extends WebAuthController
{
    public const BASE_ROUTE = 'parametro_index';

    #[Route(path: '/', name: 'parametro_index', defaults: ['page' => '1'], methods: ['GET'])]
    #[Route(path: '/page/{page<[1-9]\d*>}', name: 'parametro_index_paginated', methods: ['GET'])]
    public function index(Request $request, int $page, ParametroManager $manager): Response
    {
        $this->denyAccess([Permission::LIST]);

        $paginator = $manager->paginate($page, ParamFetcher::fromRequestQuery($request));

        return $this->render(
            'parametro/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', name: 'parametro_export', methods: ['GET'])]
    public function export(Request $request, ParametroManager $manager): Response
    {
        $this->denyAccess([Permission::EXPORT]);

        $headers = [
            'Nombre',
            'Alias',
            'Activo',
        ];

        /** @var Parametro[] $parametros */
        $parametros = $manager->dataExport(ParamFetcher::fromRequestQuery($request));
        $items = [];
        foreach ($parametros as &$parametro) {
            $item = [];
            $item[] = $parametro->getName();
            $item[] = $parametro->getAlias();
            $item[] = $parametro->isActive();

            $items[] = $item;
            unset($item, $parametro);
        }

        return $manager->export($items, $headers, 'parametro');
    }

    #[Route(path: '/new', name: 'parametro_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ParametroManager $manager): Response
    {
        $this->denyAccess([Permission::NEW]);

        $parametro = new Parametro();
        $form = $this->createForm(ParametroType::class, $parametro);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($parametro)) {
                $this->addFlash('success', 'Registro creado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('parametro_index');
        }

        return $this->render(
            'parametro/new.html.twig',
            [
                'parametro' => $parametro,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{uuid}', name: 'parametro_show', methods: ['GET'])]
    public function show(Parametro $parametro): Response
    {
        $this->denyAccess([Permission::SHOW], $parametro);

        return $this->render('parametro/show.html.twig', ['parametro' => $parametro]);
    }

    #[Route(path: '/{uuid}/edit', name: 'parametro_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Parametro $parametro, ParametroManager $manager): Response
    {
        $this->denyAccess([Permission::EDIT], $parametro);

        $form = $this->createForm(ParametroType::class, $parametro);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($parametro)) {
                $this->addFlash('success', 'Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('parametro_index', ['id' => $parametro->getId()]);
        }

        return $this->render(
            'parametro/edit.html.twig',
            [
                'parametro' => $parametro,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{uuid}/state', name: 'parametro_change_state', methods: ['POST'])]
    public function state(Request $request, Parametro $parametro, ParametroManager $manager): Response
    {
        $this->denyAccess([Permission::ENABLE, Permission::DISABLE], $parametro);

        if ($this->isCsrfTokenValid('change_state'.$parametro->getId(), $request->request->get('_token'))) {
            $parametro->changeActive();
            if ($manager->save($parametro)) {
                $this->addFlash('success', 'Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('parametro_index');
    }

    #[Route(path: '/{uuid}/delete', name: 'parametro_delete', methods: ['POST'])]
    public function delete(Request $request, Parametro $parametro, ParametroManager $manager): Response
    {
        $this->denyAccess([Permission::DELETE], $parametro);

        if ($this->isCsrfTokenValid('delete_forever'.$parametro->getId(), $request->request->get('_token'))) {
            if ($manager->remove($parametro)) {
                $this->addFlash('warning', 'Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('parametro_index');
    }
}
