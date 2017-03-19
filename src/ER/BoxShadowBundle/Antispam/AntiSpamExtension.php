<?php

namespace ER\BoxShadowBundle\Antispam;

class AntiSpamExtension extends \Twig_Extension
{
    /**
     * @var ERAntispam
     */
    private $erAntispam;

    public function __construct(ERAntispam $erAntispam)
    {
        $this->erAntispam = $erAntispam;
    }

    public function checkIfArgumentIsSpam($text)
    {
        return $this->erAntispam->isSpam($text);
    }

    // Twig va exécuter cette méthode pour savoir quelle(s) fonction(s) ajoute notre service
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('checkIfSpam', array($this, 'checkIfArgumentIsSpam')),
        );
    }

    // La méthode getName() identifie votre extension Twig, elle est obligatoire
    public function getName()
    {
        return 'ERAntispam';

    }
}
