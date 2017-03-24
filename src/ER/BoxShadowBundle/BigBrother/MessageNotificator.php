<?php

namespace ER\BoxShadowBundle\BigBrother;

use Symfony\Component\Security\Core\User\UserInterface;

class MessageNotificator
{
    protected $mailer;

    /**
     * MessageNotificator constructor.
     * @param $mailer
     */
    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function notifyByMail($message, UserInterface $user)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject("Nouveau message d'un utilisateur surveillé")
            ->setFrom('dihcar16ar@hotmail.fr')
            ->setTo('edjek@hotmail.fr')
            ->setBody("L'utilisateur surveillé '" . $user->getUsername() . "' a posté le message suivant : '" . $message . "'");

        if (!$message instanceof \Swift_Mime_Message) {
            throw new \Exception(sprintf('Expected Swift_Mime_Message, %s given', get_class($message)));
        }

        $this->mailer->send($message);
    }
}
