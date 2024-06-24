<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Controller;

use CarlosChininin\App\Infrastructure\Controller\WebAuthController;
use CarlosChininin\App\Infrastructure\Security\Permission;
use CarlosChininin\Util\Http\ParamFetcher;
use Pidia\Apps\Demo\Entity\Alumno;
use Pidia\Apps\Demo\Form\AlumnoType;
use Pidia\Apps\Demo\Manager\AlumnoManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/admin/alumno')]
class AlumnoController extends WebAuthController
{
    public const BASE_ROUTE = 'alumno_index';

    #[Route(path: '/', name: 'alumno_index', defaults: ['page' => '1'], methods: ['GET'])]
    #[Route(path: '/page/{page<[1-9]\d*>}', name: 'alumno_index_paginated', methods: ['GET'])]
    public function index(Request $request, int $page, AlumnoManager $manager): Response
    {
        $this->denyAccess([Permission::LIST]);

        $paginator = $manager->paginate($page, ParamFetcher::fromRequestQuery($request));

        return $this->render(
            'alumno/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', name: 'alumno_export', methods: ['GET'])]
    public function export(Request $request, AlumnoManager $manager): Response
    {
        $this->denyAccess([Permission::EXPORT]);

        $headers = [
            'Nombre',
            'apellido',
            'Activo',
        ];

        /** @var Alumno[] $alumnos */
        $alumnos = $manager->dataExport(ParamFetcher::fromRequestQuery($request));
        $items = [];
        foreach ($alumnos as &$alumno) {
            $item = [];
            $item[] = $alumno->getNombre();
            $item[] = $alumno->getApellido();
            $item[] = $alumno->isActive();

            $items[] = $item;
            unset($item, $alumno);
        }

        return $manager->export($items, $headers, 'alumno');
    }

    #[Route(path: '/new', name: 'alumno_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AlumnoManager $manager): Response
    {
        $this->denyAccess([Permission::NEW]);

        $alumno = new Alumno();
        $form = $this->createForm(AlumnoType::class, $alumno);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($alumno)) {
                $this->addFlash('success', 'Registro creado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('alumno_index');
        }

        return $this->render(
            'alumno/new.html.twig',
            [
                'alumno' => $alumno,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{uuid}', name: 'alumno_show', methods: ['GET'])]
    public function show(Alumno $alumno): Response
    {
        $this->denyAccess([Permission::SHOW], $alumno);

        return $this->render('alumno/show.html.twig', ['alumno' => $alumno]);
    }

    #[Route(path: '/{uuid}/edit', name: 'alumno_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Alumno $alumno, AlumnoManager $manager): Response
    {
        $this->denyAccess([Permission::EDIT], $alumno);

        $form = $this->createForm(AlumnoType::class, $alumno);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($alumno)) {
                $this->addFlash('success', 'Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('alumno_index', ['id' => $alumno->getId()]);
        }

        return $this->render(
            'alumno/edit.html.twig',
            [
                'alumno' => $alumno,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{uuid}/state', name: 'alumno_change_state', methods: ['POST'])]
    public function state(Request $request, Alumno $alumno, AlumnoManager $manager): Response
    {
        $this->denyAccess([Permission::ENABLE, Permission::DISABLE], $alumno);

        if ($this->isCsrfTokenValid('change_state'.$alumno->getId(), $request->request->get('_token'))) {
            $alumno->changeActive();
            if ($manager->save($alumno)) {
                $this->addFlash('success', 'Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('alumno_index');
    }

    #[Route(path: '/{uuid}/delete', name: 'alumno_delete', methods: ['POST'])]
    public function delete(Request $request, Alumno $alumno, AlumnoManager $manager): Response
    {
        $this->denyAccess([Permission::DELETE], $alumno);

        if ($this->isCsrfTokenValid('delete_forever'.$alumno->getId(), $request->request->get('_token'))) {
            if ($manager->remove($alumno)) {
                $this->addFlash('warning', 'Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('alumno_index');
    }
}
