<?php

namespace ER\BoxShadowBundle\Antispam;

class ERAntispam
{
    private $mailer;
    private $myself;
    private $minLenght;

    public function __construct(\Swift_Mailer $mailer, $myself, $minLenght )
    {
        $this->mailer = $mailer;
        $this->myself = $myself;
        $this->minLenght = $minLenght;
    }

    public function isSpam($text)
    {
        return strlen($text) < $this->minLenght;
    }

}