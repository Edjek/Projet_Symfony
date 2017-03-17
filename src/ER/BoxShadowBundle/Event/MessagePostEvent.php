<?php

namespace ER\BoxShadowBundle\Event;

use ER\UserBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;

class MessagePostEvent extends Event
{
    protected $message;
    protected $user;

    public function __construct($message, $user)
    {
        $this->message = $message;
        $this->user    = $user;
    }

    public function getMessage()
    {
        return $this->message;
    }

    // Le listener doit pouvoir modifier le message
    public function setMessage($message)
    {
        return $this->message = $message;
    }

    // Le listener doit avoir accès à l'utilisateur
    public function getUser()
    {
        return $this->user;
    }
}