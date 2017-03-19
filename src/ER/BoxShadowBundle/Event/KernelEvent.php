<?php

namespace ER\BoxShadowBundle\Event;

class onKernelController
{
    public function onKernelController(FilterControllerEvent $event)
    {
        // Vous pouvez récupérer le contrôleur que le noyau avait l'intention d'exécuter
        $controller = $event->getController();

        // Ici vous pouvez modifier la variable $controller, etc.
        // $controller doit être de type PHP callable

        // Si vous avez modifié le contrôleur, prévenez le noyau qu'il faut exécuter le vôtre :
        $event->setController($controller);
    }
}
