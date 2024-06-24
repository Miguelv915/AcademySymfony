<?php

namespace Pidia\Apps\Demo\Controller;

use CarlosChininin\App\Infrastructure\Controller\WebAuthController;
use CarlosChininin\App\Infrastructure\Security\Permission;
use CarlosChininin\Util\Http\ParamFetcher;
use Pidia\Apps\Demo\Entity\Categoria;
use Pidia\Apps\Demo\Form\CategoriaType;
use Pidia\Apps\Demo\Manager\CategoriaManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/categoria')]    
class CategoriaController extends WebAuthController
{
    public const BASE_ROUTE = 'categoria_index';

    #[Route(path: '/', name: 'categoria_index', defaults: ['page' => '1'], methods: ['GET'])]
    #[Route(path: '/page/{page<[1-9]\d*>}', name: 'categoria_index_paginated', methods: ['GET'])]
    public function index(Request $request, int $page, CategoriaManager $manager): Response
    {
        $this->denyAccess([Permission::LIST]);

        $paginator = $manager->paginate($page, ParamFetcher::fromRequestQuery($request));

        return $this->render(
            'categoria/index.html.twig',
            [
                'paginator' => $paginator,
            ]
        );
    }

    #[Route(path: '/export', name: 'categoria_export', methods: ['GET'])]
    public function export(Request $request, CategoriaManager $manager): Response
    {
        $this->denyAccess([Permission::EXPORT]);

        $headers = [
            'Nombre',
        ];

        /** @var Categoria[] $categorias */
        $categorias = $manager->dataExport(ParamFetcher::fromRequestQuery($request));
        $items = [];
        foreach ($categorias as &$categoria) {
            $item = [];
            $item[] = $categoria->getNombre();

            $items[] = $item;
            unset($item, $categoria);
        }

        return $manager->export($items, $headers, 'categoria');
    }

    #[Route(path: '/new', name: 'categoria_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CategoriaManager $manager): Response
    {
        $this->denyAccess([Permission::NEW]);

        $categoria = new Categoria();
        $form = $this->createForm(CategoriaType::class, $categoria);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($categoria)) {
                $this->addFlash('success', 'Registro creado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('categoria_index');
        }

        return $this->render(
            'categoria/new.html.twig',
            [
                'categoria' => $categoria,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{uuid}', name: 'categoria_show', methods: ['GET'])]
    public function show(Categoria $categoria): Response
    {
        $this->denyAccess([Permission::SHOW], $categoria);

        return $this->render('categoria/show.html.twig', ['categoria' => $categoria]);
    }

    #[Route(path: '/{uuid}/edit', name: 'categoria_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Categoria $categoria, CategoriaManager $manager): Response
    {
        $this->denyAccess([Permission::EDIT], $categoria);

        $form = $this->createForm(CategoriaType::class, $categoria);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->save($categoria)) {
                $this->addFlash('success', 'Registro actualizado!!!');
            } else {
                $this->addErrors($manager->errors());
            }

            return $this->redirectToRoute('categoria_index', ['id' => $categoria->getId()]);
        }

        return $this->render(
            'categoria/edit.html.twig',
            [
                'categoria' => $categoria,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{uuid}/state', name: 'categoria_change_state', methods: ['POST'])]
    public function state(Request $request, Categoria $categoria, CategoriaManager $manager): Response
    {
        $this->denyAccess([Permission::ENABLE, Permission::DISABLE], $categoria);

        if ($this->isCsrfTokenValid('change_state'.$categoria->getId(), $request->request->get('_token'))) {
            // $categoria->changeActive();
            if ($manager->save($categoria)) {
                $this->addFlash('success', 'Estado ha sido actualizado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('categoria_index');
    }

    #[Route(path: '/{uuid}/delete', name: 'categoria_delete', methods: ['POST'])]
    public function delete(Request $request, Categoria $categoria, CategoriaManager $manager): Response
    {
        $this->denyAccess([Permission::DELETE], $categoria);

        if ($this->isCsrfTokenValid('delete_forever'.$categoria->getId(), $request->request->get('_token'))) {
            if ($manager->remove($categoria)) {
                $this->addFlash('warning', 'Registro eliminado');
            } else {
                $this->addErrors($manager->errors());
            }
        }

        return $this->redirectToRoute('categoria_index');
    }
}
