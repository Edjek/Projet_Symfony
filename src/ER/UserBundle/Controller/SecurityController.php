<?php

namespace ER\UserBundle\Controller;

use ER\UserBundle\ERUserBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller
{
    public function loginAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('er_boxshadow_home');
        }

        $authenticatedUtils = $this->get('security.authentication_utils');

        return $this->render('ERUserBundle:security:login.html.twig', array(
            'last_username' => $authenticatedUtils->getLastUsername(),
            'error'         => $authenticatedUtils->getLastAuthenticationError(),
        ));
    }

    public function loginCheckAction(Request $request)
    {
        return $this->redirectToRoute('er_boxshadow_home');
    }

    public function registerAction(Request $request)
    {
        return $this->render('@ERUser/Security/register.html.twig');
    }
}