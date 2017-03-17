<?php

namespace ER\BoxShadowBundle\Antispam;

class ERAntispam
{
    private $mailer;
    private $locale;
    private $minLenght;

    public function __construct(\Swift_Mailer $mailer, $minLenght )
    {
        $this->mailer = $mailer;
        $this->minLenght = $minLenght;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    public function isSpam($text)
    {
        return strlen($text) < $this->minLenght;
    }

}