<?php

namespace ER\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ERUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
