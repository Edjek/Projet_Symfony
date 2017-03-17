<?php

namespace ER\BoxShadowBundle\Controller;

use ER\BoxShadowBundle\Entity\Advert;
use ER\BoxShadowBundle\Event\BoxShadowEvent;
use ER\BoxShadowBundle\Event\MessagePostEvent;
use ER\BoxShadowBundle\Form\AdvertEditType;
use ER\BoxShadowBundle\Form\AdvertType;
use ER\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdvertController extends Controller
{
    public function indexAction($page)
    {
        if ($page < 1) {
            throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
        }
        $nbPerpage = 3;

        $listAdverts = $this->getDoctrine()
            ->getManager()
            ->getRepository("ERBoxShadowBundle:Advert")
            ->getAdverts($page, $nbPerpage);

        $nbPages = ceil(count($listAdverts)/$nbPerpage);
        if ($nbPages == 0) { $nbPages = 1; }

        return $this->render('ERBoxShadowBundle:Advert:index.html.twig', array(
            'listAdverts' => $listAdverts,
            'nbPages'     => $nbPages,
            'page'        => $page
        ));
    }

    public function viewAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $advert = $em->getRepository('ERBoxShadowBundle:Advert')->find($id);

        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }

        $listApplications = $em
            ->getRepository('ERBoxShadowBundle:Application')
            ->findBy(array('advert'=> $advert));

        $listAdvertSkills = $em
            ->getRepository("ERBoxShadowBundle:AdvertSkill")
            ->findBy(array('advert' => $advert));

        return $this->render('ERBoxShadowBundle:Advert:view.html.twig', array(
            'advert' => $advert,
            'listApplications' => $listApplications,
            'listAdvertSkills' => $listAdvertSkills
        ));
    }

    /**
     * @Security("has_role('ROLE_AUTEUR')")
     */
    public function addAction(Request $request)
    {
        $advert = new Advert();
        $user = $this->getUser();
        $advert->setUser($user);
        $form = $this->createForm(AdvertType::class, $advert);

        // Si la requête est en POST, c'est que le visiteur a soumis le formulaire
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $event = new MessagePostEvent($advert->getContent(), $user);
            $this->get('event_dispatcher')->dispatch(BoxShadowEvent::POST_MESSAGE, $event);
            $advert->setContent($event->getMessage());

            $em = $this->getDoctrine()->getManager();
            $em->persist($advert);
            $em->flush();

            // Ici, on s'occupera de la création et de la gestion du formulaire
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

            // Puis on redirige vers la page de visualisation de cettte annonce
            return $this->redirectToRoute('er_boxshadow_view', array('id' => $advert->getId()));
        }

        return $this->render('ERBoxShadowBundle:Advert:add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $advert = $em->getRepository("ERBoxShadowBundle:Advert")->find($id);

        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }

        $form = $this->get('form.factory')->create(AdvertEditType::class, $advert);

        // Si la requête est en POST, c'est que le visiteur a soumis le formulaire
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em->flush();

            // Ici, on s'occupera de la création et de la gestion du formulaire
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

            return $this->redirectToRoute('er_boxshadow_view', array('id' => $advert->getId()));
        }

        return $this->render('ERBoxShadowBundle:Advert:edit.html.twig', array(
            'advert' => $advert,
            'form' => $form->createView(),
        ));
    }

    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $advert = $em->getRepository("ERBoxShadowBundle:Advert")->find($id);
        $listAdvertSkill = $em->getRepository("ERBoxShadowBundle:AdvertSkill")->findBy(array('advert' => $advert));
        $listApplications = $em->getRepository("ERBoxShadowBundle:Application")->findBy(array('advert' => $advert));

        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }
        $form = $this->get('form.factory')->create();

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            foreach ($advert->getCategories() as $category) {
                $advert->removeCategory($category);
            }
            foreach ($listApplications as $application) {
                $em->remove($application);
            }
            foreach ($listAdvertSkill as $advertSkill) {
                $em->remove($advertSkill);
            }
            $em->remove($advert);
            $em->flush();

            $request->getSession()->getFlashBag()->add('info', "L'annonce a bien été supprimée.");

            return $this->redirectToRoute('er_boxshadow_home');
        }

        return $this->render('ERBoxShadowBundle:Advert:delete.html.twig', array(
            'id' => $advert->getId(),
            'advert' => $advert,
            'form'   => $form->createView(),

        ));
    }

    public function menuAction($limit)
    {
        $em = $this->getDoctrine()->getManager();
        $listAdverts = $em->getRepository("ERBoxShadowBundle:Advert")->findBy(
            array(),
            array('date' => 'desc'),
            $limit,
            0
        );

        return $this->render('ERBoxShadowBundle:Advert:menu.html.twig', array(
            'listAdverts' => $listAdverts
        ));
    }

    public function listAction()
    {
        $listAdvert = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('ERBoxShadowBundle:Advert')
            ->getAdvertWithApplications();

        foreach ($listAdvert as $advert) {
            $advert->getApplications();
        }
    }

    public function testAction()
    {
        $advert = new Advert();
        $advert->setDate(new \Datetime());  // Champ « date » OK
        $advert->setTitle('abc');           // Champ « title » incorrect : moins de 10 caractères
        //$advert->setContent('blabla');    // Champ « content » incorrect : on ne le définit pas
        $advert->setAuthor('A');            // Champ « author » incorrect : moins de 2 caractères

        // On récupère le service validator
        $validator = $this->get('validator');

        // On déclenche la validation sur notre object
        $listErrors = $validator->is($advert);

        // Si $listErrors n'est pas vide, on affiche les erreurs
        if(count($listErrors) > 0) {
            // $listErrors est un objet, sa méthode __toString permet de lister joliement les erreurs
            return new Response((string) $listErrors);
        } else {
            return new Response("L'annonce est valide !");
        }
    }
}
