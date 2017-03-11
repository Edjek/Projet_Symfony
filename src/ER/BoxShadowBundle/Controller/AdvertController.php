<?php

namespace ER\BoxShadowBundle\Controller;

use ER\BoxShadowBundle\Entity\Advert;
use ER\BoxShadowBundle\Entity\AdvertSkill;
use ER\BoxShadowBundle\Entity\Application;
use ER\BoxShadowBundle\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdvertController extends Controller
{
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();
        $listAdverts = $em->getRepository("ERBoxShadowBundle:Advert")->findAll();

        // Et modifiez le 2nd argument pour injecter notre liste
        return $this->render('ERBoxShadowBundle:Advert:index.html.twig', array(
            'listAdverts' => $listAdverts
        ));
    }

    public function viewAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $advert = $em
            ->getRepository('ERBoxShadowBundle:Advert')
            ->find($id);

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
        $advert->setTitle('Recherche développeur Symfony.');
        $advert->setAuthor('Alexandre');
        $advert->setContent("Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…");

        $image = new Image();
        $image->setUrl('http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg');
        $image->setAlt('Job de rêve');

        $advert->setImage($image);

        $application1 = new Application();
        $application1->setAuthor('Mehdi');
        $application1->setContent('Je suis motivé');

        $application2 = new Application();
        $application2->setAuthor('rachid');
        $application2->setContent('J\'ai toutes les qualités');

        $application1->setAdvert($advert);
        $application2->setAdvert($advert);

        $em = $this->getDoctrine()->getManager();

        $listSkills = $em->getRepository("ERBoxShadowBundle:Skill")->findAll();

        foreach ($listSkills as $skill) {
            $advertSkill = new AdvertSkill();
            $advertSkill->setAdvert($advert);
            $advertSkill->setSkill($skill);
            $advertSkill->setLevel('Expert');
            $em->persist($advertSkill);
        }

        $em->persist($advert);
        $em->persist($application1);
        $em->persist($application2);

        $em->flush();

        // Si la requête est en POST, c'est que le visiteur a soumis le formulaire
        if ($request->isMethod('POST')) {
            // Ici, on s'occupera de la création et de la gestion du formulaire
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

            // Puis on redirige vers la page de visualisation de cettte annonce
            return $this->redirectToRoute('er_boxshadow_view', array(
                'id' => $advert->getId()
            ));
        }

        // Si on n'est pas en POST, alors on affiche le formulaire
        return $this->render('ERBoxShadowBundle:Advert:add.html.twig');
    }

    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $advert = $em->getRepository("ERBoxShadowBundle:Advert")->find($id);

        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }

        $listCategories = $em->getRepository("ERBoxShadowBundle:Category")->findAll();

        foreach ($listCategories as $category) {
            $advert->addCategory($category);
        }

        $em->flush();

        return $this->render('ERBoxShadowBundle:Advert:edit.html.twig', array(
            'advert' => $advert
        ));
    }

    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $advert = $em->getRepository("ERBoxShadowBundle:Advert")->find($id);
        $listAdverts = $em->getRepository("ERBoxShadowBundle:Advert")->findAll();
        $listAdvertSkill = $em->getRepository("ERBoxShadowBundle:AdvertSkill")->findByAdvert(array('advert' => $advert));
        $listApplications = $em->getRepository("ERBoxShadowBundle:Application")->findByAdvert(array('advert' => $advert));

    dump($advert->getApplications());
        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }

        foreach ($advert->getCategories() as $category) {
            $advert->removeCategory($category);
        }
        foreach ($advert->getApplications() as $application) {
            $advert->removeApplication($application);
        }
        foreach ($listAdvertSkill as $advertSkill) {
            $em->remove($advertSkill);
        }
        //$em->remove($advert);

        $em->flush();

        return $this->render('ERBoxShadowBundle:Advert:index.html.twig', array(
            'listAdverts' => $listAdverts
        ));
    }

    public function menuAction($limit)
    {
        $em = $this->getDoctrine()->getManager();
        $listAdverts = $em->getRepository("ERBoxShadowBundle:Advert")->findAll();

        return $this->render('ERBoxShadowBundle:Advert:menu.html.twig', array(
            // Tout l'intérêt est ici : le contrôleur passe
            // les variables nécessaires au template !
            'listAdverts' => $listAdverts
        ));
    }
}
