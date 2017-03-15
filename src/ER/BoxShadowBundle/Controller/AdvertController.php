<?php

namespace ER\BoxShadowBundle\Controller;

use ER\BoxShadowBundle\Entity\Advert;
use ER\BoxShadowBundle\Entity\Image;
use ER\BoxShadowBundle\Form\AdvertEditType;
use ER\BoxShadowBundle\Form\AdvertType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
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

        dump($listAdverts);
        $nbPages = ceil(count($listAdverts)/$nbPerpage);

        return $this->render('ERBoxShadowBundle:Advert:index.html.twig', array(
            'listAdverts' => $listAdverts,
            'nbPages' => $nbPages,
            'page' => $page
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

    public function addAction(Request $request)
    {
        $advert = new Advert();
        $form = $this->get('form.factory')->create(AdvertType::class, $advert);

        // Si la requête est en POST, c'est que le visiteur a soumis le formulaire
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
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

    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $advert = $em->getRepository("ERBoxShadowBundle:Advert")->find($id);
        $listAdvertSkill = $em->getRepository("ERBoxShadowBundle:AdvertSkill")->findBy(array('advert' => $advert));
        $listApplications = $em->getRepository("ERBoxShadowBundle:Application")->findBy(array('advert' => $advert));

        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }

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

        return new RedirectResponse($this->generateUrl('er_boxshadow_home'));
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
        $advert->setTitle("Recherche développeur !");
        $advert->setAuthor('Lucky Luke');
        $advert->setContent('Tire plus vite que son ombre.');
        $advert->setMail('luke@gmail.fr');

        $image = new Image();
        $image->setUrl('http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg');
        $image->setAlt('Job de rêve');

        $advert->setImage($image);

        $em = $this->getDoctrine()->getManager();
        $em->persist($advert);
        $em->flush(); // C'est à ce moment qu'est généré le slug

        return new Response('Slug généré : '.$advert->getSlug());
        // Affiche « Slug généré : recherche-developpeur »
    }
}
