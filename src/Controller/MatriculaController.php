<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Controller;

use CarlosChininin\App\Infrastructure\Controller\WebAuthController;
use CarlosChininin\App\Infrastructure\Security\Permission;
use CarlosChininin\Util\Http\ParamFetcher;
use Pidia\Apps\Demo\Entity\Matricula;
use Pidia\Apps\Demo\Form\MatriculaType;
use Pidia\Apps\Demo\Manager\MatriculaManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/admin/matricula')]
class MatriculaController extends WebAuthController
{
    public const BASE_ROUTE = 'matricula_index';

    #[Route(path: '/', name: 'matricula_index', defaults: ['page' => '1'], methods: ['GET'])]
    #[Route(path: '/page/{page<[1-9]\d*>}', name: 'matricula_index_paginated', methods: ['GET'])]
    public function index(Request $request, int $page, MatriculaManager $manager): Response
    {
        $this->denyAccess([Permission::LIST]);

        $paginator = $manager->paginate($page, ParamFetcher::fromRequestQuery($request));

        return $this->render(
            'matricula/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', name: 'matricula_export', methods: ['GET'])]
    public function export(Request $request, MatriculaManager $manager): Response
    {
        $this->denyAccess([Permission::EXPORT]);

        $headers = [
            'descuento',
            'apellido',
            'Activo',
        ];

        /** @var Matricula[] $matriculas */
        $matriculas = $manager->dataExport(ParamFetcher::fromRequestQuery($request));
        $items = [];
        foreach ($matriculas as &$matricula) {
            $item = [];
            // $item[] = $matricula->getNombre();
            // $item[] = $matricula->getApellido();
            // $item[] = $matricula->isActive();

            $items[] = $item;
            unset($item, $matricula);
        }

        return $manager->export($items, $headers, 'matricula');
    }

    #[Route(path: '/new', name: 'matricula_new', methods: ['GET', 'POST'])]
    public function new(Request $request, MatriculaManager $manager): Response
    {
        $this->denyAccess([Permission::NEW]);

        $matricula = new Matricula();
        $form = $this->createForm(MatriculaType::class, $matricula);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($matricula)) {
                $this->addFlash('success', 'Registro creado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('matricula_index');
        }

        return $this->render(
            'matricula/new.html.twig',
            [
                'matricula' => $matricula,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{uuid}', name: 'matricula_show', methods: ['GET'])]
    public function show(Matricula $matricula): Response
    {
        $this->denyAccess([Permission::SHOW], $matricula);

        return $this->render('matricula/show.html.twig', ['matricula' => $matricula]);
    }

    #[Route(path: '/{uuid}/edit', name: 'matricula_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Matricula $matricula, MatriculaManager $manager): Response
    {
        $this->denyAccess([Permission::EDIT], $matricula);

        $form = $this->createForm(MatriculaType::class, $matricula);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($matricula)) {
                $this->addFlash('success', 'Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('matricula_index', ['id' => $matricula->getId()]);
        }

        return $this->render(
            'matricula/edit.html.twig',
            [
                'matricula' => $matricula,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{uuid}/state', name: 'matricula_change_state', methods: ['POST'])]
    public function state(Request $request, Matricula $matricula, MatriculaManager $manager): Response
    {
        $this->denyAccess([Permission::ENABLE, Permission::DISABLE], $matricula);

        if ($this->isCsrfTokenValid('change_state'.$matricula->getId(), $request->request->get('_token'))) {
            $matricula->changeActive();
            if ($manager->save($matricula)) {
                $this->addFlash('success', 'Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('matricula_index');
    }

    #[Route(path: '/{uuid}/delete', name: 'matricula_delete', methods: ['POST'])]
    public function delete(Request $request, Matricula $matricula, MatriculaManager $manager): Response
    {
        $this->denyAccess([Permission::DELETE], $matricula);

        if ($this->isCsrfTokenValid('delete_forever'.$matricula->getId(), $request->request->get('_token'))) {
            if ($manager->remove($matricula)) {
                $this->addFlash('warning', 'Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('matricula_index');
    }

    // funcion para retornar la deuda de un estudiante 

    #[Route(path: '/{id}/deuda', name: 'matricula_deuda', methods: ['GET'])]
    public function getDeduda(Matricula $matricula, MatriculaManager $manager): Response
    {
        $this->denyAccess([Permission::DELETE], $matricula);

        // if ($this->isCsrfTokenValid('delete_forever'.$matricula->getId(), $request->request->get('_token'))) {
        //     if ($manager->remove($matricula)) {
        //         $this->addFlash('warning', 'Registro eliminado');
        //     } else {
        //         $this->addErrors($manager->errors());
        //     }
        // }

        return $this->json($matricula->getDeuda());
    }
}
