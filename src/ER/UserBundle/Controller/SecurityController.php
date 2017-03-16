<?php

namespace ER\UserBundle\Controller;

use ER\UserBundle\Entity\User;
use ER\UserBundle\ERUserBundle;
use ER\UserBundle\Form\UserType;
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
        $user = new User();
        $form = $this->get('form.factory')->create(UserType::class, $user);

        // Si la requête est en POST, c'est que le visiteur a soumis le formulaire
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // Ici, on s'occupera de la création et de la gestion du formulaire
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

            // Puis on redirige vers la page de visualisation de cettte annonce
            return $this->redirectToRoute('er_boxshadow_home', array('id' => $user->getId()));
        }

        return $this->render('@ERUser/Security/register.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}