<?php

namespace ER\BoxShadowBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class AdvertController extends Controller
{
    /**
     * @Route("/hello", name="index")
     */
    public function indexAction()
    {
        $content = $this->get('templating')->render('ERBoxShadowBundle:Advert:index.html.twig', array(
            'name' => 'rachid',
        ));

        return new Response($content);
    }

    /**
     * @Route("/hello/{foo}", name="test")
     */
    public function viewAction($foo)
    {
        return new Response("Affichage de l'annonce d'id : ".$foo);
    }
}
