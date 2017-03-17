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
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('ERUserBundle:security:login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }

    public function registerAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        // Si la requête est en POST, c'est que le visiteur a soumis le formulaire
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
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