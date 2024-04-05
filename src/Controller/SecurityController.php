<?php

namespace Pidia\Apps\Demo\Controller;

use LogicException;
use Pidia\Apps\Demo\Entity\Usuario;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class SecurityController extends AbstractController
{
    use TargetPathTrait;

    #[Route(path: '/login', name: 'security_login')]
    public function login(
        #[CurrentUser] ?Usuario $usuario,
        Request $request,
        AuthenticationUtils $helper,
    ): Response {
        if ($usuario) {
            return $this->redirectToRoute('homepage');
        }

        $this->saveTargetPath($request->getSession(), 'main', $this->generateUrl('homepage'));

        return $this->render('security/login.html.twig', [
            'last_username' => $helper->getLastUsername(),
            'error' => $helper->getLastAuthenticationError(),
        ]);
    }

//    #[Route(path: '/logout', name: 'security_logout')]
//    public function logout(): void
//    {
//        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
//    }
}
