<?php

namespace ER\BoxShadowBundle\DoctrineListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use ER\BoxShadowBundle\Email\ApplicationMailer;
use ER\BoxShadowBundle\Entity\Application;

class ApplicationCreationListener
{
    /**
     * @var ApplicationMailer
     */
    private $applicationMailer;

    public function __construct(ApplicationMailer $applicationMailer )
    {
        $this->applicationMailer = $applicationMailer;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof Application) {
            return;
        }

        $this->applicationMailer->sendNewNotification($entity);
    }
}
