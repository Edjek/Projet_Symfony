<?php

namespace ER\BoxShadowBundle\Listener;

use ER\BoxShadowBundle\BigBrother\MessageNotificator;
use ER\BoxShadowBundle\Event\BoxShadowEvents;
use ER\BoxShadowBundle\Event\MessagePostEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MessageListener implements EventSubscriberInterface
{
    protected $notificator;
    protected $listUsers = [];

    /**
     * MessageListener constructor.
     * @param $notificator MessageNotificator
     * @param array $listUsers
     */
    public function __construct(MessageNotificator $notificator, array $listUsers)
    {
        $this->notificator = $notificator;
        $this->listUsers = $listUsers;
    }

    static public function getSubscribedEvents()
    {
        // On retourne un tableau « nom de l'évènement » => « méthode à exécuter »
        return array(
            BoxShadowEvents::POST_MESSAGE => 'processMessage',
        );
    }

    public function processMessage(MessagePostEvent $event)
    {
        if (in_array($event->getUser()->getUserName(), $this->listUsers)) {
            $this->notificator->notifyByMail($event->getMessage(), $event->getUser());
        }
    }
}
