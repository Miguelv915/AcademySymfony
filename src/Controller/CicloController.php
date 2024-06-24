<?php

namespace Pidia\Apps\Demo\Controller;

use CarlosChininin\App\Infrastructure\Controller\WebAuthController;
use CarlosChininin\App\Infrastructure\Security\Permission;
use CarlosChininin\Util\Http\ParamFetcher;
use Pidia\Apps\Demo\Entity\Ciclo;
use Pidia\Apps\Demo\Form\CicloType;
use Pidia\Apps\Demo\Manager\CicloManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/ciclo')]    
class CicloController extends WebAuthController
{
    public const BASE_ROUTE = 'ciclo_index';

    #[Route(path: '/', name: 'ciclo_index', defaults: ['page' => '1'], methods: ['GET'])]
    #[Route(path: '/page/{page<[1-9]\d*>}', name: 'ciclo_index_paginated', methods: ['GET'])]
    public function index(Request $request, int $page, CicloManager $manager): Response
    {
        $this->denyAccess([Permission::LIST]);

        $paginator = $manager->paginate($page, ParamFetcher::fromRequestQuery($request));

        return $this->render(
            'ciclo/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', name: 'ciclo_export', methods: ['GET'])]
    public function export(Request $request, CicloManager $manager): Response
    {
        $this->denyAccess([Permission::EXPORT]);

        $headers = [
            'Precio',
        ];

        /** @var Ciclo[] $ciclos */
        $ciclos = $manager->dataExport(ParamFetcher::fromRequestQuery($request));
        $items = [];
        foreach ($ciclos as &$ciclo) {
            $item = [];
            $item[] = $ciclo->getPrecio();
            $items[] = $item;
            unset($item, $ciclo);
        }

        return $manager->export($items, $headers, 'ciclo');
    }

    #[Route(path: '/new', name: 'ciclo_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CicloManager $manager): Response
    {
        $this->denyAccess([Permission::NEW]);

        $ciclo = new Ciclo();
        $form = $this->createForm(CicloType::class, $ciclo);
        $form->handleRequest($request); 
        // 
        if ($form->isSubmitted() && $form->isValid()) {
            $nombre = $ciclo->getCategoria()->getNombre().'-'.$ciclo->getFechainicio()->format('Y');
            // $ciclo->setNombre($nombre)
            if ($manager->save($ciclo)) {
                $this->addFlash('success', 'Registro creado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('ciclo_index');
        }

        return $this->render(
            'ciclo/new.html.twig',
            [
                'ciclo' => $ciclo,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{uuid}', name: 'ciclo_show', methods: ['GET'])]
    public function show(Ciclo $ciclo): Response
    {
        $this->denyAccess([Permission::SHOW], $ciclo);

        return $this->render('ciclo/show.html.twig', ['ciclo' => $ciclo]);
    }

    #[Route(path: '/{uuid}/edit', name: 'ciclo_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ciclo $ciclo, CicloManager $manager): Response
    {
        $this->denyAccess([Permission::EDIT], $ciclo);

        $form = $this->createForm(CicloType::class, $ciclo);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($ciclo)) {
                $this->addFlash('success', 'Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('ciclo_index', ['id' => $ciclo->getId()]);
        }

        return $this->render(
            'ciclo/edit.html.twig',
            [
                'ciclo' => $ciclo,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{uuid}/state', name: 'ciclo_change_state', methods: ['POST'])]
    public function state(Request $request, Ciclo $ciclo, CicloManager $manager): Response
    {
        $this->denyAccess([Permission::ENABLE, Permission::DISABLE], $ciclo);

        if ($this->isCsrfTokenValid('change_state'.$ciclo->getId(), $request->request->get('_token'))) {
            // $ciclo->changeActive();
            if ($manager->save($ciclo)) {
                $this->addFlash('success', 'Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('ciclo_index');
    }

    #[Route(path: '/{uuid}/delete', name: 'ciclo_delete', methods: ['POST'])]
    public function delete(Request $request, Ciclo $ciclo, CicloManager $manager): Response
    {
        $this->denyAccess([Permission::DELETE], $ciclo);

        if ($this->isCsrfTokenValid('delete_forever'.$ciclo->getId(), $request->request->get('_token'))) {
            if ($manager->remove($ciclo)) {
                $this->addFlash('warning', 'Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('ciclo_index');
    }
}
