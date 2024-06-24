<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Controller;

use CarlosChininin\App\Infrastructure\Controller\WebAuthController;
use CarlosChininin\App\Infrastructure\Security\Permission;
use CarlosChininin\Util\Http\ParamFetcher;
use Pidia\Apps\Demo\Entity\Pago;
use Pidia\Apps\Demo\Form\PagoType;
use Pidia\Apps\Demo\Manager\PagoManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/admin/pago')]
class PagoController extends WebAuthController
{
    public const BASE_ROUTE = 'pago_index';

    #[Route(path: '/', name: 'pago_index', defaults: ['page' => '1'], methods: ['GET'])]
    #[Route(path: '/page/{page<[1-9]\d*>}', name: 'pago_index_paginated', methods: ['GET'])]
    public function index(Request $request, int $page, PagoManager $manager): Response
    {
        $this->denyAccess([Permission::LIST]);
        $pagosFecha = $manager->getPagosFechas();
        dump($pagosFecha);
        $paginator = $manager->paginate($page, ParamFetcher::fromRequestQuery($request));
        dump($paginator);
        return $this->render(
            'pago/index.html.twig',
            [
                'paginator' => $paginator,
                'dataGrafico'=> $pagosFecha
            ]
        );
    }

    #[Route(path: '/export', name: 'pago_export', methods: ['GET'])]
    public function export(Request $request, PagoManager $manager): Response
    {
        $this->denyAccess([Permission::EXPORT]);

        $headers = [
            'descuento',
            'apellido',
            'Activo',
        ];

        /** @var Pago[] $pagos */
        $pagos = $manager->dataExport(ParamFetcher::fromRequestQuery($request));
        $items = [];
        foreach ($pagos as &$pago) {
            $item = [];
            // $item[] = $pago->getNombre();
            // $item[] = $pago->getApellido();
            // $item[] = $pago->isActive();

            $items[] = $item;
            unset($item, $pago);
        }

        return $manager->export($items, $headers, 'pago');
    }

    #[Route(path: '/new', name: 'pago_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PagoManager $manager): Response
    {
        $this->denyAccess([Permission::NEW]);

        $pago = new Pago();
        $pago->setUsuario($this->getUser());
        $form = $this->createForm(PagoType::class, $pago);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($pago)) {
                $this->addFlash('success', 'Registro creado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('pago_index');
        }

        return $this->render(
            'pago/new.html.twig',
            [
                'pago' => $pago,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{uuid}', name: 'pago_show', methods: ['GET'])]
    public function show(Pago $pago): Response
    {
        $this->denyAccess([Permission::SHOW], $pago);

        return $this->render('pago/show.html.twig', ['pago' => $pago]);
    }

    #[Route(path: '/{uuid}/edit', name: 'pago_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Pago $pago, PagoManager $manager): Response
    {
        $this->denyAccess([Permission::EDIT], $pago);

        $form = $this->createForm(PagoType::class, $pago);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($pago)) {
                $this->addFlash('success', 'Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('pago_index', ['id' => $pago->getId()]);
        }

        return $this->render(
            'pago/edit.html.twig',
            [
                'pago' => $pago,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{uuid}/state', name: 'pago_change_state', methods: ['POST'])]
    public function state(Request $request, Pago $pago, PagoManager $manager): Response
    {
        $this->denyAccess([Permission::ENABLE, Permission::DISABLE], $pago);

        if ($this->isCsrfTokenValid('change_state'.$pago->getId(), $request->request->get('_token'))) {
            $pago->changeActive();
            if ($manager->save($pago)) {
                $this->addFlash('success', 'Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('pago_index');
    }

    #[Route(path: '/{uuid}/delete', name: 'pago_delete', methods: ['POST'])]
    public function delete(Request $request, Pago $pago, PagoManager $manager): Response
    {
        $this->denyAccess([Permission::DELETE], $pago);

        if ($this->isCsrfTokenValid('delete_forever'.$pago->getId(), $request->request->get('_token'))) {
            if ($manager->remove($pago)) {
                $this->addFlash('warning', 'Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('pago_index');
    }

}
