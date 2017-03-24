<?php

namespace ER\BoxShadowBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ContainerController extends Controller
{
    public function mailerAction()
    {
        $antispam = $this->get('er_boxshadow_antispam');
        if ($antispam->isSpam('test')) {
            throw new \Exception('Votre message a été détecté comme spam !');
        }
    }
}
